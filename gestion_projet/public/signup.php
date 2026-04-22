<?php
session_start();
require_once '../connexion/config.php';

// tableau dyal les erreurs
$errors = [];
$name = '';
$email = '';

// ila المستخدم ضغط على submit
if (isset($_POST["submit"])) {

    // récupération + nettoyage dyal data
    $name  = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $password          = $_POST["password"];
    $password_confirmer = $_POST["confirme"];

    // ---------------- validation ----------------

    // vérifier nom (minimum 3 caractères)
    if (strlen($name) < 3) {
        $errors[] = "Nom invalide (minimum 3 caractères)";
    }

    // vérifier format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide";
    }

    // password khaso يكون قوي شوية
    if (strlen($password) < 8) {
        $errors[] = "Mot de passe minimum 8 caractères";
    }

    // خاصو majuscule
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Mot de passe doit contenir une lettre majuscule";
    }

    // خاصو رقم
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Mot de passe doit contenir un chiffre";
    }

    // confirmation dyal password
    if ($password !== $password_confirmer) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }

    // ---------------- check email ----------------

    // نشوف واش email déjà kayn f database
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        $errors[] = "Email déjà utilisé";
    }

    // ---------------- insertion ----------------

    // ila ماكان حتى error → ندخلو user
    if (empty($errors)) {

        // hash password باش يكون sécurisé
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // insertion f table users
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hash]);

        // redirection ل login
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="assets/signup.css">
</head>
<body>

    <h2>Créer un compte</h2>

    <!-- afficher les erreurs ila كانو -->
    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- formulaire inscription -->
    <form method="POST" action="signup.php">

        <!-- input nom -->
        <input type="text" name="name" placeholder="Nom" value="<?= $name ?>">

        <!-- input email -->
        <input type="email" name="email" placeholder="Email" value="<?= $email ?>">

        <!-- password -->
        <input type="password" name="password" placeholder="Mot de passe">

        <!-- confirmation -->
        <input type="password" name="confirme" placeholder="Confirmer mot de passe">

        <!-- button -->
        <button type="submit" name="submit">S'inscrire</button>

    </form>

    <!-- lien login -->
    <a href="login.php">Déjà un compte ? Se connecter</a>

</body>
</html>