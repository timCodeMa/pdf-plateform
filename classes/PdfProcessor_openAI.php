<?php
class PdfProcessor {
    private $apiKey;
    private $db;

    public function __construct() {
        $this->apiKey = 'VOTRE_CLE_API_OPENAI';
        $this->db = Database::getInstance()->getConnection();
    }

    public function extractTextFromPdf($filepath) {
        // Utilisation de la bibliothèque Smalot\PdfParser
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($filepath);
        return $pdf->getText();
    }

    public function processWithOpenAI($text) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Extraire les articles de ce texte et les formater en JSON avec titre, contenu, auteur et date.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $text
                    ]
                ]
            ])
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    public function saveArticles($articles, $userId) {
        $articleObj = new Article();
        foreach ($articles as $article) {
            $articleObj->create(
                $article['title'],
                $article['content'],
                $article['author'],
                $article['date'],
                $userId
            );
        }
    }
}
