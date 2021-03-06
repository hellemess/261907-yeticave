<?php
session_start();

require_once 'db_functions.php';
require_once 'form_functions.php';
require_once 'utils.php';
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
    $errors = null;

    if (!empty($_POST)) {
        $users_count = select_data($link, 'SELECT COUNT(*) as count FROM lots')[0]['count'];
        $form_data = is_filled($fields, $required_fields);
        $form_data = validate_email($link, $form_data);

        $form_data = handle_picture(
            $form_data,
            [
                'code' => 'user',
                'number' => $users_count
            ]
        );

        $fields = $form_data['fields'];
        $errors = $form_data['errors'];
    }

    if (!empty($_POST) && empty($errors)) {
        $fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);
        $fields['registration_date'] = date('Y-m-d H:i:s');

        $user_id = insert_data($link, 'users', $fields);

        if (!$user_id) {
            $content = get_html_code(
                'templates/error.php',
                [
                    'error' => 'Произошла ошибка подключения! Текст ошибки:
                            <blockquote>
                                <i>' . mysqli_connect_error() . '</i>
                            </blockquote>'
                ]
            );
        } else {
            header('Location: /login.php');
        }
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
