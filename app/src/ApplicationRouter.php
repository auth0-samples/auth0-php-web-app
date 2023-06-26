<?php

declare(strict_types=1);

namespace Auth0\Quickstart;

use function array_key_exists;

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
        Application &$app,
    ) {
        $this->app = &$app;
    }

    /**
     * Return the request method (GET, POST, etc.).
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Return (and optionally manipulate) the currently requested uri.
     *
     * @param null|string $path  Unless null, manipulates the resulting path to match the value.
     * @param null|string $query Unless, manipulates the resulting query to match the value.
     */
    public function getUri(
        ?string $path = null,
        ?string $query = null,
    ): string {
        $httpScheme = $_SERVER['HTTPS'] ?? '';
        $httpScheme = 'on' === $httpScheme ? 'https' : 'http';

        $httpPort = (int) $_SERVER['SERVER_PORT'];
        $httpHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
        $httpHost = preg_replace('/\:' . $httpPort . '$/', '', $httpHost);

        $httpRequest = (string) $_SERVER['REQUEST_URI'];
        $httpUri = $httpScheme . '://' . $httpHost . (80 !== $httpPort ? ':' . $httpPort : '') . $httpRequest;

        // If we aren't making changes, simply return the uri.
        if (null === $path && null === $query) {
            return $httpUri;
        }

        // Parse a url into it's components so we can manipulate them more easily.
        $parsedUri = parse_url($httpUri);

        if (false === $parsedUri) {
            return $httpUri;
        }

        $parsedUri['scheme'] ??= 'http';
        $parsedUri['host'] ??= $httpHost;
        $parsedUri['path'] ??= '';
        $parsedUri['query'] = '?' . ($parsedUri['query'] ?? '');

        // Manipulate the /path portion of the uri.
        if (null !== $path) {
            $parsedUri['path'] = $path;
        }

        // Manipulate the ?query portion of the uri.
        if (null !== $query) {
            $parsedUri['query'] = $query;
        }

        if (! array_key_exists('port', $parsedUri)) {
            $parsedUri['port'] = 80;
        }

        if ('?' === $parsedUri['query']) {
            $parsedUri['query'] = '';
        }

        if ('' !== $parsedUri['query']) {
            $parsedUri['query'] = '?' . $parsedUri['query'];
        }

        // Reconstruct the manipulated uri and return it.
        return $parsedUri['scheme'] . '://' . $parsedUri['host'] . (80 !== $parsedUri['port'] ? ':' . $parsedUri['port'] : '') . $parsedUri['path'] . $parsedUri['query'];
    }

    /**
     * Process the current request and route it to the class handler.
     *
     * @param string $uri The new uri to redirect the end user to.
     */
    public function redirect(
        string $uri,
    ): void {
        header('Location: ' . $uri, true, 303);
        exit;
    }

    /**
     * Process the current request and route it to the class handler.
     */
    public function run(): void
    {
        $requestUri = parse_url($this->getUri(), PHP_URL_PATH);

        // Issue headers to disable browser caching.
        $this->setCachingHeaders();

        $routed = false;

        if ('/' === $requestUri) {
            $this->app->onIndexRoute($this);
            $routed = true;
        }

        if ('/callback' === $requestUri) {
            $this->app->onCallbackRoute($this);
            $routed = true;
        }

        if ('/login' === $requestUri) {
            $this->app->onLoginRoute($this);
            $routed = true;
        }

        if ('/logout' === $requestUri) {
            $this->app->onLogoutRoute($this);
            $routed = true;
        }

        if (false === $routed) {
            $this->app->onError404($this);
        }

        exit;
    }

    /**
     * Issue HTTP headers to disable browser caching.
     */
    public function setCachingHeaders(): void
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    }

    /**
     * Issue a HTTP response code header.
     *
     * @param int $status The HTTP status code to send.
     */
    public function setHttpStatus(
        int $status,
    ): void {
        http_response_code($status);
    }
}
