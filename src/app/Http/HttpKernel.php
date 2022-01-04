<?php


namespace App\Http;


use Boot\Foundation\HttpKernel as Kernel;

class HttpKernel extends Kernel
{


    /**
     * Injectable Request Input Form Request Validators
     * @var array
     */
    public array $requests = [
        Requests\StoreRegisterRequest::class,
        Requests\StoreEmployesRequest::class,
        Requests\StoreLoginRequest::class,
        Requests\StoreResetPasswordRequest::class,
        Requests\UpdateResetPasswordRequest::class,
    ];


    /**
     * Global Middleware
     *
     * @var array
     */
    public array $middleware = [
        Middleware\RouteContextMiddleware::class,
//        Middleware\SentryMiddleware::class,
//        Middleware\ExampleBeforeMiddleware::class
    ];

    /**
     * Route Group Middleware
     */
    public array $middlewareGroups = [
        'api' => [],
        'web' => [
            'csrf'
        ]
    ];

}