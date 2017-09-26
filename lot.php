<?php
session_start();

require_once 'functions.php';
require_once 'init.php';
require_once 'nav.php';

check_connection($link);

if (isset($_SESSION['user']['name'])) {
    $data = [
        'is_auth' => true,
        'user_name' => $_SESSION['user']['name'],
        'user_id' => $_SESSION['user']['id']
    ];
} else {
    $data = [
        'is_auth' => false
    ];
}

$lot_not_found = true;
$is_betting_available = false;

if (isset($_GET['id'])) {
    $lot = get_lot_by_id($link, $_GET['id']);
}

if (isset($lot) && !empty($lot)) {
    $lot_not_found = false;
    $data['title'] = 'Yeti Cave — ' . $lot['title'];
    $bets = get_bets_by_lot($link, $lot['id']);

    if (!empty($bets)) {
        $lot['starting_price'] = $bets[0]['cost'];
    }

    $content = [
        'lot' => $lot,
        'bets' => $bets
    ];

    if ($data['is_auth']) {
        $is_betting_available = true;

        foreach ($bets as $bet) {
            if ($bet['name'] == $data['user_name']) {
                $is_betting_available = false;
            }
        }

        if ($lot['seller'] == $data['user_id']) {
            $is_betting_available = false;
        }

        $content['is_betting_available'] = $is_betting_available;
    }
}

if ($is_betting_available) {
    $fields = [
        'cost' => ''
    ];

    $required_fields = ['cost'];
    $numeric_fields = ['cost'];
    $min = $lot['starting_price'] + $lot['step'];

    if (!empty($_POST)) {
        $form_data = is_filled($fields, $required_fields);
        $form_data = validate_numeric_data($form_data, $numeric_fields, $min);
        $fields = $form_data['fields'];
        $errors = $form_data['errors'];
    }

    $content['min'] = $min;
    $content['fields'] = $fields;
    $content['errors'] = $errors;

    if (!empty($_POST) && empty($errors)) {
        $user_bet = [
            'betting_date' => date_format(date_create('now'), 'Y-m-d H:i:s'),
            'cost' => post('cost'),
            'buyer' => $data['user_id'],
            'lot' => $lot['id']
        ];

        $bet_id = insert_data($link, 'bets', $user_bet);

        if (!$bet_id) {
            $data['content'] = get_html_code(
                'templates/error.php',
                [
                    'error' => 'Произошла ошибка подключения! Текст ошибки:
                            <blockquote>
                                <i>' . mysqli_connect_error() . '</i>
                            </blockquote>'
                ]
            );
        } else {
            header('Location: /lot.php?id=' . $lot['id']);
        }
    }
}

if ($lot_not_found) {
    http_response_code(404);
    $error_status = 404;
    $data['title'] = 'Yeti Cave — ' . 'Лот не найден';

    $data['content'] = get_html_code(
        'templates/error.php',
        [
            'error_status' => $error_status,
            'error' => 'Лот не найден. Вернитесь на
                    <a class="text-link" href="index.php">главную страницу</a>
                и выберите другой лот.'
        ]
    );
} else {
    $data['content'] = get_html_code(
        'templates/lot.php',
        $content
    );
}

$data['nav'] = $nav;

$html_code = get_html_code(
    'templates/layout.php',
    $data
);

print($html_code);
