<?php
// Read .env
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dotenv->required([
        'AUTH0_CLIENT_ID',
        'AUTH0_DOMAIN',
        'AUTH0_CLIENT_SECRET',
        'AUTH0_CALLBACK_URL',
        'AUTH0_AUDIENCE',
    ]);
} catch (\Dotenv\Exception\InvalidPathException $ex) {
    // Ignore if no dotenv
}
