<?php


namespace App\Providers;


use App\Http\Middleware\SentryMiddleware;


class SentryServiceProvider extends ServiceProvider
{
    public function register()
    {

        $this->app->bind(
            SentryMiddleware::class,
            fn (SentryMiddleware $sentryMiddleware) => new $sentryMiddleware(config('sentry'))
        );
    }
    public function boot()
    {

    }
}