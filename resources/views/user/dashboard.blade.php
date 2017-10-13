@extends('layouts.app')

@section('logout')
    @if(Auth::guard('web')->check())
        <a href="{{ url('/user/logout') }}">Logout user</a>
    @endif
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                
                @if($info->first_name == 'null' or $info->first_name == 'null')
                    <div class="panel-heading">Soloist dashboard,
                @else
                    <div class="panel-heading">Welcome {{$info->first_name}},
                @endif
                
                @if($info->user->visible == 0 and $info->user->ask_review == 0)
                    your profile is not available to watch until you fill with your data correctly<a href="{{ route('user.ask.review', $info->user->id) }}" class="btn btn-danger">Ask to the admin if my profile is ready</a></div>
                @elseif($info->user->visible == 0 and $info->user->ask_review == 1)
                    our team are reviewing your perfil, wait for our response. In case everything is okay, we will active you account automaticatly</div>
                @else
                        <div class="btn-group pull-right">
                            <a class="btn btn-pimary" href="{{ route('index.calendar') }}">My calendar</a>
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Requests <span class="badge">
                                    {{$asks_count}}
                                </span><span class="caret">
                            </button>
                            <ul class="dropdown-menu">
                                @foreach($asks as $ask)
                                    @php
                                        $dt = explode("|", $ask->date);
                                    @endphp
                                    <li>
                                        <a href="{{ route('details.request', $ask->id) }}">
                                            @if($ask->read == 0)
                                                <span class="badge">
                                                    new!
                                                </span> 
                                            @elseif($ask->available == 0 and $ask->nonavailable != 0 and $ask->read == 1)
                                                <span class="badge">
                                                    not accepted!
                                                </span>
                                            @elseif($ask->available != 0 and $ask->nonavailable == 0 and $ask->read == 1)
                                                <span class="badge">
                                                    accepted!
                                                </span>
                                            @elseif($ask->available != 0 or $ask->nonavailable != 0)
                                                <span class="badge">
                                                    new!
                                                </span>
                                            @else
                                                <span class="badge">
                                                    not answered!
                                                </span>
                                            @endif
                                            On {{$dt[1]}}<span class="glyphicon glyphicon-menu-right">
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                @include('flash::message')
                <div class="panel-body">
                    <div class="row">
                        @if(!empty($member_requests))
                            @foreach($member_requests as $request)
                                @if($request->confirmation == 0)
                                    <strong>Requests for ensembles</strong>
                                    <br>You have a request from "<a class="btn" href="{{ URL::to('/'.$request->ensemble->slug) }}">{{$request->ensemble->name}}</a>"
                                    <br>
                                    <form class="form-horizontal" method="POST" action="{{ route('member.add.instrument') }}">
                                        {{ csrf_field() }}

                                        <div class="form-group{{ $errors->has('instrument') ? ' has-error' : '' }}">
                                            <label for="instrument" class="col-md-4 control-label">Instrument</label>

                                            <div class="col-md-6">
                                                <input id="instrument" type="instrument" class="form-control" name="instrument" value="{{ old('instrument') }}" placeholder="example: piano, guitar, bass, microphone." required>

                                                @if ($errors->has('instrument'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('instrument') }}</strong>
                                                    </span>
                                                @endif

                                        </div>

                                        <input id="id" type="hidden" class="form-control" name="id" value="{{$request->id}}">

                                        <div class="form-group">
                                            <div class="col-md-6 col-md-offset-4">
                                                <button type="submit" class="btn btn-success">
                                                    Accept
                                                </button><a href="{{ route('ensemble.member.destroy', $request->id) }}" class="btn btn-danger">Decline</a>
                                            </div>
                                        </div>
                                        <p class="mute"><i>We already sent you an email. If you accept this request, you do not need to open that email.</i></p>
                                    </form>
                                    <hr>
                                @else
                                    Your ensembles:<br>
                                    "<a class="btn" href="{{ URL::to('/'.$request->ensemble->slug) }}">{{$request->ensemble->name}}</a>"<a href="{{ route('ensemble.member.destroy', $request->id) }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <div class="row">

                        @if($errors->has('token'))
                            <span class="help-block">
                                <strong style="color: red;">{{ $errors->first('token') }}</strong>
                            </span>
                        @elseif(!$errors->isEmpty())
                            <span class="help-block">
                                <strong style="color: red;">We had a problem while we was updating the info, check again</strong>
                            </span>
                        @endif
                        
                        <div class="col-md-5">
                            @if($info->profile_picture != 'null')
                                <img id="profile_picture_user" src="{{ asset("images/profile/$info->profile_picture") }}" class="img-circle float-left" alt="{{$info->profile_picture}}" width="250" height="250">
                            @else
                                <img id="profile_picture_user" src="{{ asset("images/profile/no-image.png") }}" class="img-circle float-left" alt="No image">
                            @endif
                            <!-- <form action="/user/{{$info->id}}/image" method="post" enctype="multipart/form-data"> -->
                            <p id="loading_update_user_pic"></p>
                            <p id="status_upload_update_user_image"></p>
                            <!-- Form for upload profile picture -->
                                <form method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}Update Profile Picture:<br />
                                    <input type="file" id="fileupload_update_user_image" name="image" data-url="/user/{{$info->id}}/image" multiple />
                                    <!-- <input type="file" name="image">                     -->
                                    <!-- <input type="submit" name="submit"> -->
                                </form>
                            <!-- /Form for upload profile picture -->
                        </div>
                        <div class="col-md-7">
                            <!-- Displaying data -->
                            @php
                                if ((!strpos($info->address, 'id:') and !strpos($info->address, 'address:') and !strpos($info->address, 'lat:') and !strpos($info->address, 'long:')) or $info->address==null) {
                                    $info->address = "id:no-addres|address:no-address|lat:0|long:0";
                                }
                                $data = explode("|", $info->address);
                                $data_id = explode("id:", $data[0]);
                                $data_address = explode("address:", $data[1]);
                                $data_lat = explode("lat:", $data[2]);
                                $data_long = explode("long:", $data[3]);
                            @endphp
                            <strong>Name:</strong> {{$info->first_name." ".$info->last_name}}<br>
                            <strong>username*:</strong> {{$info->slug}}<br>
                            <strong>url*:</strong> <a href="{{URL::to('/'.$info->slug)}}">bemusical.us/{{$info->slug}}</a><br>
                            <strong>e-mail*:</strong> {{$info->user->email}}<br>
                            <strong>Bio summary:</strong> {{$info->bio}}<br>
                            <strong>My Address:</strong> {{$data_address[1]}}<br>
                            @if($phone->confirmed == 0)
                                @if($phone->phone == 0)
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#phoneModal">
                                    Send my phone
                                </button>
                                @else
                                <strong>My phone:</strong> {{$phone->country_code}}{{$phone->phone}}<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#phoneModal">
                                    Confirm my phone
                                </button>
                                @endif
                            @else
                                <strong>My phone:</strong> {{$phone->country_code}}{{$phone->phone}} <strong style="color: green;">Confirmed</strong>
                                @if($minutes >= 15)
                                {!! Form::open(['route' => 'user.reset.phone', 'id' => 'phone-form', 'method' => 'POST']) !!}
                                    <input type="submit" class="btn btn-xs btn-warning" value="Reset phone">
                                {!! Form::close() !!}
                                @endif
                            @endif
                            <br>
                            <strong>Location:</strong> {{$info->location}}<br>
                            <strong>Degree:</strong> {{$info->degree}}<br>
                            <strong>Mile Radius:</strong> {{$info->mile_radious}} miles<br>
                            <strong>About Me:</strong> {!!$info->about!!}<br>
                            <!-- /Displaying data -->

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="row col-md-3">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateModal">
                                    Edit profile
                                </button>

                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#passModal">
                                    Edit password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="panel-body">
                    <div class="row">
                        <!-- Add instruments -->
                        <div class="col-md-3">
                            <div class="form-group col-md-12">
                                {!! Form::label('instruments', 'Instruments', ['class' => 'control-label']) !!}<br>
                                {!! Form::select('instruments[]', $instruments, $my_instruments, ['id'=>'select-instrument','class'=>'form-control', 'multiple', 'required']) !!}
                            </div>
                        </div>
                        <!-- /Add instruments -->

                        <!-- Add styles -->
                        <div class="col-md-3">
                            <div class="form-group col-md-12">
                                {!! Form::label('styles', 'Styles', ['class' => 'control-label']) !!}<br>
                                {!! Form::select('styles[]', $styles, $my_styles, ['id'=>'select-style','class'=>'form-control', 'multiple', 'required']) !!}
                            </div>
                        </div>
                        <!-- /Add styles -->

                        <!-- Add tags -->
                        <div class="col-md-3">
                            <div class="form-group col-md-12">
                                {!! Form::label('tags', 'Tags', ['class' => 'control-label']) !!}<br>
                                {!! Form::select('tags[]', $tags, $my_tags, ['id'=>'select-tag','class'=>'form-control', 'multiple', 'required']) !!}
                            </div>
                        </div>
                        <!-- /Add tags -->

                    </div>
                </div>
                <hr>
                <div class="panel-body">
                    <strong>BIO IMAGES</strong>
                </div>
                <div class="panel-body">
                    <!-- <form action="/add/image" method="post" enctype="multipart/form-data"> -->
                    <form method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        You can add more than one picture(MAX 5):
                        <br />
                        <input type="file" id="fileupload" name="photos[]" data-url="/add/image" multiple />
                        <!-- <input type="file" name="photos[]"> -->
                        <br />
                        <p id="loading"></p>
                        <div id="images_user_profile">
                            <div id="files_list"></div>
                        </div>
                        <p id="status_upload"></p>
                        <input type="hidden" name="file_ids" id="file_ids" value="" />
                        <!-- <input type="submit" name="submit"> -->
                    </form>
                        <!-- Displaying images -->
                        @foreach($images as $image)
                        <div id="image_user_{{$image->id}}" class="col-md-12">
                            <img src="{{ asset("images/general/$image->name") }}" class="img-rounded" alt="{{$image->name}}" width="304" height="236"><button class="btn btn-danger" onclick="destroyImg({{$image->id}}); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                        </div>
                        <p id="status_deleting_img_{{$image->id}}"></p>
                        @endforeach
                        <!-- /Displaying images -->
                </div>

                <div class="panel-body">
                    <form id="sendVideo" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row form-group">
                            <label for="video" class="col-md-4 control-label">Videos links:</label>
                            <div class="col-md-6">
                                <input id="video" type="text" class="form-control" name="video" placeholder="Youtube or Vimeo videos are admitted" required>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Add video', ['class' => 'btn btn-primary btn-block']) !!}
                        </div>
                        <p id="videoLodingForAdd"></p>
                        <p id="videoSuccessfullyAdded"></p>
                    </form> 
                    <form id="sendSong" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row form-group">
                            <label for="song" class="col-md-4 control-label">Songs links:</label>
                            <div class="col-md-6">
                                <input id="song" type="text" class="form-control" name="song" placeholder="Spotify or Soundcloud songs are admitted" required>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Add song', ['class' => 'btn btn-primary btn-block']) !!}
                        </div>
                        <p id="songLodingForAdd"></p>
                        <p id="songSuccessfullyAdded"></p>
                    </form> 
                    <div class="row">
                        <strong>VIDEOS</strong>
                        <div class="col-md-12" id="videos_user_profile"></div>
                        @foreach($videos as $video)
                            <div class="col-md-12">
                                @if($video->platform == 'youtube')
                                    <div id="video_user_{{$video->id}}"><iframe width="100%" height="315" src="https://www.youtube.com/embed/{{$video->code}}" frameborder="0" allowfullscreen></iframe>
                                @elseif($video->platform == 'vimeo')
                                    <div id="video_user_{{$video->id}}"><iframe src="https://player.vimeo.com/video/{{$video->code}}" width="100%" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                @endif
                                <button class="btn btn-danger" onclick="destroyVideo('{{$video->id}}'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div>
                                <p id="status_deleting_video_user_{{$video->id}}"></p>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <strong>SONGS</strong>
                        <div class="col-md-12" id="songs_user_profile"></div>
                        @foreach($songs as $song)
                            <div class="col-md-12">
                                @if($song->platform == 'spotify')
                                    <div id="song_user_{{$song->id}}"><iframe id="song_user_{{$song->id}}" src="https://open.spotify.com/embed?uri=spotify:track:{{$song->code}}&theme=white&view=coverart" 
                                    frameborder="0" allowtransparency="true"></iframe>
                                @elseif($song->platform == 'soundcloud')
                                    <div id="song_user_{{$song->id}}">
                                    <iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/{{$song->code}}&amp;color=0066cc&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe>
                                @endif
                                <button class="btn btn-danger" onclick="destroySong('{{$song->id}}'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div>
                                <p id="status_deleting_song_user_{{$song->id}}"></p>
                            </div>
                        @endforeach                        
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <form id="sendRepertoir" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row form-group">
                            <label for="repertoir" class="col-md-2 control-label">Briefly explain:</label>
                            <div class="col-md-10">
                                <div class="col-md-6">
                                    <input id="composer" type="text" class="form-control" name="composer" placeholder="Add composer" required>
                                </div>
                                <div class="col-md-6">
                                    <input id="work" type="text" class="form-control" name="work" placeholder="Add work" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Add repertoir', ['class' => 'btn btn-primary btn-block']) !!}
                        </div>
                        <p id="repertoirLodingForAdd"></p>
                        <p id="repertoirSuccessfullyAdded"></p>
                    </form>  
                    <div class="row">
                        <strong>Repertoir</strong>
                        <div class="col-md-12" id="repertoir_user_profile"></div>
                        @foreach($repertoires as $repertoir)
                            <div id="repertoir_user_{{$repertoir->id}}">*{{ $repertoir->repertoire_example }}<button class="btn btn-danger"><span class="glyphicon glyphicon-remove" onclick="destroyRepertoir({{$repertoir->id}})" aria-hidden="true"></span></button>
                            @if(!$repertoir->visible and $total_repertoires < 5)
                                <a href="{{ route('user.repertoir.update', $repertoir->id) }}" class="btn btn-success">Make it visible</a></div>
                            @elseif($repertoir->visible)
                                <a href="{{ route('user.repertoir.update', $repertoir->id) }}" class="btn btn-danger">Hide it</a></div>
                            @endif
                            <p id="status_deleting_repertoir_user_{{$repertoir->id}}"></p>
                        @endforeach                        
                    </div>                  
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Data -->
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update your info
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h5>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => ['user.update', $info->id], 'id' => 'update-form', 'method' => 'PUT']) !!}
                    <div class="row form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                        {!! Form::label('first_name', 'First Name', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('first_name', $info->first_name, ['class'=>'form-control', 'placeholder'=>'Type your first name', 'required']) !!}
                            @if ($errors->has('first_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                        {!! Form::label('last_name', 'Last Name', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('last_name', $info->last_name, ['class'=>'form-control', 'placeholder'=>'Type your last name', 'required']) !!}
                            @if ($errors->has('last_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('last_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('bio') ? ' has-error' : '' }}">
                        {!! Form::label('bio', 'Bio', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('bio', $info->bio, ['class'=>'form-control', 'placeholder'=>'Tell us something amazing', 'required']) !!}
                            @if ($errors->has('bio'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('bio') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="address" class="col-md-4 control-label">My address<p class="text-muted">Powered by google</p></label>

                        <div class="col-md-6">
                            <input id="searchTextField" type="text" class="form-control" name="address" value="{{$data_address[1]}}" required>
                            @if ($errors->has('address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                            @endif
                            @if ($errors->has('place_id'))
                                <span class="help-block">
                                    <strong style="color: red;">Please pick a place with google suggestions</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('degree') ? ' has-error' : '' }}">
                        {!! Form::label('degree', 'Education', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('degree', $info->degree, ['class'=>'form-control', 'placeholder'=>'Do you have any education?', 'required']) !!}
                            @if ($errors->has('degree'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('degree') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('mile_radious') ? ' has-error' : '' }}">
                        {!! Form::label('mile_radious', 'Travel mile radius', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::select('mile_radious', array($info->mile_radious => $info->mile_radious, '1' => '1 mile', '5' => '5 miles', '10' => '10 miles', '20' => '20 miles', '50' => '50 miles', '100' => '100 miles')); !!}
                            @if ($errors->has('mile_radious'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('mile_radious') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('about') ? ' has-error' : '' }}">
                        {!! Form::label('about', 'About', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::textarea('about', $info->about, ['class'=>'form-control', 'placeholder'=>'Tell to the world about you', 'required']) !!}
                            @if ($errors->has('about'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('about') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <input id="place-id" type="hidden" name="place_id" value="{{$data_id[1]}}" required>
                    <input id="place-address" type="hidden" name="place_address" value="{{$data_address[1]}}" required>
                    <input id="place-geometry" type="hidden" name="place_geometry" value="({{$data_lat[1]}}, {{$data_long[1]}})" required>
                    <input id="location" type="hidden" name="location" value="{{$info->location}}" required>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <a href="{{ route('user.update', $info->id) }}"
                   class="btn btn-primary" 
                   onclick="event.preventDefault();
                   document.getElementById('update-form').submit();">Update data</a>
            </div>
        </div>       
    </div>
</div>
<!-- /Modal Data -->

<!-- Modal Password -->
<div class="modal fade" id="passModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update password
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h5>
            </div>

            <div class="modal-body">
                
                {!! Form::open(['route' => ['user.updatePassUser', $info->user_id], 'id' => 'pass-form', 'method' => 'PUT']) !!}

                    <div class="row form-group{{ $errors->has('old_password') ? ' has-error' : '' }}">
                        <label for="old_password" class="col-md-4 control-label">Current password</label>

                        <div class="col-md-6">
                            <input id="old_password" type="password" class="form-control" name="old_password" required>

                            @if ($errors->has('old_password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('old_password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-4 control-label">New password</label>

                        <div class="col-md-6">
                            <input id="password" type="password" class="form-control" name="password" required>

                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="password-confirm" class="col-md-4 control-label">Confirm new password</label>

                        <div class="col-md-6">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>

                {!! Form::close() !!}

            </div>

            <div class="modal-footer">
                <a href="{{ route('user.updatePassUser', $info->user_id) }}"
                   class="btn btn-primary" 
                   onclick="event.preventDefault();
                   document.getElementById('pass-form').submit();">Update password</a>
            </div>
        </div>       
    </div>
</div>
<!-- /Modal Password -->

<!-- Modal PHONE -->
<div class="modal fade" id="phoneModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Confirm your phone
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h5>
            </div>
            @if($phone->phone == 0)
                <div class="modal-body">
                    {!! Form::open(['route' => 'user.send.phone', 'id' => 'phone-form', 'method' => 'POST']) !!}
                        <div class="row form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                            {!! Form::label('phone', 'Phone', ['class' => 'col-md-4 control-label']) !!}
                            <div class="input-group col-md-6">
                                <span>
                                    <select class="form-control" name="country">
                                        @if($phone->country != 'null')
                                            <option value="{{$phone->country_code}}|{{$phone->country}}" selected="selected">
                                            {{$phone->country}}</option>
                                            <option value="+1|United States">United States</option>
                                            <option value="">-</option>
                                            @foreach($codes as $code)
                                                @if($code->country != 'United States' or $code->country != $phone->country)
                                                    <option value="+{{$code->code}}|{{$code->country}}">{{$code->country}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="+1|United States" selected="selected">United States</option>
                                            <option value="">-</option>
                                            @foreach($codes as $code)
                                                @if($code->country != 'United States')
                                                    <option value="+{{$code->code}}|{{$code->country}}">{{$code->country}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="help-block">
                                        <strong>{{ $errors->first('country') }}</strong>
                                    </span>
                                </span>
                                {!! Form::number('phone', null, ['class'=>'form-control', 'placeholder'=>"What's your contact number", 'required']) !!}
                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="input-group col-md-6">
                            <input type="submit" class="btn btn-primary" value="Send my phone">
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            @else
            <div class="modal-body">
                <p id="phoneCodeStatus"></p>

                {!! Form::open(['route' => 'user.confirm.phone', 'id' => 'phone-form', 'method' => 'POST']) !!}

                    <div class="row form-group{{ $errors->has('_c_phone') ? ' has-error' : '' }}">
                        <label for="_c_phone" class="col-md-4 control-label">Phone code</label>

                        <div class="col-md-6">
                            <input id="_c_phone" type="number" class="form-control" name="_c_phone" required>
                        </div>
                        @if ($errors->has('_c_phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('_c_phone') }}</strong>
                                </span>
                        @endif
                    </div>

                    <a href="{{ route('user.confirm.phone') }}"
                    class="btn btn-primary" 
                    onclick="event.preventDefault();
                    var x = document.forms['phone-form']['_c_phone'].value;
                    if (x == '') {
                        alert('Ask for your code');
                        return false;
                    }else{
                        document.getElementById('phone-form').submit();
                    }
                   ">Send code</a>

                {!! Form::close() !!}

            </div>
            <!-- <form class="form-horizontal" method="POST" action="{{ route('user.send.code.phone') }}">
            {{ csrf_field() }}
            <input type="submit" value="confirm"></input>
            </form> -->
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="askPhoneCode()">Ask for code</button>
            </div>
            @endif
        </div>       
    </div>
</div>
<!-- /Modal PHONE -->

@endsection

@section('js')
    <script src="/js/jquery.ui.widget.js"></script>
    <script src="/js/jquery.iframe-transport.js"></script>
    <script src="/js/jquery.fileupload.js"></script>
    <script src="/chosen/chosen.jquery.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&v=3.exp&libraries=places"></script>

    <script type="text/javascript">

    function askPhoneCode(){
        $.ajax({
            type: "POST",
            url: "/user/send/code/phone",
            data: {
                "_token": "{{ csrf_token() }}",
            },
            dataType: 'json',
            beforeSend: function(){
                $('#phoneCodeStatus').text('Loading...');
            },
            success: function(response){
                $.each(response.info, function (index, info) {
                    $('#phoneCodeStatus').text(info.status);
                });
            },
            error: function(xhr){

            }
        });
        return false;
    }

    function destroyRepertoir(id){
        var url = "/user/delete/repertoir/"+id;
        $.get( url, function(data) {
            $.each(data.info, function (index, info) {
                var div2remove = '#repertoir_user_'+info.id;
                $(div2remove).remove()
                $('<p/>').html(info.status).appendTo($('#status_deleting_repertoir_user_'+info.id));
                setTimeout(function() {
                    $('#status_deleting_repertoir_user_'+info.id).fadeOut();
                }, 1000 );
            });
        });
    }

    $(function () {
        $("#sendRepertoir").submit(function(e) {
            var url = "/add/user/repertoir"; 
            $.ajax({
                type: "POST",
                url: url,
                data: $("#sendRepertoir").serialize(),
                dataType: 'json',
                beforeSend: function(){
                    $('#repertoirLodingForAdd').text('Loading...');
                    $('#repertoirSuccessfullyAdded').show();
                    $('#repertoirSuccessfullyAdded').empty();
                },
                success: function(response){
                    $.each(response.info, function (index, info) {
                        $('#repertoir').val('');

                        if(info.count < 5){
                            $('#repertoir_user_profile').prepend('<div id="repertoir_user_'+info.id+'">*'+info.name+'<button onclick="destroyRepertoir('+info.id+')" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button><a href="{{ url("/user/update/repertoir/") }}/'+info.id+'" class="btn btn-success">Make it visible</a></div><p id="status_deleting_repertoir_user_'+info.id+'>"');
                        } else {
                            $('#repertoir_user_profile').prepend('<div id="repertoir_user_'+info.id+'">*'+info.name+'<button onclick="destroyRepertoir('+info.id+')" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div><p id="status_deleting_repertoir_user_'+info.id+'">');
                        }
                        $('<p/>').html(info.status).appendTo($('#repertoirSuccessfullyAdded'));
                        setTimeout(function() {
                            $('#repertoirSuccessfullyAdded').fadeOut();
                        }, 2000 );
                    });
                    $('#repertoirLodingForAdd').text('');
                },
                error: function(xhr){

                }
            });
            e.preventDefault(); 
        });
    });

    function destroySong(id){
        var url = "/user/delete/song/"+id;
        $.get( url, function(data) {
            $.each(data.info, function (index, info) {
                var div2remove = '#song_user_'+info.id;
                $(div2remove).remove()
                $('<p/>').html(info.status).appendTo($('#status_deleting_song_user_'+info.id));
                setTimeout(function() {
                    $('#status_deleting_song_user_'+info.id).fadeOut();
                }, 1000 );
            });
        });
    }

    $(function () {
        $("#sendSong").submit(function(e) {
            var url = "/add/user/song"; 
            $.ajax({
                type: "POST",
                url: url,
                data: $("#sendSong").serialize(),
                dataType: 'json',
                beforeSend: function(){
                    $('#songLodingForAdd').text('Loading...');
                    $('#songSuccessfullyAdded').show();
                    $('#songSuccessfullyAdded').empty();
                },
                success: function(response){
                    $.each(response.songs, function (index, song) {
                        if (song.flag == 1) {
                            $('#song').val('');
                            $('<p/>').html(song.status).appendTo($('#songSuccessfullyAdded'));
                            setTimeout(function() {
                                $('#songSuccessfullyAdded').fadeOut();
                            }, 2000 );

                            if(song.platform == 'spotify'){
                                $('#songs_user_profile').prepend('<div id="song_user_'+song.id+'"><iframe src="https://open.spotify.com/embed?uri=spotify:track:'+song.code+'&theme=white&view=coverart" frameborder="0" allowtransparency="true"></iframe><button class="btn btn-danger" onclick="destroySong('+song.id+'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div><p id="status_deleting_song_user_'+song.id+'">');
                            }
                            else if(song.platform == 'soundcloud'){
                                $('#songs_user_profile').prepend('<div id="song_user_'+song.id+'"><iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'+song.code+'&amp;color=0066cc&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe><button class="btn btn-danger" onclick="destroySong('+song.id+'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div><p id="status_deleting_song_user_'+song.id+'">');
                            }            
                        } else {
                            $('<p/>').html(song.status).appendTo($('#songSuccessfullyAdded'));
                            setTimeout(function() {
                                $('#songSuccessfullyAdded').fadeOut();
                            }, 2000 );
                        }
                    });
                    $('#songLodingForAdd').text('');
                },
                error: function(xhr){

                }
            });
            e.preventDefault(); 
        });
    });

    function destroyVideo(id){
        var url = "/user/delete/video/"+id;
        $.get( url, function(data) {
            $.each(data.info, function (index, info) {
                var div2remove = '#video_user_'+info.id;
                $(div2remove).remove()
                $('<p/>').html(info.status).appendTo($('#status_deleting_video_user_'+info.id));
                setTimeout(function() {
                    $('#status_deleting_video_user_'+info.id).fadeOut();
                }, 1000 );
            });
        });
    }

    $(function () {
        $("#sendVideo").submit(function(e) {
            var url = "/add/user/video"; 
            $.ajax({
                type: "POST",
                url: url,
                data: $("#sendVideo").serialize(),
                dataType: 'json',
                beforeSend: function(){
                    $('#videoLodingForAdd').text('Loading...');
                    $('#videoSuccessfullyAdded').show();
                    $('#videoSuccessfullyAdded').empty();
                },
                success: function(response){
                    $.each(response.videos, function (index, video) {
                        if (video.flag == 1) {
                            $('#video').val('');
                            $('<p/>').html(video.status).appendTo($('#videoSuccessfullyAdded'));
                            setTimeout(function() {
                                $('#videoSuccessfullyAdded').fadeOut();
                            }, 2000 );

                            if(video.platform == 'youtube'){
                                $('#videos_user_profile').prepend('<div id="video_user_'+video.id+'"><iframe width="100%" height="315" src="https://www.youtube.com/embed/'+video.code+'" frameborder="0" allowfullscreen></iframe><button class="btn btn-danger" onclick="destroyVideo('+video.id+'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div><p id="status_deleting_video_user_'+video.id+'">');
                            }
                            else if(video.platform == 'vimeo'){
                                $('#videos_user_profile').prepend('<div id="video_user_'+video.id+'"><iframe src="https://player.vimeo.com/video/'+video.code+'" width="100%" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe><button class="btn btn-danger" onclick="destroyVideo('+video.id+'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div><p id="status_deleting_video_user_'+video.id+'">');
                            }
                        } else {
                            $('<p/>').html(video.status).appendTo($('#videoSuccessfullyAdded'));
                            setTimeout(function() {
                                $('#videoSuccessfullyAdded').fadeOut();
                            }, 2000 );
                        }
                    });
                    $('#videoLodingForAdd').text('');
                },
                error: function(xhr){

                }
            });
            e.preventDefault(); 
        });
    });

    function destroyImg(id){
        var url = "/user/image/destroy/"+id;
        $.get( url, function(data) {
            $.each(data.info, function (index, info) {
                var div2remove = '#image_user_'+info.idImg;
                $(div2remove).remove()
                $('<p/>').html(info.status).appendTo($('#status_deleting_img_'+info.idImg));
                setTimeout(function() {
                    $('#status_deleting_img_'+info.idImg).fadeOut();
                }, 1000 );
            });
        });
    }

    $(function () {
        $('#fileupload').fileupload({
            dataType: 'json',
            add: function (e, data) {
                $('#loading').text('Uploading...');
                $('#status_upload').show();
                $('#status_upload').empty();
                data.submit();
            },
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    $('<p/>').html(file.name).appendTo($('#files_list'));
                    setTimeout(function() {
                        $('#files_list').fadeOut();
                    }, 2000 );
                    if ($('#file_ids').val() != '') {
                        $('#file_ids').val($('#file_ids').val() + ',');
                    }
                    if(file.name == null){
                        if (file.failed == 'true') {
                            $('<p/>').html(file.status).appendTo($('#status_upload'));
                            setTimeout(function() {
                                $('#status_upload').fadeOut();
                            }, 2000 );
                        } else {
                            $('<p/>').html(file.status).appendTo($('#status_upload'));
                        }
                    }else{
                        $('#images_user_profile').prepend('<div id="image_user_'+file.fileID+'" class="col-md-12"><img src="{{ asset("images/general/") }}/'+file.fileName+'" class="img-rounded" alt="'+file.fileName+'" width="304" height="236"><button class="btn btn-danger" onclick="destroyImg('+file.fileID+'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div><p id="status_deleting_img_'+file.fileID+'"></p>');
                        if (file.failed == 'true') {
                            $('<p/>').html(file.status).appendTo($('#status_upload'));
                            setTimeout(function() {
                                $('#status_upload').fadeOut();
                            }, 1000 );
                        } else {
                            $('<p/>').html(file.status).appendTo($('#status_upload'));
                            setTimeout(function() {
                                $('#status_upload').fadeOut();
                            }, 1000 );
                        }
                        $('#file_ids').val($('#file_ids').val() + file.fileID);
                    }
                });
                $('#loading').text('');
            }
        });

        $('#fileupload_update_user_image').fileupload({
            dataType: 'json',
            add: function (e, data) {
                $('#loading_update_user_pic').text('Uploading...');
                $('#status_upload_update_user_image').show();
                $('#status_upload_update_user_image').empty();
                data.submit();
            },
            done: function (e, data) {
                $.each(data.result.info, function (index, info) {
                    $('<p/>').html(info.status).appendTo($('#status_upload_update_user_image'));
                    if (info.status == '<strong style="color: red;">Select an image</strong>') {
                        setTimeout(function() {
                            $('#status_upload_update_user_image').fadeOut();
                        }, 2000 );
                    }else{
                        $('#profile_picture_user').attr('src', '{{ asset("images/profile/") }}/'+info.name);
                        setTimeout(function() {
                            $('#status_upload_update_user_image').fadeOut();
                        }, 1000 );
                    }
                });
                $('#loading_update_user_pic').text('');
            }
        });

    });

    $(function(){
        $('#searchTextField').keypress(function(e){
            if ( e.which == 13 ){
                $(this).next().focus();
                return false;
            }
        });
    });

    //Api(choosen) for display and select tags
    $(function () {
        $("#select-tag").chosen({
                placeholder_text_multiple: 'Choose 5 tags',
                max_selected_options: '5',
                disable_search_threshold: 10
        }).change(function(){       
            $.ajax({
                type: "POST",
                url: "/add/tag",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "tags": $("#select-tag").val(),
                },
                dataType: 'json',
                beforeSend: function(){

                },
                success: function(response){
                    $.each(response.tags, function (index, tag) {
                        
                    });
                },
                error: function(xhr){

                }
            });
        });
    });

    //Api(choosen) for display and select instruments
    $(function () {
        $("#select-instrument").chosen({
                placeholder_text_multiple: 'Choose 5 instruments',
                max_selected_options: '5',
                disable_search_threshold: 10
        }).change(function(){
            $.ajax({
                type: "POST",
                url: "/add/instrument",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "instruments": $("#select-instrument").val(),
                },
                dataType: 'json',
                beforeSend: function(){

                },
                success: function(response){
                    $.each(response.instruments, function (index, instrument) {
                        
                    });
                },
                error: function(xhr){

                }
            });
        });
    });

    //Api(choosen) for display and select styles
    $(function () {
        $("#select-style").chosen({
                placeholder_text_multiple: 'Choose 5 styles',
                max_selected_options: '5',
                disable_search_threshold: 10
        }).change(function(){
            $.ajax({
                type: "POST",
                url: "/add/style",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "styles": $("#select-style").val(),
                },
                dataType: 'json',
                beforeSend: function(){

                },
                success: function(response){
                    $.each(response.styles, function (index, style) {
                        
                    });
                },
                error: function(xhr){

                }
            });
        });
    });
    //////////////Maps////////////////////
    function initialize() {

        var input = document.getElementById('searchTextField');
        var autocomplete = new google.maps.places.Autocomplete(input);

        (function pacSelectFirst(input){
            // store the original event binding function
            var _addEventListener = (input.addEventListener) ? input.addEventListener : input.attachEvent;

            function addEventListenerWrapper(type, listener) {
            // Simulate a 'down arrow' keypress on hitting 'return' when no pac suggestion is selected,
            // and then trigger the original listener.

            if (type == "keydown") {
              var orig_listener = listener;
              listener = function (event) {
                var suggestion_selected = $(".pac-item-selected").length > 0;
                if (event.which == 13 && !suggestion_selected) {
                  var simulated_downarrow = $.Event("keydown", {keyCode:40, which:40})
                  orig_listener.apply(input, [simulated_downarrow]);
                }

                orig_listener.apply(input, [event]);
              };
            }

            // add the modified listener
            _addEventListener.apply(input, [type, listener]);
          }

          if (input.addEventListener)
            input.addEventListener = addEventListenerWrapper;
          else if (input.attachEvent)
            input.attachEvent = addEventListenerWrapper;

        })(input);

        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }

            document.getElementById('place-id').value = place.place_id;
            document.getElementById('place-geometry').value = place.geometry.location;
            document.getElementById('place-address').value = place.formatted_address;
            var latANDlng = document.getElementById('place-geometry').value;
            var firstResultLatAndLng = latANDlng.slice(1, -1);
            var lastResultLatAndLng = firstResultLatAndLng.split(", ");
            codeLatLng(lastResultLatAndLng[0], lastResultLatAndLng[1]);
        });
    }

    function codeLatLng(lat, lng) {
        var geocoder= new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(lat, lng);
        geocoder.geocode({latLng: latlng}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    var arrAddress = results;
                    $.each(arrAddress, function(i, address_component) {
                        if (address_component.types[0] == "locality") {
                            document.getElementById('location').value = address_component.address_components[0].long_name;
                            itemLocality = address_component.address_components[0].long_name;
                        }
                    });
                }
            }
        });
    }

    google.maps.event.addDomListener(window, 'load', initialize);
    //////////////----////////////////////
    </script>
@endsection