<?php
session_start();
require_once '../connexion/config.php';

// vérifier wach kayn id f URL
if (!isset($_GET['id'])) {
    die("Invalid ticket ❌");
}

$id = (int) $_GET['id'];

// récupérer infos dyal réservation + event
$stmt = $pdo->prepare("
    SELECT r.*, e.title, e.date_event, e.location
    FROM reservations r
    JOIN events e ON r.event_id = e.id
    WHERE r.id = ?
");
$stmt->execute([$id]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

// ila mal9inach ticket
if (!$reservation) {
    die("❌ Ticket not found");
}

// check wach ticket déjà utilisé ou mazal
$isUsed = $reservation['status'] === 'used';

// ila mazal valid → nبدلو status ل used
if (!$isUsed) {
    $pdo->prepare("UPDATE reservations SET status = 'used' WHERE id = ?")
        ->execute([$id]);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ticket</title>

<style>
/* design général dyal page */
body {
    font-family: "Poppins", sans-serif;
    background: #020617;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* card dyal ticket */
.card {
    width: 360px;
    padding: 25px;
    background: #0f172a;
    border-radius: 15px;
    text-align: center;
    border: 1px solid rgba(56,189,248,0.3);
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}

/* titre */
.card h1 {
    font-size: 22px;
    margin-bottom: 15px;
}

/* status */
.valid {
    color: #22c55e;
}

.used {
    color: #ef4444;
}

/* texte */
.card p {
    margin: 8px 0;
    font-size: 14px;
    color: #cbd5f5;
}

/* ligne séparation */
.divider {
    height: 1px;
    background: rgba(255,255,255,0.1);
    margin: 15px 0;
}

/* id dyal ticket */
.ticket-id {
    margin-top: 15px;
    font-size: 13px;
    opacity: 0.7;
}
</style>

</head>
<body>

<div class="card">

    <?php if ($isUsed): ?>
        <h1 class="used"> Ticket Used</h1>
    <?php else: ?>
        <h1 class="valid">Ticket Valid</h1>
    <?php endif; ?>

    <div class="divider"></div>

    <!-- infos dyal event -->
    <p><strong> Event:</strong><br><?= htmlspecialchars($reservation['title']) ?></p>
    <p><strong> Date:</strong><br><?= htmlspecialchars($reservation['date_event']) ?></p>
    <p><strong> Location:</strong><br><?= htmlspecialchars($reservation['location']) ?></p>

    <div class="divider"></div>

    <!-- id unique dyal ticket -->
    <p class="ticket-id">Ticket ID: #<?= $reservation['id'] ?></p>

</div>

</body>
</html>