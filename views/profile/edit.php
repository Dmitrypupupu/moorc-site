<?php
use App\Helpers;
?>
<section>
  <h2>Редактировать профиль</h2>
  <form method="POST" action="/profile/edit">
    <div class="form-group">
      <label for="first_name">Имя *</label>
      <input type="text" id="first_name" name="first_name" value="<?= Helpers::e($user['first_name']) ?>" required>
    </div>
    
    <div class="form-group">
      <label for="last_name">Фамилия *</label>
      <input type="text" id="last_name" name="last_name" value="<?= Helpers::e($user['last_name']) ?>" required>
    </div>
    
    <div class="form-group">
      <label for="birth_date">Дата рождения</label>
      <input type="date" id="birth_date" name="birth_date" value="<?= Helpers::e($user['birth_date']) ?>">
    </div>
    
    <div class="form-group">
      <label for="city">Город</label>
      <input type="text" id="city" name="city" value="<?= Helpers::e($user['city']) ?>">
    </div>
    
    <div class="form-group">
      <label for="region">Регион</label>
      <input type="text" id="region" name="region" value="<?= Helpers::e($user['region']) ?>">
    </div>
    
    <div class="form-group">
      <label for="wca_id">WCA ID</label>
      <input type="text" id="wca_id" name="wca_id" value="<?= Helpers::e($user['wca_id']) ?>" placeholder="2023IVAN01">
    </div>
    
    <button type="submit">Сохранить</button>
    <a href="/profile" class="btn btn-secondary">Отмена</a>
  </form>
</section>
