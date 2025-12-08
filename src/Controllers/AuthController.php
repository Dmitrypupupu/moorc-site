<?php

namespace App\Controllers;

use App\Database;
use App\View;
use PDO;

class AuthController
{
    public function showRegister(): string
    {
        return View::render('auth.register', [
            'title' => 'Регистрация',
            'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
            'env' => $_ENV['APP_ENV'] ?? 'local',
        ]);
    }

    public function register(array $request): string
    {
        session_start();
        
        $email = $request['email'] ?? '';
        $password = $request['password'] ?? '';
        $firstName = $request['first_name'] ?? '';
        $lastName = $request['last_name'] ?? '';
        $birthDate = $request['birth_date'] ?? null;
        $city = $request['city'] ?? '';
        $region = $request['region'] ?? '';
        $wcaId = $request['wca_id'] ?? null;

        if (empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
            $_SESSION['error'] = 'Пожалуйста, заполните все обязательные поля';
            header('Location: /register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Неверный формат email';
            header('Location: /register');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Пароль должен содержать минимум 6 символов';
            header('Location: /register');
            exit;
        }

        try {
            $pdo = Database::pdo();
            
            // Check if user already exists
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Пользователь с таким email уже существует';
                header('Location: /register');
                exit;
            }

            // Create user
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('
                INSERT INTO users (email, password_hash, first_name, last_name, birth_date, city, region, wca_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $email,
                $passwordHash,
                $firstName,
                $lastName,
                $birthDate ?: null,
                $city,
                $region,
                $wcaId ?: null
            ]);

            $_SESSION['success'] = 'Регистрация успешна! Войдите в систему';
            header('Location: /login');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Ошибка регистрации: ' . $e->getMessage();
            header('Location: /register');
            exit;
        }
    }

    public function showLogin(): string
    {
        return View::render('auth.login', [
            'title' => 'Вход',
            'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
            'env' => $_ENV['APP_ENV'] ?? 'local',
        ]);
    }

    public function login(array $request): string
    {
        session_start();
        
        $email = $request['email'] ?? '';
        $password = $request['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Введите email и пароль';
            header('Location: /login');
            exit;
        }

        try {
            $pdo = Database::pdo();
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $_SESSION['error'] = 'Неверный email или пароль';
                header('Location: /login');
                exit;
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['is_admin'] = (bool)($user['is_admin'] ?? false);

            header('Location: /profile');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Ошибка входа: ' . $e->getMessage();
            header('Location: /login');
            exit;
        }
    }

    public function logout(): string
    {
        session_start();
        session_destroy();
        header('Location: /');
        exit;
    }
}
