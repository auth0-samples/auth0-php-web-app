<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/dotenv-loader.php';

$auth0 = new Auth0\SDK\Auth0([
    'domain' => getenv('AUTH0_DOMAIN'),
    'client_id' => getenv('AUTH0_CLIENT_ID'),
    'redirect_uri' => getenv('AUTH0_CALLBACK_URL'),
    'audience' => getenv('AUTH0_AUDIENCE'),
    'scope' => 'openid profile email',
]);

$auth0->login();
