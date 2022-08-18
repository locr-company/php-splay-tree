<?php

declare(strict_types=1);

spl_autoload_register(function (string $class) {
    $filename = __DIR__ . DIRECTORY_SEPARATOR . $class;
    $requireFilename = str_replace('\\', '/', str_replace('//', '/', $filename));
    if (is_file($requireFilename . '.php')) {
        require_once $requireFilename . '.php';
    }
});
