<?php
session_start();

// load Stripe + DB
require __DIR__ . '/../vendor/autoload.php';
require_once '../connexion/config.php';

// check user
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// récupérer event_id
if (!isset($_POST['event_id'])) {
    die("Event manquant ❌");
}

$event_id = (int) $_POST['event_id'];

// récupérer infos dyal event
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Event not found ❌");
}

// verifier places
if ($event['nbPlaces'] <= 0) {
    die("Sold out ❌");
}

// Stripe secret key
\Stripe\Stripe::setApiKey('sk_test_51TOagtBLRMTX4QBlssAcnT5x5neZvW4G2pjZ8IJ6ncOXdWRhXaOhUM5VV316jXPCldLU8xOlV2c78xoPcrzDfTri00T9nZSBNE'); 

// créer session paiement
$sessionStripe = \Stripe\Checkout\Session::create([

    'payment_method_types' => ['card'],

    'line_items' => [[
        'price_data' => [
            'currency' => 'mad',
            'product_data' => [
                'name' => $event['title'],
            ],
            'unit_amount' => (int)($event['price'] * 100), // DH → centimes
        ],
        'quantity' => 1,
    ]],

    'mode' => 'payment',

    // redirect après paiement
    'success_url' => 'http://localhost/gestion-des-evenement/gestion%20projet/public/success.php?event_id=' . $event_id,
    
    'cancel_url'  => 'http://localhost/gestion-des-evenement/gestion%20projet/public/index.php',
    
]);

// redirect vers Stripe
header("Location: " . $sessionStripe->url);
exit();