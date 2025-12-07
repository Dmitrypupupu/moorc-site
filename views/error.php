<?php
use App\Helpers;
?>
<section>
  <h1>Ошибка</h1>
  <div class="alert alert-error">
    <?= Helpers::e($error ?? 'Произошла неизвестная ошибка') ?>
  </div>
  <p><a href="/">&larr; Вернуться на главную</a></p>
</section>
