<?php


namespace App\Http\Controllers;


use App\WebPush\AccountApproved;
use App\WebPush\WebPushChannel;
use App\Models\PushSubscription;
use App\Support\Auth;
use App\Support\RequestInput;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class SubcriptionController
{

    public function register($response, RequestInput $input){
        $user = \Auth::user();
        //$subscription = Subscription::create(json_decode(file_get_contents('php://input'), true));
        $subs = $input->subscription;
        /*
        $subs = $input->subscription;
        $auth = Auth::user();

        PushSubscription::updateOrCreate(
            [
            'endpoint' => $subs['endpoint']
            ],
            [
                'subscribable' => $auth ? $auth->id : 0,
                'public_key' => $subs['keys']['p256dh'],
                'auth_token' => $subs['keys']['auth'],
                'content_encoding' => $subs['keys']['p256dh'],
            ]
        );
        $subscription = Subscription::create($input->subscription);
        // database name = push_subscriptions
        $auth = [
            'VAPID' => [
                'subject' => 'Infoschool',
                'publicKey' => env('VAPID_PUBLIC_KEY'), // don't forget that your public key also lives in app.js
                'privateKey' => env('VAPID_PRIVATE_KEY'), // in the real world, this would be in a secret file
            ],
        ];

        $webPush = new WebPush($auth);
        /**
         * title: "your title",
        text: "your text",
        image: "path/to/image.jpg",
        tag: "new...",
        url: "/your-url.html"

        $payload = json_encode(['title'=> 'Infoschool', 'text'=> 'votre intégrateur reseau system et securité vous 👋']);
        $report = $webPush->sendOneNotification(
            $subscription,
            $payload
        );
        //"Hello! 👋"
        // handle eventual errors here, and remove the subscription from your server if it is expired
        $endpoint = $report->getRequest()->getUri()->__toString();

        if ($report->isSuccess()) {
            $res = "[v] Message sent successfully for subscription {$endpoint}.";
        } else {
            $res = "[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}";
        }
         */
        $endpoint = $subs['endpoint'];
        $key = $subs['keys']['p256dh'];
        $token = $subs['keys']['auth'];
       // $contentEncoding = $input->subscription['keys']['p256dh'];
        //p256dh
            //auth
       // [$endpoint, $key, $token, $contentEncoding] = $input->subscription;
        $user->updatePushSubscription($endpoint, $key, $token);


        $response
            ->getBody()
            ->write(json_encode(['response'=> $subs], JSON_PRETTY_PRINT));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function key($response){
        $response
            ->getBody()
            ->write(json_encode(['key' => env('VAPID_PUBLIC_KEY')], JSON_PRETTY_PRINT));

        return $response->withHeader('Content-Type', 'application/json');
    }
}