<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CongesController extends Controller
{

    public function index()
{
    return view('employes.conges.liste');
}
public function create()
    {
        return view('employes.conges.create');
    }
    public function store(Request $request)
    {
        return redirect()->route('employes.conges.list');
    }

}
