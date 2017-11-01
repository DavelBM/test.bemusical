<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GigOption;
use App\Gig;
use Auth;
use stdClass;

class CalendarController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

	/**
	 * 
	 */
    public function index()
    {
        if (Auth::user()->confirmed == 0) 
        {
            return redirect()->route('user.unconfirmed');
        }
        elseif(Auth::user()->active == 0) 
        {
            return redirect()->route('user.blocked');
        }
        else
        {  
            $user = Auth::user()->id;
            $option = GigOption::where('user_id', $user)->firstOrFail();
        	return view('layouts.calendar')->with('option', $option)->with('user', $user);
        }
    }

	/**
	 * 
	 */
    public function get_calendar()
    {
        if (Auth::user()->confirmed == 0) 
        {
            return redirect()->route('user.unconfirmed');
        }
        elseif(Auth::user()->active == 0) 
        {
            return redirect()->route('user.blocked');
        }
        else
        { 
            $user = Auth::user()->id;
        	$data = Gig::where('user_id', $user)->get(['title', 'start', 'end', 'url', 'allDay']);
    		$data_json = Response()->json($data);

    		return $data_json;
        }
    }

    public function calendarOptions($option)
    {
        $user = Auth::user()->id;
        $data = explode('|', $option);
        $get_value = explode('value:', $data[0]);
        $get_id = explode('id:', $data[1]);
        $value = $get_value[1];
        $name = $get_id[1];

        if ($name == 'view_listDay') {
            GigOption::where('user_id', $user)
                ->update([
                    'listDay' => $value,
                ]);
        }

        if ($name == 'view_listWeek') {
            GigOption::where('user_id', $user)
                ->update([
                    'listWeek' => $value,
                ]);
        }

        if ($name == 'view_month') {
            GigOption::where('user_id', $user)
                ->update([
                    'month' => $value,
                ]);
        }

        if ($name == 'monday') {
            if ($value == 'false') {
                GigOption::where('user_id', $user)
                ->update([
                    'monday' => 0,
                ]);
            }else{
                GigOption::where('user_id', $user)
                ->update([
                    'monday' => 1,
                ]);
            }
        }

        if ($name == 'tuesday') {
            if ($value == 'false') {
                GigOption::where('user_id', $user)
                ->update([
                    'tuesday' => 0,
                ]);
            }else{
                GigOption::where('user_id', $user)
                ->update([
                    'tuesday' => 1,
                ]);
            }
        }

        if ($name == 'wednesday') {
            if ($value == 'false') {
                GigOption::where('user_id', $user)
                ->update([
                    'wednesday' => 0,
                ]);
            }else{
                GigOption::where('user_id', $user)
                ->update([
                    'wednesday' => 1,
                ]);
            }
        }

        if ($name == 'thursday') {
            if ($value == 'false') {
                GigOption::where('user_id', $user)
                ->update([
                    'thursday' => 0,
                ]);
            }else{
                GigOption::where('user_id', $user)
                ->update([
                    'thursday' => 1,
                ]);
            }
        }

        if ($name == 'friday') {
            if ($value == 'false') {
                GigOption::where('user_id', $user)
                ->update([
                    'friday' => 0,
                ]);
            }else{
                GigOption::where('user_id', $user)
                ->update([
                    'friday' => 1,
                ]);
            }
        }

        if ($name == 'saturday') {
            if ($value == 'false') {
                GigOption::where('user_id', $user)
                ->update([
                    'saturday' => 0,
                ]);
            }else{
                GigOption::where('user_id', $user)
                ->update([
                    'saturday' => 1,
                ]);
            }
        }

        if ($name == 'sunday') {
            if ($value == 'false') {
                GigOption::where('user_id', $user)
                ->update([
                    'sunday' => 0,
                ]);
            }else{
                GigOption::where('user_id', $user)
                ->update([
                    'sunday' => 1,
                ]);
            }
        }

        if ($name == 'start_business_hours') {
            GigOption::where('user_id', $user)
                ->update([
                    'start' => $value,
                ]);
        }


        if ($name == 'end_business_hours') {
            GigOption::where('user_id', $user)
                ->update([
                    'end' => $value,
                ]);
        }

        if ($name == 'dead_time_before') {
            if ($value <= 30) {
                $value = 30;
            }
            GigOption::where('user_id', $user)
                ->update([
                    'time_before_event' => $value,
                ]);
        }

        if ($name == 'dead_time_after') {
            if ($value <= 30) {
                $value = 30;
            }
            GigOption::where('user_id', $user)
                ->update([
                    'time_after_event' => $value,
                ]);
        }

        if ($name == 'default_view') {
            GigOption::where('user_id', $user)
                ->update([
                    'defaultView' => $value,
                ]);
        }

        return $value.'-'.$name;        
    }

    public function block_day(Request $request)
    {
        $gig = new Gig($request->all());
        if($request->fullOrPart == 'full'){
            $gig->user_id = $request->user_id;
            $gig->title = 'all day bloked';
            $gig->start = $request->date.' 00:00:00';
            $gig->end = $request->date.' 23:59:59';
            $gig->url = '#';
            $gig->allDay = 1;
            $gig->save(); 
        }else{
            $gig->user_id = $request->user_id;
            $gig->title = $request->title;
            $gig->start = $request->date.' '.$request->start.':00';
            $gig->end = $request->date.' '.$request->end.':00';
            $gig->url = '#';
            $gig->save();
        }
        return redirect()->route('index.calendar');
    }

    public function get_dates($date)
    {
        $info = [];
        $object = new stdClass();
        $gig = Gig::where('user_id', Auth::user()->id)->where('start', 'like', '%'. $date .'%')->get();
        $object->status = $gig;
        $info[] = $object;
        return response()->json(array('info' => $info), 200);
    }

    public function destroydate(Request $request)
    {
        $info = [];
        $user = Auth::user()->id;
        $user_date = Gig::select('user_id')->where('id', $request->id);
        if ($user_date->first()->user_id == $user) { 
            Gig::where('id', $request->id)->delete();
            $object = new stdClass();
            $object->status = 'Deleted';
            $info[] = $object;
            return response()->json(array('info' => $info), 200);
        } else {
            $object = new stdClass();
            $object->status = 'Action no permitted';
            $info[] = $object;
            return response()->json(array('info' => $info), 200);
        }
    }
}
