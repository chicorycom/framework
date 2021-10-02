<?php

namespace App\Http\Controllers;

use Boot\Foundation\Auth\Access\AuthorizesRequests;
use Boot\Foundation\Bus\DispatchesJobs;
use Boot\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
