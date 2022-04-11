<?php

declare(strict_types=1);

namespace Auth0\Quickstart\Example;

use Auth0\Quickstart\Application;
use Auth0\Quickstart\ApplicationRouter;
use Auth0\Quickstart\Contract\QuickstartExample;
use Auth0\SDK\Contract\API\ManagementInterface as ManagementAPI;
use Auth0\SDK\Utility\HttpResponse;

/**
 * This is a simple example of using the SDK's Management class for manipulating an authenticated user's metadata.
 */
final class Management implements QuickstartExample
{
    /**
     * An instance of our Quickstart Application.
     */
    private Application $app;

    public function __construct(
        Application &$app
    ) {
        $this->app = & $app;
    }

    public function setup(): self
    {
        // Register our example hook to override the default quickstart onLoginRoute behavior:
        $this->app->hook('onIndexRoute', [$this, 'onIndexRoute']);

        return $this;
    }

    /**
     * Query the users endpoint and retrieve details about a user.
     *
     * @param ManagementAPI $api An instance of the ManagementAPI to issue the request through.
     * @param string        $sub The user identifier to query.
     *
     * @return array<mixed>|null
     */
    public function getProfile(
        ManagementAPI $api,
        string $sub
    ): ?array {
        $response = $api->users()->get($sub);

        if (! HttpResponse::wasSuccessful($response)) {
            die('Management API request failed. Unable to get user.');
        }

        return HttpResponse::decodeContent($response);
    }

    /**
     * Update the user_metadata of a user, as a demonstration of the process.
     *
     * @param ManagementAPI $api An instance of the ManagementAPI to issue the request through.
     * @param string        $sub The user identifier to update metadata for.
     *
     * @return array<mixed>|null
     */
    public function updateProfile(
        ManagementAPI $api,
        string $sub
    ): ?array {
        $response = $api->users()->update($sub, [
            'user_metadata' => [
                'quickstart_example' => 'Updated ' . date(DATE_RFC2822, time()) . ' using the auth-PHP SDK quickstart!',
            ],
        ]);

        if (! HttpResponse::wasSuccessful($response)) {
            die('Management API request failed. Unable to update user.');
        }

        return HttpResponse::decodeContent($response);
    }

    public function onIndexRoute(
        ApplicationRouter $router,
        ?\stdClass $session
    ): ?bool {
        if ($session !== null) {
            $api = $this->app->getSdk()->management();

            if ($router->getMethod() === 'POST') {
                $this->updateProfile($api, $session->user['sub']);
            }

            $profile = $this->getProfile($api, $session->user['sub']);

            $this->app->getTemplate()->render('management', [
                'session' => $session,
                'router' => $router,
                'cookies' => $_COOKIE,
                'managementUserProfile' => $profile,
            ]);

            return true;
        }

        return null;
    }
}
