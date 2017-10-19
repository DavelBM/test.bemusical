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
                
                @if($ensemble->name == 'null')
                    <div class="panel-heading">Ensemble dashboard,
                @else
                    <div class="panel-heading">Welcome {{$ensemble->name}},
                @endif
                
                @if($ensemble->user->visible == 0 and $ensemble->user->ask_review == 0)
                    your profile is not available to watch until you fill with your data correctly<a href="{{ route('user.ask.review', $ensemble->user->id) }}" class="btn btn-danger">Ask to the admin if my profile is ready</a></div>
                @elseif($ensemble->user->visible == 0 and $ensemble->user->ask_review == 1)
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
                                            @elseif($ask->available != 0 and $ask->nonavailable == 0 and $ask->read == 1 and $ask->accepted_price == 1)
                                                <span class="badge">
                                                    Client paid!
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
                            @if($ensemble->profile_picture != 'null')
                                <img id="profile_picture_ensemble" src="{{ asset("images/ensemble/$ensemble->profile_picture") }}" class="img-circle float-left" alt="{{$ensemble->profile_picture}}" width="236" height="194">
                            @else
                                <img id="profile_picture_ensemble" src="{{ asset("images/profile/no-image.png") }}" class="img-circle float-left" alt="No image">
                            @endif
                            <!-- <form action="/user/{{$ensemble->id}}/image" method="post" enctype="multipart/form-data"> -->
                            <p id="loading_update_ensemble_pic"></p>
                            <p id="status_upload_update_ensemble_image"></p>
                            <!-- Form for upload profile picture -->
                            <form method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}Update Profile Picture:<br />
                                <input type="file" id="fileupload_update_ensemble_image" name="image" data-url="/ensemble/{{$ensemble->id}}/image" multiple />
                                <!-- <input type="file" name="image">                     -->
                                <!-- <input type="submit" name="submit"> -->
                            </form>
                            <!-- /Form for upload profile picture -->
                        </div>
                        <div class="col-md-7">
                            @php
                                if ((!strpos($ensemble->address, 'id:') and !strpos($ensemble->address, 'address:') and !strpos($ensemble->address, 'lat:') and !strpos($ensemble->address, 'long:')) or $ensemble->address==null) {
                                    $ensemble->address = "id:no-addres|address:no-address|lat:0|long:0";
                                }
                                $data = explode("|", $ensemble->address);
                                $data_id = explode("id:", $data[0]);
                                $data_address = explode("address:", $data[1]);
                                $data_lat = explode("lat:", $data[2]);
                                $data_long = explode("long:", $data[3]);
                            @endphp
                            <!-- Displaying data -->
                            <strong>Ensemble:</strong> {{$ensemble->name}}<br>
                            <strong>username*:</strong> {{$ensemble->slug}}<br>
                            <strong>url*:</strong> <a href="{{URL::to('/'.$ensemble->slug)}}">bemusical.us/{{$ensemble->slug}}</a><br>
                            @if($user_days >= 5)
                                <strong>e-mail*:</strong> {{$ensemble->user->email}}
                                <button type="button" class="btn btn-xs btn-warning" onclick="changeEmail({{$ensemble->user->id}})">Change my email</button>
                            @else
                                <strong>e-mail*:</strong> {{$ensemble->user->email}}
                            @endif
                            <br>
                            <br>
                            <strong>Manager name:</strong> {{$ensemble->manager_name}}<br>
                            <strong>Type of ensemble:</strong> {{$ensemble->type}}<br>
                            <strong>Bio summary:</strong> {{$ensemble->summary}}<br>
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
                            <strong>Location:</strong> {{$ensemble->location}}<br>
                            <strong>Mile Radius:</strong> {{$ensemble->mile_radious}} miles<br>
                            <strong>About Me:</strong> {{$ensemble->about}}<br>
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
                    Would you like to add bemusical members or not memeber to this ensemble?
                    {!! Form::open(['route' => 'ensemble.member', 'method' => 'POST']) !!}
                        <br>
                        <div class="row form-group">
                            <label for="member" class="col-md-2 control-label">member or notmember:</label>

                            <div class="col-md-6">
                                <input id="member" type="text" class="form-control" name="member" placeholder="bemusical.us/exaple-here OR email" value="{{ old('member') }}" required>
                            </div>

                            <div class="form-group">
                                {!! Form::submit('Add member', ['class' => 'btn btn-primary']) !!}
                            </div>
                        </div>
                        @if($errors->has('member'))
                            <span class="help-block">
                                <strong style="color: red;">{{ $errors->first('member') }}</strong>
                            </span>
                        @endif
                        
                    {!! Form::close() !!}
                </div>
                <div class="panel-body">
                    <strong>Members</strong>
                    @foreach($members as $member)
                        <br>
                        <a class="btn" href="{{ URL::to('/'.$member->slug) }}">{{$member->name}}</a> and plays {{$member->instrument}}
                        @if($member->confirmation == 0)
                            <p style="color: red;">This user does not confirm yet.</p>
                        @else
                            <a href="{{ route('ensemble.member.destroy', $member->id) }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                            <p style="color: green;">OK</p>
                        @endif
                    @endforeach
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
                    <!-- <form action="/add/ensemble/image" method="post" enctype="multipart/form-data"> -->
                    <form method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        You can add more than one picture(MAX 5):
                        <br />
                        <input type="file" id="fileupload" name="photos[]" data-url="/add/ensemble/image" multiple />
                        <!-- <input type="file" name="photos[]"> -->
                        <br />
                        <p id="loading"></p>
                        <div id="images_ensemble_profile">
                            <div id="files_list"></div>
                        </div>
                        <p id="status_upload"></p>
                        <input type="hidden" name="file_ids" id="file_ids" value="" />
                        <!-- <input type="submit" name="submit"> -->
                    </form>
                        <!-- Displaying images -->
                        @foreach($images as $image)
                        <div id="image_ensemble_{{$image->id}}" class="col-md-12">
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
                        <div class="col-md-12" id="videos_ensemble_profile"></div>
                        @foreach($videos as $video)
                            <div class="col-md-12">
                                @if($video->platform == 'youtube')
                                    <div id="video_ensemble_{{$video->id}}"><iframe width="100%" height="315" src="https://www.youtube.com/embed/{{$video->code}}" frameborder="0" allowfullscreen></iframe>
                                @elseif($video->platform == 'vimeo')
                                    <div id="video_ensemble_{{$video->id}}"><iframe src="https://player.vimeo.com/video/{{$video->code}}" width="100%" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                @endif
                                <button class="btn btn-danger" onclick="destroyVideo('{{$video->id}}'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div>
                                <p id="status_deleting_video_ensemble_{{$video->id}}"></p>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <strong>SONGS</strong>
                        <div class="col-md-12" id="songs_ensemble_profile"></div>
                        @foreach($songs as $song)
                            <div class="col-md-12">
                                @if($song->platform == 'spotify')
                                    <div id="song_ensemble_{{$song->id}}"><iframe id="song_ensemble_{{$song->id}}" src="https://open.spotify.com/embed?uri=spotify:track:{{$song->code}}&theme=white&view=coverart" 
                                    frameborder="0" allowtransparency="true"></iframe>
                                @elseif($song->platform == 'soundcloud')
                                    <div id="song_ensemble_{{$song->id}}">
                                    <iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/{{$song->code}}&amp;color=0066cc&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe>
                                @endif
                                <button class="btn btn-danger" onclick="destroySong('{{$song->id}}'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div>
                                <p id="status_deleting_song_ensemble_{{$song->id}}"></p>
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
                        <div class="col-md-12" id="repertoir_ensemble_profile"></div>
                        @foreach($repertoires as $repertoir)
                            <div id="repertoir_ensemble_{{$repertoir->id}}">*{{ $repertoir->repertoire_example }}<button class="btn btn-danger"><span class="glyphicon glyphicon-remove" onclick="destroyRepertoir({{$repertoir->id}})" aria-hidden="true"></span></button>
                            @if(!$repertoir->visible and $total_repertoires < 5)
                                <a href="{{ route('user.repertoir.update', $repertoir->id) }}" class="btn btn-success">Make it visible</a></div>
                            @elseif($repertoir->visible)
                                <a href="{{ route('user.repertoir.update', $repertoir->id) }}" class="btn btn-danger">Hide it</a></div>
                            @endif
                            <p id="status_deleting_repertoir_ensemble_{{$repertoir->id}}"></p>
                        @endforeach    
                    </div>                  
                </div>
            </div>
        </div>
    </div>
</div>

<div id="vue-app" class="container">
    <div class="row">
        <div class="col-md-5">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-comment"></span> Do you need help?
                </div>
                <div class="panel-body-chat">
                    <chat-log :messages="messages"></chat-log>          
                </div>
                <chat-composer v-on:messagesent="addMessage"></chat-composer>
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
                {!! Form::open(['route' => ['ensemble.update', $ensemble->id], 'id' => 'update-form', 'method' => 'PUT']) !!}
                    <div class="row form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        {!! Form::label('name', "Ensemble's Name", ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('name', $ensemble->name, ['class'=>'form-control', 'placeholder'=>'Type the name of the ensemble', 'required']) !!}
                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('manager') ? ' has-error' : '' }}">
                        {!! Form::label('manager', "Manager Name", ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('manager', $ensemble->manager_name, ['class'=>'form-control', 'placeholder'=>'Manager name', 'required']) !!}
                            @if ($errors->has('manager'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('manager') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                        {!! Form::label('type', "Type of ensemble", ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('type', $ensemble->type, ['class'=>'form-control', 'placeholder'=>'Type of ensemble', 'required']) !!}
                            @if ($errors->has('type'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('type') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('summary') ? ' has-error' : '' }}">
                        {!! Form::label('summary', 'Summary', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('summary', $ensemble->summary, ['class'=>'form-control', 'placeholder'=>'Tell us something amazing', 'required']) !!}
                            @if ($errors->has('summary'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('summary') }}</strong>
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
                    <!-- <div class="row form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                        {!! Form::label('location', 'Location', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('location', $ensemble->location, ['class'=>'form-control', 'placeholder'=>'Usually, where do you work?', 'required']) !!}
                            @if ($errors->has('location'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('location') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div> -->
                    <div class="row form-group{{ $errors->has('mile_radious') ? ' has-error' : '' }}">
                        {!! Form::label('mile_radious', 'Travel mile radius', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::select('mile_radious', array($ensemble->mile_radious => $ensemble->mile_radious, '1' => '1 mile', '5' => '5 miles', '10' => '10 miles', '20' => '20 miles', '50' => '50 miles', '100' => '100 miles')); !!}
                            @if ($errors->has('mile_radious'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('mile_radious') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('about') ? ' has-error' : '' }}">
                        {!! Form::label('about', 'About the ensemble', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::textarea('about', $ensemble->about, ['class'=>'form-control', 'placeholder'=>'Tell to the world about you', 'required']) !!}
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
                    <input id="location" type="hidden" name="location" value="{{$ensemble->location}}" required>

                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <a href="{{ route('user.update', $ensemble->id) }}"
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
                
                {!! Form::open(['route' => ['user.updatePassUser', $ensemble->user_id], 'id' => 'pass-form', 'method' => 'PUT']) !!}

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
                <a href="{{ route('user.updatePassUser', $ensemble->user_id) }}"
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

<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update email
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h5>
            </div>

            <div class="modal-body">
                
            <center><h3><strong>We already sent you an Email to change your password, you have 30 minutes to do it</strong></h3></center>

            </div>
        </div>       
    </div>
</div>
@endsection

@section('js')
    <script src="/js/jquery.ui.widget.js"></script>
    <script src="/js/jquery.iframe-transport.js"></script>
    <script src="/js/jquery.fileupload.js"></script>
    <script src="/chosen/chosen.jquery.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&v=3.exp&libraries=places"></script>

    <script type="text/javascript">

    function changeEmail(id){
        
        $.ajax({
            type: "POST",
            url: "/change/email",
            data: {
                "_token": "{{ csrf_token() }}",
            },
            dataType: 'json',
            beforeSend: function(){
                $("#emailModal").modal();
            },
            success: function(response){
                setTimeout(function(){
                    $('#emailModal').modal('hide');
                }, 2000);
            },
            error: function(xhr){

            }
        });
    }

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
        var url = "/ensemble/delete/repertoir/"+id;
        $.get( url, function(data) {
            $.each(data.info, function (index, info) {
                var div2remove = '#repertoir_ensemble_'+info.id;
                $(div2remove).remove()
                $('<p/>').html(info.status).appendTo($('#status_deleting_repertoir_ensemble_'+info.id));
                setTimeout(function() {
                    $('#status_deleting_repertoir_ensemble_'+info.id).fadeOut();
                }, 1000 );
            });
        });
    }

    $(function () {
        $("#sendRepertoir").submit(function(e) {
            var url = "/ensemble/add/repertoir"; 
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
                            $('#repertoir_ensemble_profile').prepend('<div id="repertoir_ensemble_'+info.id+'">*'+info.name+'<button onclick="destroyRepertoir('+info.id+')" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button><a href="{{ url("/ensemble/update/repertoir/") }}/'+info.id+'" class="btn btn-success">Make it visible</a></div><p id="status_deleting_repertoir_ensemble_'+info.id+'>"');
                        } else {
                            $('#repertoir_ensemble_profile').prepend('<div id="repertoir_ensemble_'+info.id+'">*'+info.name+'<button onclick="destroyRepertoir('+info.id+')" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div><p id="status_deleting_repertoir_ensemble_'+info.id+'">');
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
        var url = "/ensemble/delete/song/"+id;
        $.get( url, function(data) {
            $.each(data.info, function (index, info) {
                var div2remove = '#song_ensemble_'+info.id;
                $(div2remove).remove()
                $('<p/>').html(info.status).appendTo($('#status_deleting_song_ensemble_'+info.id));
                setTimeout(function() {
                    $('#status_deleting_song_ensemble_'+info.id).fadeOut();
                }, 1000 );
            });
        });
    }

    $(function () {
        $("#sendSong").submit(function(e) {
            var url = "/ensemble/add/song"; 
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
                                $('#songs_ensemble_profile').prepend('<div id="song_ensemble_'+song.id+'"><iframe src="https://open.spotify.com/embed?uri=spotify:track:'+song.code+'&theme=white&view=coverart" frameborder="0" allowtransparency="true"></iframe><button class="btn btn-danger" onclick="destroySong('+song.id+'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div><p id="status_deleting_song_ensemble_'+song.id+'">');
                            }
                            else if(song.platform == 'soundcloud'){
                                $('#songs_ensemble_profile').prepend('<div id="song_ensemble_'+song.id+'"><iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'+song.code+'&amp;color=0066cc&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe><button class="btn btn-danger" onclick="destroySong('+song.id+'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div><p id="status_deleting_song_ensemble_'+song.id+'">');
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
        var url = "/ensemble/delete/video/"+id;
        $.get( url, function(data) {
            $.each(data.info, function (index, info) {
                var div2remove = '#video_ensemble_'+info.id;
                $(div2remove).remove()
                $('<p/>').html(info.status).appendTo($('#status_deleting_video_ensemble_'+info.id));
                setTimeout(function() {
                    $('#status_deleting_video_ensemble_'+info.id).fadeOut();
                }, 1000 );
            });
        });
    }

    $(function () {
        $("#sendVideo").submit(function(e) {
            var url = "/ensemble/add/video"; 
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
                            console.log(video.platform);
                            if(video.platform == 'youtube'){
                                $('#videos_ensemble_profile').prepend('<div id="video_ensemble_'+video.id+'"><iframe width="100%" height="315" src="https://www.youtube.com/embed/'+video.code+'" frameborder="0" allowfullscreen></iframe><button class="btn btn-danger" onclick="destroyVideo('+video.id+'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div><p id="status_deleting_video_ensemble_'+video.id+'">');
                            }
                            else if(video.platform == 'vimeo'){
                                $('#videos_ensemble_profile').prepend('<div id="video_ensemble_'+video.id+'"><iframe src="https://player.vimeo.com/video/'+video.code+'" width="100%" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe><button class="btn btn-danger" onclick="destroyVideo('+video.id+'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div><p id="status_deleting_video_ensemble_'+video.id+'">');
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
        var url = "/ensemble/image/destroy/"+id;
        $.get( url, function(data) {
            $.each(data.info, function (index, info) {
                var div2remove = '#image_ensemble_'+info.idImg;
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
                        $('#images_ensemble_profile').prepend('<div id="image_ensemble_'+file.fileID+'" class="col-md-12"><img src="{{ asset("images/general/") }}/'+file.fileName+'" class="img-rounded" alt="'+file.fileName+'" width="304" height="236"><button class="btn btn-danger" onclick="destroyImg('+file.fileID+'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div><p id="status_deleting_img_'+file.fileID+'"></p>');
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

        $('#fileupload_update_ensemble_image').fileupload({
            dataType: 'json',
            add: function (e, data) {
                $('#loading_update_ensemble_pic').text('Uploading...');
                $('#status_upload_update_ensemble_image').show();
                $('#status_upload_update_ensemble_image').empty();
                data.submit();
            },
            done: function (e, data) {
                $.each(data.result.info, function (index, info) {
                    $('<p/>').html(info.status).appendTo($('#status_upload_update_ensemble_image'));
                    if (info.status == '<strong style="color: red;">Select an image</strong>') {
                        setTimeout(function() {
                            $('#status_upload_update_ensemble_image').fadeOut();
                        }, 2000 );
                    }else{
                        $('#profile_picture_ensemble').attr('src', '{{ asset("images/ensemble/") }}/'+info.name);
                        setTimeout(function() {
                            $('#status_upload_update_ensemble_image').fadeOut();
                        }, 1000 );
                    }
                });
                $('#loading_update_ensemble_pic').text('');
            }
        });
    });

    $('#searchTextField').keypress(function(e){
        if ( e.which == 13 ) // Enter key = keycode 13
        {
            $(this).next().focus();  //Use whatever selector necessary to focus the 'next' input
            return false;
        }
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
                url: "/ensemble/add/tag",
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
                url: "/ensemble/add/instrument",
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
                url: "/ensemble/add/style",
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