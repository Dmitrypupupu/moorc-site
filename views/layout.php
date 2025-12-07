<?php
$title   = $title   ?? ($appName ?? 'МООРС');
$appName = $appName ?? 'МООРС';
$env     = $env     ?? 'local';

// Start session to check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isAuthenticated = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';

// Get flash messages
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="stylesheet" href="/assets/css/style.css"/>
</head>
<body>
  <header class="site-header">
    <div class="container">
      <div class="brand">
        <a href="/"><?= htmlspecialchars($appName) ?></a>
        <?php if ($env !== 'production'): ?>
        <span class="badge">ENV: <?= htmlspecialchars($env) ?></span>
        <?php endif; ?>
      </div>
      <nav class="nav">
        <a href="/">Главная</a>
        <a href="/about">О федерации</a>
        <a href="/news">Новости</a>
        <a href="/competitions">Соревнования</a>
        <a href="/rating">Рейтинг</a>
        <a href="/documents">Документы</a>
        <a href="/membership">Членство</a>
        <a href="/contacts">Контакты</a>
        <?php if ($isAuthenticated): ?>
          <a href="/profile">Личный кабинет</a>
          <a href="/logout">Выход</a>
        <?php else: ?>
          <a href="/login">Вход</a>
          <a href="/register">Регистрация</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="container">
    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?= $content ?? '' ?>
  </main>

  <footer class="site-footer">
    <div class="container">
      <small>© <?= date('Y') ?> <?= htmlspecialchars($appName) ?> — Межрегиональная общественная организация развития спидкубинга</small>
    </div>
  </footer>
</body>
</html>
