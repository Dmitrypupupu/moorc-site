<?php
use App\Helpers;
?>
<section>
  <h1><?= Helpers::e($competition['name']) ?></h1>
  
  <div class="card">
    <p><strong>Дата:</strong> <?= Helpers::formatDate($competition['start_date']) ?>
      <?php if ($competition['start_date'] !== $competition['end_date']): ?>
        - <?= Helpers::formatDate($competition['end_date']) ?>
      <?php endif; ?>
    </p>
    <p><strong>Город:</strong> <?= Helpers::e($competition['city']) ?>
      <?php if ($competition['region']): ?>
        , <?= Helpers::e($competition['region']) ?>
      <?php endif; ?>
    </p>
    <?php if ($competition['venue']): ?>
      <p><strong>Место проведения:</strong> <?= Helpers::e($competition['venue']) ?></p>
    <?php endif; ?>
    <?php if ($competition['address']): ?>
      <p><strong>Адрес:</strong> <?= Helpers::e($competition['address']) ?></p>
    <?php endif; ?>
    <?php if ($competition['max_participants']): ?>
      <p><strong>Количество участников:</strong> <?= $registrationsCount ?> / <?= $competition['max_participants'] ?></p>
    <?php else: ?>
      <p><strong>Зарегистрировано участников:</strong> <?= $registrationsCount ?></p>
    <?php endif; ?>
    
    <?php if ($competition['registration_open'] && $competition['registration_close']): ?>
      <p><strong>Регистрация открыта:</strong> 
        <?= Helpers::formatDate($competition['registration_open'], 'd.m.Y H:i') ?> - 
        <?= Helpers::formatDate($competition['registration_close'], 'd.m.Y H:i') ?>
      </p>
    <?php endif; ?>
    
    <?php if (!$userRegistered && $competition['status'] === 'upcoming'): ?>
      <?php
      $now = time();
      $regOpen = $competition['registration_open'] ? strtotime($competition['registration_open']) : null;
      $regClose = $competition['registration_close'] ? strtotime($competition['registration_close']) : null;
      $canRegister = $regOpen && $regClose && $now >= $regOpen && $now <= $regClose;
      ?>
      <?php if ($canRegister): ?>
        <form method="POST" action="/competitions/<?= $competition['id'] ?>/register">
          <button type="submit" class="btn">Зарегистрироваться</button>
        </form>
      <?php elseif ($regOpen && $now < $regOpen): ?>
        <p class="text-muted">Регистрация откроется <?= Helpers::formatDate($competition['registration_open'], 'd.m.Y H:i') ?></p>
      <?php else: ?>
        <p class="text-muted">Регистрация закрыта</p>
      <?php endif; ?>
    <?php elseif ($userRegistered): ?>
      <p class="alert alert-success">Вы зарегистрированы на это соревнование!</p>
    <?php endif; ?>
  </div>
  
  <?php if ($competition['description']): ?>
    <h2>Описание</h2>
    <p><?= nl2br(Helpers::e($competition['description'])) ?></p>
  <?php endif; ?>
  
  <?php if (!empty($disciplines)): ?>
    <h2>Дисциплины</h2>
    <ul>
      <?php foreach ($disciplines as $disc): ?>
        <li><strong><?= Helpers::e($disc['name']) ?></strong> (<?= Helpers::e($disc['code']) ?>)
          <?php if ($disc['description']): ?>
            - <?= Helpers::e($disc['description']) ?>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  
  <?php if ($competition['regulations_url']): ?>
    <p><a href="<?= Helpers::e($competition['regulations_url']) ?>" target="_blank">Регламент соревнований</a></p>
  <?php endif; ?>
  
  <p class="mt-2"><a href="/competitions">&larr; Вернуться к календарю</a></p>
</section>
