<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
        <li class="nav__item">
            <a href="index.php?category=<?=$category[0]; ?>"><?=$category[1]; ?></a>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>
