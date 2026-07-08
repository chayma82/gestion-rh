<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeController extends Controller
{
    public function index()
{
    return view('employes.liste');
}

public function create()
{
    return view('employes.create');
}
public function edit()
{
    return view('employes.edit');
}
public function store(Request $request)
{
    return redirect()->route('employes.index');
}
public function info(Request $request)
{
 return view('employes.info');
}

}
