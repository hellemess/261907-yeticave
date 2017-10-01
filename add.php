<?php
session_start();

require_once 'db_functions.php';
require_once 'form_functions.php';
require_once 'utils.php';
require_once 'init.php';
require_once 'nav.php';
require_once 'vendor/autoload.php';

check_connection($link);

if (isset($_SESSION['user']['name'])) {
    $is_auth = true;
    $user_name = $_SESSION['user']['name'];
    $user_id = $_SESSION['user']['id'];
    $categories = get_categories($link);
    $content = null;

    $fields = [
        'title' => '',
        'category' => '',
        'description' => '',
        'starting_price' => '',
        'step' => '',
        'expiration_date' => ''
    ];

    $required_fields = ['title', 'category', 'description', 'expiration_date'];
    $numeric_fields = ['starting_price', 'step'];
    $errors = null;

    if (!empty($_POST)) {
        $lots_count = select_data($link, 'SELECT COUNT(*) as count FROM lots')[0]['count'];
        $form_data = is_filled($fields, $required_fields);
        $form_data = validate_numeric_data($form_data, $numeric_fields);
        $form_data = validate_date($form_data);

        $form_data = handle_picture(
            $form_data,
            [
                'code' => 'lot',
                'number' => $lots_count
            ],
            true
        );

        $fields = $form_data['fields'];
        $errors = $form_data['errors'];
    }

    if (!empty($_POST) && empty($errors)) {
        $fields['expiration_date'] = date('Y-m-d H:i:s', strtotime($fields['expiration_date']));
        $fields['creation_date'] = date('Y-m-d H:i:s');
        $fields['seller'] = $user_id;

        $lot_id = insert_data($link, 'lots', $fields);

        if (!$lot_id) {
            $content = get_html_code(
                'templates/error.php',
                [
                    'error' => 'Произошла ошибка подключения! Текст ошибки:
                            <blockquote>
                                <i>' . mysqli_connect_error() . '</i>
                            </blockquote>'
                ]
            );
        } else {
            header('Location: /lot.php?id=' . $lot_id);
        }
    } else {
        $content = get_html_code(
            'templates/add.php',
            [
                'categories' => $categories,
                'errors' => $errors,
                'fields' => $fields
            ]
        );
    }

    $data = [
        'title' => 'Yeti Cave — Добавление лота',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'nav' => $nav,
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
            'error' => 'Доступ запрещен. Незарегистрированные пользователи не могут добавлять лоты. Пожалуйста,
                    <a class="text-link" href="login.php">войдите</a>
                на сайт.'
        ]
    );

    $data = [
        'title' => 'Yeti Cave — Доступ запрещен',
        'is_auth' => $is_auth,
        'nav' => $nav,
        'content' => $content
    ];
}

$html_code = get_html_code(
    'templates/layout.php',
    $data
);

print($html_code);
