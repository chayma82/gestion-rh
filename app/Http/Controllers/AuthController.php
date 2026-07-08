<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function authi()
{
    return view('auth.Auth');
}
 public function create()
{
    return view('auth.ajoutentreprise');
}
public function success()
{
    return view('auth.successajout');
}

public function login(Request $request)
{
    return redirect()->route('Dashboard.index');
}
public function logout()
{
    return redirect()->route('auth.authi');
}


}
