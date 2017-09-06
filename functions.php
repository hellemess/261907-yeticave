<?php

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

function format_price($price) {
    $price = $price > 9999 ? number_format($price, 0, ',', ' ') : $price;

    return $price;
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

?>
