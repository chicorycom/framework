<?php


namespace App\Http\Controllers;


use App\Models\ParamsGeneral;
use App\Models\ParamsGrade;
use Psr\Http\Message\ResponseInterface;

class DemosController
{

    public function index($type, $genre, $request): ResponseInterface
    {
        $query_params = (object) $request->getQueryParams();
        $grade = null;
        if(isset($query_params->id)){
            $grade = ParamsGrade::find($query_params->id);
        }

        $logo = ParamsGeneral::first();


       return view("demos.$type.$genre", compact('logo', 'grade'));
    }

}