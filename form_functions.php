<?php

/**
 * Обрабатывает картинку, загруженную пользователем
 *
 * @param array $form_data Массив с данными формы
 * @param array $table     Массив с данными о базе данных, в которую будет записана ссылка на файл
 * @param bool  $required  Указание, является ли загрузка картинки обязательной при заполнении формы
 *
 * @return array Массив с данными формы
 */
function handle_picture($form_data, $table, $required = false) {
    if (!empty($_FILES['picture']['name'])) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_name = $table['code'] . '-' . ($table['number'] + 1) . '.' . substr($_FILES['picture']['type'], 6);
        $file_type = finfo_file($finfo, $_FILES['picture']['tmp_name']);
        $allowed_types = ['image/jpg', 'image/jpeg', 'image/png'];

        if (!in_array($file_type, $allowed_types)) {
            $form_data['errors']['picture'] = 'Загрузите картинку в одном из следующих форматов: JPG, JPEG или PNG.';
        } else {
            $file_path = __DIR__ . '/img/';
            move_uploaded_file($_FILES['picture']['tmp_name'], $file_path . $file_name);
        }

        if (empty($form_data['errors'])) {
            $form_data['fields']['picture'] = 'img/' . $file_name;
        }
    } else {
        if ($required) {
            $form_data['errors']['picture'] = 'Добавьте изображение.';
        }
    }

    return $form_data;
}

/**
 * Проверяет, заполнена ли форма
 *
 * @param array $fields          Массив с данными, введенными пользователем
 * @param array $required_fields Массив с обязательными для заполнения полями
 *
 * @return array Массив с данными формы и данными об ошибках, если таковые есть
 */
function is_filled($fields, $required_fields) {
    $errors = [];

    foreach (post() as $key => $value) {
        $key = str_replace('-', '_', $key);

        if (in_array($key, $required_fields) && $value === '') {
            $errors[$key] = 'Заполните это поле.';
        }

        $fields[$key] = $value;
    }

    return [
        'fields' => $fields,
        'errors' => $errors
    ];
}

/**
 * ОБрабатывает данные, отосланные пользоваьтелем в форму
 *
 * @param null|string $key           Ключ для получения данных из определенного поля
 * @param string      $default_value Данные, которые возвращаются в случае, если нужное поле не заполнено или отсутствует
 *
 * @return array|string Данные, обработанные с помощью функции htmlspecialchars()
 */
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

/**
 * Выполняет валидацию введенной пользователем даты
 *
 * @param array $form_data Массив с данными формы
 *
 * @return array Массив с данными формы и указанием ошибок, если таковые допущены
 */
function validate_date($form_data) {
    $is_date_format_correct = false;
    $date = $form_data['fields']['expiration_date'];
    $date = explode('.', $date);

    if (count($date) === 3) {
        $is_date_format_correct = checkdate($date[1], $date[0], $date[2]);
    }

    if (!$is_date_format_correct) {
        $form_data['errors']['expiration_date'] = 'Введите дату в формате ДД.ММ.ГГГГ';
    } else {
        $date = strtotime($form_data['fields']['expiration_date']);
        $now = strtotime('now');

        if ($date - $now < 0) {
            $form_data['errors']['expiration_date'] = 'Пожалуйста, выберите дату в будущем.';
        } elseif (($date - $now) / SECONDS_IN_DAY < 1) {
            $form_data['errors']['expiration_date'] = 'Пожалуйста, дайте покупателям больше времени.';
        }
    }

    return $form_data;
}

/**
 * Выполняет валидацию введенного пользователем электронного адреса
 *
 * @param mysqli $link      Ресурс соединения
 * @param array  $form_data Массив с данными формы
 *
 * @return array Массив с данными формы и указанием ошибок, если таковые допущены
 */
function validate_email($link, $form_data) {
    $result = filter_var($form_data['fields']['email'], FILTER_VALIDATE_EMAIL);

    if (!$result) {
        $form_data['errors']['email'] = 'Введите корректный адрес электронной почты.';
    } else {
        $sql = 'SELECT email FROM users '
            . 'WHERE email = ?';

        $emails_matched = select_data($link, $sql, [$form_data['fields']['email']]);

        if (!empty($emails_matched)) {
            $form_data['errors']['email'] = 'Такой адрес уже зарегистрирован.';
        }
    }

    return $form_data;
}

/**
 * Выполняет валидацию введенных пользователем числовых значений
 *
 * @param array $form_data      Массив с данными формы
 * @param array $numeric_fields Массив с полями, в которые должны быть введены числовые значения
 * @param int   $min            Минимальное значение
 *
 * @return array Массив с данными формы и указанием ошибок, если таковые допущены
 */
function validate_numeric_data($form_data, $numeric_fields, $min = 0) {
    foreach ($form_data['fields'] as $key => $value) {
        if (in_array($key, $numeric_fields)) {
            if (!ctype_digit($value)) {
                $form_data['errors'][$key] = 'Введите целое число.';
            } elseif ($value < $min) {
                $form_data['errors'][$key] = 'Введите число больше ' . format_price($min);
            }
        }
    }

    return $form_data;
}
