<?php
use App\Helpers;
?>
<section>
  <h1>Создать новое соревнование</h1>
  
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
      <?= Helpers::e($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <form method="POST" action="/competitions/create">
    <h2>Основная информация</h2>
    
    <label for="name">Название соревнования *</label>
    <input type="text" id="name" name="name" required placeholder="Например: Чемпионат МООРС 2025">
    
    <label for="city">Город *</label>
    <input type="text" id="city" name="city" required placeholder="Москва">
    
    <label for="region">Регион</label>
    <input type="text" id="region" name="region" placeholder="Московская область">
    
    <label for="venue">Место проведения</label>
    <input type="text" id="venue" name="venue" placeholder="Название площадки">
    
    <label for="address">Адрес</label>
    <textarea id="address" name="address" rows="2" placeholder="Полный адрес места проведения"></textarea>
    
    <h2>Даты</h2>
    
    <label for="start_date">Дата начала *</label>
    <input type="date" id="start_date" name="start_date" required>
    
    <label for="end_date">Дата окончания *</label>
    <input type="date" id="end_date" name="end_date" required>
    
    <label for="registration_open">Открытие регистрации</label>
    <input type="datetime-local" id="registration_open" name="registration_open">
    
    <label for="registration_close">Закрытие регистрации</label>
    <input type="datetime-local" id="registration_close" name="registration_close">
    
    <h2>Дополнительная информация</h2>
    
    <label for="max_participants">Максимум участников</label>
    <input type="number" id="max_participants" name="max_participants" min="1" placeholder="Оставьте пустым для неограниченного">
    
    <label for="description">Описание</label>
    <textarea id="description" name="description" rows="5" placeholder="Описание соревнования"></textarea>
    
    <label for="regulations_url">Ссылка на регламент</label>
    <input type="url" id="regulations_url" name="regulations_url" placeholder="https://...">
    
    <h2>Дисциплины</h2>
    
    <?php if (!empty($disciplines)): ?>
      <p>Выберите дисциплины, которые будут на соревновании:</p>
      <div class="disciplines-grid">
        <?php foreach ($disciplines as $disc): ?>
          <label class="discipline-checkbox">
            <input type="checkbox" name="disciplines[]" value="<?= $disc['id'] ?>">
            <?= Helpers::e($disc['name']) ?> (<?= Helpers::e($disc['code']) ?>)
          </label>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    
    <div class="form-actions">
      <button type="submit" class="btn">Создать соревнование</button>
      <a href="/competitions" class="btn btn-secondary">Отмена</a>
    </div>
  </form>
</section>

<style>
  .disciplines-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 0.5rem;
    margin: 1rem 0;
  }
  
  .discipline-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .discipline-checkbox:hover {
    background: #f5f5f5;
  }
  
  .discipline-checkbox input[type="checkbox"] {
    width: auto;
    margin: 0;
  }
  
  .form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
  }
  
  .btn-secondary {
    background: #6c757d;
  }
  
  .btn-secondary:hover {
    background: #5a6268;
  }
</style>
