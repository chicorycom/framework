<?php

namespace App\Http\Controllers\Auth;


use App\Events\UserLogin;
use App\Events\UserLogout;
use App\Http\Requests\StoreLoginRequest;
use Boot\Support\Auth;
use Psr\Http\Message\ResponseInterface;


class LoginController
{

    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {

        return view( 'auth.login');
        //return $view('auth.login');
    }


    /**
     * @param $response
     * @param StoreLoginRequest $input
     * @return mixed
     */
    public function login($response, StoreLoginRequest $input)
    {

        if(Auth::attempt($input->email, $input->password)) {
            event()->fire(UserLogin::class);
            $response->getBody()->write(json_encode(['message'=> 'Success'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(200);
            return $res->withHeader('Content-Type', 'application/json');
        } else {

            $response->getBody()->write(json_encode(['message'=> 'Erreur de connexion'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        Auth::logout();
        if (Auth::guest()) {
            event()->fire(UserLogout::class);
            return redirect('/login');
        }
        return redirect('/dashboard');
    }
}
