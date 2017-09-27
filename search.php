<?php
$is_search_active = false;

if (!empty($_GET['search'])) {
    $key_word = htmlspecialchars($key_word);
    $key_word = trim($_GET['search']);
    $is_search_active = !empty($key_word);
}

if ($is_search_active) {
    require_once 'functions.php';
    require_once 'init.php';
    require_once 'nav.php';

    check_connection($link);

    $user_name = null;

    if (isset($_SESSION['user']['name'])) {
        $is_auth = true;
        $user_name = $_SESSION['user']['name'];
    } else {
        $is_auth = false;
    }

    $key_word = '%' . $key_word . '%';

    $sql = 'SELECT COUNT(id) as count FROM lots '
        . 'WHERE expiration_date > NOW() '
            . 'AND (title LIKE ? '
            . 'OR description LIKE ?)';

    $lots_count = select_data($link, $sql, [$key_word, $key_word])[0]['count'];
    $lots_per_page = 3;
    $pages_count = ceil($lots_count / $lots_per_page);
    $current_page = 1;

    if (isset($_GET['page'])) {
        $current_page = $_GET['page'];
    }

    $found_lots = get_open_lots_for_page($link, $lots_per_page, $current_page, true, $key_word);
    $key_word = substr($key_word, 1, -1);
    $link = '&search=' . $key_word;

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
            'key_word' => $key_word,
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
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
