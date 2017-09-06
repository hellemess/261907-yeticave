<?php
$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';

$fields = [
    'title' => '',
    'starting-price' => '',
    'step' => '',
    'description' => '',
    'expiration-date' => ''
];

$required_fields = ['title', 'category', 'description', 'expiration-date'];
$numeric_fields = ['starting-price', 'step'];
$errors = [];

require_once 'functions.php';
require_once 'lots.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST as $key => $value) {
        if (in_array($key, $required_fields) and $value == '' || $value == 'Выберите категорию') {
            $errors[] = $key;
        } else {
            $fields[$key] = $value;
        }

        if (in_array($key, $numeric_fields) && !is_numeric($value)) {
            $errors[] = $key;
        } else {
            $fields[$key] = $value;
        }
    }

    if (isset($_FILES['picture'])) {
        $file_name = 'lot-' . (count($lots) + 1) . '.' . substr($_FILES['picture']['type'], 6);
        $file_path = __DIR__ . '/img/';

        move_uploaded_file($_FILES['picture']['tmp_name'], $file_path . $file_name);
    } else {
        $errors[] = 'picture';
    }

    if (!count($errors)) {
        $fields['picture'] = 'img/' . $file_name;
        $fields['current_price'] = $fields['starting-price'];
        $fields['starting_price'] = $fields['starting-price'];
        unset($fields['starting-price']);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !count($errors)) {
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
