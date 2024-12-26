<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'classes/Article.php';
require_once 'classes/PdfProcessor.php';
require_once 'vendor/autoload.php'; // Pour PdfParser

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['pdf_file'])) {
        $file = $_FILES['pdf_file'];
        if (in_array($file['type'], ['application/pdf'])) {
            $filename = time() . '_' . $file['name'];
            $destination = UPLOAD_DIR . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
    try {
        $processor = new PdfProcessor();
        
        // Utilisation de la nouvelle méthode de traitement progressif
        $result = $processor->processDocumentProgressively($destination);
        
        // Traitement des résultats page par page
        if (!empty($result['success'])) {
            foreach ($result['success'] as $pageNum => $articles) {
                $processor->saveArticles($articles, $_SESSION['user_id']);
            }
            $success = sprintf(
                "PDF traité avec succès! %d pages traitées, %d erreurs", 
                count($result['success']), 
                count($result['errors'])
            );
        } else {
            $error = "Aucune page n'a pu être traitée correctement";
        }
        
        // Affichage des erreurs spécifiques par page
        if (!empty($result['errors'])) {
            foreach ($result['errors'] as $pageNum => $errorMsg) {
                error_log("Erreur page $pageNum : $errorMsg");
            }
        }
        
    } catch (Exception $e) {
        $error = "Erreur lors du traitement: " . $e->getMessage();
    }
}



        }
    }
}
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="container">
    <h1>Upload de PDF</h1>
    
    <?php if(isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if(isset($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="upload-form">
        <div class="form-group">
            <label for="pdf_file">Sélectionner un PDF:</label>
            <input type="file" id="pdf_file" name="pdf_file" accept=".pdf" required>
        </div>
        <button type="submit" class="submit-btn">Uploader</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
