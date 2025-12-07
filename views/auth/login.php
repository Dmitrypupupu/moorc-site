<section>
  <h2>Вход</h2>
  <form method="POST" action="/login">
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>
    </div>
    
    <div class="form-group">
      <label for="password">Пароль</label>
      <input type="password" id="password" name="password" required>
    </div>
    
    <button type="submit">Войти</button>
    <p class="text-small text-muted">Нет аккаунта? <a href="/register">Зарегистрироваться</a></p>
  </form>
</section>
