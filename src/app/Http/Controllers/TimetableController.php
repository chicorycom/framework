<?php


namespace App\Http\Controllers;


use App\Models\Timetable;
use App\Support\RequestInput;
use Psr\Http\Message\ResponseInterface;

class TimetableController
{
    public function index(int $campus, int $classe): ResponseInterface
    {



        return view('timetable.index', compact('campus', 'classe'));
    }


    /**
     * @param $response
     * @param int $campus
     * @param int $classe
     * @return ResponseInterface
     */
    public function data($response, int $campus, int $classe): ResponseInterface
    {

        $datas = Timetable::whereCampusId($campus)
            ->whereIdClasse($classe)
            ->get();
        $response
            ->getBody()
            ->write(json_encode($datas, JSON_PRETTY_PRINT));

        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json');
    }


    /**
     * @param $response
     * @param int $campus
     * @param int $classe
     * @param RequestInput $input
     * @return mixed
     */
    public function store($response, int $campus, int $classe, RequestInput $input){

        $input->forget('csrf_value');
        $input->forget('csrf_name');


        $rules = [
            'start' => 'required|date', //2021-7-3 8:0:0
            'end' => 'required|date',
            'title' => 'required|string',
            'body' => 'required|string',
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

        $event = Timetable::create($input->all());

        $response->getBody()->write(json_encode($event, JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        return $res->withHeader('Content-Type', 'application/json');
    }

    public function resize($response, $id, RequestInput $input){
        $input->forget('csrf_value');
        $input->forget('csrf_name');
        //dd($input->all());
        Timetable::find($id)->update($input->all());
        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        return $res->withHeader('Content-Type', 'application/json');
    }

    public function destroy($response, $id){
        Timetable::find($id)->delete();
        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        return $res->withHeader('Content-Type', 'application/json');
    }
}