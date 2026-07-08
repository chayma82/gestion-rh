<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalaireController extends Controller
{
    public function index()
{
    return view('employes.salaires.liste');
}
public function create()
{
    return view('employes.salaires.create');
}
public function store(Request $request)
{
    return redirect()->route('employes.salaires.index');
}


}
