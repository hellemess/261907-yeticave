<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
        <li class="nav__item">
            <a href="search.php?category=<?=$category['id']; ?>"><?=$category['category']; ?></a>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>
