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

    $sql = 'SELECT picture, l.id, title, c.category, expiration_date, cost, betting_date FROM bets b ' .
        'JOIN lots l ' .
            'ON lot = l.id ' .
        'JOIN categories c ' .
            'ON l.category = c.id ' .
        'WHERE buyer = ? ' .
        'ORDER BY betting_date DESC';

    $user_bets = select_data($link, $sql, [$user_id]);

    $content = get_html_code(
        'templates/mylots.php',
        [
            'user_bets' => $user_bets
        ]
    );

    $data = [
        'is_auth' => $is_auth,
        'user_name' => $user_name,
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
            'error' => 'Доступ запрещен. Незарегистрированные пользователи не могут просматривать список сделанных ставок (и делать их). Пожалуйста,
                    <a class="text-link" href="login.php">войдите</a>
                на сайт.'
        ]
    );

    $data = [
        'title' => 'Yeti Cave — Доступ запрещен',
        'is_auth' => $is_auth,
        'content' => $content
    ];
}

$data['nav'] = $nav;

$html_code = get_html_code(
    'templates/layout.php',
    $data
);

print($html_code);
