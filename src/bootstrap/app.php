<?php

use App\Http\HttpKernel;
use Boot\Console\ConsoleKernel;
use DI\Container;
use Boot\Foundation\AppFactoryBridge as App;

$app = App::create(new Container);

$http_kernel = new HttpKernel($app);
$console_kernel = new ConsoleKernel($app);

$app->bind(HttpKernel::class, $http_kernel);
$app->bind(ConsoleKernel::class, $console_kernel);

$_SERVER['app'] = &$app;

if (!function_exists('app'))
{
    function app()
    {
        return $_SERVER['app'];
    }
}

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

return $app;
