<form class="form container <?=!empty($errors) ? 'form--invalid' : ''; ?>" action="/signup.php" method="post"> <!-- form--invalid -->
<h2>Регистрация нового аккаунта</h2>
<div class="form__item <?=isset($errors['email']) ? 'form__item--invalid' : ''; ?>"> <!-- form__item--invalid -->
  <label for="email">E-mail*</label>
  <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=$fields['email']; ?>">
  <span class="form__error"><?=isset($errors['email']) ? $errors['email'] : ''; ?></span>
</div>
<div class="form__item <?=isset($errors['password']) ? 'form__item--invalid' : ''; ?>">
  <label for="password">Пароль*</label>
  <input id="password" type="password" name="password" placeholder="Введите пароль">
  <span class="form__error"><?=isset($errors['password']) ? $errors['password'] : ''; ?></span>
</div>
<div class="form__item <?=isset($errors['name']) ? 'form__item--invalid' : ''; ?>">
  <label for="name">Имя*</label>
  <input id="name" type="text" name="name" placeholder="Введите имя" value="<?=$fields['name']; ?>">
  <span class="form__error"><?=isset($errors['name']) ? $errors['name'] : ''; ?></span>
</div>
<div class="form__item <?=isset($errors['contacts']) ? 'form__item--invalid' : ''; ?>">
  <label for="message">Контактные данные*</label>
  <textarea id="message" name="contacts" placeholder="Напишите как с вами связаться"><?=$fields['contacts']; ?></textarea>
  <span class="form__error"><?=isset($errors['contacts']) ? $errors['contacts'] : ''; ?></span>
</div>
<div class="form__item form__item--file form__item--last <?=isset($errors['picture']) ? 'form__item--invalid' : ''; ?>">
  <label>Изображение</label>
  <div class="preview">
    <button class="preview__remove" type="button">x</button>
    <div class="preview__img">
      <img src="../img/avatar.jpg" width="113" height="113" alt="Изображение лота">
    </div>
  </div>
  <div class="form__input-file">
    <input class="visually-hidden" type="picture" id="photo2" value="">
    <label for="photo2">
      <span>+ Добавить</span>
    </label>
    <span class="form__error"><?=isset($errors['picture']) ? $errors['picture'] : ''; ?></span>
  </div>
</div>
<span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
<button type="submit" class="button">Зарегистрироваться</button>
<a class="text-link" href="#">Уже есть аккаунт</a>
</form>
