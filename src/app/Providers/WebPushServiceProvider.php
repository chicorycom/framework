<?php


namespace App\Providers;

use App\WebPush\WebPushChannel;
use Illuminate\Support\Str;
use Minishlink\WebPush\WebPush;
use App\WebPush\ReportHandler;



class WebPushServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            WebPushChannel::class,
            fn (WebPush $webPush) => new WebPushChannel(new WebPush($this->webPushAuth()), new ReportHandler())
        );
    }

    public function boot()
    {
        // TODO: Implement boot() method.
    }


    /**
     * Get the authentication details.
     *
     * @return array
     */
    protected function webPushAuth()
    {
        $config = [];

        $publicKey = env('VAPID_PUBLIC_KEY');
        $privateKey =  env('VAPID_PRIVATE_KEY');

        if (empty($config['VAPID']['subject'])) {
            $config['VAPID']['subject'] = 'mailto:contact@chicorycom.net';
        }

        if (empty($publicKey) || empty($privateKey)) {
            return $config;
        }

        $config['VAPID'] = array_merge($config['VAPID'], compact('publicKey', 'privateKey'));
        //dd($config['VAPID']);
        return $config;
    }
}