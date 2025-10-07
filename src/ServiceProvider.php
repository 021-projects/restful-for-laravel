<?php

namespace O21\RestfulForLaravel;

use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->offerPublishing();

        $this->mergeConfigFrom(
            __DIR__.'/../config/restful.php',
            'restful'
        );

        $this->registerExceptionsHandler();
    }

    protected function offerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/restful.php' => config_path('restful.php'),
            ], 'restful-config');
        }
    }

    protected function registerExceptionsHandler(): void
    {
        $this->app->singleton(
            ExceptionsHandler::class,
            fn ($app) => new ExceptionsHandler
        );

        if (! config('restful.render_exceptions', true)) {
            return;
        }

        $this->app->afterResolving(
            Handler::class,
            fn (Handler $handler) => $handler->renderable(
                fn (\Throwable $e) => $this->app
                    ->make(ExceptionsHandler::class)
                    ->render($e)
            ),
        );
    }
}
