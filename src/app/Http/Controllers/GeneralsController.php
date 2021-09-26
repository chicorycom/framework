<?php


namespace App\Http\Controllers;


use App\Models\Cicle;
use App\Models\Menu;
use App\Models\ParamsGeneral;
use App\Models\ParamsGrade;
use App\Support\RequestInput;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManagerStatic as Image;
use Psr\Http\Message\ResponseInterface;

class GeneralsController extends Controller
{

    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        try {
            $this->unauthorized('view');
            $params = ParamsGeneral::first();
            return view( 'pages.generals', compact('params'));

        }catch (\Exception $e){
           return  view( 'errors.403')
               ->withStatus(403);
        }

    }

    /**
     * @param RequestInput $input
     * @param $response
     * @return mixed
     */
    public function logo(RequestInput $input, $response){
        if(!is_object($this->permiss) || !$this->permiss->add){
            $response->getBody()->write(json_encode(['message' => 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $input->forget('csrf_value');
        $input->forget('csrf_name');

        if($input->hasFile('logo')) {

            Image::configure(['driver' => 'imagick']);

            $filename = move(public_path('img/'), $input->file('logo'));

            $params = ParamsGeneral::first();
            $params->update(['srcimg' => $filename]);


            $response->getBody()->write(json_encode(['success' => true], JSON_PRETTY_PRINT));
            $res = $response->withStatus(200);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['error' => 'logo doit étre en jpg, bmp ou png'], JSON_PRETTY_PRINT));
        $res = $response->withStatus(422);
        return $res->withHeader('Content-Type', 'application/json');

    }

    public function grades(): ResponseInterface
    {

        try {
            $this->unauthorized('add');
            $grades = ParamsGrade::all();
            $cycles = Cicle::listes();
            return view( 'pages.generals-grades', compact('grades', 'cycles'));

        }catch (\Exception $e){
            return  view( 'errors.403')
                ->withStatus(403);
        }
    }

    /**
     * @param $response
     * @param $id
     * @return mixed
     */
    public function grade($response, $id){
        $grade = ParamsGrade::find($id);
        $response->getBody()->write(json_encode($grade, JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        return $res->withHeader('Content-Type', 'application/json');
    }


    /**
     * @param $response
     * @param RequestInput $input
     * @return mixed
     */
    public function gradeStore($response, RequestInput $input){
        if(!is_object($this->permiss) || !$this->permiss->add){
            $response->getBody()->write(json_encode(['message' => 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $input->forget('csrf_value');
        $input->forget('csrf_name');

        $rules = [
            'type' => 'required',
            'categorie' => 'required',
            'titre' => 'required|string',
            'couleurtitre' => 'required|string',
            'description' => 'required',
            'couleurdescription' => 'required',
            'coulleurfontdescription' => 'required',
            'adressBul' => 'required',
            'couleurAdresBul' => 'required',
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
        $response->getBody()->write(json_encode(['success'=>true], JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        if($input->id){
          $grade =  ParamsGrade::find($input->id);
            $input->forget('id');
            $grade->update($input->all());
            return $res->withHeader('Content-Type', 'application/json');
        }

        $input->forget('id');
        ParamsGrade::create($input->all());
        return $res->withHeader('Content-Type', 'application/json');
    }


    /**
     * @param $response
     * @param int $id
     * @return mixed
     */
    public function gradeDestroy($response, int $id){
        if(!is_object($this->permiss) || !$this->permiss->delete){
            $response->getBody()->write(json_encode(['message' => 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        ParamsGrade::find($id)->delete();

        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(204);
        return $res->withHeader('Content-Type', 'application/json');
    }

}