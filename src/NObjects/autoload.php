<?php
/**
 * Register autoload routine for classes.
 */
spl_autoload_register(function ($class) {
    $file = __DIR__ . DIRECTORY_SEPARATOR . str_replace(
        array('\\','NObjects/'),
        array('/', ''),
        $class
    ) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});
