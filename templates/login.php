<form class="form container <?=!empty($errors) ? 'form--invalid' : ''; ?>" action="/login.php" method="post">
<h2>Вход</h2>
<?php if ($_SERVER['HTTP_REFERER'] === 'http://localhost/signup.php'): ?>
<div>
    <p>Теперь вы можете войти, используя указанный адрес электронной почты и пароль.</p>
</div>
<?php endif; ?>
<div class="form__item <?=isset($errors['email']) ? 'form__item--invalid' : '' ?>">
  <label for="email">E-mail*</label>
  <input id="email" type="email" name="email" placeholder="Введите e-mail" value="<?=$fields['email']; ?>" required>
  <span class="form__error"><?=isset($errors['email']) ? $errors['email'] : ''; ?></span>
</div>
<div class="form__item form__item--last <?=isset($errors['password']) ? 'form__item--invalid' : '' ?>">
  <label for="password">Пароль*</label>
  <input id="password" type="password" name="password" placeholder="Введите пароль" required>
  <span class="form__error"><?=$errors['password'] === 'Вы ввели неверный пароль' ? 'Вы ввели неверный пароль' : 'Введите пароль'; ?></span>
</div>
<button type="submit" class="button">Войти</button>
</form>
