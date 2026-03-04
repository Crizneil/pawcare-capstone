<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are loaded by RouteServiceProvider
| and assigned the "api" middleware group.
*/

Route::middleware('api')->group(function () {
    Route::get('/pets', [App\Http\Controllers\PetController::class, 'index']);
    Route::post('/pets', [App\Http\Controllers\PetController::class, 'store']);
    Route::put('/pets/{id}', [App\Http\Controllers\PetController::class, 'update']);
    Route::delete('/pets/{id}', [App\Http\Controllers\PetController::class, 'destroy']);
});
