<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Router;
use App\Database;
use App\View;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\CompetitionController;
use App\Controllers\RatingController;
use App\Controllers\DocumentController;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

$router = new Router();
$home = new HomeController();
$auth = new AuthController();
$profile = new ProfileController();
$competition = new CompetitionController();
$rating = new RatingController();
$document = new DocumentController();

// Home
$router->get('/', fn() => $home->index());

// Health checks
$router->get('/health', function () {
    header('Content-Type: application/json; charset=utf-8');
    return json_encode([
        'status' => 'ok',
        'time'   => gmdate('c'),
        'app'    => $_ENV['APP_NAME'] ?? 'MOORC',
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
});

$router->get('/db-test', function () {
    header('Content-Type: application/json; charset=utf-8');
    try {
        $pdo = Database::pdo();
        $stmt = $pdo->query('SELECT version() as version');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $ok = true;
        $message = $row['version'] ?? null;
    } catch (Throwable $e) {
        $ok = false;
        $message = $e->getMessage();
    }
    return json_encode([
        'ok'      => $ok,
        'message' => $message,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
});

// Authentication
$router->get('/register', fn() => $auth->showRegister());
$router->post('/register', fn($req) => $auth->register($req));
$router->get('/login', fn() => $auth->showLogin());
$router->post('/login', fn($req) => $auth->login($req));
$router->get('/logout', fn() => $auth->logout());

// Profile
$router->get('/profile', fn() => $profile->index());
$router->get('/profile/edit', fn() => $profile->edit());
$router->post('/profile/edit', fn($req) => $profile->update($req));

// Competitions
$router->get('/competitions', fn() => $competition->index());
$router->get('/competitions/{id}', fn($req, $id) => $competition->show((int)$id));
$router->post('/competitions/{id}/register', fn($req, $id) => $competition->register((int)$id));

// Rating
$router->get('/rating', fn() => $rating->index());
$router->get('/rating/user/{id}', fn($req, $id) => $rating->profile((int)$id));

// Documents
$router->get('/documents', fn() => $document->index());

// Static pages
$router->get('/about', fn() => View::render('pages.about', [
    'title'   => 'О МООРС',
    'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
    'env'     => $_ENV['APP_ENV'] ?? 'local',
]));
$router->get('/news', fn() => View::render('pages.news', [
    'title'   => 'Новости',
    'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
    'env'     => $_ENV['APP_ENV'] ?? 'local',
]));
$router->get('/calendar', fn() => $competition->index());
$router->get('/membership', fn() => View::render('pages.membership', [
    'title'   => 'Членство',
    'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
    'env'     => $_ENV['APP_ENV'] ?? 'local',
]));
$router->get('/contacts', fn() => View::render('pages.contacts', [
    'title'   => 'Контакты',
    'appName' => $_ENV['APP_NAME'] ?? 'МООРС',
    'env'     => $_ENV['APP_ENV'] ?? 'local',
]));

$router->dispatch();
