<?php



// api/example
use Illuminate\Support\Facades\Route;


Route::get('/', [\App\Http\Controllers\Api\UserController::class, 'index']);
