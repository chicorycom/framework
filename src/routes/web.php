<?php


use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\RedirectIfAuthenticatedMiddleware as RedirectIfAuthenticated;
use Boot\Support\Route;
use Boot\Support\View;

Route::get('/hello/{name}', function ($request, $response, array $args) {
    $response->getBody()->write("Hello, " . $args['name']);
    return $response;
})->setName('hello');

Route::get('/', fn (View $view) => $view('welcome'))->setName('hello');
Route::get('/login', [LoginController::class, 'index'])->add(RedirectIfAuthenticated::class);
Route::post('/login', [LoginController::class, 'login'])->add(RedirectIfAuthenticated::class);

//Route::get('/reset-password/{token}', [ResetPasswordController::class, 'store'])->add(RedirectIfAuthenticated::class);
Route::post('/reset-password', [ResetPasswordController::class, 'store'])->add(RedirectIfAuthenticated::class);
Route::get('/reset-password/cancel/{key}', [ResetPasswordController::class, 'cancel'])->add(RedirectIfAuthenticated::class);
Route::get('/reset-password/{key}', [ResetPasswordController::class, 'show'])->add(RedirectIfAuthenticated::class);
Route::post('/reset-password/{key}', [ResetPasswordController::class, 'update'])->add(RedirectIfAuthenticated::class);


