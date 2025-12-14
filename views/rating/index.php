<?php
use App\Helpers;
?>
<div class="container">
  <section>
    <h1 style="margin-bottom: 2rem;">Рейтинг спидкуберов</h1>
    
    <div class="form-group" style="max-width: 400px;">
      <label for="discipline">Выберите дисциплину:</label>
      <form method="GET" action="/rating">
        <select name="discipline" id="discipline" onchange="this.form.submit()">
          <?php foreach ($disciplines as $disc): ?>
            <option value="<?= Helpers::e($disc['code']) ?>" 
              <?= $disc['code'] === $selectedDiscipline ? 'selected' : '' ?>>
              <?= Helpers::e($disc['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>
    
    <?php if (!empty($rankings)): ?>
      <h2 style="margin: 2rem 0 1.5rem;">Рейтинг по дисциплине: 
        <?php
        $currentDiscipline = array_filter($disciplines, fn($d) => $d['code'] === $selectedDiscipline);
        $currentDiscipline = reset($currentDiscipline);
        echo Helpers::e($currentDiscipline['name'] ?? $selectedDiscipline);
        ?>
      </h2>
      
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Участник</th>
            <th>Город</th>
            <th>Лучший результат</th>
            <th>Лучшее среднее</th>
            <th>Соревнований</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rankings as $rank): ?>
            <tr>
              <td><?= $rank['position'] ?></td>
              <td>
                <a href="/rating/user/<?= $rank['id'] ?>">
                  <?= Helpers::e($rank['first_name'] . ' ' . $rank['last_name']) ?>
                </a>
                <?php if ($rank['wca_id']): ?>
                  <span class="text-small text-muted">(<?= Helpers::e($rank['wca_id']) ?>)</span>
                <?php endif; ?>
              </td>
              <td>
                <?= Helpers::e($rank['city']) ?>
                <?php if ($rank['region']): ?>
                  <span class="text-small text-muted">, <?= Helpers::e($rank['region']) ?></span>
                <?php endif; ?>
              </td>
              <td class="time-value"><?= Helpers::formatTime($rank['best_single']) ?></td>
              <td class="time-value"><?= Helpers::formatTime($rank['best_average']) ?></td>
              <td><?= $rank['competitions_count'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Пока нет результатов по этой дисциплине.</p>
    <?php endif; ?>
  </section>
</div>
