<?php

declare(strict_types=1);

namespace Auth0\Quickstart;

final class ApplicationRouter
{
    /**
     * An instance of our Quickstart Application.
     */
    private Application $app;

    /**
     * ApplicationRouter constructor.
     *
     * @param Application $app An instance of our Quickstart Application.
     */
    public function __construct(
        Application &$app
    ) {
        $this->app = & $app;
    }

    /**
     * Process the current request and route it to the class handler.
     */
    final public function run()
    {
        $requestUri = parse_url($this->getUri(), PHP_URL_PATH);

        // Issue headers to disable browser caching.
        $this->disallowCaching();

        switch ($requestUri) {
            case '/' :
            $this->app->onIndexRoute($this);
            break;

        case '/callback' :
            $this->app->onCallbackRoute($this);
            break;

        case '/login' :
            $this->app->onLoginRoute($this);
            break;

        case '/logout' :
            $this->app->onLogoutRoute($this);
            break;

        default:
            $this->app->onError404($this);
            break;
        }

        exit;
    }

    /**
     * Return (and optionally manipulate) the currently requested uri.
     */
    final public function getUri(
        ?string $path = null,
        ?string $query = null
    ): string {
        $httpScheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $httpPort = (int) $_SERVER['SERVER_PORT'];
        $httpHost = $_SERVER['SERVER_NAME'];
        $httpRequest = $_SERVER['REQUEST_URI'];
        $httpUri = $httpScheme . '://' . $httpHost . ($httpPort !== 80 ? ':' . $httpPort : '') . $httpRequest;

        // If we aren't making changes the uri, simply return it.
        if ($path === null && $query === null) {
            return $httpUri;
        }

        // Parse a url into it's components so we can manipulate them more easily.
        $parsedUri = parse_url($httpUri);

        // Manipulate the /path portion of the uri.
        if ($path !== null) {
            $parsedUri['path'] = $path;
        }

        // Manipulate the ?query portion of the uri.
        if ($query !== null) {
            if ($query === '') {
                if (isset($parsedUri['query'])) {
                    unset($parsedUri['query']);
                }
            } else {
                $parsedUri['query'] = $query;
            }
        }

        if (! isset($parsedUri['port'])) {
            $parsedUri['port'] = 80;
        }

        // Reconstruct the manipulated uri and return it.
        return $parsedUri['scheme'] . '://' . $parsedUri['host'] . ($parsedUri['port'] !== 80 ? ':' . $parsedUri['port'] : '') . $parsedUri['path'] . (isset($parsedUri['query']) ? '?' . $parsedUri['query'] : '');
    }

    /**
     * Process the current request and route it to the class handler.
     *
     * @param null|string $uri The new uri to redirect the end user to.
     */
    final public function redirect(
        ?string $uri
    ): void {
        // Issue
        header('Location: ' . $uri, true, 303);
    }

    /**
     * Issue HTTP headers to disable browser caching.
     */
    final protected function disallowCaching() {
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }
}
