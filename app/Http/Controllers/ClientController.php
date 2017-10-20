<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client_info;
use App\GeneralAsk;
use App\Payment;
use App\Client;
use App\Ask;
use Auth;
use Hash;

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
        $user = Auth::user();
        $client = Client::where('id', $user->id)->firstOrFail();
        
        return view('client.dashboard')
            ->with('client', $client);
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
        $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:clients,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $general_ask = GeneralAsk::where('email', '=', $request->email);
        $ask = Ask::where('email', '=', $request->email);

        if ($ask->exists()) {

            foreach ($ask->get() as $value) {
                $info_payments = Payment::where('request_id', $ask->first()->id)->get();
                print_r($info_payments);
                foreach ($info_payments as $info_payment) {
                    if($info_payment->_id_costumer != null){
                        echo $info_payment->_id_costumer;
                        break;
                    }
                }

            }

            dd($info_payments);
            // $client = Client::create([
            //     'email'    => ,
            //     'password' => Hash::make($request->password),
            // ]);

            // $client_info = Client_info::create([
            //     'name'     => ,
            //     'company'  => ,
            //     'phone'    => 
            // ]);

        }elseif($general_ask->exists()) {

            dd($general_ask->get());
            // $client = Client::create([
            //     'email'    => ,
            //     'password' => Hash::make($request->password),
            // ]);

            // $client_info = Client_info::create([
            //     'name'     => ,
            //     'company'  => ,
            //     'phone'    => 
            // ]);

        }else{

            // $client = Client::create([
            //     'email'    => ,
            //     'password' => Hash::make($request->password),
            // ]); 

            // $client_info = Client_info::create([
            //     'name'     => ,
            //     'company'  => ,
            //     'phone'    => 
            // ]);

        }

        if(Auth::guard('client')->attempt(['email' => $request->email, 'password' => $request->password]))
        {
            return redirect()->intended(route('client.dashboard'));
        }
        return redirect()->back()->withInput($request->only('email', 'remember'));
    }
}
