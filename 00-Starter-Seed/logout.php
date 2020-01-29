<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/dotenv-loader.php';

$domain = getenv('AUTH0_DOMAIN');
$client_id = getenv('AUTH0_CLIENT_ID');

$auth0 = new Auth0\SDK\Auth0([
    'domain' => $domain,
    'client_id' => $client_id,
    'redirect_uri' => getenv('AUTH0_CALLBACK_URL'),
]);

$auth_api = new \Auth0\SDK\API\Authentication( $domain, $client_id );

$auth0->logout();

$return_to = 'http://' . $_SERVER['HTTP_HOST'];
header('Location: ' . $auth_api->get_logout_link($return_to, $client_id));
die;
