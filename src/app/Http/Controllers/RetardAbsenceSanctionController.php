<?php


namespace App\Http\Controllers;


use App\Models\AnneeScolaire;
use App\Models\Period;
use App\Models\RetardAbsenceSanction;
use App\Support\RequestInput;
use Illuminate\Validation\Rule;

class RetardAbsenceSanctionController
{

    public function store($response, RequestInput $input){
        $input->forget('csrf_value');
        $input->forget('csrf_name');
        $rules = [
            'datedu' => 'required',
            'dateau' => 'required',
            'motif' => 'required|string',
            'justifie' => 'required|boolean',
            'type' => 'required|'.Rule::in('R','A', 'S'),
            'campus_id' => 'exists:campus,id|required',
            'classe' => 'exists:cicle,id_cat|required',
            'matricule' => 'required',
        ];
        $validator = validator(
            $input->all(),
            $rules,
        // $messages
        );

        if ($validator->fails()) {
            $response->getBody()->write(json_encode($validator->errors(), JSON_PRETTY_PRINT));
            $res = $response->withStatus(422);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $input->merge(['periode' => Period::active()->id, 'anneScol' => AnneeScolaire::years()]);

        RetardAbsenceSanction::create($input->all());
        $response->getBody()->write(json_encode(['success' => true], JSON_PRETTY_PRINT));
        $res = $response->withStatus(201);
        return $res->withHeader('Content-Type', 'application/json');
    }

    public function destroy($response, $id){

        RetardAbsenceSanction::find($id)->delete();
        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(204);
        return $res->withHeader('Content-Type', 'application/json');
    }
}