<?php
session_start();

require_once 'functions.php';
require_once 'init.php';

check_connection($link);

if (isset($_SESSION['user']['name'])) {
    $is_auth = true;
    $user_name = $_SESSION['user']['name'];
    $categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

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

    require_once 'lots.php';

    if (!empty($_POST)) {
        $form_data = is_filled($fields, $required_fields);
        $form_data = validate_numeric_data($form_data, $numeric_fields);
        $form_data = handle_picture($form_data, $lots);
        $fields = $form_data['fields'];
        $errors = $form_data['errors'];
    }

    if (!empty($_POST) && empty($errors)) {
        $fields['current_price'] = $fields['starting_price'];

        $content = get_html_code(
            'templates/lot.php',
            [
                'lot' => $fields,
                'bets' => []
            ]
        );
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
        'content' => $content
    ];
} else {
    $is_auth = false;
    http_response_code(403);

    $content = get_html_code(
        'templates/error.php',
        [
            'error' => 'Доступ запрещен. Незарегистрированные пользователи не могут добавлять лоты. Пожалуйста, <a class="text-link" href="login.php">войдите</a> на сайт.'
        ]
    );

    $data = [
        'title' => 'Yeti Cave — Доступ запрещен',
        'is_auth' => $is_auth,
        'content' => $content
    ];
}

$html_code = get_html_code(
    'templates/layout.php',
    $data
);

print($html_code);
