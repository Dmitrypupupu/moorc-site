<?php

namespace App\Controllers;

use App\Database;
use App\View;
use PDO;

class DocumentController
{
    public function index(): string
    {
        try {
            $pdo = Database::pdo();
            
            // Get documents by category
            $stmt = $pdo->query('
                SELECT * FROM documents 
                WHERE is_public = true 
                ORDER BY category, created_at DESC
            ');
            $allDocs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group by category
            $documents = [];
            foreach ($allDocs as $doc) {
                $category = $doc['category'] ?? 'other';
                if (!isset($documents[$category])) {
                    $documents[$category] = [];
                }
                $documents[$category][] = $doc;
            }

            return View::render('documents.index', [
                'title' => 'Документы',
                'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
                'env' => $_ENV['APP_ENV'] ?? 'local',
                'documents' => $documents,
            ]);
        } catch (\Exception $e) {
            return View::render('error', [
                'title' => 'Ошибка',
                'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
                'env' => $_ENV['APP_ENV'] ?? 'local',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
