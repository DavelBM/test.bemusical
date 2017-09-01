<?php

namespace App\Http\Controllers\Auth;

use Mail;
use Illuminate\Support\Facades\Input;
use App\User;
use App\User_info;
use App\Ensemble;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // //Validator for letters and underscores
        // Validator::extend('without_spaces', function($attr, $value){
        //     //return preg_match('/^\S*$/u', $value);
        //     return preg_match('/^[a-zA-Z0-9-]+$/u', $value);
        // });

        if(end($data) == 'soloist'){
            return Validator::make($data, [
                'email'    => 'required|string|email|max:255|unique:users,email',
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50',
                'password' => 'required|string|min:6|confirmed',
            ]);
        }elseif(end($data) == 'ensemble'){
            return Validator::make($data, [
                'email'    => 'required|string|email|max:255|unique:users,email',
                'name'     => 'required|max:150|unique:ensembles,name',
                'password' => 'required|string|min:6|confirmed',
            ]);
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $confirmation_code = str_random(30);

        if ($data['type'] == 'soloist') {

            $name = $data['first_name'].' '.$data['last_name'];

            $user =  User::create([
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'token' => $confirmation_code,
                'type' => 'soloist',
            ]);

            $info = new User_info();
            $info->user_id         = $user->id;
            $info->slug            = $name;
            $info->first_name      = $data['first_name'];
            $info->last_name       = $data['last_name'];
            $info->about           = 'null';
            $info->profile_picture = 'null';
            $info->bio             = 'null';
            $info->address         = 'null';
            $info->phone           = 0;
            $info->location        = 'null';
            $info->degree          = 'null';
            $info->mile_radious    = 0;
            $info->save();

        }elseif ($data['type'] == 'ensemble') {

            $user =  User::create([
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'token' => $confirmation_code,
                'type' => 'ensemble',
            ]);

            $ensemble = new Ensemble();
            $ensemble->user_id         = $user->id;
            $ensemble->slug            = $data['name'];
            $ensemble->name            = $data['name'];
            $ensemble->manager_name    = 'null';
            $ensemble->type            = 'null';
            $ensemble->email           = $data['email'];
            $ensemble->profile_picture = 'null';
            $ensemble->summary         = 'null';
            $ensemble->about           = 'null';
            $ensemble->address         = 'null';
            $ensemble->phone           = 0;
            $ensemble->location        = 'null';
            $ensemble->mile_radious    = 0;
            $ensemble->save();

        }

        Mail::send('email.verify', ['token' => $user->token], function($message) {
            $message->to(Input::get('email'), Input::get('id'))
                ->subject('Verify your email address');
        });

        return $user;
    }
}
