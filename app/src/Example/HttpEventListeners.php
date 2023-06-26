<?php

declare(strict_types=1);

namespace Auth0\Quickstart\Example;

use Auth0\Quickstart\Application;
use Auth0\Quickstart\Contract\QuickstartExample;
use Auth0\SDK\Event\{HttpRequestBuilt, HttpResponseReceived};
use GuzzleHttp\Psr7\Utils;
use Hyperf\Event\ListenerProvider;

/**
 * This is a simple example of using the SDK's PSR-14 support for manipulating RequestInterface and ResponseInterface objects when sending/receiving HTTP requests.
 */
final class HttpEventListeners implements QuickstartExample
{
    /**
     * @var bool
     */
    private const ECHO_HTTP_RESPONSE = false;

    /**
     * An instance of our Quickstart Application.
     */
    private Application $app;

    /**
     * An instance of a PSR-14 ListenerProvider.
     */
    private ListenerProvider $listener;

    public function __construct(
        Application &$app,
    ) {
        $this->app = &$app;
        $this->listener = new ListenerProvider();
    }

    public function onHttpRequestBuilt(
        object $event,
    ): void {
        // Retrieve the built PSR-7 RequestInterface message.
        $request = $event->get();

        // Add a customer header to the outgoing request.
        $request = $request->withHeader('X-EXAMPLE-SENDING', 'Just a quickstart demo of changing headers on outgoing API requests.');

        // Update the PSR-7 RequestInterface object before the SDK dispatches it.
        $event->set($request);
    }

    public function onHttpResponseReceived(
        object $event,
    ): void {
        // Retrieve the built PSR-7 ResponseInterface message.
        $response = $event->get();

        // Retrieve the PSR-7 RequestInterface that this response was for.
        $request = $event->getRequest();
        $requestUri = $request->getUri();

        // Overwrite the body of responses from example.us.auth0/somewhere.
        if ('example.us.auth0.com' === $requestUri->getHost()
            && '/somewhere' === $requestUri->getPath()) {
            $response = $response->withBody(Utils::streamFor('new content for the response'));
        }

        // Echo the respond object and terminate the request.
        // @phpstan-ignore-next-line
        if (self::ECHO_HTTP_RESPONSE) {
            echo '<pre>';
            print_r($response->getBody()->__toString());
            exit;
        }

        // Update the PSR-7 ResponseInterface object before the SDK uses it.
        $event->set($response);
    }

    public function setup(): self
    {
        // Register our PSR-14 ListenerProvider.
        $this->app->getConfiguration()->setEventListenerProvider($this->listener);

        // Register the events we want to listen for.
        $this->listener->on(HttpRequestBuilt::class, function (object $event): void {
            $this->onHttpRequestBuilt($event);
        });
        $this->listener->on(HttpResponseReceived::class, function (object $event): void {
            $this->onHttpResponseReceived($event);
        });

        return $this;
    }
}
