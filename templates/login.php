<form class="form container <?=!empty($errors) ? 'form--invalid' : ''; ?>" action="/login.php" method="post">
<h2>Вход</h2>
<div class="form__item <?=isset($errors['email']) ? 'form__item--invalid' : '' ?>">
  <label for="email">E-mail*</label>
  <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=$fields['email']; ?>" required>
  <span class="form__error">Введите e-mail</span>
</div>
<div class="form__item form__item--last <?=isset($errors['password']) ? 'form__item--invalid' : '' ?>">
  <label for="password">Пароль*</label>
  <input id="password" type="password" name="password" placeholder="Введите пароль" required>
  <span class="form__error"><?=$errors['password'] == 'Вы ввели неверный пароль' ? 'Вы ввели неверный пароль' : 'Введите пароль'; ?></span>
</div>
<button type="submit" class="button">Войти</button>
</form>
