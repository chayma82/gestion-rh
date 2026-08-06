<?php
namespace App\Http\Controllers;

use App\Models\Employe;

class CongeperController extends Controller
{
    public function index(Employe $employe)
    {
        $conges = $employe->conges()->latest('date_debut')->get();

        return view('employes.conge.listeconge', compact('employe', 'conges'));
    }
}
