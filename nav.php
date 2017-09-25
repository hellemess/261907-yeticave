<?php
require_once 'functions.php';
require_once 'init.php';

$categories = select_data($link, 'SELECT id, title FROM categories ORDER BY id ASC');

$nav = get_html_code(
    'templates/nav.php',
    [
        'categories' => $categories
    ]
);
