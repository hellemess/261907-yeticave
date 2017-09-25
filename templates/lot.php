<?php
require_once 'functions.php';
?>
<section class="lot-item container">
    <h2><?=$lot[1]; ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?=$lot[2]; ?>" width="730" height="548" alt="<?=$lot[1]; ?>">
            </div>
            <p class="lot-item__category">Категория: <span><?=$lot[3]; ?></span></p>
            <p class="lot-item__description"><?=$lot[4]; ?></p>
        </div>
        <div class="lot-item__right">
            <?php if ($is_betting_available): ?>
            <div class="lot-item__state">
                <div class="lot-item__timer timer">
                    <?=calculate_remaining_time($lot[5]); ?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost"><?=format_price($lot[6]); ?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span><?=format_price($min); ?> р</span>
                    </div>
                </div>
                <form class="lot-item__form" action="/lot.php?id=<?=$lot['id']; ?>" method="post">
                    <p class="lot-item__form-item <?=!empty($errors) ? 'form__item--invalid' : ''; ?>">
                        <label for="cost">Ваша ставка</label>
                        <input id="cost" type="number" name="cost" placeholder="<?=format_price($min); ?>" min="<?=$min; ?>" required>
                        <span class="form__error" style="position: absolute;"><?=$errors['cost']; ?></span>
                    </p>
                    <button type="submit" class="button">Сделать ставку</button>
                </form>
            </div>
            <?php endif; ?>
            <div class="history">
                <h3>История ставок (<span><?=count($bets); ?></span>)</h3>
                <!-- заполните эту таблицу данными из массива $bets-->
                <table class="history__list">
                <?php foreach ($bets as $bet): ?>
                    <tr class="history__item">
                        <td class="history__name"><?=$bet[0]; ?></td>
                        <td class="history__price"><?=$bet[1]; ?> р</td>
                        <td class="history__time"><?=convert_ts(strtotime($bet[2])); ?></td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</section>
