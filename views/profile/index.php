<?php
use App\Helpers;
?>
<section>
  <h1>Личный кабинет</h1>
  
  <div class="card">
    <h3><?= Helpers::e($user['first_name'] . ' ' . $user['last_name']) ?></h3>
    <p><strong>Email:</strong> <?= Helpers::e($user['email']) ?></p>
    <?php if ($user['birth_date']): ?>
      <p><strong>Дата рождения:</strong> <?= Helpers::formatDate($user['birth_date']) ?></p>
    <?php endif; ?>
    <?php if ($user['city']): ?>
      <p><strong>Город:</strong> <?= Helpers::e($user['city']) ?></p>
    <?php endif; ?>
    <?php if ($user['region']): ?>
      <p><strong>Регион:</strong> <?= Helpers::e($user['region']) ?></p>
    <?php endif; ?>
    <?php if ($user['wca_id']): ?>
      <p><strong>WCA ID:</strong> <?= Helpers::e($user['wca_id']) ?></p>
    <?php endif; ?>
    <p><strong>Соревнований:</strong> <?= $competitionsCount ?></p>
    
    <a href="/profile/edit" class="btn">Редактировать профиль</a>
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
            <td><?= Helpers::e($pb['discipline_name']) ?></td>
            <td><?= Helpers::formatTime($pb['best_single']) ?></td>
            <td><?= Helpers::formatTime($pb['best_average']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
  
  <?php if (!empty($results)): ?>
    <h2 class="mt-2">История результатов</h2>
    <table>
      <thead>
        <tr>
          <th>Соревнование</th>
          <th>Дата</th>
          <th>Дисциплина</th>
          <th>Лучший</th>
          <th>Среднее</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($results as $result): ?>
          <tr>
            <td>
              <a href="/competitions/<?= $result['competition_id'] ?>">
                <?= Helpers::e($result['competition_name']) ?>
              </a>
            </td>
            <td><?= Helpers::formatDate($result['start_date']) ?></td>
            <td><?= Helpers::e($result['discipline_name']) ?></td>
            <td><?= Helpers::formatTime($result['best']) ?></td>
            <td><?= Helpers::formatTime($result['average']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="mt-2">У вас пока нет результатов. <a href="/competitions">Зарегистрируйтесь на соревнование!</a></p>
  <?php endif; ?>
</section>
