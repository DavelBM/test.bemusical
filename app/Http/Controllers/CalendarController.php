<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GigOption;
use App\Gig;
use Auth;

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
        $user = Auth::user()->id;
        $option = GigOption::where('user_id', $user)->firstOrFail();
    	return view('layouts.calendar')->with('option', $option);
    }

	/**
	 * 
	 */
    public function get_calendar()
    {
    	$data = Gig::get(['title', 'start', 'end', 'url']);
		$data_json = Response()->json($data);

		return $data_json;
    }
}
