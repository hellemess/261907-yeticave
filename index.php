<?php
session_start();

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

$categories = select_data($link, 'SELECT * FROM categories ORDER BY id ASC');

$sql = 'SELECT l.id, picture, l.title, c.title, starting_price, expiration_date FROM lots l ' .
    'JOIN categories c ' .
        'ON category = c.id ' .
    'WHERE expiration_date > NOW() ' .
    'ORDER BY creation_date ASC';

$lots = select_data($link, $sql);
mysqli_close($link);

$content = get_html_code(
    'templates/index.php',
    [
        'categories' => $categories,
        'lots' => $lots,
        'lot_time_remaining' => $lot_time_remaining
    ]
);

$html_code = get_html_code(
    'templates/layout.php',
    [
        'title' => 'Yeti Cave — Главная',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'nav' => $nav,
        'content' => $content
    ]
);

print($html_code);
