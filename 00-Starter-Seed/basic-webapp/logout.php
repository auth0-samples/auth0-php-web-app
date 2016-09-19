<?php 
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/dotenv-loader.php';
use Auth0\SDK\API\Authentication;

$domain        = getenv('AUTH0_DOMAIN');
$client_id     = getenv('AUTH0_CLIENT_ID');
$client_secret = getenv('AUTH0_CLIENT_SECRET');
$redirect_uri  = getenv('AUTH0_CALLBACK_URL');

$auth0 = new Authentication($domain, $client_id);

$auth0Oauth = $auth0->get_oauth_client($client_secret, $redirect_uri, [
  'persist_id_token' => true,
  'persist_refresh_token' => true,
]);
$auth0Oauth->logout(); 
header('Location: http://' . $_SERVER['HTTP_HOST']);
die();