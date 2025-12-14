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
        <a href="/" style="display: flex; align-items: center; gap: 0.5rem;">
          <span style="font-size: 2rem;">🧊</span>
          <span><?= htmlspecialchars($appName) ?></span>
        </a>
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
          <a href="/profile" style="color: var(--primary); font-weight: 600;">Личный кабинет</a>
          <a href="/logout">Выход</a>
        <?php else: ?>
          <a href="/login">Вход</a>
          <a href="/register" style="color: var(--primary); font-weight: 600;">Регистрация</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main>
    <div class="container">
      <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
    </div>
    
    <?= $content ?? '' ?>
  </main>

  <footer class="site-footer">
    <div class="container">
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem; text-align: left;">
        <div>
          <h3 style="color: var(--secondary); font-size: 1.25rem; margin-bottom: 1rem;">МООРС</h3>
          <p style="color: var(--text-light); font-size: 0.875rem;">Межрегиональная общественная организация развития спидкубинга</p>
        </div>
        <div>
          <h4 style="color: var(--secondary); font-size: 1rem; margin-bottom: 0.75rem;">Навигация</h4>
          <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.875rem;">
            <li style="margin-bottom: 0.5rem;"><a href="/about">О федерации</a></li>
            <li style="margin-bottom: 0.5rem;"><a href="/competitions">Соревнования</a></li>
            <li style="margin-bottom: 0.5rem;"><a href="/rating">Рейтинг</a></li>
            <li style="margin-bottom: 0.5rem;"><a href="/documents">Документы</a></li>
          </ul>
        </div>
        <div>
          <h4 style="color: var(--secondary); font-size: 1rem; margin-bottom: 0.75rem;">Контакты</h4>
          <ul style="list-style: none; padding: 0; margin: 0; font-size: 0.875rem;">
            <li style="margin-bottom: 0.5rem;"><a href="mailto:info@moorc.ru">info@moorc.ru</a></li>
            <li style="margin-bottom: 0.5rem;"><a href="/contacts">Все контакты</a></li>
            <li style="margin-bottom: 0.5rem;"><a href="/membership">Членство</a></li>
          </ul>
        </div>
      </div>
      <div style="border-top: 1px solid var(--border); padding-top: 1.5rem; text-align: center;">
        <p style="margin: 0; font-size: 0.875rem;"><strong><?= htmlspecialchars($appName) ?></strong> — Межрегиональная общественная организация развития спидкубинга</p>
        <p style="margin: 0.5rem 0 0; font-size: 0.875rem;">© <?= date('Y') ?> Все права защищены</p>
      </div>
    </div>
  </footer>
</body>
</html>
