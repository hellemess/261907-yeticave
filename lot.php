<?php
session_start();

define('SECONDS_IN_MINUTE', 60);
define('SECONDS_IN_HOUR', 3600);
define('SECONDS_IN_DAY', 86400);

if (isset($_SESSION['user']['name'])) {
    $is_auth = true;
    $user_name = $_SESSION['user']['name'];
} else {
    $is_auth = false;
}

// ставки пользователей, которыми надо заполнить таблицу
$bets = [
    ['name' => 'Иван', 'price' => 11500, 'ts' => strtotime('-' . rand(1, 50) .' minute')],
    ['name' => 'Константин', 'price' => 11000, 'ts' => strtotime('-' . rand(1, 18) .' hour')],
    ['name' => 'Евгений', 'price' => 10500, 'ts' => strtotime('-' . rand(25, 50) .' hour')],
    ['name' => 'Семён', 'price' => 10000, 'ts' => strtotime('last week')]
];

require_once 'functions.php';
require_once 'lots.php';

$data = [
    'is_auth' => $is_auth,
    'user_name' => $user_name
];

if (isset($_GET['id']) && isset($lots[$_GET['id']])) {
    $lot = $lots[$_GET['id']];
    $data['title'] = 'Yeti Cave — ' . $lot['title'];

    $data['content'] = get_html_code(
        'templates/lot.php',
        [
            'lot' => $lot,
            'bets' => $bets,
            'is_auth' => $is_auth
        ]
    );
} else {
    http_response_code(404);
    $data['title'] = 'Yeti Cave — ' . 'Лот не найден';
    $data['content'] = '<div class="container"><h1>404</h1><p>Лот не найден. Вернитесь на <a class="text-link" href="index.php">главную страницу</a> и выберите другой лот.</p></div>';
}

$html_code = get_html_code(
    'templates/layout.php',
    $data
);

print($html_code);

?>
