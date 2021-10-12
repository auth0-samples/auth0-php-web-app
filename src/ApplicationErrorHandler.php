<?php

declare(strict_types=1);

namespace Auth0\Quickstart;

use Auth0\SDK\Exception\Auth0Exception;
use Throwable;

final class ApplicationErrorHandler
{
    /**
     * An instance of our Quickstart Application.
     */
    private Application $app;

    /**
     * ApplicationErrorHandler constructor.
     *
     * @param Application $app An instance of our Quickstart Application.
     */
    public function __construct(
        Application &$app
    ) {
        $this->app = & $app;
    }

    /**
     * Setup onException() as the exception handler with PHP for this request.
     */
    public function hook(): void
    {
        set_exception_handler([$this, 'onException']);
    }

    /**
     * Render a throwable error in a graceful way.
     *
     * @param Throwable $throwable The throwable to report.
     */
    public function onException(
        \Throwable $throwable
    ): void {
        $exception = $throwable;

        $code = $exception->getCode();
        $error = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();

        if ($exception instanceof Auth0Exception) {
            $backtrace = $exception->getTrace();

            if (array_key_exists(1, $backtrace)) {
                $code = $backtrace[1]['line'];
                $file = $backtrace[1]['file'];
                $line = $backtrace[1]['line'];
            }
        }

        $this->app->getTemplate()->render('error', [
            'code' => $code,
            'error' => $error,
            'file' => $file,
            'line' => $line,
            'cookies' => $_COOKIE,
            'router' => $this->app->getRouter(),
        ]);

        exit;
    }
}
