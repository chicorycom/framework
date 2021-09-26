<?php


namespace App\Http\Controllers;


use App\Models\Campus;
use App\Models\Instription;
use App\Models\Note;
use App\Support\RequestInput;
use Psr\Http\Message\ResponseInterface;

class SettingCampusController extends Controller
{
    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        if(!is_object($this->permiss) || !$this->permiss->add){
            return  view( 'errors.403')
                ->withStatus(403);
        }

        $campuss = Campus::all();

        return view('pages.setting-campus', compact('campuss'));
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

        $input->forget('csrf_value');
        $input->forget('csrf_name');

        $rules = [
            'name' => 'required|string',
            'address' => 'required|string',
            'description' => 'required|string',
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

        $campus = Campus::create($input->all());
        $response->getBody()->write(json_encode($campus, JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
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
            'name' => 'required|string',
            'address' => 'required|string',
            'description' => 'required|string',
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

        Campus::find($id)->update($input->all());
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

       $student = Instription::whereCampusId($id);
       $te = $student->first();
       if($te){
           $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
           $res = $response->withStatus(401);
           return $res->withHeader('Content-Type', 'application/json');
       }
        Instription::whereCampusId($id)->delete();
        Note::whereCampusId($id)->delete();
        Campus::find($id)->delete();

        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(204);
        return $res->withHeader('Content-Type', 'application/json');
    }
}