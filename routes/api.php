<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ZoneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('/register', [AuthController::class, 'register'])->middleware('auth:sanctum'); 
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/produits', [ProduitController::class, 'index']);
    Route::post('/produits', [ProduitController::class, 'store']); 
    Route::get('/produits/{id}', [ProduitController::class, 'show']); 
    Route::put('/produits/{id}', [ProduitController::class, 'update']); 
    Route::delete('/produits/{id}', [ProduitController::class, 'destroy']); 
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/zones', [ZoneController::class, 'index']); 
    Route::post('/zones', [ZoneController::class, 'store']); 
    Route::get('/zones/{id}', [ZoneController::class, 'show']);
    Route::put('/zones/{id}', [ZoneController::class, 'update']); 
    Route::delete('/zones/{id}', [ZoneController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/produits/{id}/retirer-stock', [StockController::class, 'retirerStock']); // Retirer du stock
});