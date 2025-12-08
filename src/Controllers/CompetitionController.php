<?php

namespace App\Controllers;

use App\Database;
use App\View;
use PDO;

class CompetitionController
{
    public function index(): string
    {
        try {
            $pdo = Database::pdo();
            
            // Get upcoming competitions
            $stmt = $pdo->prepare('
                SELECT * FROM competitions 
                WHERE status != ? AND start_date >= CURRENT_DATE
                ORDER BY start_date ASC
            ');
            $stmt->execute(['cancelled']);
            $upcoming = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get past competitions
            $stmt = $pdo->query('
                SELECT * FROM competitions 
                WHERE start_date < CURRENT_DATE
                ORDER BY start_date DESC
                LIMIT 10
            ');
            $past = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return View::render('competitions.index', [
                'title' => 'Календарь соревнований',
                'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
                'env' => $_ENV['APP_ENV'] ?? 'local',
                'upcoming' => $upcoming,
                'past' => $past,
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

    public function show(int $id): string
    {
        try {
            $pdo = Database::pdo();
            
            // Get competition
            $stmt = $pdo->prepare('SELECT * FROM competitions WHERE id = ?');
            $stmt->execute([$id]);
            $competition = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$competition) {
                http_response_code(404);
                return View::render('error', [
                    'title' => 'Ошибка',
                    'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
                    'env' => $_ENV['APP_ENV'] ?? 'local',
                    'error' => 'Соревнование не найдено',
                ]);
            }

            // Get disciplines
            $stmt = $pdo->prepare('
                SELECT d.* 
                FROM disciplines d
                JOIN competition_disciplines cd ON d.id = cd.discipline_id
                WHERE cd.competition_id = ?
                ORDER BY d.sort_order
            ');
            $stmt->execute([$id]);
            $disciplines = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get registrations count
            $stmt = $pdo->prepare('
                SELECT COUNT(*) as count 
                FROM registrations 
                WHERE competition_id = ? AND status = ?
            ');
            $stmt->execute([$id, 'approved']);
            $registrationsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

            // Check if current user is registered
            $userRegistered = false;
            session_start();
            if (isset($_SESSION['user_id'])) {
                $stmt = $pdo->prepare('
                    SELECT id FROM registrations 
                    WHERE competition_id = ? AND user_id = ?
                ');
                $stmt->execute([$id, $_SESSION['user_id']]);
                $userRegistered = (bool)$stmt->fetch();
            }

            return View::render('competitions.show', [
                'title' => $competition['name'],
                'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
                'env' => $_ENV['APP_ENV'] ?? 'local',
                'competition' => $competition,
                'disciplines' => $disciplines,
                'registrationsCount' => $registrationsCount,
                'userRegistered' => $userRegistered,
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

    public function register(int $id): string
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Для регистрации необходимо войти в систему';
            header('Location: /login');
            exit;
        }

        try {
            $pdo = Database::pdo();
            
            // Check if competition exists and registration is open
            $stmt = $pdo->prepare('
                SELECT * FROM competitions 
                WHERE id = ? 
                AND registration_open <= NOW() 
                AND registration_close >= NOW()
            ');
            $stmt->execute([$id]);
            $competition = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$competition) {
                $_SESSION['error'] = 'Регистрация на это соревнование закрыта или оно не найдено';
                header('Location: /competitions/' . $id);
                exit;
            }

            // Check if already registered
            $stmt = $pdo->prepare('
                SELECT id FROM registrations 
                WHERE competition_id = ? AND user_id = ?
            ');
            $stmt->execute([$id, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Вы уже зарегистрированы на это соревнование';
                header('Location: /competitions/' . $id);
                exit;
            }

            // Check participant limit
            if ($competition['max_participants']) {
                $stmt = $pdo->prepare('
                    SELECT COUNT(*) as count 
                    FROM registrations 
                    WHERE competition_id = ? AND status = ?
                ');
                $stmt->execute([$id, 'approved']);
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
                
                if ($count >= $competition['max_participants']) {
                    $_SESSION['error'] = 'Достигнут лимит участников';
                    header('Location: /competitions/' . $id);
                    exit;
                }
            }

            // Register
            $stmt = $pdo->prepare('
                INSERT INTO registrations (competition_id, user_id, status)
                VALUES (?, ?, ?)
            ');
            $stmt->execute([$id, $_SESSION['user_id'], 'approved']);

            $_SESSION['success'] = 'Вы успешно зарегистрированы на соревнование!';
            header('Location: /competitions/' . $id);
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Ошибка регистрации: ' . $e->getMessage();
            header('Location: /competitions/' . $id);
            exit;
        }
    }

    public function create(): string
    {
        session_start();
        
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            $_SESSION['error'] = 'Недостаточно прав для создания соревнования';
            header('Location: /competitions');
            exit;
        }

        try {
            $pdo = Database::pdo();
            
            // Get all disciplines
            $stmt = $pdo->query('SELECT * FROM disciplines ORDER BY sort_order');
            $disciplines = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return View::render('competitions.create', [
                'title' => 'Создать соревнование',
                'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
                'env' => $_ENV['APP_ENV'] ?? 'local',
                'disciplines' => $disciplines,
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

    public function store(array $request): string
    {
        session_start();
        
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            $_SESSION['error'] = 'Недостаточно прав для создания соревнования';
            header('Location: /competitions');
            exit;
        }

        $name = $request['name'] ?? '';
        $city = $request['city'] ?? '';
        $region = $request['region'] ?? '';
        $venue = $request['venue'] ?? '';
        $address = $request['address'] ?? '';
        $startDate = $request['start_date'] ?? '';
        $endDate = $request['end_date'] ?? '';
        $registrationOpen = $request['registration_open'] ?? '';
        $registrationClose = $request['registration_close'] ?? '';
        $maxParticipants = $request['max_participants'] ?? null;
        $description = $request['description'] ?? '';
        $regulationsUrl = $request['regulations_url'] ?? '';
        $selectedDisciplines = $request['disciplines'] ?? [];

        if (empty($name) || empty($city) || empty($startDate) || empty($endDate)) {
            $_SESSION['error'] = 'Пожалуйста, заполните все обязательные поля';
            header('Location: /competitions/create');
            exit;
        }

        try {
            $pdo = Database::pdo();
            $pdo->beginTransaction();

            // Insert competition
            $stmt = $pdo->prepare('
                INSERT INTO competitions (
                    name, city, region, venue, address, 
                    start_date, end_date, 
                    registration_open, registration_close,
                    max_participants, description, regulations_url, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                RETURNING id
            ');
            $stmt->execute([
                $name,
                $city,
                $region,
                $venue,
                $address,
                $startDate,
                $endDate,
                $registrationOpen ?: null,
                $registrationClose ?: null,
                $maxParticipants ?: null,
                $description,
                $regulationsUrl,
                'upcoming'
            ]);
            
            $competitionId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

            // Insert selected disciplines
            if (!empty($selectedDisciplines)) {
                $stmt = $pdo->prepare('
                    INSERT INTO competition_disciplines (competition_id, discipline_id)
                    VALUES (?, ?)
                ');
                foreach ($selectedDisciplines as $disciplineId) {
                    $stmt->execute([$competitionId, (int)$disciplineId]);
                }
            }

            $pdo->commit();

            $_SESSION['success'] = 'Соревнование успешно создано!';
            header('Location: /competitions/' . $competitionId);
            exit;
        } catch (\Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Ошибка создания соревнования: ' . $e->getMessage();
            header('Location: /competitions/create');
            exit;
        }
    }
}
