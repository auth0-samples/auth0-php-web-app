<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required([
    'AUTH0_CLIENT_ID',
    'AUTH0_DOMAIN',
    'AUTH0_CLIENT_SECRET',
    'AUTH0_CALLBACK_URL',
    'AUTH0_AUDIENCE',
]);

$auth0 = new Auth0\SDK\Auth0([
  'domain' => $_ENV['AUTH0_DOMAIN'],
  'audience' => [ $_ENV['AUTH0_AUDIENCE'], $_ENV['AUTH0_CLIENT_ID'] ],
  'clientId' => $_ENV['AUTH0_CLIENT_ID'],
  'clientSecret' => $_ENV['AUTH0_CLIENT_SECRET'],
  'redirectUri' => $_ENV['AUTH0_CALLBACK_URL'],
  'organization' => [ $_ENV['AUTH0_ORGANIZATION'] ],
  'scope' => [ 'openid', 'profile', 'email', 'offline_access' ],
  'tokenAlgorithm' => $_ENV['AUTH0_ALGORITHM'] ?? 'RS256'
]);
