<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gig;

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
    	return view('layouts.calendar');
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
