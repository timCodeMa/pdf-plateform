<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'classes/Article.php';
require_once 'templates/header.php';

// Récupération des derniers articles
$article = new Article();
$latestArticles = $article->getAllArticles();
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="container">
    <div class="welcome-section">
        <h1>Gestionnaire d'Articles PDF</h1>
        <?php if(!isset($_SESSION['user_id'])): ?>
            <div class="cta-buttons">
                <a href="login.php" class="btn btn-primary">Se connecter</a>
                <a href="register.php" class="btn btn-secondary">S'inscrire</a>
            </div>
        <?php else: ?>
            <div class="user-actions">
				<a href="upload.php" class="btn btn-primary">Uploader un PDF</a>
			</div>
        <?php endif; ?>
    </div>

    <div class="recent-articles">
        <h2>Articles Récents</h2>
        <div class="articles-grid">
            <?php foreach($latestArticles as $article): ?>
                <div class="article-card">
                    <h3><?= htmlspecialchars($article['title']) ?></h3>
                    <p class="article-meta">
                        Par <?= htmlspecialchars($article['author']) ?> 
                        le <?= date('d/m/Y', strtotime($article['publication_date'])) ?>
                    </p>
                    <p class="article-preview">
                        <?= substr(htmlspecialchars($article['content']), 0, 150) ?>...
                    </p>
                    <a href="articles.php?id=<?= $article['id'] ?>" class="btn-read-more">Lire la suite</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
