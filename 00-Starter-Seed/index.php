<?php

declare(strict_types=1);

require(join(DIRECTORY_SEPARATOR, [__DIR__, 'vendor', 'autoload.php']));

use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Exception\StateException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$templates = new League\Plates\Engine('templates');
$router = new League\Route\Router;
$response = new Laminas\Diactoros\Response;
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
  $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

set_exception_handler(function (\Throwable $e) use ($templates, $request) {
  echo $templates->render('error', [
    'code' => $e->getCode(),
    'error' => $e->getMessage(),
    'file' => $e->getFile(),
    'line' => $e->getLine(),
    'backtrace' => $e->getTrace(),
    'cookies' => $_COOKIE,
    'loginUri' => $request->getUri()->withPath('/login')->withQuery(''),
  ]);
});

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required([
    'AUTH0_CLIENT_ID',
    'AUTH0_DOMAIN'
]);

$configuration = new SdkConfiguration(
    domain: $_ENV['AUTH0_DOMAIN'],
    clientId: $_ENV['AUTH0_CLIENT_ID'],
    cookieSecret: 'THIS_IS_A_TEST_OF_THE_EMERGENCY_BROADCAST_SYSTEM',
    cookieExpires: 60 * 60 * 24,
    clientSecret: $_ENV['AUTH0_CLIENT_SECRET'],
    audience: [ $_ENV['AUTH0_AUDIENCE'] ?? null, $_ENV['AUTH0_CLIENT_ID'] ],
    redirectUri: (string) $request->getUri()->withPath('/callback')->withQuery(''),
    // organization: isset($_ENV['AUTH0_ORGANIZATION']) ? [ $_ENV['AUTH0_ORGANIZATION'] ] : null,
    scope: [ 'openid', 'profile', 'email', 'offline_access' ]
);

$auth0 = new Auth0($configuration);

$session = $auth0->getCredentials();

$router->map('GET', '/', function (ServerRequestInterface $request) use ($response, $auth0, $session, $templates): ResponseInterface {
  if ($session !== null && $session->accessTokenExpired) {
    try {
      // Token has expired, attempt to renew it.
      $auth0->renew();
    } catch (StateException $e) {
      // There was an error during access token renewal. Clear the session.
      $auth0->clear();
      $state = null;
    }
  }

  $templateName = 'logged-' . ($session === null ? 'out' : 'in');

  // Render the 'templates/index.php' template.
  $response->getBody()->write($templates->render($templateName, [
    'session' => $session,
    'cookies' => $_COOKIE,
    'loginUri' => $request->getUri()->withPath('/login')->withQuery(''),
    'logoutUri' => $request->getUri()->withPath('/logout')->withQuery('')
  ]));

  // Send response to browser.
  return $response;
});

$router->map('GET', '/callback', function (ServerRequestInterface $request) use($response, $auth0): ResponseInterface {
  $auth0->exchange();

  // Redirect to your application's index route.
  $response = new Laminas\Diactoros\Response\RedirectResponse($request->getUri()->withPath('/')->withQuery(''));

  // Send response to browser.
  return $response;
});

$router->map('GET', '/login', function (ServerRequestInterface $request) use($response, $auth0): ResponseInterface {
  // Build the login uri.
  $loginUri = $auth0->authentication()->getLoginLink();

  // Redirect to Auth0's Universal Login page.
  $response = new Laminas\Diactoros\Response\RedirectResponse($loginUri);

  // Send response to browser.
  return $response;
});

$router->map('GET', '/logout', function (ServerRequestInterface $request) use($auth0): ResponseInterface {
  // Clear our local session.
  $auth0->clear();

  // Build the logout uri.
  $logoutUri = $auth0->authentication()->getLogoutLink(
    returnUri: (string) $request->getUri()->withPath('/')
  );

  // Redirect to Auth0's /logout endpoint to finish logout process.
  $response = new Laminas\Diactoros\Response\RedirectResponse($logoutUri);

  // Send response to browser.
  return $response;
});

$response = $router->dispatch($request);

(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);
