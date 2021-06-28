<?php

declare(strict_types=1);

use Auth0\Quickstart\Application;

/**
 * This file bootstraps our application.
 */

require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Import the files necessary for our Quickstart Application.
foreach ([
    'vendor/autoload.php', // Composer autoloader, for our dependencies, such as the SDK itself.

    // These classes are application boilerplate and not directly relevant to SDK usage:
    'src/ApplicationRouter.php',
    'src/ApplicationTemplates.php',
    'src/ApplicationErrorHandler.php',

    // Import our Application class, where our app logic resides, and where we'll make our SDK calls.
    'src/Application.php',
] as $import) {
    require_once join(DIRECTORY_SEPARATOR, [APP_ROOT, $import]);
}

// Load configuration from .env file in project root.
(Dotenv\Dotenv::createImmutable(APP_ROOT))->load();

// Instantiate our Quickstart Application using the .env configuration.
$app = new Application($_ENV);

if (isset($_ENV['AUTH0_EXAMPLE'])) {
    require_once join(DIRECTORY_SEPARATOR, [APP_ROOT, 'src/Contract/QuickstartExample.php']);
    require_once join(DIRECTORY_SEPARATOR, [APP_ROOT, 'src/Example', $_ENV['AUTH0_EXAMPLE'] . '.php']);

    $className = '\Auth0\Quickstart\Example\\' . $_ENV['AUTH0_EXAMPLE'];

    $app->useExample(new $className($app));
}

$app->run();
