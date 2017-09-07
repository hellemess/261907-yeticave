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

function validate_data($fields, $rules, $errors, $lots) {
    foreach (post() as $key => $value) {
        $key = str_replace('-', '_', $key);

        if (in_array($key, $rules['required_fields']) && ($value == '' || $value == 'Выберите категорию')) {
            $errors[$key] = 'Заполните это поле.';
        }

        if (in_array($key, $rules['numeric_fields']) && !is_numeric($value)) {
            $errors[$key] = 'Введите число.';
        }

        $fields[$key] = $value;
    }

    if (!empty($_FILES['picture']['name'])) {
        $file_name = 'lot-' . (count($lots) + 1) . '.' . substr($_FILES['picture']['type'], 6);
        $file_path = __DIR__ . '/img/';
        move_uploaded_file($_FILES['picture']['tmp_name'], $file_path . $file_name);
    } else {
        $errors['picture'] = 'Добавьте снимок лота.';
    }

    if (empty($errors)) {
        $fields['picture'] = 'img/' . $file_name;
        $fields['current_price'] = $fields['starting_price'];
    }

    return [
        'fields' => $fields,
        'errors' => $errors
    ];
}

?>
