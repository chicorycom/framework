<?php


namespace App\Http\Controllers;


use App\Models\Profil;
use App\Support\RequestInput;
use Psr\Http\Message\ResponseInterface;

class ProfilsController extends Controller
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

        $profils = Profil::all();

        return view('pages.profil', compact('profils'));
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
            'titre' => 'required|string',
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

        $profil = Profil::create(['titreProfils' => $input->titre]);


        $response->getBody()->write(json_encode($profil, JSON_PRETTY_PRINT));
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

        $input->forget('csrf_value');
        $input->forget('csrf_name');

        $rules = [
            'titreProfils' => 'required|string',
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

        Profil::find($id)->update($input->all());
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

        Profil::find($id)->delete();
        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(204);
        return $res->withHeader('Content-Type', 'application/json');
    }

}