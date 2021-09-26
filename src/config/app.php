<?php

return [
    /**
     * Application name
     */
    'name' => env('APP_NAME', 'Slim 4 Auth App'),

    /**
     * Application Environment
     */
    'env' => env('APP_ENV', 'production'),

    /**
     * Determine if you want to display extended debug information
     * within the browser
     */
    'app_debug' => env('APP_DEBUG', false),

    /**
     * Register App Url
     */
    'url' => env('APP_URL', 'http://chicorycom.net'),

    /**
     * Register Timezone
     */
    'timezone' => 'UTC',

    /**
     * The application locale Determines the default locale that will be used
     * by the translation service provider. You are free to set this value
     * to any of the locales which will be supported by the application
     */
    'locale' => 'en',

    /**
     * This locale will be used by the Faker Php library when generating
     * fake data
     */
    'faker_locale' => 'en_US',


    'providers' => [
        /* Booted Foundation Service Providers...  */
        \Boot\Foundation\Providers\FileSystemServiceProvider::class,
        \Boot\Foundation\Providers\TranslatorServiceProvider::class,
        \Boot\Foundation\Providers\ValidatorServiceProvider::class,
        \Boot\Providers\AuthServiceProvider::class,
        \Boot\Providers\DatabaseServiceProvider::class,
        \Boot\Providers\BladeServiceProvider::class,



        /* App Service Providers... */
       // \App\Providers\SentryServiceProvider::class,
        \App\Providers\EventServiceProvider::class,
        \App\Providers\WebPushServiceProvider::class,
    ],
    'aliases' => [
        'Auth' => \Boot\Support\Auth::class,
        'DB' => \Illuminate\Database\Capsule\Manager::class,
    ]
];