<?php
session_start();

require_once 'functions.php';
require_once 'init.php';
require_once 'nav.php';

check_connection($link);

if (isset($_SESSION['user']['name'])) {
    $is_auth = true;
    $user_name = $_SESSION['user']['name'];
    $user_id = $_SESSION['user']['id'];
    $categories = get_categories($link);
    $lots = select_data($link, 'SELECT id FROM lots');

    $fields = [
        'title' => '',
        'category' => '',
        'description' => '',
        'starting_price' => '',
        'step' => '',
        'expiration_date' => ''
    ];

    $required_fields = ['title', 'category', 'description', 'expiration_date'];
    $numeric_fields = ['starting_price', 'step'];

    if (!empty($_POST)) {
        $form_data = is_filled($fields, $required_fields);
        $form_data = validate_numeric_data($form_data, $numeric_fields);

        $form_data = handle_picture(
            $form_data,
            [
                'code' => 'lot',
                'table' => $lots
            ],
            true
        );

        $fields = $form_data['fields'];
        $errors = $form_data['errors'];
    }

    if (!empty($_POST) && empty($errors)) {
        $fields['expiration_date'] = date_format(date_create($fields['expiration_date']), 'Y-m-d H:i:s');
        $fields['creation_date'] = date_format(date_create('now'), 'Y-m-d H:i:s');
        $fields['seller'] = $user_id;

        $lot_id = insert_data($link, 'lots', $fields);

        if (!$lot_id) {
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
            header('Location: /lot.php?id=' . $lot_id);
        }
    } else {
        $content = get_html_code(
            'templates/add.php',
            [
                'categories' => $categories,
                'errors' => $errors,
                'fields' => $fields
            ]
        );
    }

    $data = [
        'title' => 'Yeti Cave — Добавление лота',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'nav' => $nav,
        'content' => $content
    ];
} else {
    $is_auth = false;
    http_response_code(403);
    $error_status = 403;

    $content = get_html_code(
        'templates/error.php',
        [
            'error_status' => $error_status,
            'error' => 'Доступ запрещен. Незарегистрированные пользователи не могут добавлять лоты. Пожалуйста,
                    <a class="text-link" href="login.php">войдите</a>
                на сайт.'
        ]
    );

    $data = [
        'title' => 'Yeti Cave — Доступ запрещен',
        'is_auth' => $is_auth,
        'nav' => $nav,
        'content' => $content
    ];
}

$html_code = get_html_code(
    'templates/layout.php',
    $data
);

print($html_code);
