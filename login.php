<?php
session_start();

require_once 'functions.php';
require_once 'init.php';
require_once 'nav.php';
require_once 'vendor/autoload.php';

check_connection($link);

if (isset($_SESSION['user']['name'])) {
    $is_auth = true;
    $user_name = $_SESSION['user']['name'];
} else {
    $is_auth = false;
}

$fields = [
    'email' => '',
    'password' => ''
];

$required_fields = ['email', 'password'];

if (!empty($_POST)) {
    $form_data = is_filled($fields, $required_fields);
    $fields = $form_data['fields'];
    $errors = $form_data['errors'];
}

if (!empty($_POST) && empty($errors)) {
    $user_data = post();
    $email = $user_data['email'];
    $password = $user_data['password'];

    $sql = 'SELECT id, email, name, password FROM users '
        . 'WHERE email = ?';

    $matching_user = select_data($link, $sql, [$email]);

    if (!empty($matching_user)) {
        $user = $matching_user[0];

        if (password_verify($password, $user['password'])) {
            $_SESSION['user']['name'] = $user['name'];
            $_SESSION['user']['id'] = $user['id'];
            header('Location: index.php');
        } else {
            $errors['password'] = 'Вы ввели неверный пароль';
        }
    } else {
        $errors['email'] = 'Пользователь с таким электронным адресом не найден. Проверьте правильность адреса или зарегистрируйтесь.';
    }
}

$content = get_html_code(
    'templates/login.php',
    [
        'errors' => $errors,
        'fields' => $fields,
        'users' => $users
    ]
);

$html_code = get_html_code(
    'templates/layout.php',
    [
        'title' => 'Yeti Cave — Войти',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'nav' => $nav,
        'content' => $content
    ]
);

print($html_code);
