<?php
$host = 'localhost';
$dbname = 'gestion_reservations';
$user = 'root';
$db_password = '';
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

try {
    $pdo = new PDO($dsn, $user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("connexion failed" . $e->getMessage()) ;
}
