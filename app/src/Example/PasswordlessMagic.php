<?php

declare(strict_types=1);

namespace Auth0\Quickstart\Example;

use Auth0\Quickstart\Contract\QuickstartExample;
use Auth0\Quickstart\{Application, ApplicationRouter};
use Auth0\SDK\Utility\HttpResponse;
use Exception;

/**
 * A simple example of how passwordless "magic links" could be configured in your Auth0 PHP SDK app:.
 */
final class PasswordlessMagic implements QuickstartExample
{
    /**
     * An instance of our Quickstart Application.
     */
    private Application $app;

    public function __construct(
        Application &$app,
    ) {
        $this->app = &$app;
    }

    public function onCallbackRoute(
        ApplicationRouter $router,
    ): bool {
        // @phpstan-ignore-next-line
        $passwordlessState = $_GET['passwordless'] ?? null;

        // @phpstan-ignore-next-line
        $hash = $_GET['hash'] ?? null;

        if ('complete' === $passwordlessState) {
            $this->app->getSdk()->exchange($router->getUri('/', ''));
            $router->redirect($router->getUri('/', ''));
        }

        if (null === $hash) {
            $this->app->getTemplate()->render('passwordless-magic-callback');

            return true;
        }

        $params = explode('&', urldecode($hash));

        foreach ($params as $index => $param) {
            [$key, $value] = explode('=', $param);
            $params[$key] = $value;
            unset($params[$index]);
        }

        $accessToken = $params['access_token'] ?? null;
        $scope = $params['scope'] ?? null;
        $expiresIn = $params['expires_in'] ?? null;

        if (null === $accessToken || null === $scope || null === $expiresIn) {
            $router->redirect($router->getUri('/', ''));

            return true;
        }

        // Authentication was successful. For some applications, this access token may be enough for your needs.
        // For the purposes for this quickstart, we'll redirect back to Auth0 using 'silent authentication' (prompt=none) to get an Id Token.
        $router->redirect($this->app->getSdk()->login($router->getUri('/callback', 'passwordless=complete'), ['prompt' => 'none']));

        return true;
    }

    public function onLoginRoute(
        ApplicationRouter $router,
    ): bool {
        $startedPasswordless = false;

        if ('POST' === $router->getMethod()) {
            // @phpstan-ignore-next-line
            $email = filter_var($_POST['passwordless_email'], FILTER_SANITIZE_EMAIL);

            if (false === $email) {
                $router->redirect($router->getUri('/', ''));

                return true;
            }

            $response = $this->app->getSdk()->authentication()->emailPasswordlessStart($email, 'link', [
                'redirect_uri' => $router->getUri('/callback', ''),
                'scope' => $this->app->getConfiguration()->formatScope(),
            ]);

            if (! HttpResponse::wasSuccessful($response)) {
                $response = HttpResponse::decodeContent($response);

                $error = $response['error'] ?? null;

                if ('bad.connection' === $error) {
                    $error = 'You must enable the "email" passwordless connection in your Auth0 Dashboard for this application first.';
                } else {
                    $error = $response['error_description'] ?? $response['error'] ?? 'Encountered API error when attempting to start passwordless login.';
                }

                // @phpstan-ignore-next-line
                throw new Exception('API Error: ' . (string) $error);
            }

            $startedPasswordless = true;
        }

        // Display login page, prompting for email address:
        $this->app->getTemplate()->render('passwordless-magic-login', [
            'startedPasswordless' => $startedPasswordless,
            'router' => $router,
            'cookies' => $_COOKIE,
        ]);

        return true;
    }

    public function setup(): self
    {
        // Register our quickstart example hook to override the default onLoginRoute behavior:
        $this->app->hook('onLoginRoute', fn (\Auth0\Quickstart\ApplicationRouter $router): bool => $this->onLoginRoute($router));
        $this->app->hook('onCallbackRoute', fn (\Auth0\Quickstart\ApplicationRouter $router): bool => $this->onCallbackRoute($router));

        return $this;
    }
}
