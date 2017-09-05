<?php

function get_html_code($template, $data) {
    $html_code = '';

    if (file_exists($template)) {
        ob_start('ob_gzhandler');
        require_once $template;
        $html_code = ob_get_clean();
    };

    return $html_code;
}

?>
