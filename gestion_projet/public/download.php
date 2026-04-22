<?php
require __DIR__ . '/../vendor/autoload.php';
require "../connexion/config.php";

use Dompdf\Dompdf;


if (!isset($_GET['id'])) {
    die("Invalid ID ❌");
}

$id = (int) $_GET['id'];


$stmt = $pdo->prepare("
    SELECT r.*, e.title, e.date_event, e.location
    FROM reservations r
    JOIN events e ON r.event_id = e.id
    WHERE r.id = ?
");
$stmt->execute([$id]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$r) {
    die("Reservation not found ❌");
}


$path = __DIR__ . "/../" . $r['qr_code'];

if (!file_exists($path)) {
    die("QR image not found ❌");
}

$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);


$html = "
<style>
body {
    font-family: DejaVu Sans, sans-serif;
    background: #f4f4f4;
}

/* TICKET */
.ticket {
    width: 420px;
    margin: 40px auto;
    background: #ffffff;
    border-radius: 15px;
    overflow: hidden;
    border: 1px solid #ddd;
}

/* HEADER */
.header {
    background: #007bff;
    color: white;
    text-align: center;
    padding: 18px;
    font-size: 20px;
    font-weight: bold;
}

/* CONTENT */
.content {
    padding: 20px;
    text-align: center;
}

/* TEXT */
.content p {
    margin: 8px 0;
    font-size: 14px;
}

/* DIVIDER */
.divider {
    height: 1px;
    background: #eee;
    margin: 15px 0;
}

/* QR */
.qr img {
    margin-top: 10px;
    border: 1px solid #ccc;
    padding: 5px;
    border-radius: 8px;
}

/* FOOTER */
.footer {
    background: #f1f5f9;
    text-align: center;
    padding: 10px;
    font-size: 12px;
    color: #555;
}
</style>

<div class='ticket'>

    <div class='header'>
         EVENT TICKET
    </div>

    <div class='content'>

        <p><strong> Event:</strong><br>".htmlspecialchars($r['title'])."</p>
        <p><strong> Date:</strong><br>".htmlspecialchars($r['date_event'])."</p>
        <p><strong> Location:</strong><br>".htmlspecialchars($r['location'])."</p>

        <div class='divider'></div>

        <p><strong> Ticket ID:</strong> #".$r['id']."</p>

        <div class='qr'>
            <img src='".$base64."' width='130'>
        </div>

    </div>

    <div class='footer'>
        Scan QR code at entry
    </div>

</div>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();


$dompdf->stream("ticket_" . $r['id'] . ".pdf", ["Attachment" => true]);