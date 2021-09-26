<?php

namespace App\Http\Controllers;

use App\Models\NoteBookText;
use App\Support\RequestInput;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;


class WorksController
{


    public function index()
    {
        return view('works.index');
    }

    public function show($classe)
    {
        $note_book_texts = NoteBookText::with('classe')
            //->whereCampusId($campus)
            ->where('classe_id', $classe)
            ->whereBetween('date_to_return', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->get();
        return view('components.works-table.tr', compact('note_book_texts'));
    }


    /**
     * @param $response
     * @param RequestInput $input
     * @return mixed
     */
    public function store($response, RequestInput $input)
    {

           //TODO store Works

        $rules = [
            'campus_id' => 'required',
            'classe_id' => 'required',
            'date_to_return' => 'required|date', //2021-7-3 8:0:0
            'type' => 'required|in:sitting,work,control',
            'matiere_id' => 'required',
            'intutiler' => 'required|string',
            'charge' => 'required|string',
            'document-file' => 'mimes:jpg,bmp,png,pdf,zip,docx'
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

        $input->forget('csrf_value');
        $input->forget('csrf_name');


        $folder =  sha1(time());
        $base_path = storage_path("app/public/") . $folder;
        $files = app()->resolve(Filesystem::class);


        if (!$files->exists($base_path)) {
            $files->makeDirectory($base_path, 0777, true);
        }

        if($input->content){
            $path = $base_path . '/index.html';
            $status = $files->put($path, $input->content);
            $input->content = $folder;
        }


        if(
            $input->hasFile("document-file") &&
            !empty($input->file('document-file')->getClientFilename())
        ){
            $name = str::slug($input->intutiler);

            try {
                $input->fichier = move($base_path, $input->file('document-file'), $name);
            } catch (\Exception $e) {
                $response->getBody()->write(json_encode(['message'=>'internal server error!!!!'], JSON_PRETTY_PRINT));
                $res = $response->withStatus(500);
                return $res->withHeader('Content-Type', 'application/json');
            }
            //$files->put($base_path, file_get_contents($input->file('document-file')));
        }
            //$filename
            //$base_path
            //$input->date_to_return = date("Y-m-d H:i:s", strtotime($input->date_to_return));
            $input->forget('id');
            $note_book_text = NoteBookText::create($input->all());
           $response->getBody()->write(json_encode($note_book_text, JSON_PRETTY_PRINT));
           $res = $response->withStatus(200);
           return $res->withHeader('Content-Type', 'application/json');
    }




    public function update($response, RequestInput $input, $id)
    {
        $input->forget('csrf_value');
        $input->forget('csrf_name');
        $notebook = NoteBookText::find($id);
        $files = app()->resolve(Filesystem::class);

        $old_path = storage_path("app/public/") . $notebook->content;


        if(
            $input->hasFile("document-file") &&
            !empty($input->file('document-file')->getClientFilename())
        ){
            $name = str::slug($input->intutiler);

            try {
                $files->delete($old_path . DIRECTORY_SEPARATOR . $notebook->fichier);
                $input->fichier = move($old_path, $input->file('document-file'), $name);
            } catch (\Exception $e) {
                $response->getBody()->write(json_encode(['message'=>'internal server error!!!!'], JSON_PRETTY_PRINT));
                $res = $response->withStatus(500);
                return $res->withHeader('Content-Type', 'application/json');
            }
            //$files->put($base_path, file_get_contents($input->file('document-file')));
        }

        $path = $old_path . '/index.html';
        $files->put($path, $input->content);
        $input->forget('content');

        $notebook->update($input->all());

        $response->getBody()->write(json_encode($notebook, JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        return $res->withHeader('Content-Type', 'application/json');
    }



    public function destroy($response, $id)
    {
        $notebook = NoteBookText::find($id);
        $files = app()->resolve(Filesystem::class);
        $path = storage_path("app/public/") . $notebook->content;
        $files->deleteDirectory($path);
        $notebook->delete();
        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(204);
        return $res->withHeader('Content-Type', 'application/json');
    }
}
