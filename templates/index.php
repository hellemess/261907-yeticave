<?php
require_once 'format_functions.php';
?>
<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $category): ?>
        <li class="promo__item promo__item--<?=$category['link']; ?>">
            <a class="promo__link" href="search.php?category=<?=$category['id']; ?>"><?=$category['category']; ?></a>
        </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
        <form method="get" action="/search.php">
            <select onchange="this.form.submit()" class="lots__select" name="category">
                <option>Все категории</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?=$category['id']; ?>"><?=$category['category']; ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <ul class="lots__list">
    <?php foreach ($lots as $lot): ?>
        <li class="lots__item lot">
            <div class="lot__image">
                <img src="<?=$lot['picture']; ?>" width="350" height="260" alt="<?=$lot['title']; ?>">
            </div>
            <div class="lot__info">
                <span class="lot__category"><?=$lot['category']; ?></span>
                <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=$lot['id']; ?>"><?=$lot['title']; ?></a></h3>
                <div class="lot__state">
                    <div class="lot__rate">
                        <span class="lot__amount">Стартовая цена</span>
                        <span class="lot__cost"><?=format_price($lot['starting_price']); ?><b class="rub">р</b></span>
                    </div>
                    <div class="lot__timer timer <?=isset($lot['class']) ? 'timer--' . $lot['class'] : ''; ?>">
                        <?=$lot['expiration_date']; ?>
                    </div>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
    </ul>
    <?=$pagination; ?>
</section>
