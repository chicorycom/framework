<?php


namespace App\Http\Controllers;


use App\Models\AnneeScolaire;
use App\Models\Note;
use App\Models\Period;
use App\Support\RequestInput;
use Psr\Http\Message\ResponseInterface;

class PeriodesController extends Controller
{

    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        try {
            $this->unauthorized('view');
            $periodes = Period::all();
            return view('pages.period', compact('periodes'));

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
            $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $input->forget('csrf_value');
        $input->forget('csrf_name');

        $rules = [
            'debuperiod' => 'required|date_format:d/m/Y',
            'finperiod' => 'required|date_format:d/m/Y',
            'titreperiod' => 'required|string',
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


        $period = Period::create($input->all());

        $response->getBody()->write(json_encode($period, JSON_PRETTY_PRINT));
        $res = $response->withStatus(201);
        return $res->withHeader('Content-Type', 'application/json');
    }


    public function update($response, RequestInput $input, $id){
        if(!is_object($this->permiss) || !$this->permiss->edit){
            $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }
        $input->forget('csrf_value');
        $input->forget('csrf_name');

        $rules = [
            'debuperiod' => 'required|date_format:d/m/Y',
            'finperiod' => 'required|date_format:d/m/Y',
            'titreperiod' => 'required|string',
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


        $period = Period::find($id)->update($input->all());

        $response->getBody()->write(json_encode($period, JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        return $res->withHeader('Content-Type', 'application/json');
    }


    /**
     * @param $response
     * @param $id
     * @return mixed
     */
    public function toggle($response, $id){

        if(!is_object($this->permiss) || !$this->permiss->edit){
            $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $error = [];
        $period = Period::find($id);
        if($period){
                Period::all()->map(fn ($data) => $data->update(['statuperiod' => false]));
                $period->statuperiod = true;
                $period->save();
            $response->getBody()->write(json_encode($period, JSON_PRETTY_PRINT));
            $res = $response->withStatus(200);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($error, JSON_PRETTY_PRINT));
        $res = $response->withStatus(422);
        return $res->withHeader('Content-Type', 'application/json');

    }


    public function destroy($id, $response){

        if(!is_object($this->permiss) || !$this->permiss->delete){
            $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $period = Period::find($id);
        $note = Note::wherePeriod($id)->first();
        if($period->statuperiod || $note){
            $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
            $res = $response->withStatus(401);
            return $res->withHeader('Content-Type', 'application/json');
        }
        Note::wherePeriod($id)->whereAnnees(AnneeScolaire::years())->delete();
        Period::find($id)->delete();
        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(204);
        return $res->withHeader('Content-Type', 'application/json');
    }

}