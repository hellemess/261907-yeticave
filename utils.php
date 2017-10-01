<?php
require_once 'format_functions.php';

/**
* Добавляет в массив элемент, который отвечает за присвоение соответствующего модификатора элементу отображения времени, оставшегося до окончания торгов
*
* @param array $lot Массив для добавления элемента
*
* @return array Массив с добавленным элементом
*/
function assign_class($lot) {
    $lot['expiration_date'] = calculate_remaining_time($lot['expiration_date']);

    if ($lot['expiration_date'] === 'Торги окончены') {
        $lot['class'] = 'end';
    } elseif (!strpos($lot['expiration_date'], 'д')) {
        $lot['class'] = 'finishing';
    } else {
        $lot['class'] = null;
    }

    return $lot;
}

/**
* Создает HTML-код на основе шаблона и данных
*
* @param string $template Адрес шаблона
* @param array $data Массив с данными для вставки в шаблон
*
* @return string HTML-код
*/
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
