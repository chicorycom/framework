<?php


namespace App\Http\Controllers;


use App\Models\Menu;
use App\Models\Permission;
use App\Models\Profil;
use App\Support\Auth;
use App\Support\RequestInput;
use Psr\Http\Message\ResponseInterface;

class PermissionsController extends Controller
{

    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        if(!is_object($this->permiss) || !$this->permiss->view){
            return  view( 'errors.403')
                ->withStatus(403);
        }
        $profils = Profil::all();
        $menus = Menu::with('children')->whereSubMenu(null)->get();
        return view('pages.permission', compact('profils', 'menus'));
    }


    /**
     * @param $response
     * @param RequestInput $input
     * @return mixed
     */
    public function store($response, RequestInput $input){
       // $permission = Permission::whereMenuId($input->menu)->whereProfilId($input->role)->first();

        if(!is_object($this->permiss) || !$this->permiss->add || !$this->permiss->edit || !$this->permiss->delete){
            $response->getBody()->write(json_encode(['error'=>['message' => 'Unauthorized']], JSON_PRETTY_PRINT));
            $res = $response->withStatus(403);
            return $res->withHeader('Content-Type', 'application/json');
        }


        if(!isset($input->head) && $input->head == 'true'){
            $menus = Menu::whereSubMenu($input->menu)->get();
            if(!empty($menus)){
                 Permission::updateOrCreate(
                    ['menu_id' => $input->menu, 'profil_id' => $input->role],
                    [$input->name => $input->checked == 'true' ? 1 : 0]
                );
                foreach ($menus as $menu){
                    Permission::updateOrCreate(
                        ['menu_id' =>  $menu->id, 'profil_id' => $input->role],
                        [$input->name => $input->checked == 'true' ? 1 : 0]
                    );
                }
            }
        }else{

            Permission::updateOrCreate(
                ['menu_id' => $input->menu, 'profil_id' => $input->role],
                [$input->name => $input->checked == 'true' ? 1 : 0]
            );
        }
        $response->getBody()->write(json_encode([], JSON_PRETTY_PRINT));
        $res = $response->withStatus(200);
        return $res->withHeader('Content-Type', 'application/json');
    }
}