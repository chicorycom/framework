<?php

namespace App\Http\Requests;


class StoreRegisterRequest extends FormRequest
{
    protected function afterValidationPasses()
    {
        $this->forget('csrf_value');
        $this->forget('csrf_name');
        $this->password = password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function rules(): array
    {
        return [
            'email' => 'unique:users,email|email|required',
            'password' => 'required_with:confirm_password|same:confirm_password|min:5',
            'confirm_password' => 'string|required'
        ];
    }

    public function messages(): array
    {
        return [
            'password.same' => ':attribute does not match :same',
            'password.required_with' => ':attribute needs :required_with to properly validate',
            'email.unique' => ':attribute already exists',
            'email.email' => ':attribute must be an email',
            'email.required' => ':attribute is required',
            'confirm_password.required' => ':attribute is required',
            'confirm_password.string' => ':attribute must be a string'
        ];
    }
}
