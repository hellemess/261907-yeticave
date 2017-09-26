<?php
define('SECONDS_IN_MINUTE', 60);
define('SECONDS_IN_HOUR', 3600);
define('SECONDS_IN_DAY', 86400);

require_once 'mysql_helper.php';

function calculate_remaining_time($date) {
    // устанавливаем часовой пояс в Московское время
    date_default_timezone_set('Europe/Moscow');

    // временная метка для полночи следующего дня
    $ts = strtotime($date);

    // временная метка для настоящего времени
    $now = strtotime('now');

    $days_remaining = floor(($ts - $now) / SECONDS_IN_DAY);

    if ($days_remaining > 1) {
        switch ($days_remaining % 10) {
            case 1:
                $lot_time_remaining = $days_remaining . ' день';
                break;
            case 2:
            case 3:
            case 4:
                $lot_time_remaining = $days_remaining . ' дня';
                break;
            default:
                $lot_time_remaining = $days_remaining . ' дней';
        }
    } else {
        $hours_remaining = floor(($ts - $now) / SECONDS_IN_HOUR);
        $hours_remaining = str_pad($hours_remaining, 2, '0', STR_PAD_LEFT);
        $minutes_remaining = floor(($ts - $now) % SECONDS_IN_HOUR / SECONDS_IN_MINUTE);
        $minutes_remaining = str_pad($minutes_remaining, 2, '0', STR_PAD_LEFT);

        // записать в эту переменную оставшееся время в этом формате (ЧЧ:ММ)
        $lot_time_remaining = $hours_remaining . ':' . $minutes_remaining;
    }

    return $lot_time_remaining;
}

function check_connection($link) {
    if (!$link) {
        $error = 'Произошла ошибка подключения! Текст ошибки: <blockquote><i>' . mysqli_connect_error() . '</i></blockquote>';

        $content = get_html_code(
            'templates/error.php',
            [
                'error' => $error
            ]
        );

        $html_code = get_html_code(
            'templates/layout.php',
            [
                'title' => 'Yeti Cave — Ошибка подключения',
                'content' => $content
            ]
        );

        exit($html_code);
    }
}

function convert_ts($ts) {
    $now = strtotime('now');
    $time_passed = $now - $ts;

    if ($time_passed < SECONDS_IN_MINUTE) {
        $time_passed = 'Только что';
    } elseif ($time_passed < SECONDS_IN_HOUR) {
        $time_passed = floor($time_passed / SECONDS_IN_MINUTE);

        if ($time_passed == 1) {
            $time_passed = 'Минуту назад';
        } elseif ($time_passed > 10 && $time_passed < 20) {
            $time_passed = $time_passed . ' минут назад';
        } else {
            switch ($time_passed % 10) {
                case 1:
                    $time_passed = $time_passed . ' минуту назад';
                    break;
                case 2:
                case 3:
                case 4:
                    $time_passed = $time_passed . ' минуты назад';
                    break;
                default:
                    $time_passed = $time_passed . ' минут назад';
            }
        }
    } elseif ($time_passed < SECONDS_IN_DAY) {
        $time_passed = floor($time_passed / SECONDS_IN_HOUR);

        if ($time_passed == 1) {
            $time_passed = 'Час назад';
        } elseif ($time_passed > 10 && $time_passed < 20) {
            $time_passed = $time_passed . ' часов назад';
        } else {
            switch ($time_passed % 10) {
                case 1:
                    $time_passed = $time_passed . ' час назад';
                    break;
                case 2:
                case 3:
                case 4:
                    $time_passed = $time_passed . ' часа назад';
                    break;
                default:
                    $time_passed = $time_passed . ' часов назад';
            }
        }
    } else {
        $time_passed = date('d.m.y в H:i', $ts);
    }

    return $time_passed;
}

function execute_query($link, $sql, $data = []) {
    $result = false;

    if ($link) {
        $stmt = db_get_prepare_stmt($link, $sql, $data);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    return $result;
}

function format_price($price) {
    $price = $price > 9999 ? number_format($price, 0, ',', ' ') : $price;

    return $price;
}

function get_bets_by_lot($link, $lot) {
    $sql = 'SELECT name, cost, betting_date FROM bets b ' .
        'JOIN users u ' .
            'ON buyer = u.id ' .
        'WHERE lot = ? ' .
        'ORDER BY betting_date DESC';

    $bets = select_data($link, $sql, [$lot]);

    return $bets;
}

function get_categories($link) {
    return select_data($link, 'SELECT id, category, link FROM categories ORDER BY id ASC');
}

function get_html_code($template, $data) {
    extract($data);

    $html_code = '';

    if (file_exists($template)) {
        ob_start('ob_gzhandler');
        require_once $template;
        $html_code = ob_get_clean();
    };

    return $html_code;
}

function get_lot_by_id($link, $id) {
    $sql = 'SELECT l.id, title, picture, c.category, description, expiration_date, starting_price, step, seller FROM lots l ' .
        'JOIN categories c ' .
            'ON l.category = c.id ' .
        'WHERE l.id = ?';

    $lot = select_data($link, $sql, [$id]);

    if (!empty($lot)) {
        $lot = $lot[0];
    }

    return $lot;
}

function get_open_lots($link) {
    $sql = 'SELECT l.id, picture, title, c.category, starting_price, expiration_date FROM lots l ' .
        'JOIN categories c ' .
            'ON l.category = c.id ' .
        'WHERE expiration_date > NOW() ' .
        'ORDER BY creation_date ASC';

    $open_lots = select_data($link, $sql);

    return $open_lots;
}

function handle_picture($form_data, $table, $required = false) {
    if (!empty($_FILES['picture']['name'])) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_name = $table['code'] . '-' . $table['number'] + 1 . '.' . substr($_FILES['picture']['type'], 6);
        $file_type = finfo_file($finfo, $_FILES['picture']['tmp_name']);

        if ($file_type !== 'image/gif' || $file_type !== 'image/jpg' || $file_type !== 'image/jpeg' || $file_type !== 'image/png') {
            $form_data['errors']['picture'] = 'Загрузите картинку в одном из следующих форматов: GIF, JPG, JPEG или PNG.';
        } else {
            $file_path = __DIR__ . '/img/';
            move_uploaded_file($_FILES['picture']['tmp_name'], $file_path . $file_name);
        }
    } else {
        if ($required) {
            $form_data['errors']['picture'] = 'Добавьте изображение.';
        }
    }

    if (!empty($_FILES['picture']['name']) && empty($form_data['errors'])) {
        $form_data['fields']['picture'] = 'img/' . $file_name;
    }

    return $form_data;
}

function insert_data($link, $table, $data) {
    $result = false;

    if ($link) {
        $keys = array_keys($data);
        $columns = implode(', ', $keys);
        $placeholders = array_pad([], count($keys), '?');
        $placeholders = implode(', ', $placeholders);
        $values = array_values($data);

        $sql = 'INSERT INTO '
            . $table
            . ' ('
                . $columns
            . ') VALUES ('
                . $placeholders
            . ')';

        $stmt = db_get_prepare_stmt($link, $sql, $values);
        mysqli_stmt_execute($stmt);
        $result = mysqli_insert_id($link);

        if ($result === 0) {
            $result = false;
        }

        mysqli_stmt_close($stmt);
    }

    return $result;
}

function is_filled($fields, $required_fields) {
    $errors = [];

    foreach (post() as $key => $value) {
        $key = str_replace('-', '_', $key);

        if (in_array($key, $required_fields) && $value == '') {
            $errors[$key] = 'Заполните это поле.';
        }

        $fields[$key] = $value;
    }

    return [
        'fields' => $fields,
        'errors' => $errors
    ];
}

function post($key = null, $default_value = '') {
    if ($key) {
        $value = isset($_POST[$key]) ? htmlspecialchars($_POST[$key]) : $default_value;

        return $value;
    } else {
        $array = [];

        foreach ($_POST as $key => $value) {
            $array[$key] = htmlspecialchars($_POST[$key]);
        }

        return $array;
    }
}

function select_data($link, $sql, $data = []) {
    $array = [];

    if ($link) {
        $stmt = db_get_prepare_stmt($link, $sql, $data);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $array[] = $row;
        }

        mysqli_stmt_close($stmt);
    }

    return $array;
}

function get_user_bets($link, $user) {
    $sql = 'SELECT picture, l.id, title, c.category, expiration_date, cost, betting_date FROM bets b ' .
        'JOIN lots l ' .
            'ON lot = l.id ' .
        'JOIN categories c ' .
            'ON l.category = c.id ' .
        'WHERE buyer = ? ' .
        'ORDER BY betting_date DESC';

    $user_bets = select_data($link, $sql, [$user]);

    return $user_bets;
}

function validate_email($link, $form_data) {
    $result = filter_var($form_data['fields']['email'], FILTER_VALIDATE_EMAIL);

    if (!$result) {
        $form_data['errors']['email'] = 'Введите корректный адрес электронной почты.';
    } else {
        $sql = 'SELECT email FROM users'
            . 'WHERE email = ?';

        $emails_matched = select_data($link, $sql, [$form_data['fields']['email']]);

        if (!empty($emails_matched)) {
            $form_data['errors']['email'] = 'Такой адрес уже зарегистрирован.';
        }
    }

    return $form_data;
}

function validate_numeric_data($form_data, $numeric_fields, $min = 0) {
    foreach ($form_data['fields'] as $key => $value) {
        if (in_array($key, $numeric_fields)) {
            if (!is_numeric($value)) {
                $form_data['errors'][$key] = 'Введите число.';
            } elseif ($value < $min) {
                $form_data['errors'][$key] = 'Введите число больше ' . format_price($min);
            }
        }
    }

    return $form_data;
}
