<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

});

Route::prefix('entity')->group(function () {

    Route::prefix('dishes')->group(function () {
        Route::get('/', [EntityController::class, 'getDishes']);
        Route::get('/{dishID}', [EntityController::class, 'getDishByID']);
        Route::put('/{dishID}', [EntityController::class, 'updateDish']);
    });

    Route::prefix('ingredients')->group(function () {
        Route::get('/', [EntityController::class, 'getIngredients']);
        Route::put('/{ingredientID}', [EntityController::class, 'updateIngredient']);
    });

    Route::get('/mixes', [EntityController::class, 'getMixes']);
    Route::post('/', [EntityController::class, 'createEntity']);
    Route::delete('/{entityID}', [EntityController::class, 'deleteEntity']);
    Route::put('/{id}', [EntityController::class, 'updateEntity']);
    Route::get('/{id}', [EntityController::class, 'getEntityByID']);
});

Route::prefix('accounting')->group(function () {
    Route::get('/', [AccountingController::class, 'getAccountings']);
    Route::post('/', [AccountingController::class, 'createAccounting']);
    Route::delete('/{id}', [AccountingController::class, 'deleteAccounting']);
    Route::put('/{id}', [AccountingController::class, 'updateAccounting']);
    Route::get('/{id}', [AccountingController::class, 'getAccountingByID']);

    Route::prefix('{accountingID}/record')->group(function () {
        Route::get('/', [AccountingController::class, 'getAccountingRecords']);
        Route::post('/', [AccountingController::class, 'createAccountingRecord']);
        Route::delete('/{id}', [AccountingController::class, 'deleteAccountingRecord']);
        Route::put('/{id}', [AccountingController::class, 'updateAccountingRecord']);
        Route::get('/{id}', [AccountingController::class, 'getAccountingRecordByID']);
    });
});

Route::prefix('purchase')->group(function () {
    Route::get('/', [PurchaseController::class, 'getPurchases']);
    Route::post('/', [PurchaseController::class, 'createPurchase']);
    Route::delete('/{id}', [PurchaseController::class, 'deletePurchase']);
    Route::put('/{id}', [PurchaseController::class, 'updatePurchase']);
    Route::get('/{id}', [PurchaseController::class, 'getPurchaseByID']);

    Route::prefix('{purchaseID}/record')->group(function () {
        Route::get('/', [PurchaseController::class, 'getPurchaseRecords']);
        Route::post('/', [PurchaseController::class, 'createPurchaseRecord']);
        Route::delete('/{id}', [PurchaseController::class, 'deletePurchaseRecord']);
        Route::put('/{id}', [PurchaseController::class, 'updatePurchaseRecord']);
        Route::get('/{id}', [PurchaseController::class, 'getPurchaseRecordByID']);
    });
});

Route::prefix('stock')->group(function () {
    Route::get('/', [stockController::class, 'getStocks']);
    Route::post('/', [stockController::class, 'createStock']);
    Route::delete('/{id}', [stockController::class, 'deleteStock']);
    Route::put('/{id}', [stockController::class, 'updateStock']);
});