<?php
require_once __DIR__ . '/Article.php';

class PdfProcessor {
    private $apiKey;
    private $db;

    public function __construct() {
        $this->apiKey = 'AIzaSyB-JKccGi9qE__ecPwmGi4Zpb_RTZW69Ao';
        $this->db = Database::getInstance()->getConnection();
    }

    public function extractTextFromPdf($filepath) {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($filepath);
        return $pdf->getText();
    }

    public function processWithGemini($text) {
	// Augmenter la limite de temps d'exécution à 300 secondes (5 minutes)
    set_time_limit(300);
    ini_set('max_execution_time', 300);
    //
	$curl = curl_init();
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $this->apiKey;
    
    $prompt = "Vous agissez comme un expert en analyse de documents et extraction d'informations. Le texte fourni contient des articles journalistiques. Votre tâche est de :

    1. Identifier chaque article distinct dans le document.
    2. Pour chaque article, extraire les informations suivantes :
       - Le titre de l'article
       - Le contenu principal
       - Le rédacteur (s'il est mentionné)
       - La date de publication (si présente)
    3. Retournez les résultats sous forme d'un tableau JSON avec les champs suivants :
    [
        {
            'titre': 'Titre de l'article',
            'contenu': 'Contenu complet de l'article',
            'redacteur': 'Nom du rédacteur (si disponible)',
            'date': 'Date de publication (si disponible)'
        }
    ]
    4. Si des sections du texte ne contiennent pas ces informations, ignorez-les.

    Voici le texte à analyser : " . $text;

    $data = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => $prompt
                    ]
                ]
            ]
        ]
    ];

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_SSL_VERIFYPEER => false,  
        CURLOPT_SSL_VERIFYHOST => false,  
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($curl);
    
    if(curl_errno($curl)) {
        throw new Exception('Erreur Curl : ' . curl_error($curl));
    }
    
    curl_close($curl);
    $result = json_decode($response, true);
    
    // Ajout d'un log pour debug
    error_log('Réponse Gemini : ' . print_r($result, true));
    
    return $result;
}

public function processDocumentProgressively($filepath) {
    $parser = new \Smalot\PdfParser\Parser();
    $pdf = $parser->parseFile($filepath);
    $pages = $pdf->getPages();
    $totalPages = count($pages);
    $results = [];
    $errors = [];
    
    foreach ($pages as $pageNum => $page) {
        try {
            $text = $page->getText();
            $response = $this->processWithGemini($text);
            
            if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                $jsonText = $response['candidates'][0]['content']['parts'][0]['text'];
                $jsonText = preg_replace('/```json\s*|\s*```/', '', $jsonText);
                $articles = json_decode($jsonText, true);
                $results[$pageNum + 1] = $articles;
            }
            
            $progress = [
                'page' => $pageNum + 1,
                'total' => $totalPages,
                'percentage' => round(($pageNum + 1) * 100 / $totalPages),
                'status' => 'success'
            ];
            
            echo json_encode($progress);
            ob_flush();
            flush();
            
        } catch (Exception $e) {
            $errors[$pageNum + 1] = $e->getMessage();
        }
    }
    
    return [
        'success' => $results,
        'errors' => $errors
    ];
}


    public function saveArticles($articles, $userId) {
    $articleObj = new Article();
    foreach ($articles as $article) {
        $articleObj->create(
            $article['titre'] ?? null,         // French field name from Gemini
            $article['contenu'] ?? null,       // French field name from Gemini
            $article['redacteur'] ?? null,     // French field name from Gemini
            $article['date'] ?? date('Y-m-d'), // French field name from Gemini
            $userId
        );
    }
}

}
