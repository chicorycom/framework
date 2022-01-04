<?php

namespace App\Events;

use App\Support\Auth;
use Boot\Foundation\Http\Session;
use Boot\Foundation\Mail\Mailable;
use Carbon\Carbon;

class UserLogin
{
    public $user;
    public $session;

    public function __construct(Session $session, Mailable $mail)
    {
        $this->user = Auth::user();
        $datetime = $carbon = new Carbon(Carbon::now(), 'Africa/Dakar');
        $date = $datetime->format('d/m/Y');
        $ip = $_SERVER['REMOTE_ADDR'];
        $this->session = $session;
        $user = $this->user;
        $mail->to($this->user->email, $this->user->prenom)
            ->bcc('contact@chicorycom.net')
            ->view('mail.auth.notification-connexion',compact('user','date','datetime', 'ip'));

       $mail->subject('Notification de connexion INFOSCHOOL')->send();

       if($user->email != 'contact@chicorycom.net' and $user->email != 'infociga@gmail.com'){
           $mail->to('infociga@gmail.com', 'Mr Mbaye')
               ->view('mail.auth.notification-connexion-b', compact('user','date','datetime', 'ip'));

           $mail->subject('Notification de connexion INFOSCHOOL')->send();
       }


        return $this;
    }
}
