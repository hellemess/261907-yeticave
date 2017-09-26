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

$categories = get_categories($link);
$lots = get_open_lots($link);

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
