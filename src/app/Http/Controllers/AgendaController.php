<?php


namespace App\Http\Controllers;


use App\Models\Agenda;
use App\Support\RequestInput;
use Carbon\Carbon;
use http\Env\Request;
use Psr\Http\Message\ResponseInterface;

class AgendaController
{


    public function index(): ResponseInterface
    {

        return view('pages.agenda');
    }


    /**
     * @param $response
     * @param RequestInput $input
     * @return mixed
     */
    public function store($response, RequestInput $input){

        $rules = [
            'debut' => 'required',
            'fin' => 'required',
            'titre' => 'required|string',
            'description' => 'required|string',
            'statu' => 'required',
        ];
        $validator = validator(
            $input->all(),
            $rules,
        // $messages
        );

        if ($validator->fails()) {
            $response->getBody()->write(json_encode($validator->errors(), JSON_PRETTY_PRINT));
            $res = $response->withStatus(402);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $start = str_replace('/', '-', $input->debut);
        $start =  date("Y-m-d H:i:s", strtotime($start));
        $end = str_replace('/', '-', $input->fin);
        $end =  date("Y-m-d H:i:s", strtotime($end));

       $agenda = Agenda::create([
            "id_pers"=> \Auth::user()->id,
            "start"=>$start,
            "end"=>$end,
            "title"=>$input->titre,
            "description"=>$input->description,
            "status"=>$input->statu
        ]);

        $response
            ->getBody()
            ->write(json_encode([
                'success' => true,
                'event' => collect($agenda->toArray())->except(['id_pers', 'status'])
                ], JSON_PRETTY_PRINT));

        return $response->withStatus(201)
            ->withHeader('Content-Type', 'application/json');
    }


    /**
     * @param $response
     * @param RequestInput $input
     * @return mixed
     */
    public function all($response){
        $request = (object) $_GET;

       $agenda = Agenda::where('status', true)
           ->where('start', '>=', $request->start)
           ->where('end', '<=', $request->end)
           ->get();
        //$agenda = $agenda->filter(fn ($data) => $data->id_pers == \Auth::user()->id || $data->status == true );

        $response
            ->getBody()
            ->write(json_encode($agenda->toArray(), JSON_PRETTY_PRINT));

        return $response->withHeader('Content-Type', 'application/json');
    }


    /**
     * @param $response
     * @return mixed
     */
    public function alert($response){
        $agenda = Agenda::whereRaw('DATEDIFF(DATE(start),DATE(NOW())) >= 0 AND DATEDIFF(DATE(start),DATE(NOW())) <= 7')
            //->where('id_pers', \Auth::user()->id)
            //->orWhere('status', true)
            ->get();
        $agenda = $agenda->filter(fn ($data) => $data->id_pers == \Auth::user()->id || $data->status == true );

        $response
            ->getBody()
            ->write(json_encode($agenda->toArray(), JSON_PRETTY_PRINT));

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function destroy($response, $id){
        Agenda::find($id)->delete();
        $response->withStatus(204);
        return $response->withHeader('Content-Type', 'application/json');
    }
}