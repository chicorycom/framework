<?php

namespace App\Http\Requests;

class StoreLoginRequest extends FormRequest
{


    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string'
        ];
    }
}
