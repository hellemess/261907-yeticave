<?php
define('SECONDS_IN_MINUTE', 60);
define('SECONDS_IN_HOUR', 3600);
define('SECONDS_IN_DAY', 86400);

/**
* Рассчитывает время, оставшееся до окончания торгов, на основе переданной даты
*
* @param string $date Дата окончания торгов
*
* @return string Время, оставшееся до окончания торгов
*/
function calculate_remaining_time($date) {
    // устанавливаем часовой пояс в Московское время
    date_default_timezone_set('Europe/Moscow');

    // временная метка для полночи следующего дня
    $ts = strtotime($date);

    // временная метка для настоящего времени
    $now = strtotime('now');

    $days_remaining = floor(($ts - $now) / SECONDS_IN_DAY);

    if ($ts < $now) {
        $lot_time_remaining = 'Торги окончены';
    } elseif ($days_remaining > 1) {
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

/**
* Создает строку, описывающую, сколько времени прошло с момента указанного времени
*
* @param integer $ts Временная метка, от которой ведется отсчет
*
* @return string Строка с указанием того, сколько прошло времени
*/
function convert_ts($ts) {
    $now = strtotime('now');
    $time_passed = $now - $ts;

    if ($time_passed < SECONDS_IN_MINUTE) {
        $time_passed = 'Только что';
    } elseif ($time_passed < SECONDS_IN_HOUR) {
        $time_passed = floor($time_passed / SECONDS_IN_MINUTE);

        if ($time_passed === 1) {
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

        if ($time_passed === 1) {
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

/**
* Форматирует цену для вывода на экран
*
* @param integer $price Цена
*
* @return string Отформатированная цена
*/
function format_price($price) {
    $price = $price > 9999 ? number_format($price, 0, ',', ' ') : $price;

    return $price;
}
