<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntityController;

Route::prefix('entity')->group(function () {
    Route::get('/ingredients', [EntityController::class, 'getIngredients']);
    Route::get('/dishes', [EntityController::class, 'getDishes']);
    Route::get('/mixes', [EntityController::class, 'getMixes']);
    Route::post('/', [EntityController::class, 'createEntity']);
    Route::delete('/{id}', [EntityController::class, 'deleteEntity']);
    Route::put('/{id}', [EntityController::class, 'updateEntity']);
    Route::get('/{id}', [EntityController::class, 'getEntityByID']);
});