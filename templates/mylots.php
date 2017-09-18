<?php
require_once 'lots.php';
require_once 'functions.php';
?>
<nav class="nav">
<ul class="nav__list container">
  <li class="nav__item">
    <a href="all-lots.html">Доски и лыжи</a>
  </li>
  <li class="nav__item">
    <a href="all-lots.html">Крепления</a>
  </li>
  <li class="nav__item">
    <a href="all-lots.html">Ботинки</a>
  </li>
  <li class="nav__item">
    <a href="all-lots.html">Одежда</a>
  </li>
  <li class="nav__item">
    <a href="all-lots.html">Инструменты</a>
  </li>
  <li class="nav__item">
    <a href="all-lots.html">Разное</a>
  </li>
</ul>
</nav>
<section class="rates container">
<h2>Мои ставки</h2>
<?php if (!empty($user_bets)): ?>
<table class="rates__list">
  <?php foreach ($user_bets as $bet): ?>
  <tr class="rates__item">
    <td class="rates__info">
      <div class="rates__img">
        <img src="<?=$lots[$bet['id']]['picture']; ?>" width="54" height="40" alt="Сноуборд">
      </div>
      <h3 class="rates__title"><a href="lot.php?id=<?=$bet['id']; ?>"><?=$lots[$bet['id']]['title']; ?></a></h3>
    </td>
    <td class="rates__category">
      <?=$lots[$bet['id']]['category']; ?>
    </td>
    <td class="rates__timer">
      <div class="timer timer--finishing">07:13:34</div>
    </td>
    <td class="rates__price">
      <?=$bet['cost']; ?>
    </td>
    <td class="rates__time">
      <?=convert_ts($bet['time']); ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php else: ?>
<p>Вы не делали ставок. Вернитесь на <a class="text-link" href="index.php">главную страницу</a> и выберите лот, чтобы сделать ставку.</p></div>
<?php endif; ?>
</section>
