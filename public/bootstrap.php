<?php

declare(strict_types=1);

define('APP_ROOT', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));

// The following globals don't get set during tests: apply some safe defaults.
if (! isset($_SERVER['SERVER_PORT'])) {
    $_SERVER['SERVER_PORT'] = 80;
}

if (! isset($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = '127.0.0.1';
}

if (! isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '/';
}
