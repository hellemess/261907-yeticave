<?php
require_once 'functions.php';
require_once 'init.php';
require_once 'vendor/autoload.php';

$transport = (new Swift_SmtpTransport('smtp.mail.ru', 465, 'ssl'))
    ->setUsername('doingsdone@mail.ru')
    ->setPassword('rds7BgcL');

$mailer = new Swift_Mailer($transport);

$sql = 'SELECT id, title FROM lots '
    . 'WHERE expiration_date <= NOW() '
        . 'AND winner IS NULL';

$closed_lots = select_data($link, $sql);

foreach ($closed_lots as $lot) {
    $sql = 'SELECT buyer FROM bets b '
        . 'WHERE lot = ? '
        . 'ORDER BY betting_date DESC '
        . 'LIMIT 1';

    $winner = select_data($link, $sql, [$lot['id']]);
    $is_winner_set = false;

    if (!empty($winner)) {
        $winner_id = $winner[0]['buyer'];

        $sql = 'SELECT email, name FROM users '
            . 'WHERE id = ?';

        $winner_data = select_data($link, $sql, [$winner_id]);

        if (!empty($winner_data)) {
            $winner = [
                'id' => $winner_id,
                'email' => $winner_data[0]['email'],
                'name' => $winner_data[0]['name']
            ];

            $sql = 'UPDATE lots SET winner = ? '
                . 'WHERE id = ?';

            $is_winner_set = execute_query($link, $sql, [$winner['id'], $lot['id']]);
        }
    }

    if ($is_winner_set) {
        $message_body = get_html_code(
            'templates/email',
            [
                'lot' => $lot,
                'winner' => $winner
            ]
        );

        $message = (new Swift_Message('Ваша ставка победила'))
            ->setFrom(['doingsdone@mail.ru' => 'Интернет-аукцион «Yeti Cave»'])
            ->setTo([$winner['email'] => $winner['name']])
            ->setBody($message_body, 'text/html');

        $result = $mailer->send($message);
    }
}
