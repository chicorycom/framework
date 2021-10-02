<?php

use App\Http\HttpKernel;

$app = new Boot\Foundation\App(
    $_ENV['APP_BASE_PATH'] ?? dirname( dirname(__DIR__) )
);


$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    HttpKernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);


return $app;
