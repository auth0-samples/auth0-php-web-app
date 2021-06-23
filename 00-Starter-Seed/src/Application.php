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
    private QuickstartExample $example;

    /**
     * Setup our Quickstart application.
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
    }

    /**
     * Configure the Auth0 SDK using the .env configuration.
     */
    final public function setupAuth0(
        array $env
    ): void {
        // Build our SdkConfiguration.
        $this->configuration = new SdkConfiguration(
            domain: $env['AUTH0_DOMAIN'] ?? null,
            clientId: $env['AUTH0_CLIENT_ID'] ?? null,
            clientSecret: $env['AUTH0_CLIENT_SECRET'] ?? null,
            cookieSecret: $env['AUTH0_COOKIE_SECRET'] ?? null,
            cookieExpires: (isset($env['AUTH0_COOKIE_EXPIRES']) ? (int) $env['AUTH0_COOKIE_EXPIRES'] : 60 * 60 * 24)
        );

        // Add 'offline_access' to scopes to ensure we get a renew token.
        $this->configuration->pushScope('offline_access');

        // Configure an additional Audience (API identifier) if setup in the .env
        if (isset($env['AUTH0_AUDIENCE'])) {
            $this->configuration->pushAudience([$env['AUTH0_AUDIENCE'], $env['AUTH0_CLIENT_ID']]);
        }

        // Configure an Organization, if setup in the .env
        if (isset($env['AUTH0_ORGANIZATION'])) {
            $this->configuration->pushOrganization($env['AUTH0_ORGANIZATION']);
        }

        // Setup the Auth0 SDK.
        $this->sdk = new Auth0($this->configuration);
    }

    /**
     * "Register" a QuickstartExample class.
     */
    final public function useExample(
        QuickstartExample $class
    ): self {
        $this->example = & $class;
        $this->example->setup();
        return $this;
    }

    /**
     * "Run" our application, responding to end-user requests.
     */
    final public function run(): void {
        // Intercept exceptions to gracefully report them.
        $this->errorHandler->hook();

        // Handle incoming requests through the router.
        $this->router->run();
    }

    /**
     * Return our instance of SdkConfiguration.
     */
    final public function &getConfiguration(): SdkConfiguration {
        return $this->configuration;
    }

    /**
     * Return our instance of ApplicationTemplates.
     */
    final public function &getTemplate(): ApplicationTemplates {
        return $this->templates;
    }

    /**
     * Return our instance of ApplicationErrorHandler.
     */
    final public function &getErrorHandler(): ApplicationErrorHandler {
        return $this->errorHandler;
    }

    /**
     * Return our instance of ApplicationRouter.
     */
    final public function &getRouter(): ApplicationRouter {
        return $this->router;
    }

    /**
     * Called from the ApplicationRouter when end user loads '/'.
     */
    final public function onIndexRoute(
        ApplicationRouter $router
    ): void {
        // Retrieve current session credentials, if end user is signed in.
        $session = $this->sdk->getCredentials();

        // If a session is available, check if the token is expired.
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

        // Send response to browser.
        $this->templates->render(
            template: 'logged-' . ($session === null ? 'out' : 'in'),
            session: $session,
            cookies: $_COOKIE,
            router: $router
        );
    }

    /**
     * Called from the ApplicationRouter when end user loads '/callback'.
     */
    final public function onCallbackRoute(
        ApplicationRouter $router
    ): void {
        $this->sdk->exchange(
            // Inform Auth0 we want to redirect to our /callback route, so we can perform the code exchange and setup the user session there.
            redirectUri: $router->getUri('/', '')
        );

        // Redirect to your application's index route.
        $router->redirect($router->getUri(
            path: '/',
            query: ''
        ));
    }

    /**
     * Called from the ApplicationRouter when end user loads '/login'.
     */
    final public function onLoginRoute(
        ApplicationRouter $router
    ): void {
        // Clear the local session.
        $this->sdk->clear();

        // Redirect to Auth0's Universal Login page.
        $router->redirect($this->sdk->authentication()->getLoginLink(
            // Inform Auth0 we want to redirect to our /callback route, so we can perform the code exchange and setup the user session there.
            redirectUri: $router->getUri('/callback', '')
        ));
    }

    /**
     * Called from the ApplicationRouter when end user loads '/logout'.
     */
    final public function onLogoutRoute(
        ApplicationRouter $router
    ) {
        // Clear the local session.
        $this->sdk->clear();

        // Redirect to Auth0's Universal Login page.
        $router->redirect($this->sdk->authentication()->getLogoutLink(
            // Inform Auth0 we want to return to our / route after logout.
            returnUri: $router->getUri('/', '')
        ));
    }

    /**
     * Called from the ApplicationRouter when end user loads an unknown URI.
     */
    final public function onError404(
        ApplicationRouter $router
    ) {
        http_response_code(404);
    }
}
