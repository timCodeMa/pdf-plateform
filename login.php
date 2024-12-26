<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'classes/User.php';
require_once 'templates/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    if ($user->login($_POST['email'], $_POST['password'])) {
        $_SESSION['user_id'] = $user->getId();
        header('Location: index.php');
        exit;
    } else {
        $error = 'Email ou mot de passe incorrect';
    }
}
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="container">
    <h1>Connexion</h1>
    <?php if($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" class="form-login">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Se connecter</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
