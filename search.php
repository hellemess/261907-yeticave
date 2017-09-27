<?php
$is_search_active = false;

require_once 'functions.php';
require_once 'init.php';

if (!empty($_GET['search'])) {
    $key_word = htmlspecialchars($key_word);
    $key_word = trim($_GET['search']);
    $is_search_active = !empty($key_word);
} else {
    $category = select_data($link, 'SELECT category FROM categories WHERE id = ?', [$_GET['category']]);

    if (!empty($category)) {
        $category_name = $category[0]['category'];
        $is_search_active = !empty($_GET['category']);
    }
}

if ($is_search_active) {
    require_once 'nav.php';

    check_connection($link);

    $user_name = null;

    if (isset($_SESSION['user']['name'])) {
        $is_auth = true;
        $user_name = $_SESSION['user']['name'];
    } else {
        $is_auth = false;
    }

    if (isset($key_word)) {
        $condition = 'AND (title LIKE ? OR description LIKE ?)';
        $key_word = '%' . $key_word . '%';
        $value = [$key_word, $key_word];
    } else {
        $condition = 'AND l.category = ?';
        $value = [$_GET['category']];
    }

    $sql = 'SELECT COUNT(id) as count FROM lots l '
        . 'WHERE expiration_date > NOW() '
            . $condition;

    $lots_count = select_data($link, $sql, $value)[0]['count'];
    $lots_per_page = 9;
    $pages_count = ceil($lots_count / $lots_per_page);
    $current_page = 1;

    if (isset($_GET['page'])) {
        $current_page = $_GET['page'];
    }

    $condition .= ' ';
    $found_lots = get_open_lots_for_page($link, $lots_per_page, $current_page, $condition, $value);

    if (isset($key_word)) {
        $key_word = substr($key_word, 1, -1);
        $link = '&search=' . $key_word;
    } else {
        $link = '&category=' . $_GET['category'];
    }

    $pagination = get_html_code(
        'templates/pagination.php',
        [
            'pages_count' => $pages_count,
            'pages' => range(1, $pages_count),
            'current_page' => $current_page,
            'link' => $link
        ]
    );

    $content = get_html_code(
        'templates/search.php',
        [
            'key_phrase' => isset($key_word) ? 'Результаты поиска по запросу «<span>' . $key_word. '</span>»' : 'Все лоты в категории «' . $category_name . '»',
            'found_lots' => $found_lots,
            'pagination' => $pagination
        ]
    );

    $html_code = get_html_code(
        'templates/layout.php',
        [
            'title' => 'Yeti Cave — Результат поиска по запросу: ' . $key_word,
            'is_auth' => $is_auth,
            'user_name' => $user_name,
            'nav' => $nav,
            'content' => $content
        ]
    );

    print($html_code);
} else {
    $back = $_SERVER['HTTP_REFERER'] ?? '/index.php';
    header('Location: ' . $back);
}
