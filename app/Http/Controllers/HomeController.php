<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\updateInfoUser;
use App\Http\Requests\updateImageUser;
use App\Http\Requests\updatePassUser;
use App\Http\Requests\repertoirRequest;
use App\UserRepertoir;
use App\User_info;
use App\User;
use App\Tag;
use App\Instrument;
use App\Ensemble;
use App\Style;
use App\UserTag;
use App\UserStyle;
use App\UserInstrument;
use App\User_image;
use App\User_video;
use App\User_song;
use App\Member;
use App\Ask;
use Hash;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'confirm', 'view']);
    }

    /**
     * Show the application dashboard.
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
        elseif(Auth::user()->type == 'ensemble') 
        {
            return redirect()->route('ensemble.dashboard');
        }
        else
        {    
            $user = Auth::user()->id;
            //Relation many to many TAGS//
            $IDuser = User::where('id', $user)->firstOrFail();
            $my_tags = $IDuser->user_tags->pluck('id')->toArray();
            //Relation many to many STYLES//
            $my_styles = $IDuser->user_styles->pluck('id')->toArray();
            //Relation many to many INSTRUMENTS//
            $my_instruments = $IDuser->user_instruments->pluck('id')->toArray();
            //Relation many to many//
            $info = User_info::where('user_id', $user)->firstOrFail();
            $tags = Tag::orderBy('name', 'DES')->pluck('name', 'id');
            $instruments = Instrument::orderBy('name', 'DES')->pluck('name', 'id');
            $styles = Style::orderBy('name', 'DES')->pluck('name', 'id');
            $images = User_image::where('user_id', $user)->orderBy('name', 'DES')->pluck('name', 'id');
            $songs = User_song::where('user_id', $user)->get();
            $videos = User_video::where('user_id', $user)->get();
            $repertoires = $IDuser->user_repertoires->all();
            $total_repertoires = UserRepertoir::where('user_id', $user)->where('visible', 1)->count();
            $member_request = Member::where('user_id', $user)->get();
            $asks = Ask::where('user_id', $user)->get();
            $asks_count = Ask::where('user_id', $user)
                             ->where('read', 0)
                             //->where('available', 0)
                             ->count();

            return view('user.dashboard')
                   ->with('info', $info)
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
                   ->with('member_requests', $member_request)
                   ->with('asks', $asks)
                   ->with('asks_count', $asks_count);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(updateInfoUser $request, $id)
    {
        $geometry = substr($request->place_geometry, 1, -1);
        $get_geometry_trimed = explode(", ", $geometry);
        $lat = $get_geometry_trimed[0];
        $lng = $get_geometry_trimed[1];

        $address = 'id:'.$request->place_id.'|address:'.$request->place_address.'|lat:'.$lat.'|long:'.$lng;
        
        $user = Auth::user()->id;
        User_info::where('user_id', $user)
        ->update([
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'about'        => $request->about,
            'bio'          => $request->bio,
            'phone'        => $request->phone,
            'degree'       => $request->degree,
            'address'      => $address,
            'location'     => $request->location,
            'mile_radious' => $request->mile_radious
        ]);
        return redirect()->route('user.dashboard');
    }

    public function updateImage(updateImageUser $request, $id)
    {
        $user = Auth::user()->id;
        if($request->file('image')){
            $file = $request->file('image');
            $name = 'profile_picture_'.time().'.'.$file->getClientOriginalExtension();
            $path = public_path().'/images/profile';
            $file->move($path, $name); 
        }

        User_info::where('user_id', $user)
        ->update([
            'profile_picture'   => $name
        ]);

        return redirect()->route('user.dashboard');
    }

    public function destroyImageUser($image)
    {
        $user = Auth::user()->id;
        User_image::where('user_id', $user)->where('name', $image)->delete();
        return redirect()->route('user.dashboard');
    }

    //View for blocking the main user dashboard
    public function unconfirmed()
    {
        $user = Auth::user()->id;
        $info = User::select('email')->where('id', $user)->firstOrFail();

        if (Auth::user()->confirmed == 0) 
        {
            return view('user.unconfirmed')->with('info', $info);
        } 
        else
        {
            return redirect()->route('user.dashboard');
        }
        
    }

    //This function helps to confirm the user when returns from the email to our page
    public function confirm($confirmation_code)
    {
        $user = User::select('id', 'token', 'confirmed', 'type')
                    ->where('token', $confirmation_code)
                    ->first();   

        if(empty($user))
        {
            return redirect()->back()->withErrors(['token'=>"This token does not exist"]);
        }   
        elseif(!empty($user) and $confirmation_code != $user->token) 
        {
            return redirect()->back()->withErrors(['token'=>"This token does not exist"]);
        }
        elseif(!empty($user) and $confirmation_code = $user->token)
        {
            User::where('id', $user->id)
                ->update([
                    'confirmed' => 1,
                    'token' => null
                ]);

            if ($user->type == 'soloist') {

                $info_user = User_info::select('slug')
                                      ->where('user_id', $user->id)
                                      ->first();

                $slug = str_slug($info_user->slug, "-");

                if (Ensemble::where('slug', '=', $slug)->exists() or User_info::where('slug', '=', $slug)->exists()) {
                    for ($i=1; $i; $i++) { 
                        if (!Ensemble::where('slug', '=', $slug.'-'.$i)->exists() and !User_info::where('slug', '=', $slug.'-'.$i)->exists()) {
                            $slug = $slug.'-'.$i;
                            break;
                        }
                    }
                }else{
                    $slug = $slug;
                }

                User_info::where('user_id', $user->id)
                    ->update([
                        'slug' => $slug
                    ]);

                return redirect()->route('user.dashboard');

            }elseif ($user->type == 'ensemble') {
                
                $ensemble = Ensemble::select('slug')
                                      ->where('user_id', $user->id)
                                      ->first();

                $slug = str_slug($ensemble->slug, "-");

                if (Ensemble::where('slug', '=', $slug)->exists() or User_info::where('slug', '=', $slug)->exists()) {
                    
                    for ($i=1; $i; $i++) { 
                        if (!Ensemble::where('slug', '=', $slug.'-'.$i)->exists() and !User_info::where('slug', '=', $slug.'-'.$i)->exists()) {
                            $slug = $slug.'-'.$i;
                            break;
                       }
                    }
                }else{
                    $slug = $slug;
                }

                Ensemble::where('user_id', $user->id)
                        ->update([
                            'slug' => $slug
                        ]);

                return redirect()->route('ensemble.dashboard'); 
            }
        }
        
    }

    public function storeInstruments(Request $request)
    {
        $user = Auth::user()->id;
        UserInstrument::where('user_id', $user)->delete();
        
        foreach ($request->instruments as $id) 
        {
            $instrument = new UserInstrument($request->all());
            $instrument->user_id = Auth::user()->id;
            $instrument->instrument_id = $id;
            $instrument->save(); 
        }
        return redirect()->route('user.dashboard');
    }

    public function storeTags(Request $request)
    {
        $user = Auth::user()->id;
        UserTag::where('user_id', $user)->delete();
        
        foreach ($request->tags as $id) 
        {
            $tag = new UserTag($request->all());
            $tag->user_id = Auth::user()->id;
            $tag->tag_id = $id;
            $tag->save(); 
        }
        return redirect()->route('user.dashboard');
    }

    public function storeStyles(Request $request)
    {
        $user = Auth::user()->id;
        UserStyle::where('user_id', $user)->delete();
        
        foreach ($request->styles as $id) 
        {
            $style = new UserStyle($request->all());
            $style->user_id = Auth::user()->id;
            $style->style_id = $id;
            $style->save(); 
        }
        return redirect()->route('user.dashboard');
    }

    public function storeImages(Request $request)
    {
        $user = Auth::user()->id;
        $num_img = User_image::where('user_id', $user)->count();
        if ($num_img < 5) {
            $image = new User_image();
            $path = public_path().'/images/general';
            if($request->file('file')){
                $files = $request->file('file');
                foreach($files as $file){
                    $fileName = 'bio'.time().'-'.$file->getClientOriginalName();
                    $file->move($path, $fileName);
                    $image->user_id = $user;
                    $image->name = $fileName;
                    $image->save();
                }
            }
        }  
    }

    public function blocked()
    {
        if (Auth::user()->active == 0) 
        {
            return view('user.blocked');
        } 
        else
        {
            return redirect()->route('user.dashboard');
        }
    }

    public function updatePassUser(updatePassUser $request, $id)
    {
        $input = $request->all();
        $user = User::find($id);
        if(!Hash::check($input['old_password'], $user->password)){
            return redirect()->back()->withErrors(['old_password'=>"That's not your current password, try again"]);
        }else{
            $user->update([
                'password'   => bcrypt($request->password)
            ]);
        }
        return redirect()->route('user.dashboard');
    }

    public function video(Request $request)
    {
        $user = Auth::user()->id;
        $total_videos = User_video::where('user_id', $user)->count();
        if ($total_videos < 5) {

            $video = new User_video($request->all());
            
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
                $video->user_id = $user;
                $video->save();
                //CHEKING IF THE VIDEO EXIST
                // $videos_exist = Youtube_video::where('user_id', $user)->pluck('link');
                
                // if(empty($videos_exist)){
                //     // $youtube->user_id = $user;
                //     // $youtube->save();
                //     dd('no existe y guardando');
                // }else{
                //     foreach ($videos_exist as $video_exist) {
                //         if($video_exist == $youtube->link)
                //         {
                //             dd('el video ya esta repetido');
                //             // return redirect()->back()->withErrors(['video'=>"You already added this video"]);
                //         }else{
                //             dd('ya hay video pero no repetido');
                //             // $youtube->user_id = $user;
                //             // $youtube->save();
                //         }
                //     }
                // }

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
                $video->user_id = $user;
                $video->save();

            }else{
                return redirect()->back()->withErrors(['video'=>"That is not an allowed link or video"]);
            }
        }else{
            return redirect()->back()->withErrors(['video'=>"You only can add 5 videos in total"]);
        }
        return redirect()->route('user.dashboard');
    }

    public function delete_video($id)
    {
        $video = User_video::find($id)->delete();
        return redirect()->route('user.dashboard');
    }

    public function song(Request $request)
    {
        $user = Auth::user()->id;
        $total_songs = User_song::where('user_id', $user)->count();

        if ($total_songs < 5) {

            $song = new User_song($request->all());
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
                $song->user_id = $user;
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
                $song->user_id = $user;
                $song->save();

            }else{
                return redirect()->back()->withErrors(['song'=>"That is not an allowed link or song"]);
            }
        }else{
            return redirect()->back()->withErrors(['song'=>"You only can add 5 songs in total"]);
        }
        return redirect()->route('user.dashboard');
    }


    public function delete_song($id)
    {
        $song = User_song::find($id)->delete();
        return redirect()->route('user.dashboard');
    }

    public function repertoir(repertoirRequest $request)
    {   
        $repertoir = new UserRepertoir($request->all());
        $repertoir->user_id = Auth::user()->id;
        $repertoir->repertoire_example = $request->repertoir;
        $repertoir->visible = 0;
        $repertoir->save();
        return redirect()->route('user.dashboard');
    }

    public function destroy_repertoir($id)
    {
        $repertoir = UserRepertoir::find($id)->delete();
        return redirect()->route('user.dashboard');
    }

    public function update_repertoir($id)
    {
        $repertoir = UserRepertoir::select('visible')->find($id);
        $repertoir->visible = !$repertoir->visible;
        UserRepertoir::find($id)->update(['visible' => $repertoir->visible]);
        return redirect()->route('user.dashboard');
    }

    public function ask_review($id)
    {
        User::where('id', $id)->update(['ask_review' => 1]);
        return redirect()->route('user.dashboard');
    }

    public function details_request($id)
    {
        $ask = Ask::where('id', $id)->firstOrFail();
        if ($ask->read == 0) {
            Ask::where('id', $id)
                ->update([
                    'read' => 1,
                ]);
        }
        return view('user.details')->with('request', $ask);
    }

}
