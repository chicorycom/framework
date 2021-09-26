<?php


namespace App\Http\Controllers;


use App\Models\AnneeScolaire;
use App\Models\Instription;
use App\Models\Note;
use App\Support\RequestInput;
use Psr\Http\Message\ResponseInterface;

class AnneesScolaireController extends Controller
{

    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        try {
            $this->unauthorized('view');
            $years = AnneeScolaire::all();
            return view('pages.annees-scolaire', compact('years'));

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
            'titre' => 'required|string',
            'debutannee' => 'required|date_format:Y-m-d',
            'finannee' => 'required|date_format:Y-m-d',
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

        $year = AnneeScolaire::create($input->all());
        $response->getBody()->write(json_encode($year, JSON_PRETTY_PRINT));
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
            'titre' => 'required|string',
            'debutannee' => 'required|date_format:Y-m-d',
            'finannee' => 'required|date_format:Y-m-d',
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


        $period = AnneeScolaire::find($id)->update($input->all());

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
        $error = [];
        $annee = AnneeScolaire::find($id);
        if($annee){
            AnneeScolaire::all()->map(fn ($data) => $data->update(['status' => false]));
            $annee->status = true;
            $annee->save();
            $response->getBody()->write(json_encode($annee, JSON_PRETTY_PRINT));
            $res = $response->withStatus(200);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($error, JSON_PRETTY_PRINT));
        $res = $response->withStatus(422);
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

        $year = AnneeScolaire::selectRaw('titre, YEAR(debutannee) AS debut, YEAR(finannee) AS fin, status')->find($id);

        if($year->status){
            $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }
        $annee = $year->debut . '-' . $year->fin;
        $students = Instription::where('anneScol', $annee)->first();

        if($students){
            $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        Instription::where('anneScol', $annee)->delete();
        Note::whereAnnees($annee)->delete();
        AnneeScolaire::find($id)->delete();

        $response->getBody()->write(json_encode(['message'=> 'Non autorisé'], JSON_PRETTY_PRINT));
        $res = $response->withStatus(204);
        return $res->withHeader('Content-Type', 'application/json');
    }
}