<?php
namespace App\Http\Controllers;

use App\Models\Employe;

use Illuminate\Http\Request;
class CongeController extends Controller
{
    public function index()
    {
        return view('employes.conge.listeconge');
    }
    public function create()
    {
        return view('employes.conge.createconge');
    }
    public function store(Request $request)
    {
        return redirect()->route('employes.conge.list');
    }
    }
