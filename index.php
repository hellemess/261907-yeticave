<?php
session_start();

require_once 'db_functions.php';
require_once 'utils.php';
require_once 'init.php';
require_once 'nav.php';
require_once 'getwinner.php';

check_connection($link);

$user_name = null;

if (isset($_SESSION['user']['name'])) {
    $is_auth = true;
    $user_name = $_SESSION['user']['name'];
} else {
    $is_auth = false;
}

$categories = get_categories($link);
$current_page = 1;

if (isset($_GET['page'])) {
    $current_page = $_GET['page'];
}

$sql = 'SELECT COUNT(id) as count FROM lots '
    . 'WHERE expiration_date > NOW()';

$lots_count = select_data($link, $sql)[0]['count'];
$lots_per_page = 3;
$pages_count = ceil($lots_count / $lots_per_page);

$lots = get_open_lots_for_page($link, $lots_per_page, $current_page);

$pagination = get_html_code(
    'templates/pagination.php',
    [
        'pages_count' => $pages_count,
        'pages' => range(1, $pages_count),
        'current_page' => $current_page
    ]
);

$content = get_html_code(
    'templates/index.php',
    [
        'categories' => $categories,
        'lots' => $lots,
        'pagination' => $pagination
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
