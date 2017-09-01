<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\updateImageUser;
use App\Http\Requests\updateInfoEnsemble;
use App\Http\Requests\repertoirRequest;
use App\Ensemble;
use Auth;
use App\User;
use App\Tag;
use App\Instrument;
use App\Style;
use App\EnsembleTag;
use App\EnsembleStyle;
use App\EnsembleInstrument;
use App\Ensemble_image;
use App\Ensemble_video;
use App\Ensemble_song;
use App\User_info;
use App\EnsembleRepertoire;
use App\Member;
use Hash;
use Mail;

class EnsembleController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
        elseif(Auth::user()->type == 'soloist') 
        {
            return redirect()->route('user.dashboard');
        } 
        else
        {
            $user = \Auth::user()->id;
            //Relation many to many TAGS//
            $ensemble = Ensemble::where('user_id', $user)->firstOrFail();
            $my_tags = $ensemble->ensemble_tags->pluck('id')->toArray();
            //Relation many to many STYLES//
            $my_styles = $ensemble->ensemble_styles->pluck('id')->toArray();
            //Relation many to many INSTRUMENTS//
            $my_instruments = $ensemble->ensemble_instruments->pluck('id')->toArray();
            //Relation many to many//
            $tags = Tag::orderBy('name', 'DES')->pluck('name', 'id');
            $instruments = Instrument::orderBy('name', 'DES')->pluck('name', 'id');
            $styles = Style::orderBy('name', 'DES')->pluck('name', 'id');
            $images = $ensemble->ensemble_images->pluck('name');
            $videos = $ensemble->ensemble_videos->all();
            $songs = $ensemble->ensemble_songs->all();

            $repertoires = $ensemble->ensemble_repertoires->all();
            $total_repertoires = EnsembleRepertoire::where('ensemble_id', $ensemble->id)->where('visible', 1)->count(); 

            $members = Member::where('ensemble_id', $ensemble->id)->get();          

            return view('ensemble.dashboard')
                   ->with('ensemble', $ensemble)
                   ->with('tags', $tags)
                   ->with('instruments', $instruments)
                   ->with('styles', $styles)
                   ->with('my_tags', $my_tags)
                   ->with('my_styles', $my_styles)
                   ->with('my_instruments', $my_instruments)
                   ->with('images', $images)
                   ->with('videos', $videos)
                   ->with('songs', $songs)
                   ->with('repertoires', $repertoires)
                   ->with('total_repertoires', $total_repertoires)
                   ->with('members', $members);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(updateInfoEnsemble $request, $id)
    {
        $user = \Auth::user()->id;
        Ensemble::where('user_id', $user)
        ->update([
            'name'         => $request->name,
            'manager_name' => $request->manager,
            'type'         => $request->type,
            'about'        => $request->about,
            'summary'      => $request->summary,
            'phone'        => $request->phone,
            'address'      => $request->address,
            'location'     => $request->location,
            'mile_radious' => $request->mile_radious
        ]);
        return redirect()->route('ensemble.dashboard');
    }

    public function updateImage(updateImageUser $request, $id)
    {
        $user = \Auth::user()->id;
        if($request->file('image')){
            $file = $request->file('image');
            $name = 'ensemble_picture_'.time().'.'.$file->getClientOriginalExtension();
            $path = public_path().'/images/ensemble';
            $file->move($path, $name); 
        }

        Ensemble::where('user_id', $user)
        ->update([
            'profile_picture'   => $name
        ]);

        return redirect()->route('ensemble.dashboard');
    }

    public function storeInstruments(Request $request)
    {
        $ensemble_id = Auth::user()->ensemble->id;
        EnsembleInstrument::where('ensemble_id', $ensemble_id)->delete();
        
        foreach ($request->instruments as $id) 
        {
            $instrument = new EnsembleInstrument($request->all());
            $instrument->ensemble_id = $ensemble_id;
            $instrument->instrument_id = $id;
            $instrument->save(); 
        }
        return redirect()->route('ensemble.dashboard');
    }

    public function storeTags(Request $request)
    {
        $ensemble_id = Auth::user()->ensemble->id;
        EnsembleTag::where('ensemble_id', $ensemble_id)->delete();
        
        foreach ($request->tags as $id) 
        {
            $tag = new EnsembleTag($request->all());
            $tag->ensemble_id = $ensemble_id;
            $tag->tag_id = $id;
            $tag->save(); 
        }
        return redirect()->route('ensemble.dashboard');
    }

    public function storeStyles(Request $request)
    {
        $ensemble_id = Auth::user()->ensemble->id;
        EnsembleStyle::where('ensemble_id', $ensemble_id)->delete();
        
        foreach ($request->styles as $id) 
        {
            $style = new EnsembleStyle($request->all());
            $style->ensemble_id = $ensemble_id;
            $style->style_id = $id;
            $style->save(); 
        }
        return redirect()->route('ensemble.dashboard');
    }

    public function storeImages(Request $request)
    {
        $ensemble_id = Auth::user()->ensemble->id;
        $num_img = Ensemble_image::where('ensemble_id', $ensemble_id)->count();
        if ($num_img < 5) {
            $image = new Ensemble_image();
            $path = public_path().'/images/general';
            if($request->file('file')){
                $files = $request->file('file');
                foreach($files as $file){
                    $fileName = 'ensemble_bio_'.time().'-'.$file->getClientOriginalName();
                    $file->move($path, $fileName);
                    $image->ensemble_id = $ensemble_id;
                    $image->name = $fileName;
                    $image->save();
                }
            }
        }  
    }

    public function destroyImageEnsemble($image)
    {
        $ensemble_id = Auth::user()->ensemble->id;
        Ensemble_image::where('ensemble_id', $ensemble_id)->where('name', $image)->delete();
        return redirect()->route('ensemble.dashboard');
    }

    public function video(Request $request)
    {
        $ensemble_id = Auth::user()->ensemble->id;
        $total_videos = Ensemble_video::where('ensemble_id', $ensemble_id)->count();
        if ($total_videos < 5) {

            $video = new Ensemble_video($request->all());
            
            //CHECK IF THIS IS A VIDEO FROM YOUTUBE
            if (strpos($request->video, 'youtube') !== false or strpos($request->video, 'youtu.be') !== false) {

                if (strpos($request->video, 'youtu.be') !== false) {
                    //IF CONTAINS YOUTUBE ID SEARCH FOR ID VIDEO
                    $display = explode("/", $request->video);
                    $id_video = end($display);
                    $video->code = $id_video;                
                }elseif (strpos($request->video, 'iframe') !== false) {
                    //IF CONTAINS YOUTUBE ID SEARCH FOR ID VIDEO
                    $display = explode("/embed/", $request->video);
                    $id_video = explode('"', $display[1]);
                    $video->code = $id_video[0];
                }elseif (strpos($request->video, 'www.youtube.com/watch?v') !== false){
                    //IF CONTAINS YOUTUBE ID SEARCH FOR ID VIDEO
                    $display = explode("=", $request->video);
                    $id_video = end($display);
                    $video->code = $id_video;
                }else{
                    return redirect()->back()->withErrors(['video'=>"That is not an allowed link or video"]);
                }

                $video->platform = 'youtube';
                $video->ensemble_id = $ensemble_id;
                $video->save();

            }elseif (strpos($request->video, 'vimeo') !== false) {
                
                if (strpos($request->video, 'iframe') !== false) {
                    //IF CONTAINS VIMEO ID, SEARCH FOR ID VIDEO
                    $display = explode('</iframe>', $request->video);
                    $display_1 = explode('/video/', $display[0]);
                    $last_link = end($display_1);
                    $id_video = explode('"', $last_link);
                    $video->code = $id_video[0];                
                }elseif(strpos($request->video, 'https://vimeo.com/') !== false){
                    //IF CONTAINS VIMEO ID, SEARCH FOR ID VIDEO
                    $display = explode("/", $request->video);
                    $id_video = end($display);
                    $video->code = $id_video;
                }else{
                    return redirect()->back()->withErrors(['video'=>"That is not an allowed link or video"]);
                }    

                $video->platform = 'vimeo';
                $video->ensemble_id = $ensemble_id;
                $video->save();

            }else{
                return redirect()->back()->withErrors(['video'=>"That is not an allowed link or video"]);
            }
        }else{
            return redirect()->back()->withErrors(['video'=>"You only can add 5 videos in total"]);
        }
        return redirect()->route('ensemble.dashboard');
    }

    public function delete_video($id)
    {
        $video = Ensemble_video::find($id)->delete();
        return redirect()->route('ensemble.dashboard');
    }

    public function song(Request $request)
    {
        $ensemble_id = Auth::user()->ensemble->id;
        $total_songs = Ensemble_song::where('ensemble_id', $ensemble_id)->count();

        if ($total_songs < 5) {

            $song = new Ensemble_song($request->all());
            //CHECK IF THIS IS A VIDEO FROM SPOTIFY
            if (strpos($request->song, 'spotify') !== false){

                if (strpos($request->song, 'open.spotify') !== false) {
                    $display = explode("/track/", $request->song);
                    $id_song = end($display);
                    $song->code = $id_song; 
                }elseif (strpos($request->song, 'spotify:track') !== false) {
                    $display = explode(":", $request->song);
                    $id_song = end($display);
                    $song->code = $id_song;
                }elseif (strpos($request->song, 'embed.spotify.com') !== false) {
                    $display = explode("%3Atrack%3A", $request->song);
                    $id_song = explode('"', $display[1]);
                    $song->code = $id_song[0];
                }else{
                    return redirect()->back()->withErrors(['song'=>"Link not allowed"]);
                }
                $song->platform = 'spotify';
                $song->ensemble_id = $ensemble_id;
                $song->save();

            }elseif (strpos($request->song, 'soundcloud') !== false) {
                
                if (strpos($request->song, 'iframe') !== false) {
                    $display = explode("api.soundcloud.com/tracks/", $request->song);
                    $id_song = explode("&amp;", $display[1]);
                    $song->code = $id_song[0];
                }else {
                    return redirect()->back()->withErrors(['song'=>"Link not allowed"]);
                }     
                $song->platform = 'soundcloud';   
                $song->ensemble_id = $ensemble_id;
                $song->save();

            }else{
                return redirect()->back()->withErrors(['song'=>"That is not an allowed link or song"]);
            }
        }else{
            return redirect()->back()->withErrors(['song'=>"You only can add 5 songs in total"]);
        }
        return redirect()->route('ensemble.dashboard');
    }


    public function delete_song($id)
    {
        $song = Ensemble_song::find($id)->delete();
        return redirect()->route('ensemble.dashboard');
    }

    public function repertoir(repertoirRequest $request)
    {   
        $repertoir = new EnsembleRepertoire($request->all());
        $repertoir->ensemble_id = Auth::user()->ensemble->id;
        $repertoir->repertoire_example = $request->repertoir;
        $repertoir->visible = 0;
        $repertoir->save();
        return redirect()->route('ensemble.dashboard');
    }

    public function destroy_repertoir($id)
    {
        $repertoir = EnsembleRepertoire::find($id)->delete();
        return redirect()->route('ensemble.dashboard');
    }

    public function update_repertoir($id)
    {
        $repertoir = EnsembleRepertoire::select('visible')->find($id);
        $repertoir->visible = !$repertoir->visible;
        EnsembleRepertoire::find($id)->update(['visible' => $repertoir->visible]);
        return redirect()->route('ensemble.dashboard');
    }

    public function member(Request $request)
    {

        if(strpos($request->member, 'bemusical.us/') !== false) {
            $display = explode("bemusical.us/", $request->member);
            $slug_member = end($display);
            
            if (Ensemble::where('slug', '=', $slug_member)->exists()) {
                return redirect()->back()->withErrors(['member'=>"You cannot add ensembles in this ensemble"]);
            }elseif(User_info::where('slug', '=', $slug_member)->exists()){
                $num_code = str_random(50);
                $token = $num_code.time();
                $user = User_info::where('slug', '=', $slug_member)->firstOrFail();

                $ensemble = Ensemble::select('id', 'name')
                                    ->where('user_id', Auth::user()->id)
                                    ->firstOrFail();

                if(   Member::where('ensemble_id', '=', $ensemble->id)
                            ->where('user_id', '=', $user->user->id)
                            ->exists()
                  )
                {
                    return redirect()->back()->withErrors(['member'=>"This user is part of your ensemble already"]);
                }

                $member = new Member;
                $member->ensemble_id  = $ensemble->id;
                $member->user_id      = $user->user->id;
                $member->name         = $user->first_name.' '.$user->last_name;
                $member->instrument   = 'null';
                $member->slug         = $slug_member;
                $member->token        = $token;
                $member->email        = $user->user->email;
                $member->confirmation = 0;
                $member->save();
                
                $data = [  
                            'token'           => $token,
                            'ensemble_name'   => $ensemble->name,
                            'name'            => $user->first_name,
                        ];

                Mail::send('email.member_request', $data, function($message) use ($user){
                    $message->from('support@bemusical.us');
                    $message->to($user->user->email);
                    $message->subject('You have an invitation');
                });
            }else{
                return redirect()->back()->withErrors(['member'=>"The user does not exist"]);
            }

        }else{
            return redirect()->back()->withErrors(['member'=>"Link not allowed"]);
        }

        return redirect()->route('ensemble.dashboard');
    }

    public function destroy_member($id)
    {
        $member = Member::find($id)->delete();
        return redirect()->route('ensemble.dashboard');
    }

    public function notmember(Request $request)
    {
        // if(strpos($request->member, 'bemusical.us/') !== false) {
        //     $display = explode("bemusical.us/", $request->member);
        //     $slug_member = end($display);
            
        //     if (Ensemble::where('slug', '=', $slug_member)->exists()) {
        //         return redirect()->back()->withErrors(['member'=>"You cannot add ensembles in this ensemble"]);
        //     }elseif(User_info::where('slug', '=', $slug_member)->exists()){
        //         $num_code = str_random(50);
        //         $token = $num_code.time();
        //         $user = User_info::where('slug', '=', $slug_member)->firstOrFail();

        //         $ensemble = Ensemble::select('id', 'name')
        //                             ->where('user_id', Auth::user()->id)
        //                             ->firstOrFail();

        //         if(   Member::where('ensemble_id', '=', $ensemble->id)
        //                     ->where('user_id', '=', $user->user->id)
        //                     ->exists()
        //           )
        //         {
        //             return redirect()->back()->withErrors(['member'=>"This user is part of your ensemble already"]);
        //         }

        //         $member = new Member;
        //         $member->ensemble_id  = $ensemble->id;
        //         $member->user_id      = $user->user->id;
        //         $member->name         = $user->first_name.' '.$user->last_name;
        //         $member->instrument   = 'null';
        //         $member->slug         = $slug_member;
        //         $member->token        = $token;
        //         $member->email        = $user->user->email;
        //         $member->confirmation = 0;
        //         $member->save();
                
        //         $data = [  
        //                     'token'           => $token,
        //                     'ensemble_name'   => $ensemble->name,
        //                     'name'            => $user->first_name,
        //                 ];

        //         Mail::send('email.member_request', $data, function($message) use ($user){
        //             $message->from('support@bemusical.us');
        //             $message->to($user->user->email);
        //             $message->subject('You have an invitation');
        //         });
        //     }else{
        //         return redirect()->back()->withErrors(['member'=>"The user does not exist"]);
        //     }

        // }else{
        //     return redirect()->back()->withErrors(['member'=>"Link not allowed"]);
        // }

        // return redirect()->route('ensemble.dashboard');
        dd('notmember, welcome');
    }
}
