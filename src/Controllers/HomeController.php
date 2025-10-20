<?php

namespace App\Controllers;

class HomeController
{
    public function index(): string
    {
        $appName = $_ENV['APP_NAME'] ?? 'МООРС';
        $env     = $_ENV['APP_ENV'] ?? 'local';

        $html = <<<HTML
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>{\$appName}</title>
  <style>
    body{font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:2rem;line-height:1.5}
    code, pre{background:#f6f8fa;padding:.2rem .4rem;border-radius:4px}
    a{color:#0366d6;text-decoration:none}
    a:hover{text-decoration:underline}
    .badge{display:inline-block;padding:.2rem .5rem;background:#eef2ff;color:#3730a3;border-radius:999px;font-size:.8rem}
  </style>
</head>
<body>
  <h1>Стартовый проект {\$appName}</h1>
  <p>Это минимальный каркас на PHP + PostgreSQL.</p>
  <p class="badge">ENV: {\$env}</p>
  <ul>
    <li>Проверка состояния: <a href="/health">/health</a></li>
    <li>Проверка БД: <a href="/db-test">/db-test</a></li>
  </ul>
  <script src="/assets/js/app.js"></script>
</body>
</html>
HTML;
        return $html;
    }
}