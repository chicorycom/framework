<?php


use Boot\Support\Route;

// api/example
Route::get('/', [\App\Http\Controllers\Api\UserController::class, 'index']);
