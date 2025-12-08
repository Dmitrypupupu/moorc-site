<?php
use App\Helpers;
?>
<section>
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h1>Календарь соревнований</h1>
    <?php if (Helpers::isAdmin()): ?>
      <a href="/competitions/create" class="btn">+ Создать соревнование</a>
    <?php endif; ?>
  </div>
  
  <?php if (!empty($upcoming)): ?>
    <h2>Предстоящие соревнования</h2>
    <div class="grid">
      <?php foreach ($upcoming as $comp): ?>
        <div class="card">
          <h3 class="card-title">
            <a href="/competitions/<?= $comp['id'] ?>"><?= Helpers::e($comp['name']) ?></a>
          </h3>
          <p><strong>Дата:</strong> <?= Helpers::formatDate($comp['start_date']) ?>
            <?php if ($comp['start_date'] !== $comp['end_date']): ?>
              - <?= Helpers::formatDate($comp['end_date']) ?>
            <?php endif; ?>
          </p>
          <p><strong>Город:</strong> <?= Helpers::e($comp['city']) ?></p>
          <?php if ($comp['venue']): ?>
            <p><strong>Место:</strong> <?= Helpers::e($comp['venue']) ?></p>
          <?php endif; ?>
          <?php if ($comp['status']): ?>
            <p class="text-small text-muted">Статус: <?= Helpers::e($comp['status']) ?></p>
          <?php endif; ?>
          <a href="/competitions/<?= $comp['id'] ?>" class="btn">Подробнее</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p>Предстоящих соревнований пока нет.</p>
  <?php endif; ?>
  
  <?php if (!empty($past)): ?>
    <h2 class="mt-2">Прошедшие соревнования</h2>
    <table>
      <thead>
        <tr>
          <th>Название</th>
          <th>Дата</th>
          <th>Город</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($past as $comp): ?>
          <tr>
            <td><?= Helpers::e($comp['name']) ?></td>
            <td><?= Helpers::formatDate($comp['start_date']) ?></td>
            <td><?= Helpers::e($comp['city']) ?></td>
            <td><a href="/competitions/<?= $comp['id'] ?>">Подробнее</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>
