<?php
session_start();

require_once 'functions.php';
require_once 'init.php';
require_once 'nav.php';

check_connection($link);

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

$lot_not_found = true;
$is_betting_available = false;

if (isset($_GET['id'])) {
    $sql = 'SELECT l.id, l.title, picture, c.title, description, expiration_date, starting_price, step FROM lots l ' .
        'JOIN categories c ' .
            'ON category = c.id ' .
        'WHERE l.id = ?';

    $lot = select_data($link, $sql, [$_GET['id']])[0];

    if (!empty($lot)) {
        $lot_not_found = false;

        $data['title'] = 'Yeti Cave — ' . $lot[1];

        $sql = 'SELECT name, cost, betting_date FROM bets b ' .
            'JOIN users u ' .
                'ON buyer = u.id ' .
            'WHERE lot = ? ' .
            'ORDER BY betting_date DESC';

        $bets = select_data($link, $sql, [$lot[0]]);

        mysqli_close($link);

        if (!empty($bets)) {
            $lot[6] = $bets[0][1];
        }

        $content = [
            'lot' => $lot,
            'bets' => $bets
        ];

        if ($data['is_auth']) {
            $is_betting_available = true;
        }
    }
}

if ($is_betting_available) {
    $fields = [
        'cost' => ''
    ];

    $required_fields = ['cost'];
    $numeric_fields = ['cost'];
    $min = $lot[6] + $lot[7];

    if (!empty($_POST)) {
        $form_data = is_filled($fields, $required_fields);
        $form_data = validate_numeric_data($form_data, $numeric_fields, $min);
        $fields = $form_data['fields'];
        $errors = $form_data['errors'];
    }

    if (!empty($_POST) && empty($errors)) {
        // $user_bets[] = [
        //     'cost' => post('cost'),
        //     'time' => strtotime('now'),
        //     'id' => $_GET['id']
        // ];
        //
        // $user_bets = json_encode($user_bets);
        //
        // setcookie('BETS', $user_bets);
        // header('Location: /mylots.php');
    }

    $content['min'] = $min;
    $content['fields'] = $fields;
    $content['errors'] = $errors;
}

if ($lot_not_found) {
    http_response_code(404);
    $data['title'] = 'Yeti Cave — ' . 'Лот не найден';

    $data['content'] = get_html_code(
        'templates/error.php',
        [
            'error' => 'Лот не найден. Вернитесь на <a class="text-link" href="index.php">главную страницу</a> и выберите другой лот.'
        ]
    );
} else {
    $content['is_betting_available'] = $is_betting_available;

    $data['content'] = get_html_code(
        'templates/lot.php',
        $content
    );
}

    // if ($data['is_auth'])  {
    //     $is_betting_available = true;
    //
    //     if (isset($_COOKIE['BETS'])) {
    //         $user_bets = json_decode($_COOKIE['BETS'], true);
    //     } else {
    //         $user_bets = [];
    //     }
    //
    //     foreach ($user_bets as $bet) {
    //         if ($bet['id'] == $lot['id']) {
    //
    //         }
    //     }
    // }



$data['nav'] = $nav;

$html_code = get_html_code(
    'templates/layout.php',
    $data
);

print($html_code);
