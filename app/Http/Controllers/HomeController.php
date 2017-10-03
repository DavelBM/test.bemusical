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
use App\GigOption;
use Hash;
use Auth;
use Storage;
use stdClass;
use Validator;

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
            $IDuser = User::where('id', $user)->firstOrFail();
            $options = $IDuser->gig_option;
            if ($options == null) {
                $save_new_options = new GigOption;
                $save_new_options->user_id = Auth::user()->id;
                $save_new_options->listDay = 'listDay';
                $save_new_options->listWeek = 'listWeek';
                $save_new_options->month = 'month';
                $save_new_options->start = '08:00';
                $save_new_options->end = '22:00';
                $save_new_options->save();
            }
            //Relation many to many TAGS//
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
            $images = User_image::where('user_id', $user)->orderBy('name', 'DES')->get();
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

    public function updateImage(Request $request, $id)
    {
        $info = [];

        $validator = Validator::make($request->all(), [
            'image' => 'image|required',
        ]);

        if ($validator->fails()) {
            $update_profile_photo_object = new stdClass();
            $update_profile_photo_object->status ='<strong style="color: red;">Select an image</strong>';
            $info[] = $update_profile_photo_object;
            return response()->json(array('info' => $info), 200);
        } else {

            $user = Auth::user()->id;
            if($request->file('image')){
                $file = $request->file('image');
                $name = 'profile_picture_'.time().'-'.$file->getClientOriginalName();
                $path = public_path().'/images/profile';
                $file->move($path, $name); 
            }

            User_info::where('user_id', $user)
            ->update([
                'profile_picture'   => $name
            ]);

            $update_profile_photo_object = new stdClass();
            $update_profile_photo_object->status ='<strong style="color: green;">Updated</strong>';
            $update_profile_photo_object->name = $name;
            $info[] = $update_profile_photo_object;

            return response()->json(array('info' => $info), 200);

        }
    }

    public function destroyImageUser($image)
    {
        $info = [];
        $user = Auth::user()->id;
        $get_name = User_image::select('user_id','name')->where('id', $image)->first();
        if ($get_name->user_id == $user) { 
            User_image::where('user_id', $user)->where('id', $image)->delete();
            $delete_photo_object = new stdClass();
            $get_name_array = explode("|", $get_name->name);
            $delete_photo_object->status = $get_name_array[1].' <strong style="color: red;">deleted successfully</strong>';
            $delete_photo_object->idImg = $image;
            $info[] = $delete_photo_object;
            return response()->json(array('info' => $info), 200);
        } else {
            $delete_photo_object = new stdClass();
            $delete_photo_object->status = 'Action no permitted';
            $info[] = $delete_photo_object;
            return response()->json(array('info' => $info), 200);
        }

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
        $instruments = [];
        $user = Auth::user()->id;
        UserInstrument::where('user_id', $user)->delete();
        
        foreach ($request->instruments as $id) 
        {
            $instrument = new UserInstrument($request->all());
            $instrument->user_id = $user;
            $instrument->instrument_id = $id;
            $instrument->save(); 
        }

        $instrument_object = new stdClass();
        $instrument_object->status ='guardado';
        $instrument_object->data = $request->instruments;
        $instruments[] = $instrument_object;
        return response()->json(array('instruments' => $instruments), 200);
    }

    public function storeTags(Request $request)
    {
        $tags = [];
        $user = Auth::user()->id;
        UserTag::where('user_id', $user)->delete();
        
        foreach ($request->tags as $id) 
        {
            $tag = new UserTag($request->all());
            $tag->user_id = $user;
            $tag->tag_id = $id;
            $tag->save(); 
        }

        $tag_object = new stdClass();
        $tag_object->status ='guardado';
        $tag_object->data = $request->tags;
        $tags[] = $tag_object;
        return response()->json(array('tags' => $tags), 200);
    }

    public function storeStyles(Request $request)
    {
        $styles = [];
        $user = Auth::user()->id;
        UserStyle::where('user_id', $user)->delete();
        
        foreach ($request->styles as $id) 
        {
            $style = new UserStyle($request->all());
            $style->user_id = $user;
            $style->style_id = $id;
            $style->save(); 
        }

        $style_object = new stdClass();
        $style_object->status ='guardado';
        $style_object->data = $request->styles;
        $styles[] = $style_object;
        return response()->json(array('styles' => $styles), 200);
    }

    public function storeImages(Request $request)
    {
        $photos = [];

        $validator = Validator::make($request->all(), [
            'photos' => 'array|required',
        ]);

        if ($validator->fails()) {
            $photo_object = new stdClass();
            $photo_object->status ='<strong style="color: red;">Select an image</strong>';
            $photo_object->failed = 'true';
            $photo[] = $photo_object;
            return response()->json(array('files' => $photos), 200);
        } else {

            $imageRules = array(
                'photos' => 'image'
            );

            $user = Auth::user()->id;
            $num_img = User_image::where('user_id', $user)->count();
            
            if ($num_img < 5) {
                //dd('entre al primer filtro');
                $path = public_path().'/images/general';
                foreach ($request->photos as $photo) {
                    $photo = array('photos' => $photo);
                    $imageValidator = Validator::make($photo, $imageRules);
                    if ($imageValidator->fails()) {
                        //dd('esto fallo');
                        $photo_object = new stdClass();
                        $photo_object->status ='<strong style="color: red;">'.$photo['photos']->getClientOriginalName().' is not an image</strong>';
                        $photo_object->failed = 'true';
                        $photos[] = $photo_object;
                        break;
                    } else {
                        //dd($photo['photos']->getClientOriginalName());
                        $filename = 'user_bio_'.time().'|'.$photo['photos']->getClientOriginalName();
                        $photo['photos']->move($path, $filename);

                        $user_photo = new User_image();
                        $user_photo->user_id = $user;
                        $user_photo->name = $filename;
                        $user_photo->save();

                        $new_num_img = User_image::where('user_id', $user)->count();
                        if ($new_num_img < 5) {
                            $photo_object = new stdClass();
                            $photo_object->name = str_replace('photos/', '',$photo['photos']->getClientOriginalName());
                            $photo_object->fileName = $user_photo->name;
                            $photo_object->fileID = $user_photo->id;
                            $photo_object->status = '<strong style="color: green;">Saved successfully</strong>';
                            $photos[] = $photo_object;
                        }else{
                            $photo_object = new stdClass();
                            $photo_object->status = 'You just can add 5 pictures';
                            $photos[] = $photo_object;
                            break;
                        }
                    }
                }
                return response()->json(array('files' => $photos), 200); 
            } else {
                $photo_object = new stdClass();
                $photo_object->status = 'You just can add 5 pictures';
                $photos[] = $photo_object;
                return response()->json(array('files' => $photos), 200);
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
        $videos = [];
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
                    //return redirect()->back()->withErrors(['video'=>"That is not an allowed link or video"]);
                    $video_object = new stdClass();
                    $video_object->status = '<strong style="color: red;">That is not an allowed link or video</strong>';
                    $video_object->flag = '0';
                    $videos[] = $video_object;
                    return response()->json(array('videos' => $videos), 200);
                }

                $video->platform = 'youtube';
                $video->user_id = $user;
                $video->save();
            //CHECK IF THIS IS A VIDEO FROM VIMEO
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
                    //return redirect()->back()->withErrors(['video'=>"That is not an allowed link or video"]);
                    $video_object = new stdClass();
                    $video_object->status = '<strong style="color: red;">That is not an allowed link or video</strong>';
                    $video_object->flag = '0';
                    $videos[] = $video_object;
                    return response()->json(array('videos' => $videos), 200);
                }    

                $video->platform = 'vimeo';
                $video->user_id = $user;
                $video->save();

            }else{
                //return redirect()->back()->withErrors(['video'=>"That is not an allowed link or video"]);
                $video_object = new stdClass();
                $video_object->status = '<strong style="color: red;">That is not an allowed link or video</strong>';
                $video_object->flag = '0';
                $videos[] = $video_object;
                return response()->json(array('videos' => $videos), 200);
            }
        }else{
            //return redirect()->back()->withErrors(['video'=>"You only can add 5 videos in total"]);
            $video_object = new stdClass();
            $video_object->status = '<strong style="color: red;">You only can add 5 videos in total</strong>';
            $video_object->flag = '0';
            $videos[] = $video_object;
            return response()->json(array('videos' => $videos), 200);
        }
        $video_object = new stdClass();
        $video_object->status = '<strong style="color: green;">Video successfully added</strong>';
        $video_object->flag = '1';
        $video_object->code = $video->code;
        $video_object->platform = $video->platform;
        $video_object->id = $video->id;
        $videos[] = $video_object;
        return response()->json(array('videos' => $videos), 200);
        //return redirect()->route('user.dashboard');
    }

    public function delete_video($id)
    {
        $info = [];
        $video = User_video::find($id);
        if ($video->user_id == Auth::user()->id) {
            $video->delete();
            $delete_song_object = new stdClass();
            $delete_song_object->status = '<strong style="color: red;">video deleted successfully</strong>';
            $delete_song_object->id = $id;
            $info[] = $delete_song_object;
            return response()->json(array('info' => $info), 200);
        } else {
            $delete_video_object = new stdClass();
            $delete_video_object->status = 'Action no permitted';
            $info[] = $delete_video_object;
            return response()->json(array('info' => $info), 200);
        }
    }

    public function song(Request $request)
    {
        $songs = [];
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
                    $song_object = new stdClass();
                    $song_object->status = '<strong style="color: red;">That is not an allowed link or song</strong>';
                    $song_object->flag = '0';
                    $songs[] = $song_object;
                    return response()->json(array('songs' => $songs), 200);
                    //return redirect()->back()->withErrors(['song'=>"Link not allowed"]);
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
                    $song_object = new stdClass();
                    $song_object->status = '<strong style="color: red;">That is not an allowed link or song</strong>';
                    $song_object->flag = '0';
                    $songs[] = $song_object;
                    return response()->json(array('songs' => $songs), 200);
                    //return redirect()->back()->withErrors(['song'=>"Link not allowed"]);
                }     
                $song->platform = 'soundcloud';   
                $song->user_id = $user;
                $song->save();

            }else{
                $song_object = new stdClass();
                $song_object->status = '<strong style="color: red;">That is not an allowed link or song</strong>';
                $song_object->flag = '0';
                $songs[] = $song_object;
                return response()->json(array('songs' => $songs), 200);
                //return redirect()->back()->withErrors(['song'=>"That is not an allowed link or song"]);
            }
        }else{
            $song_object = new stdClass();
            $song_object->status = '<strong style="color: red;">You only can add 5 songs in total</strong>';
            $song_object->flag = '0';
            $songs[] = $song_object;
            return response()->json(array('songs' => $songs), 200);
            //return redirect()->back()->withErrors(['song'=>"You only can add 5 songs in total"]);
        }
        $song_object = new stdClass();
        $song_object->status = '<strong style="color: green;">song successfully added</strong>';
        $song_object->flag = '1';
        $song_object->code = $song->code;
        $song_object->platform = $song->platform;
        $song_object->id = $song->id;
        $songs[] = $song_object;
        return response()->json(array('songs' => $songs), 200);
        //return redirect()->route('user.dashboard');
    }


    public function delete_song($id)
    {
        $info = [];
        $song = User_song::find($id);
        if ($song->user_id == Auth::user()->id) {
            $song->delete();
            $delete_song_object = new stdClass();
            $delete_song_object->status = '<strong style="color: red;">song deleted successfully</strong>';
            $delete_song_object->id = $id;
            $info[] = $delete_song_object;
            return response()->json(array('info' => $info), 200);
        } else {
            $delete_song_object = new stdClass();
            $delete_song_object->status = 'Action no permitted';
            $info[] = $delete_song_object;
            return response()->json(array('info' => $info), 200);
        }
    }

    public function repertoir(Request $request)
    {   
        $info = [];
        $validator = Validator::make($request->all(), [
            'repertoir' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            $repertoir_object = new stdClass();
            $repertoir_object->status = '<strong style="color: red;"> 50 is the max number of caracters</strong>';
            $info[] = $repertoir_object;
            return response()->json(array('info' => $info), 200);
        } else {
            $repertoir = new UserRepertoir($request->all());
            $repertoir->user_id = Auth::user()->id;
            $repertoir->repertoire_example = $request->repertoir;
            $repertoir->visible = 0;
            $repertoir->save();

            $repertoir_count = UserRepertoir::where('user_id', Auth::user()->id)->where('visible', 1)->count();

            $repertoir_object = new stdClass();
            $repertoir_object->status = '<strong style="color: green;">Repertoir "'.$request->repertoir.'" successfully added</strong>';
            $repertoir_object->name = $request->repertoir;
            $repertoir_object->id = $repertoir->id;
            $repertoir_object->count = $repertoir_count;
            $info[] = $repertoir_object;
            return response()->json(array('info' => $info), 200);
        }
    }

    public function destroy_repertoir($id)
    {
        $info = [];
        $repertoir = UserRepertoir::find($id);
        if ($repertoir->user_id == Auth::user()->id) {
            $repertoir->delete();
            $delete_repertoir_object = new stdClass();
            $delete_repertoir_object->status = '<strong style="color: red;">Repertoir deleted successfully</strong>';
            $delete_repertoir_object->id = $id;
            $info[] = $delete_repertoir_object;
            return response()->json(array('info' => $info), 200);
        } else {
            $delete_repertoir_object = new stdClass();
            $delete_repertoir_object->status = 'Action no permitted';
            $info[] = $delete_repertoir_object;
            return response()->json(array('info' => $info), 200);
        }
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
        $user = Auth::user()->id;
        $ask = Ask::where('id', $id)->where('user_id', $user)->first();
        if (empty($ask)) {
            return redirect()->back();
        }else{
            if ($ask->read == 0) {
                Ask::where('id', $id)
                    ->update([
                        'read' => 1,
                    ]);
            }
            return view('user.details')->with('request', $ask);
        }
    }
    
}
