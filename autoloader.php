<?php

spl_autoload_register(function($className) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $path .= ".php";

    if (!file_exists($path)) {
        return FALSE;
    }

    if (!is_readable($path)) {
        return FALSE;
    }

    require_once $path;
});
