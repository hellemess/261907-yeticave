<?php if ($pages_count > 1): ?>
<ul class="pagination-list">
    <li class="pagination-item pagination-item-prev">
        <a>Назад</a>
    </li>
    <?php foreach ($pages as $page): ?>
    <li class="pagination-item <?php if ((int) $page === (int) $current_page): ?>pagination-item-active<?php endif; ?>">
        <a href="?page=<?=$page; ?><?=!empty($link) ? $link : ''; ?>"><?=$page; ?></a>
    </li>
    <?php endforeach; ?>
    <li class="pagination-item pagination-item-next">
        <a href="#">Вперед</a>
    </li>
</ul>
<?php endif; ?>
