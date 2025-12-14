<div class="container">
  <section style="max-width: 500px; margin: 3rem auto;">
    <h2 style="text-align: center; margin-bottom: 2rem;">Вход в систему</h2>
    <div class="form-card">
      <form method="POST" action="/login">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>
      
      <div class="form-group">
        <label for="password">Пароль</label>
        <input type="password" id="password" name="password" required>
      </div>
      
      <button type="submit" style="width: 100%;">Войти</button>
      <p class="text-small text-muted" style="text-align: center; margin-top: 1rem;">
        Нет аккаунта? <a href="/register">Зарегистрироваться</a>
      </p>
      </form>
    </div>
  </section>
</div>
