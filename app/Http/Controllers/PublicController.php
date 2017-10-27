<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;//Exceptions for failOrFail
use Cartalyst\Stripe\Exception\BadRequestException;//This exception will be thrown when the data sent through the request is mal formed.
use Cartalyst\Stripe\Exception\UnauthorizedException;//This exception will be thrown if your Stripe API Key is incorrect.
use Cartalyst\Stripe\Exception\InvalidRequestException;//This exception will be thrown whenever the request fails for some reason.
use Cartalyst\Stripe\Exception\NotFoundException;//This exception will be thrown whenever a request results on a 404.
use Cartalyst\Stripe\Exception\CardErrorException;//This exception will be thrown whenever the credit card is invalid.
use Cartalyst\Stripe\Exception\ServerErrorException;//This exception will be thrown whenever Stripe does something wrong.
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
use App\Gig;
use App\GigOption;
use App\Tag;
use App\Instrument;
use App\Style;
use App\Payment;
use Auth;
use Mail;
use URL;
use stdClass;
use App\Client;
use Cartalyst\Stripe\Stripe;
use App\Phone;

class PublicController extends Controller
{
    public function view($slug)
    {

   		if (Ensemble::where('slug', '=', $slug)->exists()) {
   			$ensemble = Ensemble::where('slug', $slug)->firstOrFail();
            $all = User_info::all();
            $option = GigOption::where('user_id', $ensemble->user_id)->first(); 

            $getting_dates = Gig::where('user_id', $ensemble->user_id)->where('allDay', 1)->select('start')->get();
            
            $dates = [];
            foreach ($getting_dates as $get_date) {
                $date_exploded = explode(' ', $get_date->start);
                array_push($dates, $date_exploded[0]);  
            }
            
   			if (!$ensemble->user->visible) {
   				return view('admin.notReady');
   			}else{
   				if (!$ensemble->user->active) {
	   				return view('admin.blockedUser');
	   			}else{
	   				return view('ensemble.view')
                        ->with('ensemble', $ensemble)
                        ->with('all', $all)
                        ->with('option', $option)
                        ->with('dates', $dates);
	   			}
   			}

    	}elseif(User_info::where('slug', '=', $slug)->exists()){
    		$user = User_info::select('user_id')->where('slug', $slug)->firstOrFail();
            $info = User::where('id', $user->user_id)->firstOrFail();
            $option = GigOption::where('user_id', $user->user_id)->first(); 

            $getting_dates = Gig::where('user_id', $user->user_id)->where('allDay', 1)->select('start')->get();
            
            $dates = [];
            foreach ($getting_dates as $get_date) {
                $date_exploded = explode(' ', $get_date->start);
                array_push($dates, $date_exploded[0]);   
            }

    		if (!$info->visible) {
   				return view('admin.notReady');
   			}else{
	    		if (!$info->active) {
	   				return view('admin.blockedUser');
	   			}else{
	   				return view('user.view')
                        ->with('info', $info)
                        ->with('option', $option)
                        ->with('dates', $dates); 
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
        $info->mile_radious    = 20;
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

        if ($user->type == 'soloist') {
            if($user->info->mile_radious <= $distance){
                Flash::error("This musician does not live or travel to that area (".$user->info->mile_radious." miles max. and you picked a place to ".$distance." miles of distance).");
                return redirect()->back();
            }   
        }
        elseif ($user->type == 'ensemble') {
            if($user->ensemble->mile_radious <= $distance){
                Flash::error("This ensemble does not live or travel to that area (".$user->ensemble->mile_radious." miles max. and you picked a place to ".$distance." miles of distance).");
                return redirect()->back();
            }   
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
        $date = $dt->format('F j, Y');
        $from_date = Carbon::parse($request_time[0].':00');
        $to_date = $dt->addMinutes($request->duration);
        $duration_event = $from_date->format('h:i A').' - '.$to_date->format('h:i A');

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
                    'duration' => $duration_event,
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

        $this->SendTextMessage('Bemusical: Somebody has a request', $request->user_id);

        if($user->type == "soloist") {
            Flash::success('Thanks '.$request->name.', we already sent a message to '.$user->info->first_name.' asking for availability. You will hear soon about your request.');
            return redirect()->back();
        } elseif($user->type == "ensemble") {
             Flash::success('Thanks '.$request->name.', we already sent a message to '.$user->ensemble->name.' asking for availability. You will hear soon about your request.');
            return redirect()->back();
        }
    }

    public function price($get_token)
    {
        $available = substr($get_token, -1);
        $token = substr($get_token, 0, -1);
        $ask = Ask::select('date', 'duration', 'name', 'available', 'nonavailable')->where('token', $token)->firstOrFail();

        if($ask->available == 1 or $ask->nonavailable == 1){
            Flash::error('This token already was used');
            return redirect()->route('login');
        }else{
            $exploded_date = explode('|', $ask->date);
            $date = explode(' ', $exploded_date[0]);
            $day = $date[0];
            $time = $date[1];

            $d = Carbon::parse($day)->format('F j, Y');
            $ft = Carbon::parse($time);
            $tt = Carbon::parse($time);
            $to_time = $tt->addMinutes($ask->duration);
            $duration_event = $ft->format('h:i A').' - '.$tt->format('h:i A');
            $time_hours = $ask->duration/60;

            return view('user.price_input')
                    ->with('name', $ask->name)
                    ->with('day', $d)
                    ->with('lenght', $duration_event)
                    ->with('token', $token)
                    ->with('time_hours', $time_hours);
        }
    }

    public function send_price(Request $request)
    {
        // $available = substr($request->token, -1);
        // $token = substr($request->token, 0, -1);
        $token = $request->token;

        $review = Ask::where('token', $token)->firstOrFail();
        $user = User::where('id', $review->user_id)->firstOrFail();

        if($user->type == "soloist") {
            $info = User_info::select('first_name', 'last_name')->where('user_id', $review->user_id)->firstOrFail();
        } elseif($user->type == "ensemble") {
             $ensemble = Ensemble::select('name')->where('user_id', $review->user_id)->firstOrFail();
        }
        
        if($review->available == 1 || $review->nonavailable == 1 || $review->price != null || $review->accepted_price == 1){
            Flash::error('This token already was used');
            return redirect()->route('login');
        }else{
            Ask::where('token', $token)
            ->update([
                'price'       => $request->price,
                'available'   => 1,
                'nonavailable'=> 0,
            ]);

            $dt = explode("|", $review->date);
            $address = explode("|", $review->address);
            $addrNAME = explode("address:", $address[1]);
            
            if($user->type == "soloist") {
                $data = [
                    'name'    => $review->name,
                    'name_use'=> $info->first_name.' '.$info->last_name,
                    'email'   => $review->email,
                    'phone'   => $review->phone,
                    'date'    => $dt[1],
                    'address' => $addrNAME[1],
                    'duration'=> $review->duration,
                    'price'   => $request->price,
                    'token'   => $review->token,
                ];
            } elseif($user->type == "ensemble") {
                 $data = [
                    'name'    => $review->name,
                    'name_use'=> $ensemble->name,
                    'email'   => $review->email,
                    'phone'   => $review->phone,
                    'date'    => $dt[1],
                    'address' => $addrNAME[1],
                    'duration'=> $review->duration,
                    'price'   => $request->price,
                    'token'   => $review->token,
                ];
            }

            Mail::send('email.request_send_price_client', $data, function($message) use ($review){
                $message->from('support@bemusical.us');
                $message->to($review->email);
                $message->subject("Hi, we have a price proposal for your event");
            });

            if($user->type == "soloist") {
                Mail::send('email.admin.request_send_price_client', $data, function($message) use ($review, $info){
                    $message->from('support@bemusical.us');
                    $message->to('david@bemusic.al');
                    $message->subject('Admin, '.$info->first_name.' '.$info->last_name.' is available and gives the price to '.$review->name);
                });
            } elseif($user->type == "ensemble") {
                Mail::send('email.admin.request_send_price_client', $data, function($message) use ($review, $ensemble){
                    $message->from('support@bemusical.us');
                    $message->to('david@bemusic.al');
                    $message->subject('Admin, '.$ensemble->name.' is available and gives the price to '.$review->name);
                }); 
            }

            Flash::success('You accept the request, you can find all the info in your dashboard');
            return redirect()->route('login');
        }
    }

    public function asking_request($get_token)
    {
        ///////////SEND MAIL TO CLIENT THAT THE USER CANT ASSIST TO THE EVENT
        $available = substr($get_token, -1);
        $token = substr($get_token, 0, -1);

        $ask = Ask::where('token', $token)->firstOrFail();

        if($ask->available == 1 or $ask->nonavailable == 1){
            Flash::error('This token already was used');
            return redirect()->route('login');
        }else{
            $user = User::where('id', $ask->user_id)->first();
            $users = User::where('visible', 1)->get();
            $datetimestamp = explode('|', $ask->date);
            $datetimestampexploded = explode(' ', $datetimestamp[0]);
            $date = $datetimestampexploded[0];
            $time = $datetimestampexploded[1];

            $addressDATA = explode('|', $ask->address);
            $idCLEAN = explode('id:', $addressDATA[0]);
            $addressCLEAN = explode('address:', $addressDATA[1]);
            $latCLEAN = explode('lat:', $addressDATA[2]);
            $lngCLEAN = explode('long:', $addressDATA[3]);

            $user_searching = new stdClass();        
            if ($user->type == 'soloist') {
                $user_searching->name = $ask->name;
                $user_searching->name_user = $user->info->first_name.' '.$user->info->last_name;
                $user_searching->styles = $user->user_styles->pluck('id')->toArray();
                $user_searching->tags = $user->user_tags->pluck('id')->toArray();
                $user_searching->instruments = $user->user_instruments->pluck('id')->toArray();
                $user_searching->type = $user->type;
                $user_searching->address_google = $addressCLEAN[1];
                $user_searching->id_google = $idCLEAN[1];
                $user_searching->lat_google = $latCLEAN[1];
                $user_searching->lng_google = $lngCLEAN[1];
                $user_searching->date = $date;
                $user_searching->time = $time;
                $user_searching->duration = $ask->duration;
                // $user_searching->recommendatios
            } elseif ($user->type == 'ensemble') {
                $user_searching->name = $ask->name;
                $user_searching->name_user = $user->ensemble->name;
                $user_searching->styles = $user->ensemble->ensemble_styles->pluck('id')->toArray();
                $user_searching->tags = $user->ensemble->ensemble_tags->pluck('id')->toArray();
                $user_searching->instruments = $user->ensemble->ensemble_instruments->pluck('id')->toArray();
                $user_searching->type = $user->type;
                $user_searching->address_google = $addressCLEAN[1];
                $user_searching->id_google = $idCLEAN[1];
                $user_searching->lat_google = $latCLEAN[1];
                $user_searching->lng_google = $lngCLEAN[1];
                $user_searching->date = $date;
                $user_searching->time = $time;
                $user_searching->duration = $ask->duration;
                // $user_searching->recommendatios
            }
        
            $dayname = (new Carbon($user_searching->date))->format('l');
            $availableUsers = [];
            $nonAvailableUsers = [];
            $finalAvailableUsersDistance = [];
            $finalAvailableUsersId = [];
            $finalAvailableUsersType = [];

            $place_id = $user_searching->id_google;
            $place_address = $user_searching->address_google;
            $lat_origin = $user_searching->lat_google;
            $lng_origin = $user_searching->lng_google;

            foreach ($users as $user_selected) {
                try {
                    $option = GigOption::select('monday','tuesday','wednesday','thursday','friday','saturday','sunday','start','end','time_before_event','time_after_event')->where('user_id', $user_selected->id)->firstOrFail();

                    $busyDays = Gig::select('start','allDay')->where('user_id', $user_selected->id)->where('allDay', 1)->get();

                    $busyHours = Gig::select('start','end')->where('user_id', $user_selected->id)->where('allDay', 0)->get();

                    if (count($busyDays) == 0) {
                        if ($user_selected->type == 'soloist') {
                            if ($user_selected->info->address != 'null') {
                                array_push($availableUsers, $user_selected->email);
                            }
                        } elseif ($user_selected->type == 'ensemble') {
                            if ($user_selected->ensemble->address != 'null') {
                                array_push($availableUsers, $user_selected->email);
                            }
                        }
                    }
                    
                    foreach ($busyDays as $busyDay) {             

                        $busyDay_notime = explode(' ', $busyDay->start);

                        if($busyDay_notime[0] == $user_searching->date){
                            array_push($nonAvailableUsers, $user_selected->email);
                        } else {
                            if($option->monday == 0 and $dayname == 'Monday'){
                                array_push($nonAvailableUsers, $user_selected->email);
                            }elseif($option->tuesday == 0 and $dayname == 'Tuesday'){
                                array_push($nonAvailableUsers, $user_selected->email);
                            }elseif($option->wednesday == 0 and $dayname == 'Wednesday'){
                                array_push($nonAvailableUsers, $user_selected->email);
                            }elseif($option->thursday == 0 and $dayname == 'Thursday'){
                                array_push($nonAvailableUsers, $user_selected->email);
                            }elseif($option->friday == 0 and $dayname == 'Friday'){
                                array_push($nonAvailableUsers, $user_selected->email);
                            }elseif($option->saturday == 0 and $dayname == 'Saturday'){
                                array_push($nonAvailableUsers, $user_selected->email);
                            }elseif($option->sunday == 0 and $dayname == 'Sunday'){
                                array_push($nonAvailableUsers, $user_selected->email);
                            }else{
                                array_push($availableUsers, $user_selected->email);
                            }
                        }
                    }
                } catch(ModelNotFoundException $e) {

                }
            }
            $availableUsersNoRepited = array_unique($availableUsers);
            $nonAvailableUsersNoRepited = array_unique($nonAvailableUsers);
            $usersAvailable = array_diff($availableUsersNoRepited, $nonAvailableUsersNoRepited);
            $destinations = '';
            $userDestinations = [];
            $mile_radious_from_user_array = [];
            foreach ($usersAvailable as $user_email) {
                $singer = User::where('email', $user_email)->first();

                if( $singer->type == 'ensemble' )
                {
                    $addres_from_user = explode('|', $singer->ensemble->address);
                    $lat_from_user = explode('lat:', $addres_from_user[2]);
                    $lng_from_user = explode('long:', $addres_from_user[3]);
                    $mile_radious_from_user = $singer->ensemble->mile_radious;
                    array_push($userDestinations, $singer->email);
                    array_push($mile_radious_from_user_array, $mile_radious_from_user);
                } 
                else if( $singer->type == 'soloist' )
                {
                    $addres_from_user = explode('|', $singer->info->address);
                    $lat_from_user = explode('lat:', $addres_from_user[2]);
                    $lng_from_user = explode('long:', $addres_from_user[3]);
                    $mile_radious_from_user = $singer->info->mile_radious;
                    array_push($userDestinations, $singer->email);
                    array_push($mile_radious_from_user_array, $mile_radious_from_user);
                }
                
                $destinations .= $lat_from_user[1].",".$lng_from_user[1]."|";
            }

            $origin = $lat_origin.",".$lng_origin;
            $destinations = trim($destinations, "|");
            $finalData = $this->GetDrivingDistance($origin, $destinations);

            for ($x=0; $x<count($userDestinations); $x++) {
                if ($finalData[$x]['distance'] == 'undefined') {
                    array_push($nonAvailableUsers, $userDestinations[$x]);
                }

                $distance_exploded = explode(' mi', $finalData[$x]['distance']);

                if (strpos($distance_exploded[0], ',')) {
                    $exploded_from_user = explode(",", $distance_exploded[0]);
                    $gd = $exploded_from_user[0].$exploded_from_user[1];
                    $distance_from_point_to_point = (int)$gd;
                }else{
                    $distance_from_point_to_point = (int)$distance_exploded[0];
                }

                if ($distance_from_point_to_point > $mile_radious_from_user_array[$x]) {
                    array_push($nonAvailableUsers, $userDestinations[$x]);
                } else {
                    array_push($availableUsers, $userDestinations[$x]);
                }
            }

            $availableUsersNoRepited = array_unique($availableUsers);
            $nonAvailableUsersNoRepited = array_unique($nonAvailableUsers);
            $usersAvailable = array_diff($availableUsersNoRepited, $nonAvailableUsersNoRepited);

            $tags = Tag::orderBy('name', 'DES')->select('id', 'name')->get();
            $instruments = Instrument::orderBy('name', 'DES')->select('id', 'name')->get();
            $styles = Style::orderBy('name', 'DES')->select('id', 'name')->get();

            $usersAvailable = array_values($usersAvailable);
            $lastusersAvailable = [];
            foreach ($usersAvailable as $userAvailableID) {
                $id_from_usersAvailable = User::select('id')->where('email', $userAvailableID)->first();
                array_push($lastusersAvailable, $id_from_usersAvailable->id);
            }

            if(($key = array_search($ask->user_id, $lastusersAvailable)) !== false) {
                unset($lastusersAvailable[$key]);
            }
            $lastusersAvailable = array_values($lastusersAvailable);
            shuffle($lastusersAvailable);

            $usersRecommendations = array_slice($lastusersAvailable, 0, 5, true);

            $user_searching->recommendatios = $usersRecommendations;

        
            $recomendation_name = [];
            $recomendation_slug = [];
            $recomendation_image = [];
            $recomendation_location = [];

            foreach ($usersRecommendations as $value) {
                $user_recomendation = User::where('id', $value)->first(); 
                if ($user_recomendation->type == 'soloist') {
                    array_push($recomendation_name, $user_recomendation->info->first_name.' '.$user_recomendation->info->last_name);
                    array_push($recomendation_slug, $user_recomendation->info->slug);
                    array_push($recomendation_image, $user_recomendation->info->profile_picture);
                    array_push($recomendation_location, $user_recomendation->info->location);
                } elseif ($user_recomendation->type == 'ensemble') {
                    array_push($recomendation_name, $user_recomendation->ensemble->name);
                    array_push($recomendation_slug, $user_recomendation->ensemble->slug);
                    array_push($recomendation_image, $user_recomendation->ensemble->profile_picture);
                    array_push($recomendation_location, $user_recomendation->ensemble->location);
                }
            }

            if (Client::where('email', $ask->email)->exists()) {
                $flag_client = 1;
            } else {
                $flag_client = 0;
            }
            
            $data = [ 
                'names'       => $recomendation_name,
                'slugs'       => $recomendation_slug,
                'images'      => $recomendation_image,
                'locations'   => $recomendation_location,
                'name_client' => $user_searching->name,
                'name_user'   => $user_searching->name_user,
                'type'        => $user_searching->type.'s',
                'date'        => (new Carbon($user_searching->date))->format('l jS \\of F Y'),
                'flag'        => $flag_client,

            ];

            Mail::send('email.nonavailable_recommendations', $data, function($message) use ($ask){
                $message->from('support@bemusical.us');
                $message->to($ask->email);
                $message->subject("we are sorry, but the user is not available");
            });
            
            Ask::where('token', $token)
            ->update([
                'available'   => 0,
                'nonavailable'=> 1,
            ]);
            Flash::success('You accept the request, you can find all the info in your dashboard');
            return redirect()->route('login');
        }
    }

    public function return_answer_price($token)
    {
        // Default values when client get here are 
        // accepted_price => 0,
        // nonavailable   => 0,
        // available      => 1,
        // comment        => null,
        $ask = Ask::where('token', $token)->firstOrFail();

        if($ask->available != 0 and $ask->nonavailable == 0 and $ask->price != null and $ask->accepted_price != 0){
            Flash::error('This token already was used');
            return redirect()->route('login');
        }elseif($ask->accepted_price == 1 and $ask->nonavailable == 1 and $ask->available == 0 and $ask->comment != null){
            Flash::error('This token already was used');
            return redirect()->route('login');
        }else{
            $exploded_date = explode('|', $ask->date);
            $date = explode(' ', $exploded_date[0]);
            $day = $date[0];
            $time = $date[1];

            $d = Carbon::parse($day)->format('F j, Y');
            $ft = Carbon::parse($time);
            $tt = Carbon::parse($time);
            $to_time = $tt->addMinutes($ask->duration);
            $duration_event = $ft->format('h:i A').' - '.$tt->format('h:i A');
            
            if ($ask->user->type == 'soloist') {
                $name_user = $ask->user->info->first_name.' '.$ask->user->info->last_name;
                $slug_user = $ask->user->info->slug;
            } elseif ($ask->user->type == 'ensemble') {
                $name_user = $ask->user->ensemble->name;
                $slug_user = $ask->user->ensemble->slug;
            }

            return view('user.confirmation_client')
                    ->with('p_key', 'pk_test_56MwMMhnoEpoqPocMMcXSZQH')
                    ->with('name_user', $name_user)
                    ->with('slug_user', $slug_user)
                    ->with('price', $ask->price)
                    ->with('name', $ask->name)
                    ->with('day', $d)
                    ->with('lenght', $duration_event)
                    ->with('id', $ask->id)
                    ->with('token', $token);
        }
    }

    public function return_answer_price_cash($token)
    {
        $ask = Ask::where('token', $token)->firstOrFail();

        if($ask->available != 0 and $ask->nonavailable == 0 and $ask->price != null and $ask->accepted_price != 0){
            Flash::error('This token already was used');
            return redirect()->route('login');
        }elseif($ask->accepted_price == 1 and $ask->nonavailable == 1 and $ask->available == 0 and $ask->comment != null){
            Flash::error('This token already was used');
            return redirect()->route('login');
        }else{
            $exploded_date = explode('|', $ask->date);
            $date = explode(' ', $exploded_date[0]);
            $day = $date[0];
            $time = $date[1];

            $d = Carbon::parse($day)->format('F j, Y');
            $ft = Carbon::parse($time);
            $tt = Carbon::parse($time);
            $to_time = $tt->addMinutes($ask->duration);
            $duration_event = $ft->format('h:i A').' - '.$tt->format('h:i A');
            
            if ($ask->user->type == 'soloist') {
                $name_user = $ask->user->info->first_name.' '.$ask->user->info->last_name;
                $slug_user = $ask->user->info->slug;
            } elseif ($ask->user->type == 'ensemble') {
                $name_user = $ask->user->ensemble->name;
                $slug_user = $ask->user->ensemble->slug;
            }

            return view('user.confirmation_client_cash')
                    ->with('p_key', 'pk_test_56MwMMhnoEpoqPocMMcXSZQH')
                    ->with('name_user', $name_user)
                    ->with('slug_user', $slug_user)
                    ->with('price', $ask->price)
                    ->with('name', $ask->name)
                    ->with('day', $d)
                    ->with('lenght', $duration_event)
                    ->with('id', $ask->id)
                    ->with('token', $token);
        }
    }

    public function return_reject(Request $request)
    {   
        // When client rejects a quote we are going to 
        // accepted_price => 1,
        // nonavailable   => 1,
        // available      => 0,
        // comment        => =!null,
        $ask = Ask::where('token', $request->token)->firstOrFail();
        $user = User::select('type', 'id', 'email')->where('id', $ask->user_id)->firstOrFail();

        $data = [
            'client' => $ask->name,
        ];

        if (($ask->available == 0 and $ask->nonavailable == 1 and $ask->comment != null and $ask->accepted_price == 1) or (($ask->available == 1 and $ask->nonavailable == 0 and $ask->comment == null and $ask->accepted_price == 1))) {

            if($user->type == 'soloist')
            {
                $info = User_info::select('slug')->where('user_id', $user->id)->firstOrFail();
            }
            elseif($user->type == 'ensemble')
            {
                $info = Ensemble::select('slug')->where('user_id', $user->id)->firstOrFail();
            }
            Flash::warning('This token was already used');
            return redirect()->route('index.public', $info->slug);
        } else {
            if($user->type == 'soloist')
            {
                $info = User_info::select('slug')->where('user_id', $user->id)->firstOrFail();
                Ask::where('token', $request->token)
                ->update([
                    'accepted_price' => 1,
                    'nonavailable'   => 1,
                    'available'      => 0,
                    'comment'        => $request->reject,
                ]);

                Mail::send('email.client_rejected', $data, function($message) use ($user){
                    $message->from('support@bemusical.us');
                    $message->to($user->email);
                    $message->subject("we are sorry, but the client rejected your quote");
                });

                Flash::error('You did no accept the price, and it was sent it to the user.');
                return redirect()->route('index.public', $info->slug);              
            }
            elseif ($user->type == 'ensemble') 
            {
                $info = Ensemble::select('slug')->where('user_id', $user->id)->firstOrFail();
                Ask::where('token', $request->token)
                ->update([
                    'accepted_price' => 1,
                    'nonavailable'   => 1,
                    'available'      => 0,
                    'comment'        => $request->reject,
                ]);

                Mail::send('email.client_rejected', $data, function($message) use ($user){
                    $message->from('support@bemusical.us');
                    $message->to($user->email);
                    $message->subject("we are sorry, but the client rejected your quote");
                });

                Flash::success('You did no accept the price, and it was sent it to the user.');
                return redirect()->route('index.public', $info->slug);              
            }
        }
    }

    public function return_confirmed(Request $request, $id)
    {
        //////////////STRIPE////////////////
        if ($request->_s_name != null) {

            $stripe = new Stripe('sk_test_e7FsM5lCe5UwmUEB4djNWmtz');
            $token = $request->stripeToken;
            $ask = Ask::where('id', $id)->firstOrFail();
            if (Client::where('email', $ask->email)->exists()) {
                $flag_client = 1;
            } else {
                $flag_client = 0;
            }
            
            if (strpos($ask->price, '.')) {
                $i_d_price = explode(".", $ask->price);
            }

            
            try{
                if ($request->_s_save == 'save') {
                    $customer = $stripe->customers()->create([
                        'description' => $ask->name,
                        'email' => $ask->email,
                    ]);

                    $card = $stripe->cards()->create($customer['id'], $token);

                    $charge = $stripe->charges()->create([
                        "customer" => $customer['id'],
                        "amount" => $i_d_price[0],
                        "currency" => "USD",
                        "description" => $ask->id.".Bemusical Gig",
                    ]);

                    $payment = Payment::create([
                        'ask_id'           => $ask->id,
                        'email'            => $ask->email,
                        'phone'            => $ask->phone,
                        '_billing_address' => $request->_s_address,
                        '_billing_zip'     => $card['address_zip'],
                        '_id_costumer'     => $customer['id'],
                        '_id_card'         => $card['id'],
                        '_id_token'        => $token,
                        '_id_charge'       => $charge['id'],
                        'amount'           => $i_d_price[0],
                        'payed'            => 1,
                        'type'             => 'stripe'
                    ]);
                }else{
                    $charge = $stripe->charges()->create([
                        "amount" => $i_d_price[0],
                        "currency" => "USD",
                        "description" => $ask->id.".Bemusical Gig",
                        "source" => $token,
                    ]);

                    $payment = Payment::create([
                        'ask_id'     => $ask->id,
                        'email'      => $ask->email,
                        '_id_charge' => $charge['id'],
                        'amount'     => $i_d_price[0],
                        'payed'      => 1,
                        'type'       => 'stripe'
                    ]);
                }
            }catch(ServerErrorException $e) {
                Flash::error('ERORR OCURRRED TRY AGAIN');
                return redirect()->back();
            }catch(BadRequestException $e) {
                Flash::error('ERORR OCURRRED TRY AGAIN');
                return redirect()->back();
            }catch(UnauthorizedException $e) {
                Flash::error('ERORR OCURRRED TRY AGAIN');
                return redirect()->back();
            }catch(InvalidRequestException $e) {
                Flash::error('ERORR OCURRRED TRY AGAIN');
                return redirect()->back();
            }catch(NotFoundException $e) {
                Flash::error('ERORR OCURRRED TRY AGAIN');
                return redirect()->back();
            }catch(CardErrorException $e) {
                Flash::error('ERORR OCURRRED TRY AGAIN');
                return redirect()->back();
            }

                // THIS IS GENERATED SINCE THE CLIENT INPUTS HIS/HER CARD
                // $token = $stripe->tokens()->create([
                //     'card' => [
                //         'number'    => '4242424242424242',
                //         'exp_month' => 10,
                //         'cvc'       => 314,
                //         'exp_year'  => 2020,
                //     ],
                // ]);

                // WE USE THIS WHEN ALREADY SAVED THE INFORMATION AT LEAST ONCE.
                // $charge = $stripe->charges()->create([
                //     'customer' => $customer['id'],
                //     'currency' => 'USD',
                //     'amount'   => 50.49,
                // ]);
            if($ask->user->type == 'soloist')
            {
                $info = User_info::select('slug')->where('user_id', $ask->user_id)->firstOrFail();
                $start_date = explode('|', $ask->date);
                $format_date =Carbon::parse($start_date[0]);
                $get_data_time = $format_date->addMinutes($ask->duration);
                $end_date = $get_data_time->toDateTimeString();

                $gig = new Gig();
                $gig->user_id    = $ask->user_id;
                $gig->request_id = $ask->id;
                $gig->title      = $ask->name.'-'.$ask->company;
                $gig->start      = $start_date[0];
                $gig->end        = $end_date;
                $gig->url        = URL::to('/details/request/'.$ask->id);
                $gig->save(); 

                $data = [ 
                    'id'      => $ask->user->id,
                    'u_email' => $ask->user->email,
                    'u_name'  => $ask->user->info->first_name.' '.$ask->user->info->last_name,
                    'c_email' => $ask->email,
                    'c_name'  => $ask->name,
                    'price'   => $ask->price,
                    'type'    => $payment->type,
                    'amount'  => $payment->amount,
                    'day'     => $start_date[1],
                    'flag'    => $flag_client,
                ];

                $this->SendMailApproved($data);

                Ask::where('id', $ask->id)->update(['accepted_price'   => 1]);

                Flash::success('We sent you an email with all the information that you need');
                return redirect()->route('index.public', $info->slug);
            }
            elseif($ask->user->type == 'ensemble') 
            {
                $info = Ensemble::select('slug')->where('user_id', $ask->user_id)->firstOrFail();
                $start_date = explode('|', $ask->date);
                $format_date = Carbon::parse($start_date[0]);
                $get_data_time = $format_date->addMinutes($ask->duration);
                $end_date = $get_data_time->toDateTimeString();

                $gig = new Gig();
                $gig->user_id    = $ask->user_id;
                $gig->request_id = $ask->id;
                $gig->title      = $ask->name.'-'.$ask->company;
                $gig->start      = $start_date[0];
                $gig->end        = $end_date;
                $gig->url        = URL::to('/details/request/'.$ask->id);
                $gig->save(); 

                $data = [ 
                    'id'      => $ask->user->id,
                    'u_email' => $ask->user->email,
                    'u_name'  => $ask->user->ensemble->name,
                    'c_email' => $ask->email,
                    'c_name'  => $ask->name,
                    'price'   => $ask->price,
                    'type'    => $payment->type,
                    'amount'  => $payment->amount,
                    'day'     => $start_date[1],
                    'flag'    => $flag_client,
                ];

                $this->SendMailApproved($data);

                Ask::where('id', $ask->id)->update(['accepted_price'   => 1]);

                Flash::success('We sent you an email with all the information that you need');
                return redirect()->route('index.public', $info->slug);
            }
        }
        //////////////CASH////////////////
        if ($request->_c_name != null) {
            $stripe = new Stripe('sk_test_e7FsM5lCe5UwmUEB4djNWmtz');
            $token = $request->_c_stripeToken;
            $ask = Ask::where('id', $id)->firstOrFail();
            if (Client::where('email', $ask->email)->exists()) {
                $flag_client = 1;
            } else {
                $flag_client = 0;
            }

            if (strpos($ask->price, '.')) {
                $i_d_price = explode(".", $ask->price);
            }

            
            try{
                if ($request->_c_save == 'save') {   
                    $customer = $stripe->customers()->create([
                        'description' => $ask->name,
                        'email' => $ask->email,
                    ]);

                    $card = $stripe->cards()->create($customer['id'], $token);

                    $charge = $stripe->charges()->create([
                        "customer" => $customer['id'],
                        "amount" => $i_d_price[0]*(0.12),
                        "currency" => "USD",
                        "description" => $ask->id.".Bemusical Gig. Cash payment (12% of ".$i_d_price[0].")",
                    ]);

                    $payment = Payment::create([
                        'ask_id'           => $ask->id,
                        'email'            => $ask->email,
                        'phone'            => $ask->phone,
                        '_billing_address' => $request->_c_address,
                        '_billing_zip'     => $card['address_zip'],
                        '_id_costumer'     => $customer['id'],
                        '_id_card'         => $card['id'],
                        '_id_token'        => $token,
                        '_id_charge'       => $charge['id'],
                        'amount'           => $i_d_price[0]*(0.12),
                        'payed'            => 1,
                        'type'             => 'cash'
                    ]);
                }else{
                    $charge = $stripe->charges()->create([
                        "amount" => $i_d_price[0]*(0.12),
                        "currency" => "USD",
                        "description" => $ask->id.".Bemusical Gig",
                        "source" => $token,
                    ]);

                    $payment = Payment::create([
                        'ask_id'     => $ask->id,
                        'email'      => $ask->email,
                        '_id_charge' => $charge['id'],
                        'amount'     => $i_d_price[0]*(0.12),
                        'payed'      => 1,
                        'type'       => 'cash'
                    ]);
                }
            }catch(ServerErrorException $e) {
                Flash::error('ERORR OCURRRED TRY AGAIN');
                return redirect()->back();
            }catch(BadRequestException $e) {
                Flash::error('ERORR OCURRRED TRY AGAIN');
                return redirect()->back();
            }catch(UnauthorizedException $e) {
                Flash::error('ERORR OCURRRED TRY AGAIN');
                return redirect()->back();
            }catch(InvalidRequestException $e) {
                Flash::error('ERORR OCURRRED TRY AGAIN');
                return redirect()->back();
            }catch(NotFoundException $e) {
                Flash::error('ERORR OCURRRED TRY AGAIN');
                return redirect()->back();
            }catch(CardErrorException $e) {
                Flash::error('ERORR OCURRRED TRY AGAIN');
                return redirect()->back();
            }

            if($ask->user->type == 'soloist')
            {
                $info = User_info::select('slug')->where('user_id', $ask->user_id)->firstOrFail();
                $start_date = explode('|', $ask->date);
                $format_date =Carbon::parse($start_date[0]);
                $get_data_time = $format_date->addMinutes($ask->duration);
                $end_date = $get_data_time->toDateTimeString();

                $gig = new Gig();
                $gig->user_id    = $ask->user_id;
                $gig->request_id = $ask->id;
                $gig->title      = $ask->name.'-'.$ask->company;
                $gig->start      = $start_date[0];
                $gig->end        = $end_date;
                $gig->url        = URL::to('/details/request/'.$ask->id);
                $gig->save(); 

                Ask::where('id', $ask->id)->update(['accepted_price'   => 1]);

                $data = [ 
                    'id'      => $ask->user->id,
                    'u_email' => $ask->user->email,
                    'u_name'  => $ask->user->info->first_name.' '.$ask->user->info->last_name,
                    'c_email' => $ask->email,
                    'c_name'  => $ask->name,
                    'price'   => $ask->price,
                    'type'    => $payment->type,
                    'amount'  => $payment->amount,
                    'day'     => $start_date[1],
                    'flag'    => $flag_client,
                ];

                $this->SendMailApproved($data);

                Flash::success('We sent you an email with all the information that you need');
                return redirect()->route('index.public', $info->slug);
            }
            elseif($ask->user->type == 'ensemble') 
            {
                $info = Ensemble::select('slug')->where('user_id', $ask->user_id)->firstOrFail();
                $start_date = explode('|', $ask->date);
                $format_date = Carbon::parse($start_date[0]);
                $get_data_time = $format_date->addMinutes($ask->duration);
                $end_date = $get_data_time->toDateTimeString();

                $gig = new Gig();
                $gig->user_id    = $ask->user_id;
                $gig->request_id = $ask->id;
                $gig->title      = $ask->name.'-'.$ask->company;
                $gig->start      = $start_date[0];
                $gig->end        = $end_date;
                $gig->url        = URL::to('/details/request/'.$ask->id);
                $gig->save(); 

                Ask::where('id', $ask->id)->update(['accepted_price'   => 1]);
                $data = [ 
                    'id'      => $ask->user->id,
                    'u_email' => $ask->user->email,
                    'u_name'  => $ask->user->ensemble->name,
                    'c_email' => $ask->email,
                    'c_name'  => $ask->name,
                    'price'   => $ask->price,
                    'type'    => $payment->type,
                    'amount'  => $payment->amount,
                    'day'     => $start_date[1],
                    'flag'    => $flag_client,
                ];

                $this->SendMailApproved($data);

                Flash::success('We sent you an email with all the information that you need');
                return redirect()->route('index.public', $info->slug);
            }
        }
        //////////////BANK TRANSFER////////////////
        if ($request->public_token != null && $request->account_ID != null) {
            $info = [];

            $headers[] = 'Content-Type: application/json';
            $params = array(
               'client_id' => '576eebcb0259902a3980f33a',
               'secret' => 'ddab6b25ee1c214666fcd7a6861c51',
               'public_token' => $request->public_token,
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://sandbox.plaid.com/item/public_token/exchange");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_TIMEOUT, 80);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            if(!$result = curl_exec($ch)) {
               trigger_error(curl_error($ch));
            }
            curl_close($ch);

            $jsonParsed = json_decode($result);

            $btok_params = array(
               'client_id' => '576eebcb0259902a3980f33a',
               'secret' => 'ddab6b25ee1c214666fcd7a6861c51',
               'access_token' => $jsonParsed->access_token,
               'account_id' => $request->account_ID,
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://sandbox.plaid.com/processor/stripe/bank_account_token/create");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($btok_params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_TIMEOUT, 80);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            if(!$result = curl_exec($ch)) {
               trigger_error(curl_error($ch));
            }
            curl_close($ch);

            $btok_parsed = json_decode($result);

            $stripe = new Stripe('sk_test_e7FsM5lCe5UwmUEB4djNWmtz');
            $payment_object = new stdClass();
            $ask = Ask::where('id', $id)->firstOrFail();
            if (Client::where('email', $ask->email)->exists()) {
                $flag_client = 1;
            } else {
                $flag_client = 0;
            }
            
            if (strpos($ask->price, '.')) {
                $i_d_price = explode(".", $ask->price);
            }

            try{
                $customer = $stripe->customers()->create([
                    "source"      => $btok_parsed->stripe_bank_account_token,
                    "email"       => $ask->email,
                    "description" => "Storing ".$ask->name." with bank account",
                ]);

                $charge = $stripe->charges()->create([
                    "customer" => $customer['id'],
                    "amount"   => $i_d_price[0],
                    "currency" => "USD",
                ]); 

                $payment = Payment::create([
                    'ask_id'           => $ask->id,
                    'email'            => $ask->email,
                    'phone'            => $ask->phone,
                    '_id_costumer'     => $customer['id'],
                    '_id_charge'       => $charge['id'],
                    'amount'           => $i_d_price[0],
                    'payed'            => 1,
                    'type'             => 'transfer'
                ]);

                if($ask->user->type == 'soloist')
                {
                    $info = User_info::select('slug')->where('user_id', $ask->user_id)->firstOrFail();
                    $start_date = explode('|', $ask->date);
                    $format_date =Carbon::parse($start_date[0]);
                    $get_data_time = $format_date->addMinutes($ask->duration);
                    $end_date = $get_data_time->toDateTimeString();

                    $gig = new Gig();
                    $gig->user_id    = $ask->user_id;
                    $gig->request_id = $ask->id;
                    $gig->title      = $ask->name.'-'.$ask->company;
                    $gig->start      = $start_date[0];
                    $gig->end        = $end_date;
                    $gig->url        = URL::to('/details/request/'.$ask->id);
                    $gig->save(); 

                    $data = [ 
                        'id'      => $ask->user->id,
                        'u_email' => $ask->user->email,
                        'u_name'  => $ask->user->info->first_name.' '.$ask->user->info->last_name,
                        'c_email' => $ask->email,
                        'c_name'  => $ask->name,
                        'price'   => $ask->price,
                        'type'    => $payment->type,
                        'amount'  => $payment->amount,
                        'day'     => $start_date[1],
                        'flag'    => $flag_client,
                    ];

                    $this->SendMailApproved($data);

                    Ask::where('id', $ask->id)->update(['accepted_price'   => 1]);

                    $payment_object->status ='OK';
                    $payment_object->message = "REDIRECTING---WE SEND YOU AN EMAIL WITH ALL THE INFORMATION---REDIRECTING";
                    $payment_object->slug = $info->slug;
                    $info[] = $payment_object;
                    return response()->json(array('info' => $info), 200);
                }
                elseif($ask->user->type == 'ensemble') 
                {
                    $info = Ensemble::select('slug')->where('user_id', $ask->user_id)->get();

                    $start_date = explode('|', $ask->date);
                    $format_date = Carbon::parse($start_date[0]);
                    $get_data_time = $format_date->addMinutes($ask->duration);
                    $end_date = $get_data_time->toDateTimeString();

                    $gig = new Gig();
                    $gig->user_id    = $ask->user_id;
                    $gig->request_id = $ask->id;
                    $gig->title      = $ask->name.'-'.$ask->company;
                    $gig->start      = $start_date[0];
                    $gig->end        = $end_date;
                    $gig->url        = URL::to('/details/request/'.$ask->id);
                    $gig->save(); 

                    $data = [ 
                        'id'      => $ask->user->id,
                        'u_email' => $ask->user->email,
                        'u_name'  => $ask->user->ensemble->name,
                        'c_email' => $ask->email,
                        'c_name'  => $ask->name,
                        'price'   => $ask->price,
                        'type'    => $payment->type,
                        'amount'  => $payment->amount,
                        'day'     => $start_date[1],
                        'flag'    => $flag_client,
                    ];

                    $this->SendMailApproved($data);

                    Ask::where('id', $ask->id)->update(['accepted_price'   => 1]);
                    
                    $payment_object->status ='OK';
                    $payment_object->message = "REDIRECTING---WE SEND YOU AN EMAIL WITH ALL THE INFORMATION---REDIRECTING";
                    $payment_object->slug = $info[0]->slug;
                    $info[] = $payment_object;
                    return response()->json(array('info' => $info), 200);
                }
            }catch(ModelNotFoundException $e) {
                $payment_object->status ='ERROR';
                $payment_object->message = 'ERORR OCURRRED TRY AGAIN';
                $info[] = $payment_object;
                return response()->json(array('info' => $info), 200);
            }
        }

        ///////////////ApplePay, Google payment///////////////
        if ($request->app_token != null) {
            $info = [];
            try{
                \Stripe\Stripe::setApiKey("sk_test_e7FsM5lCe5UwmUEB4djNWmtz");
                $token = $request->app_token.'1';
                $charge = \Stripe\Charge::create(array(
                  "amount" => 1000,
                  "currency" => "usd",
                  "description" => "Example charge",
                  "source" => $token,
                ));
                $payment_object = new stdClass();
                $payment_object->status ='OK';
                $info[] = $payment_object;
                return response()->json(array('info' => $info), 200);
            }catch(ServerErrorException $e) {
                $payment_object = new stdClass();
                $payment_object->status ='ERROR';
                $info[] = $payment_object;
                return response()->json(array('info' => $info), 200);
            }catch(BadRequestException $e) {
                $payment_object = new stdClass();
                $payment_object->status ='ERROR';
                $info[] = $payment_object;
                return response()->json(array('info' => $info), 200);
            }catch(UnauthorizedException $e) {
                $payment_object = new stdClass();
                $payment_object->status ='ERROR';
                $info[] = $payment_object;
                return response()->json(array('info' => $info), 200);
            }catch(InvalidRequestException $e) {
                $payment_object = new stdClass();
                $payment_object->status ='ERROR';
                $info[] = $payment_object;
                return response()->json(array('info' => $info), 200);
            }catch(NotFoundException $e) {
                $payment_object = new stdClass();
                $payment_object->status ='ERROR';
                $info[] = $payment_object;
                return response()->json(array('info' => $info), 200);
            }catch(CardErrorException $e) {
                $payment_object = new stdClass();
                $payment_object->status ='ERROR';
                $info[] = $payment_object;
                return response()->json(array('info' => $info), 200);
            }
        }

    }

    public function general_request(generalRequest $request)
    {   
        $time = $request->time;
        $date_timestamp = $request->day.' '.$time.':00';

        $users = User::where('visible', 1)->get();
        $date = (new Carbon($request->day))->format('l jS \\of F Y');
        $address = $request->place;
        $dayname = (new Carbon($request->day))->format('l');
        $availableUsers = [];
        $nonAvailableUsers = [];
        $finalAvailableUsersDistance = [];
        $finalAvailableUsersId = [];
        $finalAvailableUsersType = [];

        $place_id = $request->place_id;
        $place_address = $request->place_address;
        $place_geometry = $request->place_geometry;

        $lat_lng_origin = substr($request->place_geometry, 1, -1);
        $origin = explode(', ', $lat_lng_origin);
        $lat_origin = $origin[0];
        $lng_origin = $origin[1];

        $address = 'id:'.$request->place_id.'|address:'.$request->place_address.'|lat:'.$lat_origin.'|long:'.$lng_origin;
        
        if ($request->comment == null) {
            $comment = ' ';
        } else {
            $comment = $request->comment;
        }

        if ($request->soloist == 'soloist' && $request->ensemble == null) {
            $type_of = 'soloist';
        }elseif ($request->soloist == null && $request->ensemble == null) {
            $type_of = 'soloist, ensemble';
        }elseif ($request->soloist == 'soloist' && $request->ensemble == 'ensemble') {
            $type_of = 'soloist, ensemble';
        }elseif ($request->soloist == null && $request->ensemble == 'ensemble') {
            $type_of = 'ensemble';
        }

        foreach ($users as $user) {
            try {
                $option = GigOption::select('monday','tuesday','wednesday','thursday','friday','saturday','sunday','start','end','time_before_event','time_after_event')->where('user_id', $user->id)->firstOrFail();

                $busyDays = Gig::select('start','allDay')->where('user_id', $user->id)->where('allDay', 1)->get();

                $busyHours = Gig::select('start','end')->where('user_id', $user->id)->where('allDay', 0)->get();

                if (count($busyDays) == 0) {
                    if ($user->type == 'soloist') {
                        if ($user->info->address != 'null') {
                            array_push($availableUsers, $user->email);
                        }
                    } elseif ($user->type == 'ensemble') {
                        if ($user->ensemble->address != 'null') {
                            array_push($availableUsers, $user->email);
                        }
                    }
                }
                
                foreach ($busyDays as $busyDay) {             

                    $busyDay_notime = explode(' ', $busyDay->start);

                    if($busyDay_notime[0] == $request->day){
                        array_push($nonAvailableUsers, $user->email);
                    } else {
                        if($option->monday == 0 and $dayname == 'Monday'){
                            array_push($nonAvailableUsers, $user->email);
                        }elseif($option->tuesday == 0 and $dayname == 'Tuesday'){
                            array_push($nonAvailableUsers, $user->email);
                        }elseif($option->wednesday == 0 and $dayname == 'Wednesday'){
                            array_push($nonAvailableUsers, $user->email);
                        }elseif($option->thursday == 0 and $dayname == 'Thursday'){
                            array_push($nonAvailableUsers, $user->email);
                        }elseif($option->friday == 0 and $dayname == 'Friday'){
                            array_push($nonAvailableUsers, $user->email);
                        }elseif($option->saturday == 0 and $dayname == 'Saturday'){
                            array_push($nonAvailableUsers, $user->email);
                        }elseif($option->sunday == 0 and $dayname == 'Sunday'){
                            array_push($nonAvailableUsers, $user->email);
                        }else{
                            array_push($availableUsers, $user->email);
                        }
                    }
                }
            } catch(ModelNotFoundException $e) {

            }
        }
        $availableUsersNoRepited = array_unique($availableUsers);
        $nonAvailableUsersNoRepited = array_unique($nonAvailableUsers);
        $usersAvailable = array_diff($availableUsersNoRepited, $nonAvailableUsersNoRepited);
        $destinations = '';
        $userDestinations = [];
        $mile_radious_from_user_array = [];
        foreach ($usersAvailable as $user) {
            $singer = User::where('email', $user)->first();

            if( $singer->type == 'ensemble' )
            {
                $addres_from_user = explode('|', $singer->ensemble->address);
                $lat_from_user = explode('lat:', $addres_from_user[2]);
                $lng_from_user = explode('long:', $addres_from_user[3]);
                $mile_radious_from_user = $singer->ensemble->mile_radious;
                array_push($userDestinations, $singer->email);
                array_push($mile_radious_from_user_array, $mile_radious_from_user);
            } 
            else if( $singer->type == 'soloist' )
            {
                $addres_from_user = explode('|', $singer->info->address);
                $lat_from_user = explode('lat:', $addres_from_user[2]);
                $lng_from_user = explode('long:', $addres_from_user[3]);
                $mile_radious_from_user = $singer->info->mile_radious;
                array_push($userDestinations, $singer->email);
                array_push($mile_radious_from_user_array, $mile_radious_from_user);
            }
            
            $destinations .= $lat_from_user[1].",".$lng_from_user[1]."|";
        }

        $origin = $lat_origin.",".$lng_origin;
        $destinations = trim($destinations, "|");
        $finalData = $this->GetDrivingDistance($origin, $destinations);

        for ($x=0; $x<count($userDestinations); $x++) {
            if ($finalData[$x]['distance'] == 'undefined') {
                array_push($nonAvailableUsers, $userDestinations[$x]);
            }

            $distance_exploded = explode(' mi', $finalData[$x]['distance']);

            if (strpos($distance_exploded[0], ',')) {
                $exploded_from_user = explode(",", $distance_exploded[0]);
                $gd = $exploded_from_user[0].$exploded_from_user[1];
                $distance_from_point_to_point = (int)$gd;
            }else{
                $distance_from_point_to_point = (int)$distance_exploded[0];
            }

            if ($distance_from_point_to_point > $mile_radious_from_user_array[$x]) {
                array_push($nonAvailableUsers, $userDestinations[$x]);
            } else {
                array_push($availableUsers, $userDestinations[$x]);
            }
        }

        $availableUsersNoRepited = array_unique($availableUsers);
        $nonAvailableUsersNoRepited = array_unique($nonAvailableUsers);
        $usersAvailable = array_diff($availableUsersNoRepited, $nonAvailableUsersNoRepited);

        $tags = Tag::orderBy('name', 'DES')->select('id', 'name')->get();
        $instruments = Instrument::orderBy('name', 'DES')->select('id', 'name')->get();
        $styles = Style::orderBy('name', 'DES')->select('id', 'name')->get();

        $usersAvailable = array_values($usersAvailable);
        
        //////////////////////////////////////////////////////////////////////////
        /////////////////////////WITH FILTERS/////////////////////////////////////
        $user_availables = [];
        $user_nonavailables = [];

        //Variables for tags
        $user_tags_pushed = [];
        $user_tags_cached = [];
        $user_tags_discarted = [];
        $tags_user = [];
        $tags_request = $request->tags;

        //Variable for instruments
        $user_instruments_pushed = [];
        $user_instruments_cached = [];
        $user_instruments_discarted = [];
        $instruments_user = [];
        $instruments_request = $request->instruments;

        //Variable for styles
        $user_styles_pushed = [];
        $user_styles_cached = [];
        $user_styles_discarted = [];
        $styles_user = [];
        $styles_request = $request->styles;

        $users_array_request = $usersAvailable;
        // dd($users_array_request);
        foreach ($users_array_request as $user_email) {
            $user_filter = User::select('id', 'email', 'type')->where('email', $user_email)->first();
            $busyHours = Gig::select('start','end')->where('user_id', $user_filter->id)->where('allDay', 0)->get();
            $option = GigOption::where('user_id', $user_filter->id)->first();
            
            if (empty($busyHours->start) or empty($busyHours->end)) {
                //array_push($status, 'entre a vacio');
                if ($user_filter->type == 'soloist') {
                    if ($user_filter->info->address != 'null') {
                        if ($request->soloist == 'soloist' && $request->ensemble == null) {
                            array_push($user_availables, $user_filter->id);
                        }elseif ($request->soloist == null && $request->ensemble == null) {
                            array_push($user_availables, $user_filter->id);
                        }elseif ($request->soloist == 'soloist' && $request->ensemble == 'ensemble') {
                            array_push($user_availables, $user_filter->id);
                        }else{
                            array_push($user_nonavailables, $user_filter->id);
                        }
                    }
                } elseif ($user_filter->type == 'ensemble') {
                    if ($user_filter->ensemble->address != 'null') {
                        if ($request->soloist == null && $request->ensemble == 'ensemble') {
                            array_push($user_availables, $user_filter->id);
                        }elseif ($request->soloist == null && $request->ensemble == null) {
                            array_push($user_availables, $user_filter->id);
                        }elseif ($request->soloist == 'soloist' && $request->ensemble == 'ensemble') {
                            array_push($user_availables, $user_filter->id);
                        }else{
                            array_push($user_nonavailables, $user_filter->id);
                        }
                    }
                }
            }else{
                foreach ($busyHours as $busyHour) {
                    $dateTimeRequested = Carbon::parse($request->day.' '.$time.':00');
                    $busyDay_start_in = Carbon::parse($busyHour->start)->subMinute($option->time_before_event);
                    $busyDay_end_in = Carbon::parse($busyHour->end)->addMinute($option->time_after_event);
                    $durationRequested = Carbon::parse($request->day.' '.$time.':00')->addMinute($request->duration);

                    if ($dateTimeRequested->between($busyDay_start_in, $busyDay_end_in)) {
                        array_push($user_nonavailables, $user_filter->id);
                    } elseif($durationRequested->between($busyDay_start_in, $busyDay_end_in)){
                        array_push($user_nonavailables, $user_filter->id);
                    } else {
                        if ($user_filter->type == 'soloist') {
                            if ($user_filter->info->address != 'null') {
                                if ($request->soloist == 'soloist' && $request->ensemble == null) {
                                    array_push($user_availables, $user_filter->id);
                                }elseif ($request->soloist == null && $request->ensemble == null) {
                                    array_push($user_availables, $user_filter->id);
                                }elseif ($request->soloist == 'soloist' && $request->ensemble == 'ensemble') {
                                    array_push($user_availables, $user_filter->id);
                                }else{
                                    array_push($user_nonavailables, $user_filter->id);
                                }
                            }
                        } elseif ($user_filter->type == 'ensemble') {
                            if ($user_filter->ensemble->address != 'null') {
                                if ($request->soloist == null && $request->ensemble == 'ensemble') {
                                    array_push($user_availables, $user_filter->id);
                                }elseif ($request->soloist == null && $request->ensemble == null) {
                                    array_push($user_availables, $user_filter->id);
                                }elseif ($request->soloist == 'soloist' && $request->ensemble == 'ensemble') {
                                    array_push($user_availables, $user_filter->id);
                                }else{
                                    array_push($user_nonavailables, $user_filter->id);
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($request->tags)) {

                if ($user_filter->type == 'ensemble')
                {
                    $tags_user = $user_filter->ensemble->ensemble_tags->pluck('id')->toArray();
                }
                elseif ($user_filter->type == 'soloist') 
                {
                    $tags_user = $user_filter->user_tags->pluck('id')->toArray();
                }

                $tags_request = array_map('intval', $request->tags);
            
                foreach ($tags_user as $tag) {
                    if (in_array($tag, $request->tags)) {
                        array_push($user_tags_cached, $user_filter->id);
                    }else{
                        array_push($user_tags_discarted, $user_filter->id);
                    }
                }
            }

            if (!empty($request->instruments)) {

                if ($user_filter->type == 'ensemble')
                {
                    $instruments_user = $user_filter->ensemble->ensemble_instruments->pluck('id')->toArray();
                }
                elseif ($user_filter->type == 'soloist') 
                {
                    $instruments_user = $user_filter->user_instruments->pluck('id')->toArray();
                }

                $instruments_request = array_map('intval', $request->instruments);
            
                foreach ($instruments_user as $instrument) {
                    if (in_array($instrument, $request->instruments)) {
                        array_push($user_instruments_cached, $user_filter->id);
                    }else{
                        array_push($user_instruments_discarted, $user_filter->id);
                    }
                }
            }

            if (!empty($request->styles)) {

                if ($user_filter->type == 'ensemble')
                {
                    $styles_user = $user_filter->ensemble->ensemble_styles->pluck('id')->toArray();
                }
                elseif ($user_filter->type == 'soloist') 
                {
                    $styles_user = $user_filter->user_styles->pluck('id')->toArray();
                }

                $styles_request = array_map('intval', $request->styles);
            
                foreach ($styles_user as $style) {
                    if (in_array($style, $request->styles)) {
                        array_push($user_styles_cached, $user_filter->id);
                    }else{
                        array_push($user_styles_discarted, $user_filter->id);
                    }
                }
            }
        }

        $user_tags_pushed = array_diff($user_tags_discarted, $user_tags_cached);
        $user_instruments_pushed = array_diff($user_instruments_discarted, $user_instruments_cached);
        $user_styles_pushed = array_diff($user_styles_discarted, $user_styles_cached);
        
        foreach ($user_tags_pushed as $user_individual) {
            array_push($user_nonavailables, $user_individual);
        }

        foreach ($user_instruments_pushed as $user_individual) {
            array_push($user_nonavailables, $user_individual);
        }

        foreach ($user_styles_pushed as $user_individual) {
            array_push($user_nonavailables, $user_individual);
        }

        $availableUsersNoRepited = array_unique($user_availables);
        $nonAvailableUsersNoRepited = array_unique($user_nonavailables);
        $users_final_results = array_diff($availableUsersNoRepited, $nonAvailableUsersNoRepited);
        $users_final_results = array_values($users_final_results);

        /////////////////////////WITH FILTERS/////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////

        $usersAvailablebyID = [];
        foreach ($usersAvailable as $userAvai) {
            $user_by_email = User::select('id')->where('email', $userAvai)->first();
            $usersAvailableinID = $user_by_email->id;
            array_push($usersAvailablebyID, $usersAvailableinID);
        }
        $usersAvailablebyID = array_values($usersAvailablebyID);
        $usersAvailable = array_values($usersAvailable);

        $array_tags_from_request = $request->tags;
        if($array_tags_from_request != null){
            $string_tags_from_request = implode(",", $array_tags_from_request);
        }else{
            $string_tags_from_request = 'null';
        }

        $array_instruments_from_request = $request->instruments;
        if($array_instruments_from_request != null){
            $string_instruments_from_request = implode(",", $array_instruments_from_request);
        }else{
            $string_instruments_from_request = 'null';
        }

        $array_styles_from_request = $request->styles;
        if($array_styles_from_request != null){
            $string_styles_from_request = implode(",", $array_styles_from_request);
        }else{
            $string_styles_from_request = 'null';
        }

        if($usersAvailablebyID != null){
            $string_usersAvailablebyID  = implode(",", $usersAvailablebyID);
        }else{
            $string_usersAvailablebyID = 'null';
        }

        if($users_final_results != null){
            $string_users_final_results = implode(",", $users_final_results);
        }else{
            $string_users_final_results = 'null';
        }     

        $general_ask                 = new GeneralAsk();
        $general_ask->name           = $request->name;
        $general_ask->email          = $request->email;
        // $general_ask->phone          = $request->phone;
        $general_ask->date           = $date_timestamp;
        $general_ask->address        = $address;
        $general_ask->duration       = $request->duration;
        $general_ask->comment        = $comment;
        $general_ask->type_of        = $type_of;
        $general_ask->tags           = $string_tags_from_request;
        $general_ask->instruments    = $string_instruments_from_request;
        $general_ask->styles         = $string_styles_from_request;
        $general_ask->times          = 0;
        $general_ask->read           = 0;
        $general_ask->assined        = 0;
        $general_ask->array_per_date = $string_usersAvailablebyID;
        $general_ask->original_array = $string_users_final_results;
        $general_ask->sended_at      = $date_timestamp;
        // dd($general_ask);
        $general_ask->save();

        // $data = [  
        //             'name'     => $general_ask->name,
        //             'email'    => $general_ask->email,
        //             'company'  => $general_ask->company,
        //             'phone'    => $general_ask->phone,
        //             'address'  => $request->place_address,
        //             'date'     => $date,
        //             'duration' => $general_ask->duration,
        //             'type'     => $general_ask->type,
        //             'comment'  => $comment,
        //         ];

        // Mail::send('email.admin.request_general', $data, function($message) {
        //     $message->from('support@bemusical.us');
        //     $message->to('david@bemusic.al');
        //     $message->subject('Somebody has a GENERAL request for a service');
        // });


        //  Flash::success('Thanks '.$request->name.', we already sent a message to the admin page asking for availability. You will hear soon about your request.');
        // return redirect()->back();

        return view('layouts.query_results')
            ->with('date', $date)
            ->with('place_id', $place_id)
            ->with('place_address', $place_address)
            ->with('place_geometry', $place_geometry)
            ->with('address', $request->address)
            ->with('place_r', $request->address)
            ->with('date_r', $request->day)
            ->with('users', $users_final_results)
            ->with('tags', $tags)
            ->with('instruments', $instruments)
            ->with('styles', $styles)
            ->with('flag', 1)
            ->with('users_by_date', $usersAvailablebyID);
    }

    public function allowtimes($get_data)
    {
        $data = explode('&', $get_data);
        $date = explode('=', $data[0]);
        $date_exploted = explode('-', $date[1]);
        $day = $date_exploted[2];
        $month = $date_exploted[1];
        $year = $date_exploted[0];

        $id = explode('=', $data[1]);
        $gigs = Gig::select('start', 'end')->where('user_id', $id[1])->get();
        $time_unavailable = [];
        $time_unavailable_end = [];
        foreach ($gigs as $gig) {
            $gig_date = explode(' ', $gig->start);
            $gig_date_end = explode(' ', $gig->end);
            $gig_date_exploted = explode('-', $gig_date[0]);
            $gig_date_exploted_end = explode('-', $gig_date_end[0]);
            $gig_day = $gig_date_exploted[2];
            $gig_month = $gig_date_exploted[1];
            $gig_year = $gig_date_exploted[0];
            $gig_day_end = $gig_date_exploted_end[2];
            $gig_month_end = $gig_date_exploted_end[1];
            $gig_year_end = $gig_date_exploted_end[0];
            if ($gig_day == $day) {
                if ($gig_month == $month) {
                    if ($gig_year == $year) {
                        $time = explode(':', $gig_date[1]);
                        $time_end = explode(':', $gig_date_end[1]);
                        $time_sent = sprintf("%02d", $time[0]).':'.sprintf("%02d", $time[1]);
                        $time_sent_end = sprintf("%02d", $time_end[0]).':'.sprintf("%02d", $time_end[1]);
                        $full_time = $time_sent;
                        $full_time_end = $time_sent_end;
                        array_push($time_unavailable, $gig->start); 
                        array_push($time_unavailable_end, $gig->end); 
                        // array_push($time_unavailable, $full_time); 
                        // array_push($time_unavailable_end, $full_time_end); 
                    }
                }
            }
        }

        return array($time_unavailable, $time_unavailable_end);
    }

    public function query(Request $request){
        $users = User::where('visible', 1)->get();
        $date = (new Carbon($request->day))->format('l jS \\of F Y');
        $address = $request->place;
        $dayname = (new Carbon($request->day))->format('l');
        $availableUsers = [];
        $nonAvailableUsers = [];
        $finalAvailableUsersDistance = [];
        $finalAvailableUsersId = [];
        $finalAvailableUsersType = [];

        $place_id = $request->place_id;
        $place_address = $request->place_address;
        $place_geometry = $request->place_geometry;

        $lat_lng_origin = substr($request->place_geometry, 1, -1);
        $origin = explode(', ', $lat_lng_origin);
        $lat_origin = $origin[0];
        $lng_origin = $origin[1];

        foreach ($users as $user) {
            try {
                $option = GigOption::select('monday','tuesday','wednesday','thursday','friday','saturday','sunday','start','end','time_before_event','time_after_event')->where('user_id', $user->id)->firstOrFail();

                $busyDays = Gig::select('start','allDay')->where('user_id', $user->id)->where('allDay', 1)->get();

                $busyHours = Gig::select('start','end')->where('user_id', $user->id)->where('allDay', 0)->get();

                if (count($busyDays) == 0) {
                    if ($user->type == 'soloist') {
                        if ($user->info->address != 'null') {
                            array_push($availableUsers, $user->email);
                        }
                    } elseif ($user->type == 'ensemble') {
                        if ($user->ensemble->address != 'null') {
                            array_push($availableUsers, $user->email);
                        }
                    }
                }
                
                foreach ($busyDays as $busyDay) {             

                    $busyDay_notime = explode(' ', $busyDay->start);

                    if($busyDay_notime[0] == $request->day){
                        array_push($nonAvailableUsers, $user->email);
                    } else {
                        if($option->monday == 0 and $dayname == 'Monday'){
                            array_push($nonAvailableUsers, $user->email);
                        }elseif($option->tuesday == 0 and $dayname == 'Tuesday'){
                            array_push($nonAvailableUsers, $user->email);
                        }elseif($option->wednesday == 0 and $dayname == 'Wednesday'){
                            array_push($nonAvailableUsers, $user->email);
                        }elseif($option->thursday == 0 and $dayname == 'Thursday'){
                            array_push($nonAvailableUsers, $user->email);
                        }elseif($option->friday == 0 and $dayname == 'Friday'){
                            array_push($nonAvailableUsers, $user->email);
                        }elseif($option->saturday == 0 and $dayname == 'Saturday'){
                            array_push($nonAvailableUsers, $user->email);
                        }elseif($option->sunday == 0 and $dayname == 'Sunday'){
                            array_push($nonAvailableUsers, $user->email);
                        }else{
                            array_push($availableUsers, $user->email);
                        }
                    }
                }
            } catch(ModelNotFoundException $e) {

            }
        }
        $availableUsersNoRepited = array_unique($availableUsers);
        $nonAvailableUsersNoRepited = array_unique($nonAvailableUsers);
        $usersAvailable = array_diff($availableUsersNoRepited, $nonAvailableUsersNoRepited);
        $destinations = '';
        $userDestinations = [];
        $mile_radious_from_user_array = [];
        foreach ($usersAvailable as $user) {
            $singer = User::where('email', $user)->first();

            if( $singer->type == 'ensemble' )
            {
                $addres_from_user = explode('|', $singer->ensemble->address);
                $lat_from_user = explode('lat:', $addres_from_user[2]);
                $lng_from_user = explode('long:', $addres_from_user[3]);
                $mile_radious_from_user = $singer->ensemble->mile_radious;
                array_push($userDestinations, $singer->email);
                array_push($mile_radious_from_user_array, $mile_radious_from_user);
            } 
            else if( $singer->type == 'soloist' )
            {
                $addres_from_user = explode('|', $singer->info->address);
                $lat_from_user = explode('lat:', $addres_from_user[2]);
                $lng_from_user = explode('long:', $addres_from_user[3]);
                $mile_radious_from_user = $singer->info->mile_radious;
                array_push($userDestinations, $singer->email);
                array_push($mile_radious_from_user_array, $mile_radious_from_user);
            }
            
            $destinations .= $lat_from_user[1].",".$lng_from_user[1]."|";
        }

        $origin = $lat_origin.",".$lng_origin;
        $destinations = trim($destinations, "|");
        $finalData = $this->GetDrivingDistance($origin, $destinations);

        for ($x=0; $x<count($userDestinations); $x++) {
            if ($finalData[$x]['distance'] == 'undefined') {
                array_push($nonAvailableUsers, $userDestinations[$x]);
            }

            $distance_exploded = explode(' mi', $finalData[$x]['distance']);

            if (strpos($distance_exploded[0], ',')) {
                $exploded_from_user = explode(",", $distance_exploded[0]);
                $gd = $exploded_from_user[0].$exploded_from_user[1];
                $distance_from_point_to_point = (int)$gd;
            }else{
                $distance_from_point_to_point = (int)$distance_exploded[0];
            }

            if ($distance_from_point_to_point > $mile_radious_from_user_array[$x]) {
                array_push($nonAvailableUsers, $userDestinations[$x]);
            } else {
                array_push($availableUsers, $userDestinations[$x]);
            }
        }

        $availableUsersNoRepited = array_unique($availableUsers);
        $nonAvailableUsersNoRepited = array_unique($nonAvailableUsers);
        $usersAvailable = array_diff($availableUsersNoRepited, $nonAvailableUsersNoRepited);

        $tags = Tag::orderBy('name', 'DES')->select('id', 'name')->get();
        $instruments = Instrument::orderBy('name', 'DES')->select('id', 'name')->get();
        $styles = Style::orderBy('name', 'DES')->select('id', 'name')->get();

        $usersAvailable = array_values($usersAvailable);
        $lastusersAvailable = [];
        foreach ($usersAvailable as $userAvailableID) {
            $id_from_usersAvailable = User::select('id')->where('email', $userAvailableID)->first();
            array_push($lastusersAvailable, $id_from_usersAvailable->id);
        }
        return view('layouts.query_results')
            ->with('date', $date)
            ->with('place_id', $place_id)
            ->with('place_address', $place_address)
            ->with('place_geometry', $place_geometry)
            ->with('address', $address)
            ->with('place_r', $request->place)
            ->with('date_r', $request->day)
            ->with('users', $lastusersAvailable)
            ->with('tags', $tags)
            ->with('instruments', $instruments)
            ->with('styles', $styles)
            ->with('flag', 0);
    }

    public function filter(Request $request)
    {
        $time = $request->time;
        $user_availables = [];
        $user_nonavailables = [];

        //Variables for tags
        $user_tags_pushed = [];
        $user_tags_cached = [];
        $user_tags_discarted = [];
        $tags_user = [];
        $tags_request = $request->tags;

        //Variable for instruments
        $user_instruments_pushed = [];
        $user_instruments_cached = [];
        $user_instruments_discarted = [];
        $instruments_user = [];
        $instruments_request = $request->instruments;

        //Variable for styles
        $user_styles_pushed = [];
        $user_styles_cached = [];
        $user_styles_discarted = [];
        $styles_user = [];
        $styles_request = $request->styles;

        $users_array_request = $request->users;
        //$status = [];
        foreach ($users_array_request as $user_email) {
            $user = User::select('id', 'email', 'type')->where('id', $user_email)->first();
            $busyHours = Gig::select('start','end')->where('user_id', $user->id)->where('allDay', 0)->get();
            $option = GigOption::where('user_id', $user->id)->first();
            
            if (empty($busyHours->start) or empty($busyHours->end)) {
                //array_push($status, 'entre a vacio');
                if ($user->type == 'soloist') {
                    if ($user->info->address != 'null') {
                        if ($request->soloist == 'soloist' && $request->ensemble == null) {
                            array_push($user_availables, $user->id);
                        }elseif ($request->soloist == null && $request->ensemble == null) {
                            array_push($user_availables, $user->id);
                        }elseif ($request->soloist == 'soloist' && $request->ensemble == 'ensemble') {
                            array_push($user_availables, $user->id);
                        }else{
                            array_push($user_nonavailables, $user->id);
                        }
                    }
                } elseif ($user->type == 'ensemble') {
                    if ($user->ensemble->address != 'null') {
                        if ($request->soloist == null && $request->ensemble == 'ensemble') {
                            array_push($user_availables, $user->id);
                        }elseif ($request->soloist == null && $request->ensemble == null) {
                            array_push($user_availables, $user->id);
                        }elseif ($request->soloist == 'soloist' && $request->ensemble == 'ensemble') {
                            array_push($user_availables, $user->id);
                        }else{
                            array_push($user_nonavailables, $user->id);
                        }
                    }
                }
            }else{
                //array_push($status, $busyHours);
                foreach ($busyHours as $busyHour) {
                    $dateTimeRequested = Carbon::parse($request->day.' '.$time.':00');
                    $busyDay_start_in = Carbon::parse($busyHour->start)->subMinute($option->time_before_event);
                    $busyDay_end_in = Carbon::parse($busyHour->end)->addMinute($option->time_after_event);
                    $durationRequested = Carbon::parse($request->day.' '.$time.':00')->addMinute($request->duration);

                    if ($dateTimeRequested->between($busyDay_start_in, $busyDay_end_in)) {
                        array_push($user_nonavailables, $user->id);
                    } elseif($durationRequested->between($busyDay_start_in, $busyDay_end_in)){
                        array_push($user_nonavailables, $user->id);
                    } else {
                        if ($user->type == 'soloist') {
                            if ($user->info->address != 'null') {
                                if ($request->soloist == 'soloist' && $request->ensemble == null) {
                                    array_push($user_availables, $user->id);
                                }elseif ($request->soloist == null && $request->ensemble == null) {
                                    array_push($user_availables, $user->id);
                                }elseif ($request->soloist == 'soloist' && $request->ensemble == 'ensemble') {
                                    array_push($user_availables, $user->id);
                                }else{
                                    array_push($user_nonavailables, $user->id);
                                }
                            }
                        } elseif ($user->type == 'ensemble') {
                            if ($user->ensemble->address != 'null') {
                                if ($request->soloist == null && $request->ensemble == 'ensemble') {
                                    array_push($user_availables, $user->id);
                                }elseif ($request->soloist == null && $request->ensemble == null) {
                                    array_push($user_availables, $user->id);
                                }elseif ($request->soloist == 'soloist' && $request->ensemble == 'ensemble') {
                                    array_push($user_availables, $user->id);
                                }else{
                                    array_push($user_nonavailables, $user->id);
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($request->tags)) {

                if ($user->type == 'ensemble')
                {
                    $tags_user = $user->ensemble->ensemble_tags->pluck('id')->toArray();
                }
                elseif ($user->type == 'soloist') 
                {
                    $tags_user = $user->user_tags->pluck('id')->toArray();
                }

                $tags_request = array_map('intval', $request->tags);
                
                if(empty($tags_user)){
                    array_push($user_tags_discarted, $user->id);
                }else{
                    foreach ($tags_user as $tag) {
                        if (in_array($tag, $request->tags)) {
                            array_push($user_tags_cached, $user->id);
                        }else{
                            array_push($user_tags_discarted, $user->id);
                        }
                    }
                }
            }

            if (!empty($request->instruments)) {

                if ($user->type == 'ensemble')
                {
                    $instruments_user = $user->ensemble->ensemble_instruments->pluck('id')->toArray();
                }
                elseif ($user->type == 'soloist') 
                {
                    $instruments_user = $user->user_instruments->pluck('id')->toArray();
                }

                $instruments_request = array_map('intval', $request->instruments);
                
                if(empty($instruments_user)){
                    array_push($user_instruments_discarted, $user->id);
                }else{
                    foreach ($instruments_user as $instrument) {
                        if (in_array($instrument, $request->instruments)) {
                            array_push($user_instruments_cached, $user->id);
                        }else{
                            array_push($user_instruments_discarted, $user->id);
                        }
                    }
                }
            }

            if (!empty($request->styles)) {

                if ($user->type == 'ensemble')
                {
                    $styles_user = $user->ensemble->ensemble_styles->pluck('id')->toArray();
                }
                elseif ($user->type == 'soloist') 
                {
                    $styles_user = $user->user_styles->pluck('id')->toArray();
                }

                $styles_request = array_map('intval', $request->styles);
                
                if(empty($styles_user)){
                    array_push($user_styles_discarted, $user->id);
                }else{
                    foreach ($styles_user as $style) {
                        if (in_array($style, $request->styles)) {
                            array_push($user_styles_cached, $user->id);
                        }else{
                            array_push($user_styles_discarted, $user->id);
                        }
                    }
                }
            }
        }

        $user_tags_pushed = array_diff($user_tags_discarted, $user_tags_cached);
        $user_instruments_pushed = array_diff($user_instruments_discarted, $user_instruments_cached);
        $user_styles_pushed = array_diff($user_styles_discarted, $user_styles_cached);
        
        foreach ($user_tags_pushed as $user_individual) {
            array_push($user_nonavailables, $user_individual);
        }

        foreach ($user_instruments_pushed as $user_individual) {
            array_push($user_nonavailables, $user_individual);
        }

        foreach ($user_styles_pushed as $user_individual) {
            array_push($user_nonavailables, $user_individual);
        }

        $availableUsersNoRepited = array_unique($user_availables);
        $nonAvailableUsersNoRepited = array_unique($user_nonavailables);
        $users_final_results = array_diff($availableUsersNoRepited, $nonAvailableUsersNoRepited);
        $users_final_results = array_values($users_final_results);

        $array_users_picture = [];
        $array_users_name = [];
        $array_users_bio = [];
        $array_users_slug = [];

        foreach ($users_final_results as $final_user) {
            $java_user = User::where('id', $final_user)->first();
            
            if ($java_user->type == 'ensemble') {
                if ($java_user->ensemble->profile_picture != 'null') {
                    array_push($array_users_picture, "images/ensemble/".$java_user->ensemble->profile_picture);
                } else {
                    array_push($array_users_picture, "images/profile/no-image.png");
                }
                
                array_push($array_users_name, $java_user->ensemble->name);
                array_push($array_users_bio, $java_user->ensemble->summary);
                array_push($array_users_slug, $java_user->ensemble->slug);
            }elseif ($java_user->type == 'soloist') {
                if ($java_user->info->profile_picture != 'null') {
                    array_push($array_users_picture, "images/profile/".$java_user->info->profile_picture);
                } else {
                    array_push($array_users_picture, "images/profile/no-image.png");
                }
                array_push($array_users_name, $java_user->info->first_name.' '.$java_user->info->last_name);
                array_push($array_users_bio, $java_user->info->bio);
                array_push($array_users_slug, $java_user->info->slug);
            } 
        }

        //return array($array_users_picture, $array_users_name, $array_users_bio, $array_users_slug, $status);
        return array($array_users_picture, $array_users_name, $array_users_bio, $array_users_slug);
    }

    // public function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    // {
    //     $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&units=imperial";
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     $response = curl_exec($ch);
    //     curl_close($ch);
    //     $response_a = json_decode($response, true);
    //     $status = $response_a['rows'][0]['elements'][0]['status'];
    //     if ($status == 'ZERO_RESULTS') {
    //         return array('distance' => 'undefined', 'time' => 'undefined');
    //     }elseif ($status == 'UNKNOWN_ERROR') {
    //         return array('distance' => 'undefined', 'time' => 'undefined');
    //     }else {
    //         $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
    //         $time = $response_a['rows'][0]['elements'][0]['duration']['text'];
    //         return array('distance' => $dist, 'time' => $time);
    //     }
    // }


    // public function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    // {
    //     $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&units=imperial";
    //     $response = file_get_contents($url);
    //     $response_a = json_decode($response, true);
    //     $status = $response_a['rows'][0]['elements'][0]['status'];
    //     if ($status == 'ZERO_RESULTS') {
    //         return array('distance' => 'undefined', 'time' => 'undefined');
    //     }elseif ($status == 'UNKNOWN_ERROR') {
    //         return array('distance' => 'undefined', 'time' => 'undefined');
    //     }else {
    //         $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
    //         $time = $response_a['rows'][0]['elements'][0]['duration']['text'];
    //         return array('distance' => $dist, 'time' => $time);
    //     }
    // }

    public function GetDrivingDistance($origin, $destinations)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origin."&destinations=".$destinations."&mode=driving&key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&units=imperial";
        $response = file_get_contents($url);
        $response_a = json_decode($response, true);
        $data = [];
        $i = 0;
        foreach ($response_a['rows'][0]['elements'] as $destData) {
            $status = $destData['status'];
            if ($status == 'ZERO_RESULTS') {
                $data[$i]['distance'] = "undefined";
                $data[$i]['duration'] = "undefined";
            }elseif ($status == 'UNKNOWN_ERROR') {
                $data[$i]['distance'] = "undefined";
                $data[$i]['duration'] = "undefined";
            }elseif ($status == 'OK') {
                $data[$i]['distance'] = $destData['distance']['text'];
                $data[$i]['duration'] = $destData['duration']['text'];
            }else {
                $data[$i]['distance'] = "undefined";
                $data[$i]['duration'] = "undefined";
            }
            $i++;
        }
        return $data;
    }

    public function SendMailApproved($data)
    {
        Mail::send('email.confirmation_client', $data, function($message) use ($data){
            $message->from('support@bemusical.us');
            $message->to($data['c_email']);
            $message->subject($data['c_name']." Details of payment and gig");
        });

        Mail::send('email.confirmation_user', $data, function($message) use ($data){
            $message->from('support@bemusical.us');
            $message->to($data['u_email']);
            $message->subject($data['u_name']." your quote was approved - Details");
        });

        Mail::send('email.confirmation_admin', $data, function($message) use ($data){
            $message->from('support@bemusical.us');
            $message->to('david@bemusic.al');
            $message->subject("Payment approved");
        });

        $this->SendTextMessage('Bemusical: Gig confirmated', $data['id']);
    }

    public function SendTextMessage($text_message, $user_id)
    {
        $phone = Phone::where('user_id', $user_id);
        if ($phone->first()->confirmed == 1) {
            $code_country = $phone->first()->country_code;
            $phone_number = $code_country.$phone->first()->phone;

            $sid = "ACf29b73d8c11a7d9d84656693aac302f5";
            $token = "d340b51f8ff42b20daeb1607d0459713";
            $client = new \Twilio\Rest\Client($sid, $token);
            
            $message = $client->messages->create(
                $phone_number,
                array(
                    'from' => '+16502156754',
                    'body' => $text_message
                )
            );
        }
    }
}
