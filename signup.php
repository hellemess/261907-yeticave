<?php

require_once 'functions.php';
require_once 'init.php';
require_once 'nav.php';

check_connection($link);

if (isset($_SESSION['user']['name'])) {
    $is_auth = true;
    $user_name = $_SESSION['user']['name'];
} else {
    $is_auth = false;
}

$data = [
    'is_auth' => $is_auth,
    'nav' => $nav
];

if (!$is_auth) {
    $fields = [
        'email' => '',
        'name' => '',
        'password' => '',
        'contacts' => ''
    ];

    $required_fields = ['email', 'name', 'password', 'contacts'];

    if (!empty($_POST)) {
        $users = select_data($link, 'SELECT email FROM users');
        $form_data = is_filled($fields, $required_fields);
        $form_data = validate_email($form_data, $users);
        $form_data = handle_picture($form_data, ['user', $users]);
        $fields = $form_data['fields'];
        $errors = $form_data['errors'];
    }

    $content = get_html_code(
        'templates/signup.php',
        [
            'fields' => $fields,
            'errors' => $errors
        ]
    );

    $data['title'] = 'Yeti Cave — Регистрация';
    $data['content'] = $content;
} else {
    http_response_code(403);
    $error_status = 403;

    $content = get_html_code(
        'templates/error.php',
        [
            'error_status' => $error_status,
            'error' => 'Доступ запрещен. Вы уже зарегистрированы. Если вы хотите зарегистрироваться заново, используя другой адрес электронной почты, пожалуйста,
                    <a class="text-link" href="logout.php">выйдите</a>
                и попробуйте снова.'
        ]
    );

    $data['title'] = 'Yeti Cave — Доступ запрещен';
    $data['content'] = $content;
}

$html_code = get_html_code(
    'templates/layout.php',
    $data
);

print($html_code);
