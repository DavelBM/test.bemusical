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
        $info_client = Client_info::where('client_id', $user->id)->firstOrFail();
        
        return view('client.dashboard')
            ->with('client', $client)
            ->with('info', $info_client);
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

        $id_ask_array = [];
        $payments_array = [];
        $general_ask = GeneralAsk::where('email', '=', $request->email);
        $ask = Ask::where('email', '=', $request->email);

        if ($ask->exists()) {
            foreach ($ask->get() as $_ask) {
                array_push($id_ask_array, $_ask->id);
            }
            for ($i=0; $i < count($id_ask_array); $i++) { 
                $object_payment = Payment::where('request_id', $id_ask_array[$i])->first();
                if($object_payment != null){
                    array_push($payments_array, $object_payment);
                }
            }

            $payments = array_filter($payments_array);
            $array_payment_somet = [];
            for ($x=0; $x < count($payments); $x++) { 
                if ($payments[$x]['_id_costumer'] != null) {
                    array_push($array_payment_somet, $payments[$x]['id']);
                }
            }

             
            if (count($array_payment_somet) == 0) {
                $client = Client::create([
                    'email'    => $ask->first()->email,
                    'password' => Hash::make($request->password),
                ]);

                $client_info = Client_info::create([
                    'client_id' => $client->id,
                    'name' => $ask->first()->name,
                    'address' => 'no-address',
                    'company' => $ask->first()->company,
                    'phone' => 0,
                    'zip' => 'no-zip-code'
                ]);
            }else{
                for ($y=0; $y < count($array_payment_somet); $y++) {
                    $client = Client::create([
                        'email'    => $ask->first()->email,
                        'password' => Hash::make($request->password),
                    ]);

                    $client_info = Client_info::create([
                        'id_costumer' => Payment::where('id', $array_payment_somet[$y])->first()->_id_costumer,
                        'client_id' => $client->id,
                        'name' => $ask->first()->name,
                        'address' => 'no-address',
                        'company' => $ask->first()->company,
                        'phone' => 0,
                        'zip' => 'no-zip-code'
                    ]);
                    break;
                }   
            }

        }elseif($general_ask->exists()) {

            $client = Client::create([
                'email'    => $general_ask->first()->email,
                'password' => Hash::make($request->password),
            ]);

            $client_info = Client_info::create([
                'client_id' => $client->id,
                'name' => $general_ask->first()->name,
                'address' => 'no-address',
                'company' => 'no-company',
                'phone' => 0,
                'zip' => 'no-zip-code'
            ]);

        }else{

            $client = Client::create([
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $client_info = Client_info::create([
                'client_id' => $client->id,
                'name' => 'no-name',
                'address' => 'no-address',
                'company' => 'no-company',
                'phone' => 0,
                'zip' => 'no-zip-code'
            ]);

        }

        if(Auth::guard('client')->attempt(['email' => $request->email, 'password' => $request->password]))
        {
            return redirect()->intended(route('client.dashboard'));
        }
        return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    public function update(Request $request)
    {
        return ['status' => 'OK'];
    }
}
