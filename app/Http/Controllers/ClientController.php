<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Client_info;
use App\GeneralAsk;
use App\Payment;
use App\Client;
use App\Ask;
use Cartalyst\Stripe\Stripe;
use Auth;
use Hash;
use Validator;
use Illuminate\Contracts\Encryption\DecryptException;

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
        $gigs_requests = Ask::orderBy('date', 'asc')->where('email', $client->email)->with('user')->get();
        $gigs_general_requests = GeneralAsk::orderBy('date', 'asc')->where('email', $client->email)->get();
        // $gigs_payed = Payment::orderBy('created_at', 'asc')->with('request')->where('email', $client->email)->get();
        $gigs_payed = Ask::orderBy('date', 'asc')->where('email', $client->email)->with('payment')->with('user')->get();

        $stripe = new Stripe('sk_test_e7FsM5lCe5UwmUEB4djNWmtz');

        return view('client.dashboard')
            ->with('client', $client)
            ->with('info', $info_client)
            ->with('requests', $gigs_requests)
            ->with('grequests', $gigs_general_requests)
            ->with('prequests', $gigs_payed)
            ->with('stripe', $stripe);
    }

    /*
    *REGISTER VIEW CLIENT
    */
    public function register()
    {
        // if( (Auth::guest('client'))  ){
        //     return redirect()->route('client.dashboard');
        // }else{
            return view('auth.register-client');
        // }
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
                $object_payment = Payment::where('ask_id', $id_ask_array[$i])->first();
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
        Validator::make($request->all(), [
            'first_name' => 'max:50|required',
            'last_name'  => 'max:50|required',
            'address'    => 'max:120|required',
            'zip'        => 'digits_between:4,6|required',
            'company'    => 'max:120|required',
            'phone'      => 'digits:10|required',
        ])->validate();

        Client_info::where('client_id', Auth::user()->id)
        ->update([
            'name' => $request->first_name.' '.$request->last_name,
            'address' => $request->address,
            'company' => $request->company,
            'phone' => $request->phone,
            'zip' => $request->zip
        ]);

        return redirect()->route('client.dashboard');
    }

    public static function encode($string) {
        $encrypted = Illuminate\Support\Facades\Crypt::encryptString($string);
        $data = base64_encode($encrypted);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    public static function decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        $decrypted = Illuminate\Support\Facades\Crypt::decryptString(base64_decode($data));
        return $decrypted;
    }
}
