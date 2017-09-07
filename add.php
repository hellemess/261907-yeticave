<?php
$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';

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

$errors = [];

require_once 'functions.php';
require_once 'lots.php';

if (!empty($_POST)) {
    $fields = validate_data($fields, $rules, $errors, $lots)['fields'];
    $errors = validate_data($fields, $rules, $errors, $lots)['errors'];
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
