<div class="container">
<section class="lots">
  <h2><?=$key_phrase; ?></h2>
  <?php if (!empty($found_lots)): ?>
  <ul class="lots__list">
    <?php foreach ($found_lots as $lot): ?>
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
            <span class="lot__cost"><?=$lot['starting_price']; ?><b class="rub">р</b></span>
          </div>
          <div class="lot__timer timer">
            <?=calculate_remaining_time($lot['expiration_date']); ?>
          </div>
        </div>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
  <?=$pagination; ?>
  <?php else: ?>
  <p>К сожалению, по вашему запросу ничего не найдено.<p>
  <?php endif; ?>
</section>
</div>
