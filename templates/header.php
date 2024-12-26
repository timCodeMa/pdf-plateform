<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire d'Articles PDF</title>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
    <nav>
		<ul>
			<li><a href="<?= SITE_URL ?>/index.php">Accueil</a></li>
			<?php if(isset($_SESSION['user_id'])): ?>
				<li><a href="<?= SITE_URL ?>/upload.php">Upload PDF</a></li>
				<li><a href="<?= SITE_URL ?>/articles.php">Articles</a></li>
				<li><a href="<?= SITE_URL ?>/logout.php">Déconnexion</a></li>
			<?php else: ?>
				<li><a href="<?= SITE_URL ?>/login.php">Connexion</a></li>
				<li><a href="<?= SITE_URL ?>/register.php">Inscription</a></li>
			<?php endif; ?>
		</ul>
	</nav>
