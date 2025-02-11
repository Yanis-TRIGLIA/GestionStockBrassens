<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\PanierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


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


Route::get('/login', function () {
    return response()->json(['message' => 'Veuillez vous connecter'], 401);
})->name('login');



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/produits', [ProduitController::class, 'store']);
    Route::post('/produits/{id}', [ProduitController::class, 'update']);
    Route::post('/produits/{id}/delete', [ProduitController::class, 'destroy']);
});
Route::get('/produits', [ProduitController::class, 'index']);
Route::get('/produits/{id}', [ProduitController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/zones', [ZoneController::class, 'index']);
    Route::post('/zones', [ZoneController::class, 'store']);
    Route::get('/zones/{id}', [ZoneController::class, 'show']);
    Route::post('/zones/{id}', [ZoneController::class, 'update']);
    Route::post('/zones/{id}/delete', [ZoneController::class, 'destroy']);
});
Route::get('/categorie', [CategoriesController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/categorie', [CategoriesController::class, 'store']);
    Route::get('/categorie/{id}', [CategoriesController::class, 'show']);
    Route::post('/categorie/{id}', [CategoriesController::class, 'update']);
    Route::post('/categorie/{id}/delete', [CategoriesController::class, 'destroy']);
});

Route::get('/sorties', function () {
    return response()->json(
        \App\Models\Sortie::with(['produit.categories', 'produit', 'zone', 'personne'])->latest()->get()
    );
});



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/produits/{id}/retirer-stock', [StockController::class, 'retirerStock']); // Retirer du stock
    Route::post('/cancel_sortie/{id}/delete', [StockController::class, 'supprimerSortie']); // Supprimer sortie
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users/{id}', [UserController::class, 'update']);
    Route::post('/users/{id}/delete', [UserController::class, 'destroy']);
});

Route::get('/admin/stats', [AdminController::class, 'stats']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/panier', [PanierController::class, 'show']); // Voir le panier
    Route::post('/panier/ajouter', [PanierController::class, 'addProduct']); // Ajouter un produit
    Route::post('/panier/supprimer/{produitId}', [PanierController::class, 'removeProduct']);
    Route::put('/panier/update/{produitId}', [PanierController::class, 'updateProductQuantity']);
    Route::post('/panier/vider', [PanierController::class, 'clear']); // Vider le panier
});
