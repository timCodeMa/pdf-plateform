<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'classes/Article.php';
require_once 'templates/header.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$article = new Article();
$articles = $article->getAllArticles();
?>

<div class="container">
    <h1>Liste des Articles</h1>
    
    <div class="articles-grid">
        <?php foreach($articles as $art): ?>
            <div class="article-card">
                <h2><?= htmlspecialchars($art['title']) ?></h2>
                <p class="article-meta">
                    Par <?= htmlspecialchars($art['author']) ?> 
                    le <?= date('d/m/Y', strtotime($art['publication_date'])) ?>
                </p>
                <div class="article-preview">
                    <?= substr(htmlspecialchars($art['content']), 0, 200) ?>...
                </div>
                <a href="article-detail.php?id=<?= $art['id'] ?>" class="btn-read-more">Lire la suite</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
