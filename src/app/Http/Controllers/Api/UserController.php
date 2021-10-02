<?php


namespace App\Http\Controllers\Api;



class UserController extends APIController
{
    public function index($response){

        return $this->json($response, ['api'=>'REST']);
    }
}