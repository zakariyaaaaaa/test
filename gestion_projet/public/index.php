<?php
session_start();
require_once '../connexion/config.php';

// récupérer les events li mazal (date >= today)
$stmt = $pdo->prepare("SELECT * FROM events WHERE date_event >= CURDATE()");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Events</title>

    <!-- css global dyal site -->
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<!-- TOPBAR -->
<div class="topbar">

    <!-- titre dyal page -->
    <h1> ✨ Events</h1>

    <div class="right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- ila user connecté -->
            <a href="dashboard.php">👤 Dashboard</a>
            <a href="logout.php">⏻ Logout</a>
        <?php else: ?>
            <!-- ila ma connectach -->
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>

</div>

<!-- CONTAINER -->
<div class="container">

<?php foreach ($events as $event): ?>
    <div class="card">

        <!-- infos dyal event -->
        <div class="info">
            <h3><?= htmlspecialchars($event['title']) ?></h3>
            <p><?= htmlspecialchars($event['date_event']) ?></p>
            <p><?= htmlspecialchars($event['location']) ?></p>
            <p><?= htmlspecialchars($event['price']) ?> DH</p>
            <p>Places: <?= (int)$event['nbPlaces'] ?></p>
        </div>

        <!-- bouton côté droite -->
        <div>

        <?php if (isset($_SESSION['user_id'])): ?>

            <?php if ($event['nbPlaces'] > 0): ?>
                <!-- ila kayn places → réserver -->
                <form method="POST" action="payment.php">
                    <input type="hidden" name="event_id" value="<?= (int)$event['id'] ?>">
                    <button class="btn">Réserver</button>
                </form>

            <?php else: ?>
                <span class="sold">SOLD OUT</span>
            <?php endif; ?>

        <?php else: ?>
            <!-- ila user ma connectéch -->
            <a href="login.php" class="btn">Login</a>
        <?php endif; ?>

        </div>

    </div>
<?php endforeach; ?>

</div>

</body>
</html>