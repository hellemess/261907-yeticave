<?php
session_start();

require_once 'functions.php';
require_once 'init.php';
require_once 'nav.php';
require_once 'vendor/autoload.php';

check_connection($link);

if (isset($_SESSION['user']['name'])) {
    $is_auth = true;
    $user_name = $_SESSION['user']['name'];
    $user_id = $_SESSION['user']['id'];
    $user_bets = get_user_bets($link, $user_id);

    foreach ($user_bets as $bet) {
        $bet = assign_class($bet);

        if ($bet['winner'] === $user_id) {
            $bet['won'] = true;
            $bet['class'] = 'win';
            $bet['expiration_date'] = 'Ставка выиграла';

            $sql = 'SELECT contacts FROM users u '
                . 'JOIN lots l '
                    . 'ON u.id = seller '
                . 'JOIN bets b '
                    . 'ON lot = l.id '
                . 'WHERE b.id = ?';

            $bet['contacts'] = select_data($link, $sql, [$bet['bet']])[0]['contacts'];
        }



        $user_bets[] = $bet;
        array_shift($user_bets);
    }

    $content = get_html_code(
        'templates/mylots.php',
        [
            'user_bets' => $user_bets
        ]
    );

    $data = [
        'title' => 'Yeti Cave — Мои лоты',
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
