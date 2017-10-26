<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use App\Http\Requests\updatePassUser;
use App\Http\Requests\StoreNewAdmin;
use Laracasts\Flash\Flash;
use App\Admin;
use App\User;
use App\User_info;
use App\Ensemble;
use App\Tag;
use App\Instrument;
use App\Style;
use App\GeneralAsk;
use App\Ask;
use Mail;
use Hash;
use Auth;
use Carbon\Carbon;
use Validator;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $me = Admin::where('id', \Auth::user()->id)->first();
        $admins = Admin::orderBy('id', 'ASC')->paginate(5);
        $tags = Tag::orderBy('id', 'DES')->get();
        $instruments = Instrument::orderBy('id', 'DES')->get();
        $styles = Style::orderBy('id', 'DES')->get();
        $number_of_members = User::all()->count();
        $general_asks_count = GeneralAsk::where('read', 0)->count();

        return view('admin.dashboard')
                        ->with('admins', $admins)
                        ->with('tags', $tags)
                        ->with('instruments', $instruments)
                        ->with('styles', $styles)
                        ->with('number_of_members', $number_of_members)
                        ->with('asks_count', $general_asks_count)
                        ->with('me', $me);
    }

    public function create()
    {
        if (\Auth::user()->permission == 'higher') {
            return view('admin.register');
        }else{
            return redirect()->route('admin.dashboard');
        }
    }

    public function updatePassAdmin(updatePassUser $request, $id)
    {
        $input = $request->all();
        $user = Admin::find($id);
        if(!Hash::check($input['old_password'], $user->password)){
            return redirect()->back()->withErrors(['old_password'=>"That's not your current password, try again"]);
        }else{
            $user->update([
                'password'   => bcrypt($request->password)
            ]);
        }
        return redirect()->route('admin.dashboard');
    }

    public function change_email(Request $request)
    {
        $code = str_random(10);
        $token = $code.time();
        $encrypted = Crypt::encryptString($token);
        $user = Admin::where('id', Auth::user()->id);
        
        $user->update([
            'token' => $encrypted
        ]);

        $data = [ 
            'email' => $user->first()->email,
            'name'  => $user->first()->name,
            'token' => '/admin/update/email/'.$encrypted
        ];

        Mail::send('email.update_email', $data, function($message) use ($data){
            $message->from('support@bemusical.us');
            $message->to($data['email']);
            $message->subject("Change email");
        });

        return ['status' => 'OK'];
    }

    public function update_email($token)
    {
        try {
            $decrypted = Crypt::decryptString($token);
            $user = Admin::where('id', Auth::user()->id)->first();
        } catch (DecryptException $e) {
            return view('user.update_email')->with('time', 0)->with('status', 'ERROR');
        }
        if ($token == $user->token) {
            $array_token = str_split($decrypted, 10);
            $created_to_explode = date("n|j|Y|G|i|s|", $array_token[1]);
            $_e = explode('|', $created_to_explode);
            $timestamp_created = Carbon::create($_e[2],$_e[0],$_e[1],$_e[3],$_e[4],$_e[5]);

            $now = Carbon::now();
            $verify_now = Carbon::parse($now);
            $timestamp_difference = $timestamp_created->diffInMinutes($verify_now);

            return view('admin.update_email')->with('time', $timestamp_difference)->with('status', 'OK');
        } else {
            return view('admin.update_email')->with('time', 0)->with('status', 'ERROR');
        }
    }

    public function updating_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'   => 'email|required'
        ])->validate();

        Admin::where('id', Auth::user()->id)->update([
            'email'               => $request->email,
            'times_email_changed' => Admin::where('id', Auth::user()->id)->first()->times_email_changed+1,
            'token'               => null,
        ]); 

        return redirect()->route('admin.dashboard');     
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNewAdmin $request)
    {
        $admin = new Admin($request->all());
        $admin->name = $request->name;
        $admin->email = $request->email;
        if($request->permission == 1)
        {
            $admin->permission = 'higher';
        }
        else
        {
            $admin->permission = 'lower';
        }
        $admin->password = bcrypt($request->password);
        $admin->save();
        return redirect()->route('admin.dashboard');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admin = Admin::find($id);
        $admin->delete();
        return redirect()->route('admin.dashboard');
    }

    public function manage_user()
    {
        $users = User::orderBy('id', 'DES')->paginate(25);
        return view('admin.manage')->with('users', $users);
    }

    public function blockuser($id)
    {
        User::where('id', $id)
            ->update([
                'active' => 0
            ]);
        return redirect()->route('admin.manage_user');
    }

    public function unlockuser($id)
    {
        User::where('id', $id)
            ->update([
                'active' => 1
            ]);
        return redirect()->route('admin.manage_user');
    }

    public function nonvisible($id)
    {
        User::where('id', $id)
            ->update([
                'visible' => 0
            ]);
        return redirect()->route('admin.manage_user');
    }

    public function visible($id)
    {
        User::where('id', $id)
            ->update([
                'visible' => 1
            ]);
        return redirect()->route('admin.manage_user');
    }

    public function general_requests()
    {
        $users = User::select('id', 'email')->get();
        $emails = [];
        foreach ($users as $user) {
            array_push($emails, $user->email);
        }
        $general_asks = GeneralAsk::orderBy('id', 'desc')->get();

        return view('admin.general_requests')
                        ->with('general_asks', $general_asks)
                        ->with('emails', $emails);
    }

    public function display_map($address)
    {
        $get_data = explode('&', $address);
        $id_place = explode('id=', $get_data[0]);
        $lat_place = explode('lat=', $get_data[1]);
        $lng_place = explode('lng=', $get_data[2]);
        return view('admin.maps')
            ->with('id', $id_place[1])
            ->with('lat', $lat_place[1])
            ->with('lng', $lng_place[1]);
    }

    public function general_requests_update($id)
    {
        GeneralAsk::where('id', $id)
                  ->update([
                        'read' => 1
                    ]);
    }

    public function assign_user(Request $request)
    {
        $user = User::select('id', 'email')->where('email', $request->email)->first();
        $general_ask = GeneralAsk::where('id', $request->id_request)->firstOrFail();
        
        if (empty($user)) {
            Flash::error('This user does not exist in the database');
            return redirect()->back();
        }

        $ask              = new Ask();
        $ask->user_id     = $user->id;
        $ask->name        = $general_ask->name;
        $ask->email       = $general_ask->email;
        $ask->company     = $general_ask->company;
        $ask->phone       = $general_ask->phone;
        $ask->event_type  = $request->type;
        $ask->date        = $general_ask->date;
        $ask->address     = $general_ask->address;
        $ask->duration    = $general_ask->duration;
        $ask->token       = ' ';
        $ask->available   = 1;
        $ask->nonavailable= 0;
        $ask->read        = 0;
        $ask->created_at  = $general_ask->created_at;
        $ask->save();

        $date = explode('|', $ask->date);

        GeneralAsk::where('id', $request->id_request)
                  ->update([
                        'assined' => 1
                    ]);

        $data = [  
                    'name'     => $user->info->first_name,
                    'name_c'   => $ask->name,
                    'event'    => $ask->event_type,
                    'date'     => $date[1],
                    'duration' => $general_ask->duration
                ];

        Mail::send('email.request_from_admin', $data, function($message) use($user) {
            $message->from('support@bemusical.us');
            $message->to($user->email);
            $message->subject('Cogratulation you already accepted a new gig');
        });
        Flash::error('You already assigned the event to '.$user->info->first_name);
        return redirect()->route('admin.general.request');
    }
}
