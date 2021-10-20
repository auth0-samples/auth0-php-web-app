<?php

declare(strict_types=1);

namespace Auth0\Quickstart;

use Auth0\Quickstart\Contract\QuickstartExample;
use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;

final class Application
{
    /**
     * An instance of our SDK's Auth0 configuration, so we could potentially make changes later.
     */
    private SdkConfiguration $configuration;

    /**
     * An instance of the Auth0 SDK.
     */
    private Auth0 $sdk;

    /**
     * An instance of our application's template rendering helper class, for sending responses.
     */
    private ApplicationTemplates $templates;

    /**
     * An instance of our application's error handling class, for gracefully reporting exceptions.
     */
    private ApplicationErrorHandler $errorHandler;

    /**
     * An instance of our application's router class, for handling end-user requests to URIs.
     */
    private ApplicationRouter $router;

    /**
     * An instance of a QuickstartExample class, specified from the AUTH0_USE_EXAMPLE env.
     */
    private ?QuickstartExample $example = null;

    /**
     * An array of hooks with callback functions for examples to override default behavior.
     *
     * @var array<string, callable>
     */
    private array $exampleHooks = [];

    /**
     * Setup our Quickstart application.
     *
     * @param array<string,mixed> $env Auth0 configuration imported from .env file.
     */
    public function __construct(
        array $env
    ) {
        // Configure the SDK using our .env configuration.
        $this->setupAuth0($env);

        // Setup our template engine, for sending responses back to the browser.
        $this->templates = new ApplicationTemplates($this);
        $this->errorHandler = new ApplicationErrorHandler($this);
        $this->router = new ApplicationRouter($this);
        $this->example = null;
    }

    /**
     * Configure the Auth0 SDK using the .env configuration.
     *
     * @param array<string,mixed> $env Auth0 configuration imported from .env file.
     */
    public function setupAuth0(
        array $env
    ): void {
        // Build our SdkConfiguration.
        $this->configuration = new SdkConfiguration([
            'domain' => $env['AUTH0_DOMAIN'] ?? null,
            'customDomain' => $env['AUTH0_CUSTOM_DOMAIN'] ?? null,
            'clientId' => $env['AUTH0_CLIENT_ID'] ?? null,
            'clientSecret' => $env['AUTH0_CLIENT_SECRET'] ?? null,
            'cookieSecret' => $env['AUTH0_COOKIE_SECRET'] ?? null,
            'cookieExpires' => (int) ($env['AUTH0_COOKIE_EXPIRES'] ?? 60 * 60 * 24),
            'audience' => ($env['AUTH0_AUDIENCE'] ?? null) !== null ? [trim($env['AUTH0_AUDIENCE'])] : null,
            'organization' => ($env['AUTH0_ORGANIZATION'] ?? null) !== null ? [trim($env['AUTH0_ORGANIZATION'])] : null,
        ]);

        // Add 'offline_access' to scopes to ensure we get a renew token.
        $this->configuration->pushScope('offline_access');

        // Setup the Auth0 SDK.
        $this->sdk = new Auth0($this->configuration);
    }

    /**
     * "Register" a QuickstartExample class.
     */
    public function useExample(
        QuickstartExample $class
    ): self {
        $this->example = & $class;
        $this->example->setup();
        return $this;
    }

    /**
     * "Register" a QuickstartExample class.
     */
    public function hook(
        string $eventName,
        callable $callback
    ): self {
        $this->exampleHooks[$eventName] = $callback;
        return $this;
    }

    /**
     * "Run" our application, responding to end-user requests.
     */
    public function run(): void
    {
        // Intercept exceptions to gracefully report them.
        $this->errorHandler->hook();

        // Handle incoming requests through the router.
        $this->router->run();
    }

    /**
     * Return our instance of Auth0.
     */
    public function &getSdk(): Auth0
    {
        return $this->sdk;
    }

    /**
     * Return our instance of SdkConfiguration.
     */
    public function &getConfiguration(): SdkConfiguration
    {
        return $this->configuration;
    }

    /**
     * Return our instance of ApplicationTemplates.
     */
    public function &getTemplate(): ApplicationTemplates
    {
        return $this->templates;
    }

    /**
     * Return our instance of ApplicationErrorHandler.
     */
    public function &getErrorHandler(): ApplicationErrorHandler
    {
        return $this->errorHandler;
    }

    /**
     * Return our instance of ApplicationRouter.
     */
    public function &getRouter(): ApplicationRouter
    {
        return $this->router;
    }

    /**
     * Called from the ApplicationRouter when end user loads '/'.
     */
    public function onIndexRoute(
        ApplicationRouter $router
    ): void {
        // Retrieve current session credentials, if end user is signed in.
        $session = $this->sdk->getCredentials();

        // If a session is available, check if the token is expired.
        // @phpstan-ignore-next-line
        if ($session !== null && $session->accessTokenExpired) {
            try {
                // Token has expired, attempt to renew it.
                $this->sdk->renew();
            } catch (\Auth0\SDK\Exception\StateException $exception) {
                // There was an error during access token renewal. Clear the session.
                $this->sdk->clear();
                $session = null;
            }
        }

        // If you have an example class enabled ("AUTH0_EXAMPLE" in your .env file), check if a hook is setup to override default behavior:
        $event = $this->exampleHooks['onIndexRoute'] ?? null;

        if ($event === null || $event($router, $session) === null) {
            // Send response to browser.
            $this->templates->render('logged-' . ($session === null ? 'out' : 'in'), [
                'session' => $session,
                'router' => $router,
                'cookies' => $_COOKIE,
            ]);
        }
    }

    /**
     * Called from the ApplicationRouter when end user loads '/callback'.
     */
    public function onCallbackRoute(
        ApplicationRouter $router
    ): void {
        // If you have an example class enabled ("AUTH0_EXAMPLE" in your .env file), check if a hook is setup to override default behavior:
        $event = $this->exampleHooks['onCallbackRoute'] ?? null;

        if ($event === null || $event($router) === null) {
            // Inform Auth0 we want to redirect to our /callback route, so we can perform the code exchange and setup the user session there.
            $this->sdk->exchange($router->getUri('/callback', ''));

            // Redirect to your application's index route.
            $router->redirect($router->getUri('/', ''));
        }
    }

    /**
     * Called from the ApplicationRouter when end user loads '/login'.
     */
    public function onLoginRoute(
        ApplicationRouter $router
    ): void {
        // Clear the local session.
        $this->sdk->clear();

        // If you have an example class enabled ("AUTH0_EXAMPLE" in your .env file), check if a hook is setup to override default behavior:
        $event = $this->exampleHooks['onLoginRoute'] ?? null;

        if ($event === null || $event($router) === null) {
            // Redirect to Auth0's Universal Login page.
            $router->redirect($this->sdk->login($router->getUri('/callback', '')));
        }
    }

    /**
     * Called from the ApplicationRouter when end user loads '/logout'.
     */
    public function onLogoutRoute(
        ApplicationRouter $router
    ): void {
        // Redirect to Auth0's Universal Login page.
        $router->redirect($this->sdk->logout($router->getUri('/', '')));
    }

    /**
     * Called from the ApplicationRouter when end user loads an unknown route.
     */
    public function onError404(
        ApplicationRouter $router
    ): void {
        $router->setHttpStatus(404);
    }
}
