<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;//Exceptions for failOrFail
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
use Auth;
use Mail;
use URL;

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
        dd('hola sending price');
        // $available = substr($request->token, -1);
        // $token = substr($request->token, 0, -1);

        // $review = Ask::where('token', $token)->firstOrFail();
        // $user = User::where('id', $review->user_id)->firstOrFail();

        // if($user->type == "soloist") {
        //     $info = User_info::select('first_name', 'last_name')->where('user_id', $review->user_id)->firstOrFail();
        // } elseif($user->type == "ensemble") {
        //      $ensemble = Ensemble::select('name')->where('user_id', $review->user_id)->firstOrFail();
        // }
        
        // if($review->available == 1 or $review->nonavailable == 1 or $review->price != null or $review->accepted_price == 1){
        //     Flash::error('This token already was used');
        //     return redirect()->route('login');
        // }else{
        //     if($available == 1){
        //         Ask::where('token', $token)
        //         ->update([
        //             'price'       => $request->price,
        //             'available'   => 1,
        //             'nonavailable'=> 0,
        //         ]);

        //         $dt = explode("|", $review->date);
        //         $address = explode("|", $review->address);
        //         $addrNAME = explode("address:", $address[1]);
                
        //         if($user->type == "soloist") {
        //             $data = [
        //                 'name'    => $review->name,
        //                 'name_use'=> $info->first_name.' '.$info->last_name,
        //                 'email'   => $review->email,
        //                 'phone'   => $review->phone,
        //                 'date'    => $dt[1],
        //                 'address' => $addrNAME[1],
        //                 'duration'=> $review->duration,
        //                 'price'   => $request->price,
        //                 'token'   => $review->token,
        //             ];
        //         } elseif($user->type == "ensemble") {
        //              $data = [
        //                 'name'    => $review->name,
        //                 'name_use'=> $ensemble->name,
        //                 'email'   => $review->email,
        //                 'phone'   => $review->phone,
        //                 'date'    => $dt[1],
        //                 'address' => $addrNAME[1],
        //                 'duration'=> $review->duration,
        //                 'price'   => $request->price,
        //                 'token'   => $review->token,
        //             ];
        //         }

        //         Mail::send('email.request_send_price_client', $data, function($message) use ($review){
        //             $message->from('support@bemusical.us');
        //             $message->to($review->email);
        //             $message->subject("Hi, we have a price proposal for your event");
        //         });

        //         if($user->type == "soloist") {
        //             Mail::send('email.admin.request_send_price_client', $data, function($message) use ($review, $info){
        //                 $message->from('support@bemusical.us');
        //                 $message->to('david@bemusic.al');
        //                 $message->subject('Admin, '.$info->first_name.' '.$info->last_name.' is available and gives the price to '.$review->name);
        //             });
        //         } elseif($user->type == "ensemble") {
        //             Mail::send('email.admin.request_send_price_client', $data, function($message) use ($review, $ensemble){
        //                 $message->from('support@bemusical.us');
        //                 $message->to('david@bemusic.al');
        //                 $message->subject('Admin, '.$ensemble->name.' is available and gives the price to '.$review->name);
        //             }); 
        //         }

        //         Flash::success('You accept the request, you can find all the info in your dashboard');
        //         return redirect()->route('login');
        //     }elseif($available == 0){
        //         Ask::where('token', $token)
        //         ->update([
        //             'price'       => $request->price,
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
        dd('hola NOT AVAILABLE');
        // ///////////SEND MAIL TO CLIENT THAT THE USER CANT ASSIST TO THE EVENT
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

    public function return_answer_price($get_token)
    {
        $available = substr($get_token, -1);
        $token = substr($get_token, 0, -1);
        $review = Ask::select('available', 'nonavailable', 'user_id', 'accepted_price', 'price')->where('token', $token)->firstOrFail();
        $user = User::select('type', 'id')->where('id', $review->user_id)->firstOrFail();

        if ($review->available != 0 and $review->nonavailable == 0 and $review->price != null and $review->accepted_price != 0) {
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
        }elseif($review->available != 0 and $review->nonavailable == 0 and $review->price != null and $review->accepted_price == 0) {
            if($user->type == 'soloist')
            {
                $info = User_info::select('slug')->where('user_id', $user->id)->firstOrFail();
                if($available == 1){
                    $ask = Ask::where('token', $token)->first();
                
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

                    Ask::where('token', $token)
                    ->update([
                        'accepted_price'   => 1,
                    ]);
                    Flash::success('You accept the price, and it was sent it to the user. Everithing is done. Just wait until the day of your event');
                    return redirect()->route('index.public', $info->slug);
                }elseif ($available == 0) {

                    Ask::where('token', $token)
                    ->update([
                        'accepted_price'   => 0,
                    ]);
                    Flash::error('You did no accept the price, and it was sent it to the user.');
                    return redirect()->route('index.public', $info->slug);              
                }
            }
            elseif ($user->type == 'ensemble') 
            {
                $info = Ensemble::select('slug')->where('user_id', $user->id)->firstOrFail();
                if($available == 1){
                    $ask = Ask::where('token', $token)->first();
                    
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

                    Ask::where('token', $token)
                    ->update([
                        'accepted_price'   => 1,
                    ]);
                    Flash::success('You accept the price, and it was sent it to the user. Everithing is done. Just wait until the day of your event');
                    return redirect()->route('index.public', $info->slug);
                }elseif ($available == 0) {
                    Ask::where('token', $token)
                    ->update([
                        'accepted_price'   => 0,
                    ]);
                    Flash::success('You did no accept the price, and it was sent it to the user.');
                    return redirect()->route('index.public', $info->slug);              
                }
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

        foreach ($usersAvailable as $user) {
            $singer = User::where('email', $user)->first();

            if( $singer->type == 'ensemble' )
            {
                $addres_from_user = explode('|', $singer->ensemble->address);
                $lat_from_user = explode('lat:', $addres_from_user[2]);
                $lng_from_user = explode('long:', $addres_from_user[3]);
                $mile_radious_from_user = $singer->ensemble->mile_radious;
            } 
            else if( $singer->type == 'soloist' )
            {
                $addres_from_user = explode('|', $singer->info->address);
                $lat_from_user = explode('lat:', $addres_from_user[2]);
                $lng_from_user = explode('long:', $addres_from_user[3]);
                $mile_radious_from_user = $singer->info->mile_radious;
            }
            $dist = $this->GetDrivingDistance($lat_origin, $lat_from_user[1], $lng_origin, $lng_from_user[1]);
            
            if ($dist['distance'] == 'undefined') {
                array_push($nonAvailableUsers, $singer->email);
            }

            $distance_exploded = explode(' mi', $dist['distance']);

            if (strpos($distance_exploded[0], ',')) {
                $exploded_from_user = explode(",", $distance_exploded[0]);
                $gd = $exploded_from_user[0].$exploded_from_user[1];
                $distance_from_point_to_point = (int)$gd;
            }else{
                $distance_from_point_to_point = (int)$distance_exploded[0];
            }

            if ($distance_from_point_to_point > $mile_radious_from_user) {
                array_push($nonAvailableUsers, $singer->email);
            } else {
                array_push($availableUsers, $singer->email);
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

        foreach ($users_array_request as $user_email) {
            $user_filter = User::select('id', 'email', 'type')->where('email', $user_email)->first();
            $busyHours = Gig::select('start','end')->where('user_id', $user_filter->id)->where('allDay', 0)->get();
            $option = GigOption::where('user_id', $user_filter->id)->first();

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

        foreach ($usersAvailable as $user) {
            $singer = User::where('email', $user)->first();

            if( $singer->type == 'ensemble' )
            {
                $addres_from_user = explode('|', $singer->ensemble->address);
                $lat_from_user = explode('lat:', $addres_from_user[2]);
                $lng_from_user = explode('long:', $addres_from_user[3]);
                $mile_radious_from_user = $singer->ensemble->mile_radious;
            } 
            else if( $singer->type == 'soloist' )
            {
                $addres_from_user = explode('|', $singer->info->address);
                $lat_from_user = explode('lat:', $addres_from_user[2]);
                $lng_from_user = explode('long:', $addres_from_user[3]);
                $mile_radious_from_user = $singer->info->mile_radious;
            }
            $dist = $this->GetDrivingDistance($lat_origin, $lat_from_user[1], $lng_origin, $lng_from_user[1]);
            
            if ($dist['distance'] == 'undefined') {
                array_push($nonAvailableUsers, $singer->email);
            }

            $distance_exploded = explode(' mi', $dist['distance']);

            if (strpos($distance_exploded[0], ',')) {
                $exploded_from_user = explode(",", $distance_exploded[0]);
                $gd = $exploded_from_user[0].$exploded_from_user[1];
                $distance_from_point_to_point = (int)$gd;
            }else{
                $distance_from_point_to_point = (int)$distance_exploded[0];
            }

            if ($distance_from_point_to_point > $mile_radious_from_user) {
                array_push($nonAvailableUsers, $singer->email);
            } else {
                array_push($availableUsers, $singer->email);
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
                array_push($array_users_picture, "images/ensemble/".$java_user->ensemble->profile_picture);
                array_push($array_users_name, $java_user->ensemble->name);
                array_push($array_users_bio, $java_user->ensemble->summary);
                array_push($array_users_slug, $java_user->ensemble->slug);
            }elseif ($java_user->type == 'soloist') {
                array_push($array_users_picture, "images/profile/".$java_user->info->profile_picture);
                array_push($array_users_name, $java_user->info->first_name.' '.$java_user->info->last_name);
                array_push($array_users_bio, $java_user->info->bio);
                array_push($array_users_slug, $java_user->info->slug);
            } 
        }

        //return array($array_users_picture, $array_users_name, $array_users_bio, $array_users_slug, $status);
        return array($array_users_picture, $array_users_name, $array_users_bio, $array_users_slug);
    }

    public function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&units=imperial";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        $status = $response_a['rows'][0]['elements'][0]['status'];
        if ($status == 'ZERO_RESULTS') {
            return array('distance' => 'undefined', 'time' => 'undefined');
        }elseif ($status == 'UNKNOWN_ERROR') {
            return array('distance' => 'undefined', 'time' => 'undefined');
        }else {
            $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
            $time = $response_a['rows'][0]['elements'][0]['duration']['text'];
            return array('distance' => $dist, 'time' => $time);
        }
    }
}
