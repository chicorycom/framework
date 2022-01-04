<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Boot\Support\Auth;
use App\Http\Requests\StoreRegisterRequest;

class RegisterController
{
    public function form()
    {
        return view('auth.register');
    }

    public function register(StoreRegisterRequest $input)
    {
        if ($input->failed()) return back();

        $user = User::forceCreate($input->all());

        $fails = !Auth::attempt($user->email, $user->password);

        if ($fails) return back();

        return redirect('/home');
    }
}
