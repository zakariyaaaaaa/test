<?php
session_start();
require "../connexion/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ SQL فيه جميع المعلومات
$stmt = $pdo->prepare("
    SELECT r.*, e.title, e.date_event, e.location, e.price
    FROM reservations r
    JOIN events e ON r.event_id = e.id
    WHERE r.user_id = ?
    ORDER BY r.reservation_date DESC
");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<style>
body {
    font-family: Arial;
    background:#f5f5f5;
}

.container {
    width:80%;
    margin:auto;
}

.card {
    background:white;
    padding:15px;
    margin:15px 0;
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

.status-valid { color:green; font-weight:bold; }
.status-used { color:red; font-weight:bold; }

img {
    margin-top:10px;
    border:1px solid #ddd;
    padding:5px;
    border-radius:5px;
}

.btn {
    display:inline-block;
    margin-top:10px;
    padding:8px 12px;
    background:#007bff;
    color:white;
    text-decoration:none;
    border-radius:5px;
}

.btn:hover {
    background:#0056b3;
}
</style>

</head>
<body>

<div class="container">

<h2>🎟️ My Tickets</h2>

<?php if (empty($reservations)): ?>
    <p>Ma kayn hata réservation ❌</p>
<?php else: ?>

    <?php foreach($reservations as $r): ?>

        <div class="card">

            <h3><?= htmlspecialchars($r['title']) ?></h3>

            <!-- 📅 Date -->
            <p>📅 Date: <?= date("d M Y", strtotime($r['date_event'])) ?></p>

            <!-- 📍 Location -->
            <p>📍 Location: <?= htmlspecialchars($r['location']) ?></p>

            <!-- 💰 Price -->
            <p>💰 Price: <?= htmlspecialchars($r['price']) ?> DH</p>

            <!-- Status -->
            <p>
                Status:
                <span class="status-<?= $r['status'] ?>">
                    <?= $r['status'] === 'valid' ? '✅ Valid' : '❌ Used' ?>
                </span>
            </p>

            <!-- QR Code -->
            <?php if(!empty($r['qr_code'])): ?>
                <img src="../<?= $r['qr_code'] ?>" width="150">
            <?php else: ?>
                <p>QR not generated ⚠️</p>
            <?php endif; ?>

            <br>

            <!-- Download -->
            <a class="btn" href="download.php?id=<?= $r['id'] ?>">
                📄 Download PDF
            </a>

        </div>

    <?php endforeach; ?>

<?php endif; ?>

<br>
<a href="index.php">⬅ Retour</a> |
<a href="logout.php">🚪 Logout</a>

</div>

</body>
</html>