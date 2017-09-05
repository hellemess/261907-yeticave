<?php
define('SECONDS_IN_MINUTE', 60);
define('SECONDS_IN_HOUR', 3600);

$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';

$categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

$lots = [
  [
    'title' => '2014 Rossignol District Snowboard',
    'category' => 'Доски и лыжи',
    'price' => 10999,
    'picture' => 'img/lot-1.jpg',
    'alt' => 'Сноуборд'
  ],
  [
    'title' => 'DC Ply Mens 2016/2017 Snowboard',
    'category' => 'Доски и лыжи',
    'price' => 159999,
    'picture' => 'img/lot-2.jpg',
    'alt' => 'Сноуборд'
  ],
  [
    'title' => 'Крепления Union Contact Pro 2015 года размер L/XL',
    'category' => 'Крепления',
    'price' => 8000,
    'picture' => 'img/lot-3.jpg',
    'alt' => 'Крепления'
  ],
  [
    'title' => 'Ботинки для сноуборда DC Mutiny Charocal',
    'category' => 'Ботинки',
    'price' => 10999,
    'picture' => 'img/lot-4.jpg',
    'alt' => 'Ботинки'
  ],
  [
    'title' => 'Куртка для сноуборда DC Mutiny Charocal',
    'category' => 'Одежда',
    'price' => 7500,
    'picture' => 'img/lot-5.jpg',
    'alt' => 'Куртка'
  ],
  [
    'title' => 'Маска Oakley Canopy',
    'category' => 'Разное',
    'price' => 5400,
    'picture' => 'img/lot-6.jpg',
    'alt' => 'Маска'
  ]
];

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

$content = get_html_code('templates/index.php', ['categories' => $categories, 'lots' => $lots, 'lot_time_remaining' => $lot_time_remaining]);

$html_code = get_html_code('templates/layout.php', ['title' => 'Yeti Cave — Главная', 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'user_name' => $user_name, 'content' => $content]);

print($html_code);
?>
