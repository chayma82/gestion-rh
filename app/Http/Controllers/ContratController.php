<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContratController extends Controller
{

    public function index()
{
    return view('employes.contrats.liste');
}
public function create()
{
    return view('employes.contrats.create');
}
public function store(Request $request)
{
    return redirect()->route('employes.contrats.index');
}
public function info(Request $request)
{
 return view('employes.contrats.info');
}
}
