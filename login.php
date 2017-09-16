<?php
session_start();

$is_auth = false;

require_once 'functions.php';

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

require_once 'userdata.php';

if (!empty($_POST) && empty($errors)) {
    foreach ($users as $user) {
        $user_data = post();
        $email = $user_data['email'];
        $password = $user_data['password'];

        if ($email = $user['email'] && password_verify($password, $user['password'])) {
            $_SESSION['user']['name'] = $user['name'];
            header('Location: index.php');
        } else {
            $errors['password'] = 'Вы ввели неверный пароль';
        }
    }

    $content = get_html_code(
        'templates/login.php',
        [
            'errors' => $errors,
            'fields' => $fields
        ]
    );
} else {
    $content = get_html_code(
        'templates/login.php',
        [
            'errors' => $errors,
            'fields' => $fields
        ]
    );
}

$html_code = get_html_code(
    'templates/layout.php',
    [
        'title' => 'Yeti Cave — Войти',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'content' => $content
    ]
);

print($html_code);
?>
