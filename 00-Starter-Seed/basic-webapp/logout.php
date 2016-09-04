<?php 
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/dotenv-loader.php';
$auth0 = new \Auth0\SDK\Auth0(array(
    'domain'        => getenv('AUTH0_DOMAIN'),
    'client_id'     => getenv('AUTH0_CLIENT_ID'),
    'client_secret' => getenv('AUTH0_CLIENT_SECRET'),
    'redirect_uri'  => getenv('AUTH0_CALLBACK_URL')
  ));
$auth0->logout(); 
header('Location: http://' . $_SERVER['HTTP_HOST']);
die();