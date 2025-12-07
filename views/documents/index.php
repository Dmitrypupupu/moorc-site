<?php
use App\Helpers;

$categoryNames = [
    'regulations' => 'Регламенты',
    'reports' => 'Отчёты',
    'protocols' => 'Протоколы',
    'other' => 'Прочее',
];
?>
<section>
  <h1>Документы</h1>
  
  <?php if (!empty($documents)): ?>
    <?php foreach ($documents as $category => $docs): ?>
      <h2 class="mt-2"><?= $categoryNames[$category] ?? ucfirst($category) ?></h2>
      <ul>
        <?php foreach ($docs as $doc): ?>
          <li>
            <?php if ($doc['file_url']): ?>
              <a href="<?= Helpers::e($doc['file_url']) ?>" target="_blank">
                <?= Helpers::e($doc['title']) ?>
              </a>
            <?php else: ?>
              <?= Helpers::e($doc['title']) ?>
            <?php endif; ?>
            <?php if ($doc['description']): ?>
              <br><span class="text-small text-muted"><?= Helpers::e($doc['description']) ?></span>
            <?php endif; ?>
            <span class="text-small text-muted">
              (<?= Helpers::formatDate($doc['created_at'], 'd.m.Y') ?>)
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endforeach; ?>
  <?php else: ?>
    <p>Документы пока не добавлены.</p>
  <?php endif; ?>
</section>
