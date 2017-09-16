<?php
session_start();

define('SECONDS_IN_MINUTE', 60);
define('SECONDS_IN_HOUR', 3600);

if (isset($_SESSION['user']['name'])) {
    $is_auth = true;
    $user_name = $_SESSION['user']['name'];
} else {
    $is_auth = false;
}

$categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

require_once 'lots.php';

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

// временная метка для полночи следующего дня
$tomorrow = strtotime('tomorrow midnight');

// временная метка для настоящего времени
$now = strtotime('now');

$hours_remaining = floor(($tomorrow - $now) / SECONDS_IN_HOUR);
$hours_remaining = str_pad($hours_remaining, 2, '0', STR_PAD_LEFT);
$minutes_remaining = floor(($tomorrow - $now) % SECONDS_IN_HOUR / SECONDS_IN_MINUTE);
$minutes_remaining = str_pad($minutes_remaining, 2, '0', STR_PAD_LEFT);

// записать в эту переменную оставшееся время в этом формате (ЧЧ:ММ)
$lot_time_remaining = $hours_remaining . ':' . $minutes_remaining;

require_once 'functions.php';

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
        'content' => $content
    ]
);

print($html_code);
?>
