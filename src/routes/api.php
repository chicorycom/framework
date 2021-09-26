<?php

use App\Http\Controllers\SubcriptionController;
use Boot\Support\Route;

// api/example
Route::post('/push/subscriber', [SubcriptionController::class, 'register']);
