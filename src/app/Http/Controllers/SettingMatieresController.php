<?php


namespace App\Http\Controllers;


use App\Models\Material;
use App\Support\RequestInput;
use Psr\Http\Message\ResponseInterface;

class SettingMatieresController extends Controller
{

    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        if(!is_object($this->permiss) || !$this->permiss->view){
            return  view( 'errors.403')
                ->withStatus(403);
        }

        $matieres = Material::all();
        return view('pages.setting-matieres', compact('matieres'));
    }


    /**
     * @param $response
     * @param RequestInput $input
     * @return mixed
     */
    public function store($response, RequestInput $input){

        if(!is_object($this->permiss) || !$this->permiss->add){
            $response->getBody()->write(json_encode(['message'=> 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $rules = [
            'matiere' => 'required|unique:matiere_classe|string',
        ];

        $validator = validator(
            $input->all(),
            $rules,
            ['matiere.unique' => ':attribute existe déjà']
        );

        if ($validator->fails()) {
            $response->getBody()->write(json_encode($validator->errors(), JSON_PRETTY_PRINT));
            $res = $response->withStatus(422);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $matiere = Material::create(['matiere' => $input->matiere]);


        $response->getBody()->write(json_encode($matiere, JSON_PRETTY_PRINT));
        $res = $response->withStatus(201);
        return $res->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param $response
     * @param RequestInput $input
     * @param $id
     * @return mixed
     */
    public function update($response, RequestInput $input, $id){

        if(!is_object($this->permiss) || !$this->permiss->edit){
            $response->getBody()->write(json_encode(['message'=> 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $rules = [
            'matiere' => 'required|string',
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

        Material::find($id)->update(['matiere' => $input->matiere]);
        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        return $res->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param $id
     * @param $response
     * @return mixed
     */
    public function destroy($id, $response){

        if(!is_object($this->permiss) || !$this->permiss->delete){
            $response->getBody()->write(json_encode(['message'=> 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        Material::find($id)->delete();
        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(204);
        return $res->withHeader('Content-Type', 'application/json');
    }

}