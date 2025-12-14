<?php
use App\Helpers;
?>
<div class="container">
  <section>
    <h1><?= Helpers::e($user['first_name'] . ' ' . $user['last_name']) ?></h1>
  
  <div class="card">
    <?php if ($user['city']): ?>
      <p><strong>Город:</strong> <?= Helpers::e($user['city']) ?>
        <?php if ($user['region']): ?>
          , <?= Helpers::e($user['region']) ?>
        <?php endif; ?>
      </p>
    <?php endif; ?>
    <?php if ($user['wca_id']): ?>
      <p><strong>WCA ID:</strong> <?= Helpers::e($user['wca_id']) ?></p>
    <?php endif; ?>
    <?php if ($user['is_member']): ?>
      <p><span class="badge">Член МООРС</span></p>
    <?php endif; ?>
  </div>
  
  <?php if (!empty($personalBests)): ?>
    <h2 class="mt-2">Личные рекорды</h2>
    <table>
      <thead>
        <tr>
          <th>Дисциплина</th>
          <th>Лучший результат</th>
          <th>Лучшее среднее</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($personalBests as $pb): ?>
          <tr>
            <td>
              <a href="/rating?discipline=<?= Helpers::e($pb['discipline_code']) ?>">
                <?= Helpers::e($pb['discipline_name']) ?>
              </a>
            </td>
            <td class="time-value"><?= Helpers::formatTime($pb['best_single']) ?></td>
            <td class="time-value"><?= Helpers::formatTime($pb['best_average']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
  
  <?php if (!empty($competitions)): ?>
    <h2 class="mt-2">История соревнований</h2>
    <table>
      <thead>
        <tr>
          <th>Соревнование</th>
          <th>Дата</th>
          <th>Город</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($competitions as $comp): ?>
          <tr>
            <td><a href="/competitions/<?= $comp['id'] ?>"><?= Helpers::e($comp['name']) ?></a></td>
            <td><?= Helpers::formatDate($comp['start_date']) ?></td>
            <td><?= Helpers::e($comp['city']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
  
  <p class="mt-2"><a href="/rating">&larr; Вернуться к рейтингу</a></p>
  </section>
</div>
