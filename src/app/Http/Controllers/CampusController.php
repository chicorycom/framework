<?php


namespace App\Http\Controllers;


use App\Models\Campus;
use App\Models\Cicle;
use App\Models\Material;
use App\Models\Note;
use App\Models\NoteBookText;
use App\Models\Period;
use App\Support\RequestInput;
use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface;

class CampusController extends Controller
{

    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        try {
            $this->unauthorized('view');
            $campus = Campus::with('students')->get();
            return view( 'pages.campus', compact('campus'));

        }catch (\Exception $e){
            return  view( 'errors.403')
                ->withStatus(403);
        }
    }

    /**
     * @param $response
     * @param RequestInput $input
     */
    public function store($response, RequestInput $input){

    }


    /**
     * @param $id
     * @param $slug
     * @return ResponseInterface
     */
    public function view($id, $slug): ResponseInterface
    {
        try {
            $this->unauthorized('add');
            $classroom = Cicle::listes();
            $campus = Campus::find($id);

            return view( "classroom.{$slug}", compact('classroom', 'campus') );

        }catch (\Exception $e){
            return  view( 'errors.403')
                ->withStatus(403);
        }
    }


    /**
     * @param $campus_id
     * @param $classe
     * @return ResponseInterface
     */
    public function classroom($campus_id, $classe): ResponseInterface
    {
        //dd($this->permiss);
        if(!is_object($this->permiss) || !$this->permiss->add){
            return  view( 'errors.403')
                ->withStatus(403);
        }

       $campus = Campus::with(['students' => function($query) use ($classe) {
            $query->whereIdClasse($classe);
        }])->find($campus_id);

       $periods = Period::all();
       $matieres = Material::all();
       $active_periode = Period::active();


        $list_devoir =  Note::notes()
           ->where('campus_id',$campus_id)
           ->where('classe', $classe)
           ->where('period', $active_periode->id)
           ->get([
               'nbdevor',
               'date',
               'classe',
               'period',
               'campus_id',
               'matiere',
               'type',
               'coesifient'
           ]);

        $note_book_texts = NoteBookText::with('classe')->where('classe_id', $classe)
            ->whereBetween('date_to_return', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->get();

        return view( 'classroom.pages.home', compact('campus', 'classe', 'periods', 'matieres','active_periode','list_devoir', 'note_book_texts'));
    }
}