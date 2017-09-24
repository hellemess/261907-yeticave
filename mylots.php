<?php
session_start();

require_once 'functions.php';
require_once 'init.php';

check_connection($link);

if (isset($_SESSION['user']['name'])) {
    $is_auth = true;
    $user_name = $_SESSION['user']['name'];
    $user_bets = [];

    if (isset($_COOKIE['BETS'])) {
        $user_bets = json_decode($_COOKIE['BETS'], true);
    }

    $content = get_html_code(
        'templates/mylots.php',
        [
            'user_bets' => array_reverse($user_bets)
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

    $content = get_html_code(
        'templates/error.php',
        [
            'error' => 'Доступ запрещен. Незарегистрированные пользователи не могут просматривать список сделанных ставок (и делать их). Пожалуйста, <a class="text-link" href="login.php">войдите</a> на сайт.'
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
