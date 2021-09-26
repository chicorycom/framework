<?php


namespace App\Http\Controllers;


use App\Models\Campus;
use App\Models\Cicle;
use App\Models\Instription;
use App\Models\Note;
use App\Support\RequestInput;
use Psr\Http\Message\ResponseInterface;

class CycleClasseController extends Controller
{
    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        try {
            $this->unauthorized('view');
            $cycles = Cicle::listes();
            return view('pages.cycle-classe', compact('cycles'));

        }catch (\Exception $e){
            return  view( 'errors.403')
                ->withStatus(403);
        }
    }


    /**
     * @param $response
     * @param RequestInput $input
     * @return mixed
     */
    public function store($response, RequestInput $input){
        if(!is_object($this->permiss) || !$this->permiss->add){
            $response->getBody()->write(json_encode(['message' => 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }


        $input->forget('csrf_value');
        $input->forget('csrf_name');

        $rules = [
            'titre_cat' => 'required|string'
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

         Cicle::create($input->all());
        $cycles = Cicle::listes();
        $response->getBody()->write(json_encode($cycles, JSON_PRETTY_PRINT));
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
        $input->forget('csrf_value');
        $input->forget('csrf_name');

        $rules = [
            'titre_cat' => 'required|string'
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

        Cicle::whereIdCat($id)->update($input->all());
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
            $response->getBody()->write(json_encode(['message' => 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }
       $cycle = Cicle::whereIdCat($id)->first();

       if($cycle){
           Instription::whereIdClasse($id)->delete();
           Note::whereClasse($id)->delete();
           Cicle::whereSupCat($id)->delete();
           Cicle::whereIdCat($id)->delete();
           $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
           $res = $response->withStatus(204);
           return $res->withHeader('Content-Type', 'application/json');
       }


        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(422);
        return $res->withHeader('Content-Type', 'application/json');
    }

}