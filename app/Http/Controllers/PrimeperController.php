<?php
namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Http\Request;

class PrimeperController extends Controller
{
   public function index(Employe $employe)
    {
        $primes = $employe->primes()->latest('date_creation')->get();

        return view('employes.prime.liste', compact('employe', 'primes'));
    }
}
