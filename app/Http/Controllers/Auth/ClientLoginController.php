<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:client')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login-client');
    }

    public function login(Request $request)
    {
        //Validate the form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        //Attempt to log the user in
        if(Auth::guard('client')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember))
        {
            //if success, redirect to their intended location
            return redirect()->intended(route('client.dashboard'));
        }
        //if unsuccess, redirect back to he login with the form data
        return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    public function logout()
    {
        Auth::guard('client')->logout();

        return redirect('/');
    }
}
