<?php
session_start();
require_once '../connexion/config.php';

// message d'erreur ila login ma tsawbch
$error = "";

// ila user clika 3la bouton login
if (isset($_POST['login'])) {

    // récupération dyal les champs
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // recherche user f database b email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // vérification password (hash)
    if ($user && password_verify($password, $user['password'])) {

        // création session dyal user
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        // redirect l page principale
        header("Location: index.php");
        exit();

    } else {
        // ila email ou password ghalat
        $error = "Email ou mot de passe incorrect";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- css dyal login -->
    <link rel="stylesheet" href="assets/login.css">
</head>

<body>

<div class="auth-container">

    <!-- form login -->
    <form method="POST" class="auth-card">

        <h2> Login</h2>

        <!-- afficher error ila kayn -->
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <!-- champ email -->
        <input type="email" name="email" placeholder="📧 Email" required>

        <!-- champ password -->
        <input type="password" name="password" placeholder="🔒 Password" required>

        <!-- bouton login -->
        <button type="submit" name="login">Login</button>

        <!-- lien signup -->
        <a href="signup.php">Create account</a>

    </form>

</div>

</body>
</html>