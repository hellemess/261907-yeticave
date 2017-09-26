<?php
require_once 'functions.php';
require_once 'init.php';

$categories = get_categories($link);

$nav = get_html_code(
    'templates/nav.php',
    [
        'categories' => $categories
    ]
);
