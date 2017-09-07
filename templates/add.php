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
<form class="form form--add-lot container <?=!empty($errors) ? 'form--invalid' : ''; ?>" action="/add.php" method="post" enctype="multipart/form-data">
<h2>Добавление лота</h2>
<div class="form__container-two">
  <div class="form__item <?=isset($errors['title']) ? 'form__item--invalid' : ''; ?>">
    <label for="lot-name">Наименование</label>
    <input id="lot-name" type="text" name="title" placeholder="Введите наименование лота" value="<?=$fields['title']; ?>">
    <span class="form__error"><?=isset($errors['title']) ? $errors['title'] : ''; ?></span>
  </div>
  <div class="form__item <?=isset($errors['category']) ? 'form__item--invalid' : ''; ?>">
    <label for="category">Категория</label>
    <select id="category" name="category">
      <option>Выберите категорию</option>
      <option>Доски и лыжи</option>
      <option>Крепления</option>
      <option>Ботинки</option>
      <option>Одежда</option>
      <option>Инструменты</option>
      <option>Разное</option>
    </select>
    <span class="form__error"><?=isset($errors['category']) ? $errors['category'] : ''; ?></span>
  </div>
</div>
<div class="form__item form__item--wide <?=isset($errors['description']) ? 'form__item--invalid' : ''; ?>">
  <label for="message">Описание</label>
  <textarea id="message" name="description" placeholder="Напишите описание лота"><?=$fields['description']; ?></textarea>
  <span class="form__error"><?=isset($errors['description']) ? $errors['description'] : ''; ?></span>
</div>
<div class="form__item form__item--file <?=isset($errors['picture']) ? 'form__item--invalid' : ''; ?>"> <!-- form__item--uploaded -->
  <label>Изображение</label>
  <div class="preview">
    <button class="preview__remove" type="button">x</button>
    <div class="preview__img">
      <img src="../img/avatar.jpg" width="113" height="113" alt="Изображение лота">
    </div>
  </div>
  <div class="form__input-file">
    <input class="visually-hidden" type="file" name="picture" id="photo2">
    <label for="photo2">
      <span>+ Добавить</span>
    </label>
    <span class="form__error"><?=isset($errors['picture']) ? $errors['picture'] : ''; ?></span>
  </div>
</div>
<div class="form__container-three">
  <div class="form__item form__item--small <?=isset($errors['starting_price']) ? 'form__item--invalid' : ''; ?>">
    <label for="lot-rate">Начальная цена</label>
    <input id="lot-rate" type="text" name="starting-price" placeholder="0" value="<?=$fields['starting-price']; ?>">
    <span class="form__error"><?=isset($errors['starting_price']) ? $errors['starting_price'] : ''; ?></span>
  </div>
  <div class="form__item form__item--small <?=isset($errors['step']) ? 'form__item--invalid' : ''; ?>">
    <label for="lot-step">Шаг ставки</label>
    <input id="lot-step" type="text" name="step" placeholder="0" value="<?=$fields['step']; ?>">
    <span class="form__error"><?=isset($errors['step']) ? $errors['step'] : ''; ?></span>
  </div>
  <div class="form__item <?=isset($errors['expiration_date']) ? 'form__item--invalid' : ''; ?>">
    <label for="lot-date">Дата завершения</label>
    <input class="form__input-date" id="lot-date" type="text" name="expiration-date" placeholder="20.05.2017" value="<?=$fields['expiration-date']; ?>">
    <span class="form__error"><?=isset($errors['expiration_date']) ? $errors['expiration_date'] : ''; ?></span>
  </div>
</div>
<span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
<button type="submit" class="button">Добавить лот</button>
</form>
