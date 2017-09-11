<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\newMemberRequest;
use App\Http\Requests\specificRequest;
use App\Http\Requests\generalRequest;
use Laracasts\Flash\Flash;
use Carbon\Carbon;
use App\Ensemble;
use App\User_info;
use App\User;
use App\Member;
use App\Ask;
use App\GeneralAsk;
use Auth;
use Mail;

class PublicController extends Controller
{
    public function view($slug)
    {

   		if (Ensemble::where('slug', '=', $slug)->exists()) {
   			$ensemble = Ensemble::where('slug', $slug)->firstOrFail();
   			
   			if (!$ensemble->user->visible) {
   				return view('admin.notReady');
   			}else{
   				if (!$ensemble->user->active) {
	   				return view('admin.blockedUser');
	   			}else{
	   				return view('ensemble.view')->with('ensemble', $ensemble);
	   			}
   			}

    	}elseif(User_info::where('slug', '=', $slug)->exists()){
    		$user = User_info::select('user_id')->where('slug', $slug)->firstOrFail();
            $info = User::where('id', $user->user_id)->firstOrFail();

    		if (!$info->visible) {
   				return view('admin.notReady');
   			}else{
	    		if (!$info->active) {
	   				return view('admin.blockedUser');
	   			}else{
	   				return view('user.view')->with('info', $info); 
	   			}  
	   		}

    	}else{

    		dd('no existe el slug');

    	}

    }

    public function review_for_slug($slug_r)
    {
        $slug = str_slug($slug_r, "-");
        if (Ensemble::where('slug', '=', $slug)->exists() or User_info::where('slug', '=', $slug)->exists()) {
            for ($i=1; $i; $i++) { 
                if (!Ensemble::where('slug', '=', $slug.'-'.$i)->exists() and !User_info::where('slug', '=', $slug.'-'.$i)->exists()) {
                    $slug = $slug.'-'.$i;
                    $flag = true;
                    break;
                }
            }
        }else{
            $slug = $slug;
            $flag = false;
        }
        return array($slug, $flag);
    }

    public function member_invitation($code)
    {
        if(Member::where('token', '=', $code)->where('confirmation', '=', 0)->exists()){
            $member = Member::select('id', 'slug')->where('token', '=', $code)->firstOrFail();
            if($member->slug == 'null'){
                return view('user.request_notmember')->with('id', $member->id);
            }else{
                return view('user.request_member')->with('id', $member->id);
            }
        }
        elseif (Member::where('token', '=', $code)->where('confirmation', '=', 1)->exists()) {
            //RETURN VIEW
            dd('This token was already used');
        }
        else{
            //RETURN VIEW
            dd('We have troubles looking for your request');
        }
        
    }

    public function add_instrument_to_member(Request $request)
    {
        Member::where('id', $request->id)
        ->update([
            'instrument'   => $request->instrument,
            'confirmation' => 1,
        ]);
        return redirect()->route('user.dashboard'); 
    }

    public function member_new(newMemberRequest $request)
    {
        $name = $request->first_name.' '.$request->last_name;
        $slug = str_slug($name, "-");

        $member = Member::select('email')->where('id', $request->id)->firstOrFail();

        $user = new User();
        $user->email      = $member->email;
        $user->password   = bcrypt($request->password);
        $user->confirmed  = 1;
        $user->active     = 1;
        $user->visible    = 0;
        $user->ask_review = 0;
        $user->token      = NULL;
        $user->type       = 'soloist';
        $user->save();

        if (Ensemble::where('slug', '=', $slug)->exists() or User_info::where('slug', '=', $slug)->exists()) 
        {
            for ($i=1; $i; $i++) { 
                if (!Ensemble::where('slug', '=', $slug.'-'.$i)->exists() and !User_info::where('slug', '=', $slug.'-'.$i)->exists()) {
                    $slug = $slug.'-'.$i;
                    break;
                }
            }
        }else{
            $slug = $slug;
        }

        $info = new User_info();
        $info->user_id         = $user->id;
        $info->slug            = $slug;
        $info->first_name      = $request->first_name;
        $info->last_name       = $request->last_name;
        $info->about           = 'null';
        $info->profile_picture = 'null';
        $info->bio             = 'null';
        $info->address         = 'null';
        $info->phone           = 0;
        $info->location        = 'null';
        $info->degree          = 'null';
        $info->mile_radious    = 0;
        $info->save();

        Member::where('id', $request->id)
        ->update([
            'user_id'      => $user->id,
            'name'         => $name,
            'slug'         => $slug,
            'instrument'   => $request->instrument,
            'confirmation' => 1,
        ]);

        if (Auth::attempt(['email' => $member->email, 'password' => $request->password])) {
            return redirect()->route('user.dashboard');
        }
    }

    public function specific_request(specificRequest $request)
    {   
        $user = User::where('id', $request->user_id)->firstOrFail();
        if (strpos($request->distance_google, ',')) {
            $exploded = explode(",", $request->distance_google);
            $gd = $exploded[0].$exploded[1];
            $distance = (int)$gd;
        }else{
            $distance = (int)$request->distance_google;
        }

        if($user->info->mile_radious <= $distance){
            Flash::error("This musician does not live or travel to that area (".$user->info->mile_radious." miles max. and you picked a place to ".$distance." miles of distance).");
            return redirect()->back();
        }

        $num_code = str_random(50);
        $token = $num_code.time();
        $request_time = explode(" ", $request->time);
        $date_timestamp = $request->day.' '.$request_time[0].':00';

        $year = Carbon::createFromFormat('Y-m-d H:i:s', $date_timestamp)->year;
        $month = Carbon::createFromFormat('Y-m-d H:i:s', $date_timestamp)->month;
        $day = Carbon::createFromFormat('Y-m-d H:i:s', $date_timestamp)->day;
        $hour = Carbon::createFromFormat('Y-m-d H:i:s', $date_timestamp)->hour;
        $minute = Carbon::createFromFormat('Y-m-d H:i:s', $date_timestamp)->minute;
        $dt = Carbon::create($year, $month, $day, $hour, $minute, 0);
        $date = $dt->toDayDateTimeString();

        $geometry = substr($request->place_geometry, 1, -1);
        $get_geometry_trimed = explode(", ", $geometry);
        $lat = $get_geometry_trimed[0];
        $lng = $get_geometry_trimed[1];

        $address = 'id:'.$request->place_id.'|address:'.$request->place_address.'|lat:'.$lat.'|long:'.$lng;

        $ask                 = new Ask();
        $ask->user_id        = $request->user_id;
        $ask->name           = $request->name;
        $ask->email          = $request->email;
        $ask->company        = $request->company;
        $ask->phone          = $request->phone;
        $ask->event_type     = $request->event_type;
        $ask->date           = $date_timestamp.'|'.$date;
        $ask->address        = $address;
        $ask->duration       = $request->duration;
        $ask->token          = $token;
        $ask->accepted_price = 0;
        $ask->available      = 0;
        $ask->nonavailable   = 0;
        $ask->read           = 0;
        $ask->save();

        if($user->type == "soloist") {
            $data = [ 
                    'token'    => $token, 
                    'email'    => $user->email, 
                    'name'     => $ask->name,
                    'email_c'  => $ask->email,
                    'company'  => $ask->company,
                    'phone_c'  => $ask->phone,
                    'address'  => $request->place_address,
                    'event'    => $ask->event_type,
                    'date'     => $date,
                    'distance' => $distance,
                    'duration' => $ask->duration,
                    'user_name'=> $user->info->first_name.' '.$user->info->last_name,
                ];
        } elseif($user->type == "ensemble") {
             $data = [ 
                    'token'    => $token, 
                    'email'    => $user->email, 
                    'name'     => $ask->name,
                    'email_c'  => $ask->email,
                    'company'  => $ask->company,
                    'phone_c'  => $ask->phone,
                    'address'  => $request->place_address,
                    'event'    => $ask->event_type,
                    'date'     => $date,
                    'distance' => $distance,
                    'duration' => $ask->duration,
                    'user_name'=> $user->ensemble->name,
                ];
        }

        Mail::send('email.request_specified', $data, function($message) use ($user){
            $message->from('support@bemusical.us');
            $message->to($user->email);
            $message->subject('Congratulations, you have a new request');
        });

        Mail::send('email.admin.request_specified', $data, function($message) use ($user){
            $message->from('support@bemusical.us');
            $message->to('david@bemusic.al');
            $message->subject('Somebody has a request for '.$user->email);
        });


        if($user->type == "soloist") {
            Flash::success('Thanks '.$request->name.', we already sent a message to '.$user->info->first_name.' asking for availability. You will hear soon about your request.');
            return redirect()->back();
        } elseif($user->type == "ensemble") {
             Flash::success('Thanks '.$request->name.', we already sent a message to '.$user->ensemble->name.' asking for availability. You will hear soon about your request.');
            return redirect()->back();
        }
    }

    public function price($token)
    {
        return view('user.price_input')->with('token', $token);
    }

    public function send_price(Request $request)
    {
        dd($request);
        // $available = substr($get_token, -1);
        // $token = substr($get_token, 0, -1);

        // $review = Ask::select('available', 'nonavailable')->where('token', $token)->firstOrFail();
        // if($review->available == 1 or $review->nonavailable == 1){
        //     Flash::error('This token already was used');
        //     return redirect()->route('login');
        // }else{
        //     if($available == 1){
        //         Ask::where('token', $token)
        //         ->update([
        //             'available'   => 1,
        //             'nonavailable'=> 0,
        //         ]);
        //         Flash::success('You accept the request, you can find all the info in your dashboard');
        //         return redirect()->route('login');
        //     }elseif ($available == 0) {
        //         Ask::where('token', $token)
        //         ->update([
        //             'available'   => 0,
        //             'nonavailable'=> 1,
        //         ]);
        //         Flash::warning('You did not accept the request, we will contact you to know what happend.');
        //         return redirect()->route('login');                
        //     }
        // }
    }

    public function asking_request($get_token)
    {
        ///////////SEND MAIL TO CLIENT THAT THE USER CANT ASSIST TO THE EVENT
        $available = substr($get_token, -1);
        $token = substr($get_token, 0, -1);

        $review = Ask::select('available', 'nonavailable')->where('token', $token)->firstOrFail();
        if($review->available == 1 or $review->nonavailable == 1){
            Flash::error('This token already was used');
            return redirect()->route('login');
        }else{
            if($available == 1){
                Ask::where('token', $token)
                ->update([
                    'available'   => 1,
                    'nonavailable'=> 0,
                ]);
                Flash::success('You accept the request, you can find all the info in your dashboard');
                return redirect()->route('login');
            }elseif ($available == 0) {
                Ask::where('token', $token)
                ->update([
                    'available'   => 0,
                    'nonavailable'=> 1,
                ]);
                Flash::warning('You did not accept the request, we will contact you to know what happend.');
                return redirect()->route('login');                
            }
        }
    }

    public function general_request(generalRequest $request)
    {   
        $date_timestamp = $request->day.' '.$request->time.':00';

        $year = Carbon::createFromFormat('Y-m-d H:i:s', $date_timestamp)->year;
        $month = Carbon::createFromFormat('Y-m-d H:i:s', $date_timestamp)->month;
        $day = Carbon::createFromFormat('Y-m-d H:i:s', $date_timestamp)->day;
        $hour = Carbon::createFromFormat('Y-m-d H:i:s', $date_timestamp)->hour;
        $minute = Carbon::createFromFormat('Y-m-d H:i:s', $date_timestamp)->minute;
        $dt = Carbon::create($year, $month, $day, $hour, $minute, 0);
        $date = $dt->toDayDateTimeString();

        $geometry = substr($request->place_geometry, 1, -1);
        $get_geometry_trimed = explode(", ", $geometry);
        $lat = $get_geometry_trimed[0];
        $lng = $get_geometry_trimed[1];

        $address = 'id:'.$request->place_id.'|address:'.$request->place_address.'|lat:'.$lat.'|long:'.$lng;
        
        if ($request->comment == null) {
            $comment = ' ';
        } else {
            $comment = $request->comment;
        }

        $general_ask           = new GeneralAsk();
        $general_ask->name     = $request->name;
        $general_ask->email    = $request->email;
        $general_ask->company  = $request->company;
        $general_ask->phone    = $request->phone;
        $general_ask->date     = $date_timestamp.'|'.$date;
        $general_ask->address  = $address;
        $general_ask->duration = $request->duration;
        $general_ask->comment  = $comment;
        $general_ask->type     = $request->type;
        $general_ask->read     = 0;
        $general_ask->assined  = 0;
        $general_ask->save();

        $data = [  
                    'name'     => $general_ask->name,
                    'email'    => $general_ask->email,
                    'company'  => $general_ask->company,
                    'phone'    => $general_ask->phone,
                    'address'  => $request->place_address,
                    'date'     => $date,
                    'duration' => $general_ask->duration,
                    'type'     => $general_ask->type,
                    'comment'  => $comment,
                ];

        Mail::send('email.admin.request_general', $data, function($message) {
            $message->from('support@bemusical.us');
            $message->to('david@bemusic.al');
            $message->subject('Somebody has a GENERAL request for a service');
        });


         Flash::success('Thanks '.$request->name.', we already sent a message to the admin page asking for availability. You will hear soon about your request.');
        return redirect()->back();

    }
}
