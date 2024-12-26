<?php
class Article {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($title, $content, $author, $publicationDate, $userId) {
		// Valeurs par défaut pour les champs qui peuvent être null
		$author = $author ?? 'Auteur inconnu';
		$publicationDate = $publicationDate ?? date('Y-m-d');
		
		// Vérification des champs obligatoires
		if (empty($title)) {
			$title = 'Sans titre';
		}
		if (empty($content)) {
			$content = 'Contenu non disponible';
		}
	
		$query = "INSERT INTO articles (title, content, author, publication_date, user_id) 
				VALUES (:title, :content, :author, :publication_date, :user_id)";
		
		$stmt = $this->db->prepare($query);
		return $stmt->execute([
			'title' => $title,
			'content' => $content,
			'author' => $author,
			'publication_date' => $publicationDate,
			'user_id' => $userId
		]);
	}

    public function getAllArticles() {
        $query = "SELECT * FROM articles ORDER BY publication_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getArticleById($id) {
        $query = "SELECT * FROM articles WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
}
