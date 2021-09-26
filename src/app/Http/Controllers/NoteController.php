<?php


namespace App\Http\Controllers;


use App\WebPush\AccountApproved;
use App\WebPush\WebPushChannel;
use App\Models\AnneeScolaire;
use App\Models\Campus;
use App\Models\Cicle;
use App\Models\Instription;
use App\Models\Material;
use App\Models\Note;
use App\Models\Period;
use App\Models\RangMatiere;
use App\Support\Auth;
use App\Support\RequestInput;

class NoteController extends Controller
{

    /**
     * @param RequestInput $input
     * @param $response
     * @param int $campus
     * @param int $classe
     * @param WebPushChannel $webPushChannel
     * @return mixed
     * @throws \ErrorException
     */
    public function store(RequestInput $input, $response, int $campus, int $classe, WebPushChannel $webPushChannel){

        if(!is_object($this->permiss) || !$this->permiss->add){
            $response->getBody()->write(json_encode(['message'=> 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $input->merge(['campus' => $campus, 'classe' => $classe]);

        $matriculs = Instription::with('classe')->where('campus_id', $campus)
            ->where('id_classe', $classe)
            ->where('anneScol', AnneeScolaire::years())
            //->get(['matricule']);
            ->get();

        $validats = [];
        $messages = [];

        foreach ($matriculs as $matricul){
            $validats['devoir_'.$matricul->matricule . '_' . $classe] = 'required';
            $messages['devoir_'.$matricul->matricule . '_' . $classe . '.required'] = "{$matricul->nom} {$matricul->prenom} doit avoir une note";
        }
        $input->forget('productFilter_id_product');
        $input->forget('productFilter_b!name');
      // $validats = $matriculs->map(fn ($matricul, $index) => 'devoir_'.$matricul->matricule . $index . $classe);
        $rule = array_merge($validats,[
            'campus' => 'exists:campus,id|required',
            'classe' => 'exists:cicle,id_cat|required',
            'matierClasse' => 'required',
            //'periodeDevoir' => 'required',
            'typeDevoir' => 'required',
           //'dateDevoir' => 'required',
            'coefficientDevoir' => 'required|numeric|min:1|max:8',
            //'baremDevoir' => 'required',
        ]);

        $validator = validator(
            $input->all(),
            $rule,
            $messages
        );

        if ($validator->fails()) {
            $response->getBody()->write(json_encode($validator->errors(), JSON_PRETTY_PRINT));
            $res = $response->withStatus(422);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $active_periode = Period::active();
        $devoir = Note::notes()
            ->where('campus_id',$input->campus)
            ->where('classe', $input->classe)
            ->where('period', $active_periode->id)
            ->where('matiere', $input->matierClasse)
            ->where('type', $input->typeDevoir)
            ->get([
                'nbdevor',
                'classe',
                'period',
                'campus_id',
                'matiere',
                'type',
            ])->count();

        $nbrdevoir = $devoir == 0 ?  1 : $devoir + 1;
        \DB::beginTransaction();

        try {
            foreach ($matriculs as $student){
                $name = 'devoir_'.$student->matricule . '_' . $classe;
                Note::create([
                    'classe' => $input->classe,
                    'campus_id' => $input->campus,
                    'matricule' => $student->matricule,
                    'matiere' => $input->matierClasse,
                    'type' => $input->typeDevoir,
                    'notes' => $input->$name ?: 0.0,
                    'coesifient'=> $input->coefficientDevoir ?: 1,
                    'period' => $active_periode->id,
                    'bareme'=>$input->baremDevoir ?: 20,
                    'date' => $input->dateDevoir ?: date('d/m/Y'),
                    'nbdevor' => $nbrdevoir,
                    'annees' => AnneeScolaire::years()
                ]);
            }

            /*foreach ($matriculs as $student){
                $notes = $student->gradeNotes($input->periodeDevoir, $student->matricule);

                foreach ($notes as $note){
                    if($input->matierClasse == $note->matiere){
                        $mt = number_format((floatval($note->moyenneDS) + floatval($note->composition)) / 2, 2);
                        $mg = $mt * $note->coesifient;

                        RangMatiere::updateOrCreate(
                            [
                                'campus' => $input->campus,
                                'classe' => $input->classe,
                                'matiere' => $input->matierClasse,
                                'period' => $active_periode->id,
                                'annees' => AnneeScolaire::years(),
                                'matricule' => $student->matricule,
                            ],
                            [
                                'rang' => $student->rangMatiere($input->campus, $input->classe, $active_periode->id, $mg, $input->matierClasse)
                            ]
                        );
                        break;
                    }
                }
            }*/
            $this->update_rang($matriculs, $input, $active_periode->id);

            \DB::commit();
            // all good
       } catch (\Exception $e) {
            \DB::rollback();
            $response->getBody()->write(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
            $res = $response->withStatus($e->getCode());
            return $res->withHeader('Content-Type', 'application/json');
            // something went wrong
        }
        $auth = Auth::user();
        $matiere = Material::find($input->matierClasse);
        $type = $input->typeDevoir == 'DS' ? 'Devoir' : 'Composition';
        $webPushChannel->send(AccountApproved::toWebPush([
            'title' => 'Enregistrement 👋!',
            'body' => "{$auth->prenom} {$auth->nom} vient d'enregistré $type numero  {$nbrdevoir} {$matiere->matiere} de la classe de {$matriculs[0]->classe->titre_cat}"
        ])
        );
        $response->getBody()->write(json_encode(['success' => true], JSON_PRETTY_PRINT));
        $res = $response->withStatus(201);
        return $res->withHeader('Content-Type', 'application/json');
    }


    /**
     * @param $response
     * @param int $campus
     * @param int $classe
     * @param string $slug
     * @return mixed
     */
    public function edit($response, int $campus, int $classe, string $slug){

        if(!is_object($this->permiss) || !$this->permiss->edit){
            $response->getBody()->write(json_encode(['message'=> 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        [$nbdevor, $matiere, $period, $type] = explode("_", $slug);

        $query = Note::with('students')
            ->whereCampusId($campus)
            ->whereClasse($classe)
            ->whereNbdevor($nbdevor)
            ->whereMatiere($matiere)
            ->wherePeriod($period)
            ->whereType($type)
            ->whereAnnees(AnneeScolaire::years());

        $max_min = ['max' => $query->max('notes'), 'min'=>$query->min('notes'), 'moy' => number_format($query->avg('notes'), 2) ];
        $query->get();

        $response->getBody()->write(json_encode([$query->get(), $max_min], JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        return $res->withHeader('Content-Type', 'application/json');

    }


    /**
     * @param $response
     * @param RequestInput $input
     * @param int $id
     * @param WebPushChannel $webPushChannel
     * @return mixed
     * @throws \ErrorException
     */
    public function update($response, RequestInput $input, int $id, WebPushChannel $webPushChannel){

        if(!is_object($this->permiss) || !$this->permiss->edit){
            $response->getBody()->write(json_encode(['message'=> 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        if($input->value > 20){
            $response->getBody()->write(json_encode(['error'=>''], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $note = Note::with('students.classe')->find($id);

        $note->update(['notes' => $input->value]);
        $students = Instription::where('campus_id', $note->campus_id)
                         ->where('classe', $note->classe)
                         ->where('anneScol', AnneeScolaire::years());

        $input->matierClasse = $note->matiere;
        $input->campus = $note->campus_id;
        $input->classe = $note->classe;
        $this->update_rang($students, $input, $note->period);
        $auth = Auth::user();
        $type = $note->type == 'DS' ? 'Devoir' : 'Composition';
        $webPushChannel->send(AccountApproved::toWebPush([
            'title' => 'Modification 👋!',
            'body' => "{$auth->prenom} {$auth->nom} vient de modiffié le $type numero  {$note->nbdevor} {$note->matter->matiere} de {$note->students->prenom} {$note->students->nom} de la classe de {$note->students->classe->titre_cat}"
        ]));
        $response->getBody()->write(json_encode($input->all(), JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        return $res->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param $response
     * @param RequestInput $input
     * @return mixed
     */
    public function ratrapage($response, RequestInput $input){
        if(!is_object($this->permiss) || !$this->permiss->add){
            $response->getBody()->write(json_encode(['message'=> 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }
        $input->forget('csrf_value');
        $input->forget('csrf_name');
        $rule = [
            'campus_id' => 'exists:campus,id|required',
            'classe' => 'exists:cicle,id_cat|required',
            'nbdevor' => 'required',
            'matiere' => 'required',
            'period' => 'required',
            'type' => 'required',
            'coesifient' => 'required',
            'bareme' => 'required',
            'date' => 'required',
            'matricule' => 'exists:instription,matricule|required',
        ];

        $validator = validator(
            $input->all(),
            $rule,
        );

        if ($validator->fails()) {
            $response->getBody()->write(json_encode($validator->errors(), JSON_PRETTY_PRINT));
            $res = $response->withStatus(422);
            return $res->withHeader('Content-Type', 'application/json');
        }

        $input->notes = $input->notes ?: 0;
        $input->annees = AnneeScolaire::years();

        $note = Note::create($input->all());
        $students = Instription::where('campus_id', $note->campus_id)
            ->where('classe', $note->classe)
            ->where('anneScol', AnneeScolaire::years());

        $input->matierClasse = $note->matiere;
        $input->campus = $note->campus_id;
        $input->classe = $note->classe;
        $this->update_rang($students, $input, $note->period);

        $response->getBody()->write(json_encode(['success' => true], JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        return $res->withHeader('Content-Type', 'application/json');
    }


    /**
     * @param $students
     * @param $input
     * @param $period
     * @return bool
     */
    private function update_rang($students, $input, $period){
        foreach ($students as $student){
            $notes = $student->gradeNotes($period, $student->matricule);

            foreach ($notes as $note){
                if($input->matierClasse == $note->matiere){
                    $mt = number_format((floatval($note->moyenneDS) + floatval($note->composition)) / 2, 2);
                    $mg = $mt * $note->coesifient;

                    RangMatiere::updateOrCreate(
                        [
                            'campus' => $input->campus,
                            'classe' => $input->classe,
                            'matiere' => $input->matierClasse,
                            'period' => $period,
                            'annees' => AnneeScolaire::years(),
                            'matricule' => $student->matricule,
                        ],
                        [
                            'rang' => $student->rangMatiere($input->campus, $input->classe, $period, $mg, $input->matierClasse)
                        ]
                    );
                    break;
                }
            }
        }
        return true;
    }

    /**
     * @param $response
     * @param RequestInput $input
     * @param int $nbdevoir
     * @return mixed
     */
    public function destroy($response, RequestInput $input, int $nbdevoir){
        if(!is_object($this->permiss) || !$this->permiss->delete){
            $response->getBody()->write(json_encode(['message'=> 'Non autorisé'], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }
        Note::whereCampusId($input->campus)
            ->whereNbdevor($nbdevoir)
            ->whereClasse($input->classe)
            ->whereMatiere($input->matiere)
            ->wherePeriod($input->period)
            ->delete();

        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(204);
        return $res->withHeader('Content-Type', 'application/json');
    }
}