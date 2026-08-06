<?php
namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Http\Request;

class AvanceperController extends Controller
{
    public function index(Employe $employe)
    {
        $avances = $employe->avancesalaires()->latest('date_creation')->get();

        return view('employes.avance.listeavance', compact('employe', 'avances'));
    }
}
