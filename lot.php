<?php
session_start();

// ставки пользователей, которыми надо заполнить таблицу
$bets = [
    ['name' => 'Иван', 'price' => 11500, 'ts' => strtotime('-' . rand(1, 50) .' minute')],
    ['name' => 'Константин', 'price' => 11000, 'ts' => strtotime('-' . rand(1, 18) .' hour')],
    ['name' => 'Евгений', 'price' => 10500, 'ts' => strtotime('-' . rand(25, 50) .' hour')],
    ['name' => 'Семён', 'price' => 10000, 'ts' => strtotime('last week')]
];

if (isset($_SESSION['user']['name'])) {
    $data = [
        'is_auth' => true,
        'user_name' => $_SESSION['user']['name']
    ];
} else {
    $data = [
        'is_auth' => false
    ];
}

require_once 'functions.php';
require_once 'lots.php';

if (isset($_GET['id']) && isset($lots[$_GET['id']])) {
    $lot = $lots[$_GET['id']];
    $lot['id'] = $_GET['id'];
    $min = $lot['current_price'] + $lot['step'];
    $data['title'] = 'Yeti Cave — ' . $lot['title'];

    if ($data['is_auth']) {
        $fields = [
            'cost' => ''
        ];

        $required_fields = ['cost'];
        $numeric_fields = ['cost'];

        if (isset($_COOKIE['BETS'])) {
            $user_bets = json_decode($_COOKIE['BETS'], true);
        } else {
            $user_bets = [];
        }

        $is_bet_placed = false;

        foreach ($user_bets as $bet) {
            if ($bet['id'] == $lot['id']) {
                $is_bet_placed = true;
            }
        }

        if (!empty($_POST)) {
            $form_data = is_filled($fields, $required_fields);
            $form_data = validate_numeric_data($form_data, $numeric_fields, $min);
            $fields = $form_data['fields'];
            $errors = $form_data['errors'];
        }

        if (!empty($_POST) && empty($errors)) {
            $user_bets[] = [
                'cost' => post('cost'),
                'time' => strtotime('now'),
                'id' => $_GET['id']
            ];

            $user_bets = json_encode($user_bets);

            setcookie('BETS', $user_bets);
            header('Location: /mylots.php');
        }

        $content = [
            'lot' => $lot,
            'min' => $min,
            'bets' => $bets,
            'is_bet_placed' => $is_bet_placed,
            'fields' => $fields,
            'errors' => $errors
        ];
    } else {
        $content = [
            'lot' => $lot,
            'bets' => $bets
        ];
    }

    $data['content'] = get_html_code(
        'templates/lot.php',
        $content
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
