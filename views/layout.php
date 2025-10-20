<?php
$title   = $title   ?? ($appName ?? 'МООРС');
$appName = $appName ?? 'МООРС';
$env     = $env     ?? 'local';
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
        <span class="badge">ENV: <?= htmlspecialchars($env) ?></span>
      </div>
      <nav class="nav">
        <a href="/">Главная</a>
        <a href="/about">О МООРС</a>
        <a href="/news">Новости</a>
        <a href="/calendar">Календарь</a>
        <a href="/rating">Рейтинг</a>
        <a href="/membership">Членство</a>
        <a href="/contacts">Контакты</a>
      </nav>
    </div>
  </header>

  <main class="container">
    <?= $content ?? '' ?>
  </main>

  <footer class="site-footer">
    <div class="container">
      <small>© <?= date('Y') ?> <?= htmlspecialchars($appName) ?></small>
    </div>
  </footer>
</body>
</html>
