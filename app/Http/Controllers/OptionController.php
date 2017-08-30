<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\storeInstrument;
use App\Http\Requests\storeTag;
use App\Http\Requests\storeStyle;
use App\Tag;
use App\Instrument;
use App\Style;

class OptionController extends Controller
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
    public function instrument(storeInstrument $request)
    {
    	$instrument = new Instrument($request->all());
    	$instrument->name = $request->instrument;
        $instrument->save();
        return redirect()->route('admin.dashboard');
    }

    public function destroyInstrument($id)
    {
        $instrument = Instrument::find($id);
        $instrument->delete();
        return redirect()->route('admin.dashboard');
    }

    public function tag(storeTag $request)
    {
    	$tag = new Tag($request->all());
        $tag->name = $request->tag;
        $tag->save();
        return redirect()->route('admin.dashboard');
    }

    public function destroyTag($id)
    {
        $tag = Tag::find($id);
        $tag->delete();
        return redirect()->route('admin.dashboard');
    }

    public function style(storeStyle $request)
    {
    	$style = new Style($request->all());
        $style->name = $request->style;
        $style->save();
        return redirect()->route('admin.dashboard');
    }

    public function destroyStyle($id)
    {
        $style = Style::find($id);
        $style->delete();
        return redirect()->route('admin.dashboard');
    }
}
