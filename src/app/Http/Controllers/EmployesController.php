<?php


namespace App\Http\Controllers;


use App\Http\Requests\StoreEmployesRequest;
use App\Models\Admin;
use App\Models\Cicle;
use App\Models\Material;
use App\Models\Profil;
use App\Support\Auth;
use App\Support\RequestInput;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManagerStatic as Image;
use Psr\Http\Message\ResponseInterface;

class EmployesController extends Controller
{


        public function index(): ResponseInterface
        {

            if(!is_object($this->permiss) || !$this->permiss->view){
                return  view( 'errors.403')
                    ->withStatus(403);
            }
            $users = Admin::all();
            return view('pages.employees', compact('users'));
        }

    /**
     * @return ResponseInterface
     */
        public function create(): ResponseInterface
        {
            if(!is_object($this->permiss) || !$this->permiss->add){
                return  view( 'errors.403')
                    ->withStatus(403);
            }

            $profils = Profil::all();
            $cycles = Cicle::listes();
            $matieres = Material::all();

            return view('pages.employees-create', compact('profils', 'cycles', 'matieres'));
        }


        public function store($response, StoreEmployesRequest $input){
            if(!is_object($this->permiss) || !$this->permiss->add){
                $response->getBody()->write(json_encode(['message'=> 'Non autorisé'], JSON_PRETTY_PRINT));
                $res = $response->withStatus(403);
                return $res->withHeader('Content-Type', 'application/json');
            }

            if ($input->failed()) {
                $response->getBody()->write(json_encode($input->validator()->errors(), JSON_PRETTY_PRINT));
                $res = $response->withStatus(422);
                return $res->withHeader('Content-Type', 'application/json');
            }
            $input->image = 'default_avatar_male.jpg';

            if(isset($input->accessClasse) != null){

              //  dd(implode(',', $input->accessClasse));
                $input->accessClasse = implode(',', $input->accessClasse);
            }

            if($input->hasFile('image') && !empty($input->file('image')->getClientFilename())){


                Image::configure(['driver' => 'imagick']);
                $filename = move(public_path('img/avartasPersonnage/'), $input->file('image'));

                //$image_path = 'img/avartasPersonnage/' .$filename;

                $thumb_path = 'img/avartasPersonnage/min_'. $filename;


                Image::make(public_path('img/avartasPersonnage/') . $filename)
                    ->resize(160, 160)
                    ->save(public_path($thumb_path));

                $input->image = $filename;
            }
            $input->password = Auth::encrypt($input->password);
            $input->verif = $input->statue;
           $employ = Admin::create($input->all());

            $response->getBody()->write(json_encode($employ, JSON_PRETTY_PRINT));
            $res = $response->withStatus(201);
            return $res->withHeader('Content-Type', 'application/json');
        }


    /**
     * @param int $id
     * @return ResponseInterface
     */
        public function edit(int $id): ResponseInterface
        {

            $profils = Profil::all();
            $cycles = Cicle::listes();
            $matieres = Material::all();
            $user = Admin::find($id);
            return view('pages.employees-edit', compact('user', 'profils', 'cycles', 'matieres'));
        }


        /**
         * @param $id
         * @param $response
         * @param RequestInput $input
         * @return mixed
         */
        public function update($id, $response, RequestInput $input){

            if(!is_object($this->permiss) || !$this->permiss->edit){
                $response->getBody()->write(json_encode(['error' => ['message'=> 'Non autorisé']], JSON_PRETTY_PRINT));
                $res = $response->withStatus(403);
                return $res->withHeader('Content-Type', 'application/json');
            }

            $input->forget('csrf_value');
            $input->forget('csrf_name');

            $user = Admin::find($id);

            $rules = [
                'civil' => 'required|'.Rule::in('Mme','Mlle', 'Mr'),
                'prenom' => 'required|string',
                'nom' => 'required|string',
                'profil' => 'required',
                'adresse' => 'required|string',
                'email' => 'required|unique:admin,email,'. $user->id,
                'password' => 'min:6',
                'telephone' => 'required|string',
                'image' => 'mimes:jpg,bmp,png'
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

            $input->image = $user->image;
            //$input->accessClasse = $input->accessClasse ?? [];
            if($input->accessClasse){
                $input->accessClasse = implode(',', $input->accessClasse);
            }

            if($input->hasFile('image') && !empty($input->file('image')->getClientFilename())){

                if(file_exists(public_path("img/avartasPersonnage/{$user->image}"))){
                    unlink(public_path("img/avartasPersonnage/{$user->image}"));
                    unlink(public_path("img/avartasPersonnage/min_{$user->image}"));
                }

                Image::configure(['driver' => 'imagick']);
                $filename = move(public_path('img/avartasPersonnage/'), $input->file('image'));


                $thumb_path = 'img/avartasPersonnage/min_'. $filename;


                Image::make(public_path('img/avartasPersonnage/') . $filename)
                    ->resize(160, 160)
                    ->save(public_path($thumb_path));

                $input->image = $filename;
            }

            $input->password = $input->password ? $this->Crypte($input->password) : $user->password;
            $input->verif = $input->statue;
            $user->update($input->all());

            $response->getBody()->write(json_encode($user, JSON_PRETTY_PRINT));
            $res = $response->withStatus(201);
            return $res->withHeader('Content-Type', 'application/json');
        }

        public function destroy($id, $response){
            if(!is_object($this->permiss) || !$this->permiss->delete){
                $response->getBody()->write(json_encode(['error' => ['message'=> 'Non autorisé']], JSON_PRETTY_PRINT));
                $res = $response->withStatus(403);
                return $res->withHeader('Content-Type', 'application/json');
            }

            $user = Admin::find($id);
            $user->delete();
            $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
            $res = $response->withStatus(204);
            return $res->withHeader('Content-Type', 'application/json');
        }
}