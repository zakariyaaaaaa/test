<?php
session_start();
require_once '../connexion/config.php';
require __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// vérifier user
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id  = $_SESSION['user_id'];
$event_id = (int) $_GET['event_id'];

// récupérer event
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Event not found ❌");
}

// vérifier places
if ($event['nbPlaces'] <= 0) {
    die("Sold out ❌");
}

// insert reservation
$stmt = $pdo->prepare("INSERT INTO reservations (user_id, event_id) VALUES (?, ?)");
$stmt->execute([$user_id, $event_id]);

$reservation_id = $pdo->lastInsertId();

// update places
$pdo->prepare("UPDATE events SET nbPlaces = nbPlaces - 1 WHERE id = ?")
    ->execute([$event_id]);

// user name
$name = $_SESSION['user_name'] ?? "User";

// contenu QR
$data = "Ticket\n";
$data .= "Name: $name\n";
$data .= "Event: {$event['title']}\n";
$data .= "Date: {$event['date_event']}\n";
$data .= "Location: {$event['location']}\n";
$data .= "ID: $reservation_id";

// generate QR
$qrCode = new QrCode($data);
$writer = new PngWriter();
$result = $writer->write($qrCode);

// save QR
$fileName = "qrcode_" . $reservation_id . ".png";
$fullPath = __DIR__ . "/../uploads/" . $fileName;

$result->saveToFile($fullPath);

// save DB
$dbPath = "uploads/" . $fileName;

$pdo->prepare("UPDATE reservations SET qr_code = ? WHERE id = ?")
    ->execute([$dbPath, $reservation_id]);
?>

<!DOCTYPE html>
<html>
<head>
<title>Success</title>
</head>
<body>

<h2>✅ Payment Success</h2>
<p>Ticket created 🎟️</p>

<img src="../<?= $dbPath ?>" width="200">

<br><br>
<a href="dashboard.php">Go to dashboard</a>

</body>
</html>