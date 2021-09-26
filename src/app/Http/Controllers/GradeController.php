<?php


namespace App\Http\Controllers;


use App\Models\AnneeScolaire;
use App\Models\Average;
use App\Models\Cicle;
use App\Models\Instription;
use App\Models\Material;
use App\Models\Parametre;
use App\Models\ParamsGrade;
use App\Models\Period;
use Psr\Http\Message\ResponseInterface;

class GradeController
{
    private $campus;
    private $classe;
    private $studentsQuery;

    /**
     * @param $id
     * @param $period
     * @param $response
     */
    public function index($id, $period, $response)
    {


        $student = Instription::with('classe')->find($id);
        $img = public_path("img/avartasEtudiant/min_" . $student->photo);

        if($student->sexe === 'F'){
            $img = file_exists($img) ? "img/avartasEtudiant/min_" . $student->photo : 'img/default_avatar_female.png';
        }else{
            $img = file_exists($img) ? "img/avartasEtudiant/min_" . $student->photo : 'img/default_avatar_male.jpg';
        }


        $classe =  Cicle::with('gradeType')
                    ->whereIdCat($student->id_classe)
                    ->first();
        $paramsGrade = $classe->gradeType()->first();
        if($paramsGrade == null){
            return view('pages.grades-not-config');
        }
        $generalParams = Parametre::whereType(1)->first();
        $paramsGrade->logo = $generalParams->srcimg;

        $params = null;

        if($paramsGrade->type != 1){
            $params = "_2";
        }

        $annees = AnneeScolaire::years();
        $periode = Period::whereId($period)->first();
        if($periode->last){
            $periode->first = $student->moyennFirst($period);
        }


        $students = $this->students($student->campus_id,$student->id_classe);
        $notes = $student->gradeNotes($period, $student->matricule);


        calculeAll([$student->campus_id, $student->id_classe, $period, $students]);
        // calculeAll([$student->campus_id, $student->id_classe, $period, $students]);

        $numberGrade = $student->matricule.'-'.$student->id_classe . ' '.$period.'-' . AnneeScolaire::years();



        return view('pages.grade' . $params, [
            'param' => $paramsGrade,
            'annees' => $annees,
            'period' => $periode,
            'student' => $student,
            'notes' => $notes,
            'img' => $img,
            'students' => $students,
            'numberGrade' => $numberGrade
        ]);
    }


    /**
     * @param $campus
     * @param $classe
     * @param $period
     * @return ResponseInterface
     */
    public function grades($campus, $classe, $period){

        $students = $this->students($campus,$classe);



/*
        foreach ($students as $student){
            $tes = $student->notes->map(fn ($note) => $note->period == $period ?  $note : '' );
            $te = $tes->map(fn ($n) => Material::distinct()->whereIdMatiere($n->matiere)->get(['matiere']));
            foreach ($te as $t){
                $tes->map(function($nn) use($te) {
                   // dump($te);
                });
            }
            dd($te);
        }*/


        //dd($students->map(fn ($student) =>  =>  == $period ? $student : ''));

        $notes = calculeAll([$campus, $classe, $period, $students]);

        $class =  Cicle::with('gradeType')
            ->whereIdCat($classe)
            ->first();
        $paramsGrade = $class->gradeType()->first();
        if($paramsGrade == null){
            return view('pages.grades-not-config');
        }
        $generalParams = Parametre::whereType(1)->first();
        $paramsGrade->logo = $generalParams->srcimg;

        $params = null;

        if($paramsGrade->type != 1){
            $params = "_2";
        }

        $annees = AnneeScolaire::years();

        $periode = Period::whereId($period)->first();


        return view('pages.grades' . $params, [
            'students' => $students,
            'param' => $paramsGrade,
            'annees' => $annees,
            'period' => $periode,
        ]);

    }

    private function students($campus, $classe){

        return Instription::with('classe')
                ->with('notes')
                ->orderBy('nom', 'ASC')
                ->whereCampusId($campus)
                ->whereIdClasse($classe)
                ->where('anneScol', AnneeScolaire::years())
                ->get();
    }

}