<?php

namespace App\Http\Requests;



use Illuminate\Validation\Rule;

class StoreEmployesRequest extends FormRequest
{
    protected function afterValidationPasses()
    {
        $this->forget('csrf_value');
        $this->forget('csrf_name');
    }

    public function rules(): array
    {
        return [
            'civil' => 'required|'.Rule::in('Mme','Mlle', 'Mr'),
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'profil' => 'required',
            'adresse' => 'required|string',
            'email' => 'unique:admin,email|email|required',
            'password' => 'required|min:6',
            'telephone' => 'required|string',
            'image' => 'mimes:jpg,bmp,png'
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => ':attribute existe déjà',
            'password.min' => 'le mot de passe doit contenir au moins 6 caractères!',
        ];
    }
}
