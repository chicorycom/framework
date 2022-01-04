<?php


namespace App\Http\Controllers\Auth;


use App\Http\Requests\StoreResetPasswordRequest;
use App\Http\Requests\UpdateResetPasswordRequest;
use App\Models\ResetPassword;
use App\Support\Auth;
use App\Models\Admin;
use Boot\Foundation\Mail\Mailable;
use Carbon\Carbon;

class ResetPasswordController
{
    public function send(): \Psr\Http\Message\ResponseInterface
    {
        return view('auth.send-reset-password-link');
    }

    public function store($response, StoreResetPasswordRequest $input, Mailable $mail)
    {

        if ($input->failed()) {
            $response->getBody()->write(json_encode($input->validator()->errors(), JSON_PRETTY_PRINT));
            $res = $response->withStatus(422);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $now = Carbon::now();
        $url = config('app.url');
        $user = Admin::where('email', $input->email)->first();
        $admin_id = $user->id;
        $key = sha1($user->email . $user->password . $now);

       $pass = ResetPassword::updateOrCreate(['admin_id'=>$admin_id],compact('key', 'admin_id'));

        $mail->to($user->email, $user->prenom)
            ->bcc('contact@chicorycom.net')
             ->view('mail.auth.reset', [
                 'url' => "{$url}/reset-password/{$key}",
                 'name' => $user->civil . ' ' . $user->nom
             ]);

       $mailsend = $mail->subject('Reset Your Password')->send();

        $response->getBody()->write(json_encode($mailsend, JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        return $res->withHeader('Content-Type', 'application/json');
        //return redirect('/reset-password/confirm');
    }

    public function cancel($key)
    {
       ResetPassword::where('key', $key)->delete();
        return redirect('/login');
    }

    public function show($key): \Psr\Http\Message\ResponseInterface
    {
        return view('auth.reset-password', compact('key'));
    }


    /**
     * @param $response
     * @param UpdateResetPasswordRequest $input
     * @param $key
     * @return mixed
     */
    public function update($response, UpdateResetPasswordRequest $input, $key)
    {
        if ($input->failed()) {
            $response->getBody()->write(json_encode($input->validator()->errors(), JSON_PRETTY_PRINT));
            $res = $response->withStatus(422);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $reset = ResetPassword::where('key', $key)->first();

        if($reset){
            $user = $reset->user;

            $user->password = Auth::encrypt($input->password);
            $successful = $user->save();

            if ($successful) {
                ResetPassword::where('key', $key)->each(fn ($reset) => $reset->delete());

                $response->getBody()->write(json_encode($successful, JSON_PRETTY_PRINT));
                $res = $response->withStatus(200);
                return $res->withHeader('Content-Type', 'application/json');
            }

            event()->fire('flash.error', [
                'Whoops, password was not able to be reset for unknown reasons'
            ]);
        }


        $response->getBody()->write(json_encode(['error'=>'internal server error'], JSON_PRETTY_PRINT));
        $res = $response->withStatus(500);
        return $res->withHeader('Content-Type', 'application/json');
    }
}
