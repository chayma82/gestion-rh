<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvanceController;
use App\Http\Controllers\AvanceperController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\CongeperController;
use App\Http\Controllers\CongesController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\FactureVenteController;
use App\Http\Controllers\FactureAchatController;
use App\Http\Controllers\PrimeController;
use App\Http\Controllers\PrimeperController;
use App\Http\Controllers\SalaireController;

//employes

Route::get('/employes', [EmployeController::class, 'index'])
    ->name('employes.index');

Route::get('/employes/create', [EmployeController::class, 'create'])
    ->name('employes.create');

Route::post('/employes', [EmployeController::class, 'store'])
    ->name('employes.store');

Route::get('/employes/{employe}/info', [EmployeController::class, 'info'])
    ->name('employes.info');

Route::get('/employes/{employe}/edit', [EmployeController::class, 'edit'])
    ->name('employes.edit');

Route::put('/employes/{employe}', [EmployeController::class, 'update'])
    ->name('employes.update');

Route::delete('/employes/{employe}', [EmployeController::class, 'destroy'])
    ->name('employes.destroy');
// archive et desarchive
Route::get('/employes/archives', [EmployeController::class, 'archives'])
    ->name('employes.archives');

Route::put('/employes/{employe}/desarchiver', [EmployeController::class, 'desarchiver'])
    ->name('employes.desarchiver');

//employe-conge
Route::get('/employes/{employe}/conges', [CongeperController::class, 'index'])
    ->name('employe.conge.index');

//employe-avance
Route::get('/employes/{employe}/avances', [AvanceperController::class, 'index'])
    ->name('employe.avance.index');

//employe-prime
Route::get('/employes/{employe}/primes', [PrimeperController::class, 'index'])
    ->name('employe.prime.index');

//factures

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

// Paiement individuel / en masse
Route::post('/salaires/{salaire}/payer', [SalaireController::class, 'payer'])
    ->name('employes.salaires.payer');
Route::post('/salaires/{salaire}/annuler', [SalaireController::class, 'annulerPaiement'])
    ->name('employes.salaires.annuler');
Route::post('/salaires/payer-tous', [SalaireController::class, 'payerTous'])
    ->name('employes.salaires.payerTous');

// Configuration du jour de paie
Route::post('/salaires/config', [SalaireController::class, 'updateConfig'])
    ->name('employes.salaires.config');

//avance
Route::get('/avances', [AvanceController::class, 'index'])
    ->name('employes.avances.index');
Route::get('/avances/create', [AvanceController::class, 'create'])
    ->name('employes.avances.create');
Route::post('/avances', [AvanceController::class, 'store'])
    ->name('employes.avances.store');

// prime
Route::get('/primes', [PrimeController::class, 'index'])
    ->name('employes.primes.index');
Route::get('/primes/create', [PrimeController::class, 'create'])
    ->name('employes.primes.create');
Route::post('/primes', [PrimeController::class, 'store'])
    ->name('employes.primes.store');

//contrats
Route::get('/contrats', [ContratController::class, 'index'])
    ->name('employes.contrats.index');
Route::get('/contrats/create', [ContratController::class, 'create'])
    ->name('employes.contrats.create');
Route::post('/contrats', [ContratController::class, 'store'])
    ->name('employes.contrats.store');
Route::get('/contrats/{contrat}/edit', [ContratController::class, 'edit'])
    ->name('employes.contrats.edit');
Route::put('/contrats/{contrat}', [ContratController::class, 'update'])
    ->name('employes.contrats.update');
Route::put('/contrats/{contrat}/resilier',[ContratController::class,'resilier'])
->name('employes.contrats.resilier');


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




// ================= CLIENTS =================
Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');

Route::get('/clients/{client}/archiver', [ClientController::class, 'archiver'])->name('clients.archiver');
Route::get('/clients/archives', [ClientController::class, 'archives'])->name('clients.archives');
Route::get('/clients/{client}/desarchiver', [ClientController::class, 'desarchiver'])->name('clients.desarchiver');

// ================= FOURNISSEURS =================
Route::get('/fournisseurs', [FournisseurController::class, 'index'])->name('fournisseurs.index');
Route::get('/fournisseurs/create', [FournisseurController::class, 'create'])->name('fournisseurs.create');
Route::post('/fournisseurs', [FournisseurController::class, 'store'])->name('fournisseurs.store');
Route::get('/fournisseurs/{fournisseur}/edit', [FournisseurController::class, 'edit'])->name('fournisseurs.edit');
Route::put('/fournisseurs/{fournisseur}', [FournisseurController::class, 'update'])->name('fournisseurs.update');

Route::patch('/fournisseurs/{fournisseur}/archiver', [FournisseurController::class, 'archiver'])->name('fournisseurs.archiver');
Route::get('/fournisseurs/archives', [FournisseurController::class, 'archives'])->name('fournisseurs.archives');
Route::patch('/fournisseurs/{fournisseur}/desarchiver', [FournisseurController::class, 'desarchiver'])->name('fournisseurs.desarchiver');




//ventes

Route::get('/factures/ventes', [FactureVenteController::class, 'index'])
    ->name('factures.ventes.index');

Route::get('/factures/ventes/archives', [FactureVenteController::class, 'archives'])
    ->name('factures.ventes.archives');

Route::get('/factures/ventes/create', [FactureVenteController::class, 'create'])
    ->name('factures.ventes.create');

Route::post('/factures/ventes', [FactureVenteController::class, 'store'])
    ->name('factures.ventes.store');

Route::get('/factures/ventes/{facture}', [FactureVenteController::class, 'info'])
    ->name('factures.ventes.info');

Route::get('/factures/ventes/{facture}/edit', [FactureVenteController::class, 'edit'])
    ->name('factures.ventes.edit');

Route::put('/factures/ventes/{facture}', [FactureVenteController::class, 'update'])
    ->name('factures.ventes.update');

Route::delete('/factures/ventes/{facture}', [FactureVenteController::class, 'destroy'])
    ->name('factures.ventes.destroy');

Route::patch('/factures/ventes/{facture}/desarchiver', [FactureVenteController::class, 'desarchiver'])
    ->name('factures.ventes.desarchiver');

Route::patch('/factures/ventes/{facture}/marquer-payee', [FactureVenteController::class, 'marquerPayee'])
    ->name('factures.ventes.marquerPayee');

Route::patch('/factures/ventes/{facture}/annuler-paiement', [FactureVenteController::class, 'annulerPaiement'])
    ->name('factures.ventes.annulerPaiement');

Route::post('/factures/ventes/payer-tout', [FactureVenteController::class, 'payerTout'])
    ->name('factures.ventes.payerTout');



//achats

Route::get('/factures/achats', [FactureAchatController::class, 'index'])
    ->name('factures.achats.index');

Route::get('/factures/achats/archives', [FactureAchatController::class, 'archives'])
    ->name('factures.achats.archives');

Route::get('/factures/achats/create', [FactureAchatController::class, 'create'])
    ->name('factures.achats.create');

Route::post('/factures/achats', [FactureAchatController::class, 'store'])
    ->name('factures.achats.store');

Route::get('/factures/achats/{facture}', [FactureAchatController::class, 'info'])
    ->name('factures.achats.info');

Route::get('/factures/achats/{facture}/edit', [FactureAchatController::class, 'edit'])
    ->name('factures.achats.edit');

Route::put('/factures/achats/{facture}', [FactureAchatController::class, 'update'])
    ->name('factures.achats.update');

Route::delete('/factures/achats/{facture}', [FactureAchatController::class, 'destroy'])
    ->name('factures.achats.destroy');

Route::patch('/factures/achats/{facture}/desarchiver', [FactureAchatController::class, 'desarchiver'])
    ->name('factures.achats.desarchiver');

Route::patch('/factures/achats/{facture}/marquer-payee', [FactureAchatController::class, 'marquerPayee'])
    ->name('factures.achats.marquerPayee');

Route::patch('/factures/achats/{facture}/annuler-paiement', [FactureAchatController::class, 'annulerPaiement'])
    ->name('factures.achats.annulerPaiement');

Route::post('/factures/achats/payer-tout', [FactureAchatController::class, 'payerTout'])
    ->name('factures.achats.payerTout');

Route::get('factures/ventes/{facture}/paiement', [FactureVenteController::class, 'paiement'])
    ->name('factures.ventes.paiement');
Route::post('factures/ventes/{facture}/paiement', [FactureVenteController::class, 'enregistrerPaiement'])
    ->name('factures.ventes.paiement.store');
Route::get('factures/ventes/quittance/{paiement}', [FactureVenteController::class, 'quittance'])
    ->name('factures.ventes.quittance');

Route::get('factures/ventes/quittance/{paiement}/telecharger', [FactureVenteController::class, 'quittancePdf'])
    ->name('factures.ventes.quittance.pdf');
Route::get('/factures/ventes/{facture}/pdf', [FactureVenteController::class, 'facturePdf'])
    ->name('factures.ventes.facture.pdf');
