<?php


namespace App\Http\Controllers\Api;


class APIController
{
    protected function json($response, $data){
        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
       return $res->withHeader('Content-Type', 'application/json');
    }
}