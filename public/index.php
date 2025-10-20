<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Router;
use App\Database;
use App\Controllers\HomeController;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

$router = new Router();
$home = new HomeController();

$router->get('/', fn() => $home->index());

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

$router->dispatch();