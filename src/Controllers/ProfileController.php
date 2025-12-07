<?php

namespace App\Controllers;

use App\Database;
use App\View;
use PDO;

class ProfileController
{
    public function index(): string
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        try {
            $pdo = Database::pdo();
            
            // Get user data
            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get user's competition count
            $stmt = $pdo->prepare('
                SELECT COUNT(DISTINCT competition_id) as count 
                FROM registrations 
                WHERE user_id = ? AND status = ?
            ');
            $stmt->execute([$_SESSION['user_id'], 'approved']);
            $competitionsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

            // Get user's results
            $stmt = $pdo->prepare('
                SELECT 
                    r.*,
                    c.name as competition_name,
                    c.start_date,
                    d.name as discipline_name,
                    d.code as discipline_code
                FROM results r
                JOIN competitions c ON r.competition_id = c.id
                JOIN disciplines d ON r.discipline_id = d.id
                WHERE r.user_id = ?
                ORDER BY c.start_date DESC, d.sort_order
                LIMIT 20
            ');
            $stmt->execute([$_SESSION['user_id']]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get user's best results per discipline
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
            $stmt->execute([$_SESSION['user_id']]);
            $personalBests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return View::render('profile.index', [
                'title' => 'Личный кабинет',
                'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
                'env' => $_ENV['APP_ENV'] ?? 'local',
                'user' => $user,
                'competitionsCount' => $competitionsCount,
                'results' => $results,
                'personalBests' => $personalBests,
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

    public function edit(): string
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        try {
            $pdo = Database::pdo();
            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return View::render('profile.edit', [
                'title' => 'Редактировать профиль',
                'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
                'env' => $_ENV['APP_ENV'] ?? 'local',
                'user' => $user,
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

    public function update(array $request): string
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        try {
            $pdo = Database::pdo();
            
            $stmt = $pdo->prepare('
                UPDATE users 
                SET first_name = ?, 
                    last_name = ?, 
                    birth_date = ?, 
                    city = ?, 
                    region = ?, 
                    wca_id = ?,
                    updated_at = now()
                WHERE id = ?
            ');
            $stmt->execute([
                $request['first_name'] ?? '',
                $request['last_name'] ?? '',
                $request['birth_date'] ?: null,
                $request['city'] ?? '',
                $request['region'] ?? '',
                $request['wca_id'] ?: null,
                $_SESSION['user_id']
            ]);

            $_SESSION['success'] = 'Профиль обновлен';
            header('Location: /profile');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Ошибка обновления: ' . $e->getMessage();
            header('Location: /profile/edit');
            exit;
        }
    }
}
