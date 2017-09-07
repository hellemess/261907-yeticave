<?php
$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';

$categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

$fields = [
    'title' => '',
    'category' => '',
    'description' => '',
    'starting_price' => '',
    'step' => '',
    'expiration_date' => ''
];

$rules = [
    'required_fields' => ['title', 'category', 'description', 'expiration_date'],
    'numeric_fields' => ['starting_price', 'step']
];

require_once 'functions.php';
require_once 'lots.php';

if (!empty($_POST)) {
    $validation_data = validate_data($fields, $rules, $lots);
    $fields = $validation_data['fields'];
    $errors = $validation_data['errors'];
}

if (!empty($_POST) && empty($errors)) {
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

$html_code = get_html_code(
    'templates/layout.php',
    [
        'title' => 'Yeti Cave — Добавление лота',
        'is_auth' => $is_auth,
        'user_avatar' => $user_avatar,
        'user_name' => $user_name,
        'content' => $content
    ]
);

print($html_code);
?>
