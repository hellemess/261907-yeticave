<?php
require_once 'functions.php';
?>
<section class="rates container">
<h2>Мои ставки</h2>
<?php if (!empty($user_bets)): ?>
<table class="rates__list">
  <?php foreach ($user_bets as $bet): ?>
  <tr class="rates__item">
    <td class="rates__info">
      <div class="rates__img">
        <img src="<?=$bet['picture']; ?>" width="54" height="40" alt="<?=$bet['title']; ?>">
      </div>
      <h3 class="rates__title"><a href="lot.php?id=<?=$bet['id']; ?>"><?=$bet['title']; ?></a></h3>
    </td>
    <td class="rates__category">
      <?=$bet['category']; ?>
    </td>
    <td class="rates__timer">
      <div class="timer timer--finishing"><?=calculate_remaining_time($bet['expiration_date']); ?></div>
    </td>
    <td class="rates__price">
      <?=$bet['cost']; ?>
    </td>
    <td class="rates__time">
      <?=convert_ts(strtotime($bet['betting_date'])); ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php else: ?>
<p>Вы не делали ставок. Вернитесь на <a class="text-link" href="index.php">главную страницу</a> и выберите лот, чтобы сделать ставку.</p></div>
<?php endif; ?>
</section>
