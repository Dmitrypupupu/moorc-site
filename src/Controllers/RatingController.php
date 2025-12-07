<?php

namespace App\Controllers;

use App\Database;
use App\View;
use PDO;

class RatingController
{
    public function index(): string
    {
        try {
            $pdo = Database::pdo();
            
            // Get all disciplines
            $stmt = $pdo->query('
                SELECT * FROM disciplines 
                WHERE is_active = true 
                ORDER BY sort_order
            ');
            $disciplines = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get selected discipline from query parameter
            $selectedDiscipline = $_GET['discipline'] ?? '333';
            
            // Get rankings for selected discipline
            $stmt = $pdo->prepare('
                SELECT 
                    u.id,
                    u.first_name,
                    u.last_name,
                    u.city,
                    u.region,
                    u.wca_id,
                    MIN(r.best) as best_single,
                    MIN(r.average) as best_average,
                    COUNT(DISTINCT r.competition_id) as competitions_count
                FROM users u
                JOIN results r ON u.id = r.user_id
                JOIN disciplines d ON r.discipline_id = d.id
                WHERE d.code = ? AND r.best IS NOT NULL
                GROUP BY u.id, u.first_name, u.last_name, u.city, u.region, u.wca_id
                ORDER BY 
                    CASE WHEN MIN(r.average) IS NOT NULL THEN MIN(r.average) ELSE 999999999 END,
                    MIN(r.best)
            ');
            $stmt->execute([$selectedDiscipline]);
            $rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Add ranking position
            $position = 1;
            foreach ($rankings as &$rank) {
                $rank['position'] = $position++;
            }

            return View::render('rating.index', [
                'title' => 'Рейтинг',
                'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
                'env' => $_ENV['APP_ENV'] ?? 'local',
                'disciplines' => $disciplines,
                'selectedDiscipline' => $selectedDiscipline,
                'rankings' => $rankings,
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

    public function profile(int $userId): string
    {
        try {
            $pdo = Database::pdo();
            
            // Get user data
            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                http_response_code(404);
                return View::render('error', [
                    'title' => 'Ошибка',
                    'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
                    'env' => $_ENV['APP_ENV'] ?? 'local',
                    'error' => 'Пользователь не найден',
                ]);
            }

            // Get user's personal bests
            $stmt = $pdo->prepare('
                SELECT 
                    d.name as discipline_name,
                    d.code as discipline_code,
                    MIN(r.best) as best_single,
                    MIN(r.average) as best_average
                FROM results r
                JOIN disciplines d ON r.discipline_id = d.id
                WHERE r.user_id = ? AND r.best IS NOT NULL
                GROUP BY d.id, d.name, d.code, d.sort_order
                ORDER BY d.sort_order
            ');
            $stmt->execute([$userId]);
            $personalBests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get competition history
            $stmt = $pdo->prepare('
                SELECT DISTINCT
                    c.id,
                    c.name,
                    c.city,
                    c.start_date,
                    c.end_date
                FROM competitions c
                JOIN results r ON c.id = r.competition_id
                WHERE r.user_id = ?
                ORDER BY c.start_date DESC
            ');
            $stmt->execute([$userId]);
            $competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return View::render('rating.profile', [
                'title' => $user['first_name'] . ' ' . $user['last_name'],
                'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
                'env' => $_ENV['APP_ENV'] ?? 'local',
                'user' => $user,
                'personalBests' => $personalBests,
                'competitions' => $competitions,
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
