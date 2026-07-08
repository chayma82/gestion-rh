<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\CongesController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\SalaireController;

//employes

Route::get('/employes', [EmployeController::class, 'index'])
    ->name('employes.index');

Route::get('/employes/create', [EmployeController::class, 'create'])
    ->name('employes.create');

Route::post('/employes', [EmployeController::class, 'store'])
    ->name('employes.store');

Route::get('/employes/info', [EmployeController::class, 'info'])
    ->name('employes.info');

Route::get('/employes/edit', [EmployeController::class, 'edit'])
    ->name('employes.edit');

//employe-conge
Route::get('/employes/conge', [CongeController::class, 'index'])
    ->name('employes.conge.index');

Route::get('/employes/conge/create', [CongeController::class, 'create'])
    ->name('employes.conge.create');

Route::post('/employes/conge', [CongeController::class, 'store'])
    ->name('employes.conge.store');

//factures
Route::get('/factures', [FactureController::class, 'index'])
    ->name('factures.index');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('Dashboard.index');

//conges
Route::get('/conges', [CongesController::class, 'index'])
    ->name('employes.conges.index');
Route::get('/employes/conges/create', [CongesController::class, 'create'])
    ->name('employes.conges.create');

Route::post('/employes/conges', [CongesController::class, 'store'])
    ->name('employes.conges.store');

//salaires
Route::get('/salaires', [SalaireController::class, 'index'])
    ->name('employes.salaires.index');
Route::get('/salaires/create', [SalaireController::class, 'create'])
    ->name('employes.salaires.create');
Route::post('/salaires', [SalaireController::class, 'store'])
    ->name('employes.salaires.store');
//contrats
Route::get('/contrats', [ContratController::class, 'index'])
    ->name('employes.contrats.index');
Route::get('/contrats/create', [ContratController::class, 'create'])
    ->name('employes.contrats.create');
Route::post('/contrats', [ContratController::class, 'store'])
    ->name('employes.contrats.store');
Route::get('/contrats/info', [ContratController::class, 'info'])
    ->name('employes.contrats.info');

// Authentification
Route::post('/login', [AuthController::class, 'login'])
    ->name('login');
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/auth', [AuthController::class, 'authi'])
    ->name('auth.authi');

Route::get('/auth/create', [AuthController::class, 'create'])
    ->name('auth.create');
Route::get('/auth/success', [AuthController::class, 'success'])
    ->name('auth.success');
