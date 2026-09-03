<?php

namespace App\Http\Controllers;

use App\Models\Employe;

class ContratperController extends Controller
{
    public function index(Employe $employe)
    {
        $contrats = $employe->contrats()
            ->latest('date_debut')
            ->get();

        return view('employes.contrat.liste', compact('employe', 'contrats'));
    }
}
