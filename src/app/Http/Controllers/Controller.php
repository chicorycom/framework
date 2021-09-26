<?php


namespace App\Http\Controllers;


use App\Models\Menu;

class Controller
{

    public $permiss;

    public function __construct(){
        $url = explode('/', $_SERVER['REQUEST_URI']);
        $slug = Menu::where('slug', $url[1])->first() ?: $url[1];

        if(is_string($slug) && $slug == 'classroom' || $slug == 'devoir' || $slug == 'student' || $slug == 'register-student' || $slug == 'devoir-ratrapage'){
            $slug = Menu::where('slug', 'campus')->first() ?: $url[1];
        }

        if(is_object($slug)){
            $this->permiss = \Auth::user()->profils->permission()->where('menu_id', $slug->id)->first();
        }
    }

    /**
     * @param string $method
     * @return bool
     * @throws \Exception
     */
    protected function unauthorized(string $method): bool
    {
        if(!is_object($this->permiss) || !$this->permiss->$method){
            throw new \Exception('Unauthorized', 403);
        }
        return true;
    }

}