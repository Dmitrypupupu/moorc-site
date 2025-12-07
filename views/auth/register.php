<section>
  <h2>Регистрация</h2>
  <form method="POST" action="/register">
    <div class="form-group">
      <label for="email">Email *</label>
      <input type="email" id="email" name="email" required>
    </div>
    
    <div class="form-group">
      <label for="password">Пароль *</label>
      <input type="password" id="password" name="password" required minlength="6">
    </div>
    
    <div class="form-group">
      <label for="first_name">Имя *</label>
      <input type="text" id="first_name" name="first_name" required>
    </div>
    
    <div class="form-group">
      <label for="last_name">Фамилия *</label>
      <input type="text" id="last_name" name="last_name" required>
    </div>
    
    <div class="form-group">
      <label for="birth_date">Дата рождения</label>
      <input type="date" id="birth_date" name="birth_date">
    </div>
    
    <div class="form-group">
      <label for="city">Город</label>
      <input type="text" id="city" name="city">
    </div>
    
    <div class="form-group">
      <label for="region">Регион</label>
      <input type="text" id="region" name="region">
    </div>
    
    <div class="form-group">
      <label for="wca_id">WCA ID (если есть)</label>
      <input type="text" id="wca_id" name="wca_id" placeholder="2023IVAN01">
    </div>
    
    <button type="submit">Зарегистрироваться</button>
    <p class="text-small text-muted">Уже есть аккаунт? <a href="/login">Войти</a></p>
  </form>
</section>
