<?php


namespace App\Http\Controllers;


use App\Http\Requests\StoreRegisterRequest;
use App\Models\AnneeScolaire;
use App\Models\Campus;
use App\Models\Cicle;
use App\Models\Instription;
use App\Models\Note;
use App\Models\Period;
use App\Support\RequestInput;
use App\WebPush\AccountApproved;
use App\WebPush\WebPushChannel;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManagerStatic as Image;
use Psr\Http\Message\ResponseInterface;

class StudentController extends Controller
{


    /**
     * @param $response
     * @param StoreRegisterRequest $input
     * @param $campus
     * @param WebPushChannel $webPushChannel
     * @return mixed
     * @throws \ErrorException
     */
    public function store($response, StoreRegisterRequest $input, $campus, WebPushChannel $webPushChannel){

        /**
         * ACL Permission
         */

        if(!is_object($this->permiss) || !$this->permiss->add){
            $response->getBody()->write(json_encode(['message' => 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $auth =  \Auth::user();

        if ($input->failed()) {
            $response->getBody()->write(json_encode($input->validator()->errors(), JSON_PRETTY_PRINT));
            $res = $response->withStatus(422);
            return $res->withHeader('Content-Type', 'application/json');
        }


        if($input->campus_id != $campus ) {
            $response->getBody()->write(json_encode($input->validator()->errors(), JSON_PRETTY_PRINT));
            $res = $response->withStatus(422);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $filename = $input->sexe === 'M' ? "default_avatar_male.jpg" : "default_avatar_female.png";



        if($input->hasFile('photo') && !empty($input->file('photo')->getClientFilename())){

                Image::configure(array('driver' => 'imagick'));

                $filename = move(public_path('img/avartasEtudiant/'), $input->file('photo'));

                $thumb_path = 'img/avartasEtudiant/min_' . $filename;

                Image::make(public_path('img/avartasEtudiant/') . $filename)
                ->resize(160, 160)
                ->save(public_path($thumb_path));
        }

        $student = Campus::find($campus)
           ->students()
           ->create([
               //'campus_id' => $input->campus_id,
               'id_classe' => $input->id_classe,
               'matricule' => $input->matricule,
               'prenom' => $input->prenom,
               'nom' => $input->nom,
               'date_naissance' => $input->date_naissance,
               'lieu_naissance' => $input->lieu_naissance,
               'sexe' => $input->sexe,
               'adresse' => $input->adresse,
               'email' => $input->email,
               'telephone' => $input->telephone,
               'photo' => $filename,
               //'inscript_ver' => $input->montent ?: 0,
               //'date_inscription' => date('Y-m-d h:m:s'),
               'anneScol' => AnneeScolaire::years(),
            ]);
        $webPushChannel->send(AccountApproved::toWebPush([
            'title' => 'Inscription 👋!',
            'body' => "{$auth->prenom} {$auth->nom} vient d'inscrire  {$input->prenom} {$input->nom} à la classe de {$student->classe->titre_cat}"
        ]));
        $response->getBody()->write(json_encode($student, JSON_PRETTY_PRINT));
        $res = $response->withStatus(201);
        return $res->withHeader('Content-Type', 'application/json');
    }


    public function show($id): ResponseInterface
    {

        $this->unauthorized('view');
        $student = Instription::with('classe')
            ->with('campus')
            ->with('devoirs')
            ->with('composition')
            ->find($id);
        $period = Period::active();

        if($student)
            return view( "classroom.student", compact('student', 'period') );

        return view( "errors.404")
            ->withStatus(404);

    }


    public function update($response, RequestInput $input, $id){

        if(!is_object($this->permiss) || !$this->permiss->edit){
            $response->getBody()->write(json_encode(['message' => 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $student = Instription::find($id);
        $input->forget('csrf_value');
        $input->forget('csrf_name');
        $input->forget('campus_id');
        $input->forget('_METHOD');
        $form = $input->all();


        $rules = [
            //'campus_id' => 'exists:campus,id|required',
            'id_classe' => 'exists:cicle,id_cat|required',
            'matricule' => 'unique:instription,matricule|string,'.$student->id,
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'date_naissance' => 'required|string',
            'lieu_naissance' => 'required|string',
            'sexe' => 'required|'.Rule::in('F','M'),
            'adresse' => 'required|string',
            'email' => 'unique:instription,email,'. $student->id,
            'telephone' => 'required|string',
            'photo' => 'mimes:jpg,bmp,png'
        ];


          $messages = [
              'email.unique' => ':attribute existe déjà',
              'matricule.unique' => ':attribute existe déjà',
              'sexe.in' => 'Error genre'
         ];


        $validator = validator(
            $form,
            $rules,
            $messages
        );

        if ($validator->fails()) {
            $response->getBody()->write(json_encode($validator->errors(), JSON_PRETTY_PRINT));
            $res = $response->withStatus(422);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $student->update($input->all());
       // 'email' => 'unique:instription,email|email', $student->id,
        $response->getBody()->write(json_encode(['success'=> true], JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        return $res->withHeader('Content-Type', 'application/json');
    }


    public function edit($id): ResponseInterface
    {
        if(!is_object($this->permiss) || !$this->permiss->edit){
            return  view( 'errors.403')
                ->withStatus(403);
        }

        $student = Instription::with('classe')->find($id);
        $classroom = Cicle::listes();
        $campus = Campus::all();
        if($student)
            return view('classroom.student-edit', compact('student', 'classroom', 'campus'));

        return view( "errors.404")
            ->withStatus(404);
    }


    public function avatarUpdate($response, RequestInput $input){
        if($input->hasFile('photo')){

            Image::configure(array('driver' => 'imagick'));
            $filename = move(public_path('img/avartasEtudiant/'), $input->file('photo'));

            $thumb_path = 'img/avartasEtudiant/min_' . $filename;

            Image::make(public_path('img/avartasEtudiant/') . $filename)
                ->resize(160, 160)
                ->save(public_path($thumb_path));


            $old = public_path('img/avartasEtudiant/') . $input->oldPhoto;
            $thumb_path_old = public_path('img/avartasEtudiant/min_') . $input->oldPhoto;
            //remove file task
            if (file_exists($old)) {
                unlink($old);
                unlink($thumb_path_old);
            }

            Instription::whereId($input->id)->update(['photo' => $filename]);

            $response->getBody()->write(json_encode(['success' => true], JSON_PRETTY_PRINT));
            $res = $response->withStatus(201);
            return $res->withHeader('Content-Type', 'application/json');
        }


        $response->getBody()->write(json_encode(['success' => true], JSON_PRETTY_PRINT));
        $res = $response->withStatus(401);
        return $res->withHeader('Content-Type', 'application/json');
    }


    /**
     * @param $response
     * @param int $id
     * @return mixed
     */
    public function destroy($response, int $id){

        if(!is_object($this->permiss) || !$this->permiss->delete){
            $response->getBody()->write(json_encode(['message' => 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $student = Instription::find($id);

        Note::whereMatricule($student->matricule)->delete();
        $student->delete();
        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(204);
        return $res->withHeader('Content-Type', 'application/json');
    }
}