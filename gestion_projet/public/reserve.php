<?php
session_start();
require_once '../connexion/config.php';

// verifier wach user connecte
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// load library dyal QR code
require __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// recuperer user + event id
$user_id  = $_SESSION['user_id'];
$event_id = (int) $_POST['event_id'];

// recuperer infos dyal event
$stmt = $pdo->prepare("SELECT title, date_event, location, nbPlaces FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

// ila event ma kaynch
if (!$event) {
    header("Location: index.php");
    exit();
}

// verifier wach kayn places
if ($event['nbPlaces'] > 0) {

    // check ila user deja reserve had event
    $check = $pdo->prepare("SELECT id FROM reservations WHERE user_id = ? AND event_id = ?");
    $check->execute([$user_id, $event_id]);

    if ($check->fetch()) {
        header("Location: dashboard.php?error=already_reserved");
        exit();
    }

    // insertion dyal reservation
    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, event_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $event_id]);

    // id dyal reservation jdida
    $reservation_id = $pdo->lastInsertId();

    // tn9is nbPlaces
    $pdo->prepare("UPDATE events SET nbPlaces = nbPlaces - 1 WHERE id = ?")
        ->execute([$event_id]);

    // smiya dyal user (fallback ila makanch)
    $name = $_SESSION['user_name'] ?? "User";

    // contenu li ghadi ykoun f QR
    $data = "Ticket\n";
    $data .= "Name: " . $name . "\n";
    $data .= "Event: " . $event['title'] . "\n";
    $data .= "Date: " . $event['date_event'] . "\n";
    $data .= "Location: " . $event['location'] . "\n";
    $data .= "Reservation ID: " . $reservation_id;

    // creation QR code
    $qrCode = new QrCode($data);
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    // definir path dyal fichier
    $fileName = "qrcode_" . $reservation_id . ".png";
    $fullPath = __DIR__ . "/../uploads/" . $fileName;

    // sauvegarde image
    $result->saveToFile($fullPath);

    // verifier wach t sauvegarda mzyan
    if (!file_exists($fullPath)) {
        die("QR failed ❌");
    }

    // path li ghadi it7at f DB
    $dbPath = "uploads/" . $fileName;

    // update reservation b qr_code
    $pdo->prepare("UPDATE reservations SET qr_code = ? WHERE id = ?")
        ->execute([$dbPath, $reservation_id]);

    // redirect l dashboard
    header("Location: dashboard.php?success=1");
    exit();

} else {
    // ila sold out
    header("Location: index.php?error=sold_out");
    exit();
}