<?php
require_once 'mysql_helper.php';

/**
 * Проверяет, установлено ли соединение с базой данных, необходимой для работы сайта, и если нет, то отображает страницу с текстом ошибки
 *
 * @param mysqli $link Ресурс соединения
 */
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

/**
 * Выполняет запрос к базе данных и сообщает результат
 *
 * @param mysqli $link Ресурс соединения
 * @param string $sql  SQL-запрос с плейсхолдерами вместо значений
 * @param array  $data Данные для вставки на место плейсхолдеров
 *
 * @return bool Результат выполнения запроса
 */
function execute_query($link, $sql, $data = []) {
    $result = false;

    if ($link) {
        $stmt = db_get_prepare_stmt($link, $sql, $data);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    return $result;
}

/**
 * Возвращает массив с данными ставок на определенный лот
 *
 * @param mysqli $link Ресурс соединения
 * @param string $lot  Идентификатор лота
 *
 * @return array Массив с данными ставок на определенный лот
 */
function get_bets_by_lot($link, $lot) {
    $sql = 'SELECT name, cost, betting_date FROM bets b ' .
        'JOIN users u ' .
            'ON buyer = u.id ' .
        'WHERE lot = ? ' .
        'ORDER BY betting_date DESC';

    $bets = select_data($link, $sql, [$lot]);

    return $bets;
}

/**
 * Возвращает массив с данными категорий
 *
 * @param mysqli $link Ресурс соединения
 *
 * @return array Массив с данными категорий
 */
function get_categories($link) {
    return select_data($link, 'SELECT id, category, link FROM categories ORDER BY id ASC');
}

/**
 * Возвращает массив с данными лота
 *
 * @param mysqli $link Ресурс соединения
 * @param string $id   Идентификатор лота
 *
 * @return array Массив с данными лота
 */
function get_lot_by_id($link, $id) {
    $sql = 'SELECT l.id, title, picture, c.category, description, expiration_date, starting_price, step, seller FROM lots l ' .
        'JOIN categories c ' .
            'ON l.category = c.id ' .
        'WHERE l.id = ?';

    $lot = select_data($link, $sql, [$id]);

    if (!empty($lot)) {
        $lot = $lot[0];
        $lot = assign_class($lot);
    }

    return $lot;
}

/**
 * Возвращает массив с данными лотов для вывода на определенной странице
 *
 * @param mysqli $link          Ресурс соединения
 * @param int    $lots_per_page Количество лотов на странице
 * @param int    $current_page  Номер страницы
 * @param string $condition     Условие отбора лотов для вставки в SQL-запрос с плейсхолдерами на месте значений
 * @param array  $value         Данные для вставки на место плейсхолдеров
 *
 * @return array Массив с данными лотов
 */
function get_open_lots_for_page($link, $lots_per_page, $current_page, $condition = '', $value = []) {
    $offset = ($current_page - 1) * $lots_per_page;

    $sql = 'SELECT l.id, picture, title, c.category, starting_price, expiration_date FROM lots l '
        . 'JOIN categories c '
            . 'ON l.category = c.id '
        . 'WHERE expiration_date > NOW() '
        . $condition
        . 'ORDER BY creation_date ASC '
        . 'LIMIT ? '
        . 'OFFSET ?';

    $data = !empty($value) ? array_merge($value, [$lots_per_page, $offset]) : [$lots_per_page, $offset];

    $open_lots = select_data($link, $sql, $data);

    foreach ($open_lots as $lot) {
        $lot = assign_class($lot);
        $open_lots[] = $lot;
        array_shift($open_lots);
    }

    return $open_lots;
}

/**
 * Возвращает массив с данными ставок пользователя
 *
 * @param mysqli $link Ресурс соединения
 * @param string $user Идентификатор пользователя
 *
 * @return array Массив с данными ставок пользователя
 */
function get_user_bets($link, $user) {
    $sql = 'SELECT picture, l.id, title, c.category, expiration_date, cost, betting_date, winner, b.id AS bet FROM bets b ' .
        'JOIN lots l ' .
            'ON lot = l.id ' .
        'JOIN categories c ' .
            'ON l.category = c.id ' .
        'WHERE buyer = ? ' .
        'ORDER BY betting_date DESC';

    $user_bets = select_data($link, $sql, [$user]);

    return $user_bets;
}

/**
 * Добавляет запись в базу данных и возвращает ее идентификатор
 *
 * @param mysqli $link  Ресурс соединения
 * @param string $table Имя базы данных
 * @param array  $data  Данные для добавления записи
 *
 * @return int|bool Результат выполнения запроса
 */
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

/**
 * Возвращает данные из базы, соответствующие запросу
 *
 * @param mysqli $link Ресурс соединения
 * @param string $sql  SQL-запрос с плейсхолдерами вместо значений
 * @param array  $data Данные для вставки на место плейсхолдеров
 *
 * @return array Массив с результатом выполнения запроса
 */
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
