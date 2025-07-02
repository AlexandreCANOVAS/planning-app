<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComptabiliteController;
use App\Http\Controllers\Api\ComptabiliteController as ApiComptabiliteController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Routes pour les notifications
    Route::get('/notifications', 'App\Http\Controllers\Api\NotificationController@index');
    
    // Routes pour les employés
    Route::get('/employe/soldes-conges', [App\Http\Controllers\Api\EmployeController::class, 'getSoldesConges']);
});

Route::get('/comptabilite/calcul', [ApiComptabiliteController::class, 'calcul']);

// Route pour calculer les heures d'un employé pour un mois spécifique
Route::get('/calculer-heures/{employe_id}/{mois}/{annee}', [ComptabiliteController::class, 'calculerHeures']);
