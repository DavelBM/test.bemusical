<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Client;

class ClientController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:client', ['except' => ['store', 'register']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('client.dashboard');
    }

    /*
    *REGISTER VIEW CLIENT
    */
    public function register()
    {
        return view('auth.register-client');
    }

    /*
    *STORE CLIENT
    */
    public function store(Request $request)
    {
        dd($request);
        //return view('auth.register-client');
    }
}
