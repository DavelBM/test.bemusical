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
                            <strong>e-mail*:</strong> {{$ensemble->user->email}}<br>
                            <strong>Manager name:</strong> {{$ensemble->manager_name}}<br>
                            <strong>Type of ensemble:</strong> {{$ensemble->type}}<br>
                            <strong>Bio summary:</strong> {{$ensemble->summary}}<br>
                            <strong>My Address:</strong> {{$data_address[1]}}<br>
                            <strong>My phone:</strong> {{$ensemble->phone}}<br>
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
                            {!! Form::open(['route' => 'ensemble.instrument', 'method' => 'POST']) !!}
                                <div class="form-group col-md-12">
                                    {!! Form::label('instruments', 'Instruments', ['class' => 'control-label']) !!}<br>
                                    {!! Form::select('instruments[]', $instruments, $my_instruments, ['id'=>'select-instrument','class'=>'form-control', 'multiple', 'required']) !!}
                                </div>

                                <div class="form-group">
                                        {!! Form::submit('Add', ['class' => 'btn btn-primary btn-block']) !!}
                                </div>
                            {!! Form::close() !!}
                        </div>
                        <!-- /Add instruments -->

                        <!-- Add styles -->
                        <div class="col-md-3">
                            {!! Form::open(['route' => 'ensemble.style', 'method' => 'POST']) !!}
                                <div class="form-group col-md-12">
                                    {!! Form::label('styles', 'Styles', ['class' => 'control-label']) !!}<br>
                                    {!! Form::select('styles[]', $styles, $my_styles, ['id'=>'select-style','class'=>'form-control', 'multiple', 'required']) !!}
                                </div>

                                <div class="form-group">
                                        {!! Form::submit('Add', ['class' => 'btn btn-primary btn-block']) !!}
                                </div>
                            {!! Form::close() !!}
                        </div>
                        <!-- /Add styles -->

                        <!-- Add tags -->
                        <div class="col-md-3">
                            {!! Form::open(['route' => 'ensemble.tag', 'method' => 'POST']) !!}
                                <div class="form-group col-md-12">
                                    {!! Form::label('tags', 'Tags', ['class' => 'control-label']) !!}<br>
                                    {!! Form::select('tags[]', $tags, $my_tags, ['id'=>'select-tag','class'=>'form-control', 'multiple', 'required']) !!}
                                </div>

                                <div class="form-group">
                                        {!! Form::submit('Add', ['class' => 'btn btn-primary btn-block']) !!}
                                </div>
                            {!! Form::close() !!}
                        </div>
                        <!-- /Add tags -->

                    </div>
                </div>
                <hr>
                <div class="panel-body">
                    <strong>BIO IMAGES</strong>
                </div>
                <div class="panel-body">
                    <!-- <form action="/ensemble/add/image" method="post" enctype="multipart/form-data"> -->
                    <form method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        You can add more than one picture(MAX 5):
                        <br />
                        <input type="file" id="fileupload" name="photos[]" data-url="/ensemble/add/image" multiple />
                        <!-- <input type="file" name="photos[]"> -->
                        <br />
                        <p id="loading"></p>
                        <div id="images_ensemble_profile">
                            <div id="files_list"></div>
                        </div>
                        <p id="status_upload"></p>
                        <input type="hidden" name="file_ids" id="file_ids" value="" />
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
                    {!! Form::open(['route' => 'ensemble.video', 'method' => 'POST']) !!}

                        <div class="row form-group">
                            <label for="video" class="col-md-4 control-label">Videos links:</label>

                            <div class="col-md-6">
                                <input id="video" type="text" class="form-control" name="video" required>
                            </div>
                        </div>
                        @if ($errors->has('video'))
                            <span class="help-block">
                                <strong style="color: red;">{{ $errors->first('video') }}</strong>
                            </span>
                        @endif
                        <div class="form-group">
                                {!! Form::submit('Add video', ['class' => 'btn btn-primary btn-block']) !!}
                        </div>
                    {!! Form::close() !!}

                    {!! Form::open(['route' => 'ensemble.song', 'method' => 'POST']) !!}

                        <div class="row form-group">
                            <label for="song" class="col-md-4 control-label">Songs links:</label>

                            <div class="col-md-6">
                                <input id="song" type="text" class="form-control" name="song" required>
                            </div>
                        </div>
                        @if ($errors->has('song'))
                            <span class="help-block">
                                <strong style="color: red;">{{ $errors->first('song') }}</strong>
                            </span>
                        @endif
                        <div class="form-group">
                                {!! Form::submit('Add song', ['class' => 'btn btn-primary btn-block']) !!}
                        </div>
                    {!! Form::close() !!}
                    <div class="row">
                        <strong>VIDEOS</strong>
                        <div class="col-md-12">
                        @foreach($videos as $video)
                            @if($video->platform == 'youtube')
                                <iframe width="100%" height="315" src="https://www.youtube.com/embed/{{$video->code}}" frameborder="0" allowfullscreen></iframe>
                            @elseif($video->platform == 'vimeo')
                                <iframe src="https://player.vimeo.com/video/{{$video->code}}" width="100%" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                            @endif
                            <a href="{{ route('ensemble.video.destroy', $video->id) }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                        @endforeach
                        </div>
                    </div>

                    <div class="row">
                        <strong>SONGS</strong>
                        <div class="col-md-12">
                        @foreach($songs as $song)
                            @if($song->platform == 'spotify')
                                <iframe src="https://open.spotify.com/embed?uri=spotify:track:{{$song->code}}&theme=white&view=coverart" 
                                frameborder="0" allowtransparency="true"></iframe>
                            @elseif($song->platform == 'soundcloud')
                                <iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/{{$song->code}}&amp;color=0066cc&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe>
                            @endif
                                <a href="{{ route('ensemble.song.destroy', $song->id) }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                        @endforeach                        
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    {!! Form::open(['route' => 'ensemble.repertoir', 'method' => 'POST']) !!}

                        <div class="row form-group">
                            <label for="repertoir" class="col-md-4 control-label">Briefly explain:</label>

                            <div class="col-md-6">
                                <input id="repertoir" type="text" class="form-control" name="repertoir" required>
                            </div>
                        </div>
                        @if ($errors->has('repertoir'))
                            <span class="help-block">
                                <strong style="color: red;">{{ $errors->first('repertoir') }}</strong>
                            </span>
                        @endif
                        <div class="form-group">
                                {!! Form::submit('Add repertoir', ['class' => 'btn btn-primary btn-block']) !!}
                        </div>
                    {!! Form::close() !!}  
                    <div class="row">
                        <strong>Repertoir</strong>
                        <div class="col-md-12">
                        @foreach($repertoires as $repertoir)
                            *{{ $repertoir->repertoire_example }}<a href="{{ route('ensemble.repertoir.destroy', $repertoir->id) }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                            @if(!$repertoir->visible and $total_repertoires < 5)
                                <a href="{{ route('ensemble.repertoir.update', $repertoir->id) }}" class="btn btn-success">Make it visible</a>
                            @elseif($repertoir->visible)
                                <a href="{{ route('user.repertoir.update', $repertoir->id) }}" class="btn btn-danger">Hide it</a>
                            @endif
                            <br>
                        @endforeach                        
                        </div>
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
                    <div class="row form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        {!! Form::label('phone', "Ensemble's phone", ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::number('phone', $ensemble->phone, ['class'=>'form-control', 'placeholder'=>"What's your contact number", 'required']) !!}
                            @if ($errors->has('phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
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

@endsection

@section('js')
    <script src="/js/jquery.ui.widget.js"></script>
    <script src="/js/jquery.iframe-transport.js"></script>
    <script src="/js/jquery.fileupload.js"></script>
    <script src="/chosen/chosen.jquery.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&v=3.exp&libraries=places"></script>

    <script type="text/javascript">
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
                        $('<p/>').html(file.status).appendTo($('#status_upload'));
                    }else{
                        $('#images_ensemble_profile').prepend('<div id="image_ensemble_'+file.fileID+'" class="col-md-12"><img src="{{ asset("images/general/") }}/'+file.fileName+'" class="img-rounded" alt="'+file.fileName+'" width="304" height="236"><button class="btn btn-danger" onclick="destroyImg('+file.fileID+'); return false;"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></div><p id="status_deleting_img_'+file.fileID+'"></p>');
                        $('<p/>').html(file.status).appendTo($('#status_upload'));
                        setTimeout(function() {
                            $('#status_upload').fadeOut();
                        }, 1000 );
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
                data.submit();
            },
            done: function (e, data) {
                $.each(data.result.info, function (index, info) {
                    $('<p/>').html(info.status).appendTo($('#status_upload_update_ensemble_image'));
                   $('#profile_picture_ensemble').attr('src', '{{ asset("images/ensemble/") }}/'+info.name);
                    setTimeout(function() {
                        $('#status_upload_update_ensemble_image').fadeOut();
                    }, 1000 );
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
    $("#select-tag").chosen({
            placeholder_text_multiple: 'Choose 5 tags',
            max_selected_options: '5',
            disable_search_threshold: 10
    }).change(function(){
        console.log('tag works');
    });

    //Api(choosen) for display and select instruments
    $("#select-instrument").chosen({
            placeholder_text_multiple: 'Choose 5 instruments',
            max_selected_options: '5',
            disable_search_threshold: 10
    }).change(function(){
        console.log('instrument works');
    });

    //Api(choosen) for display and select styles
    $("#select-style").chosen({
            placeholder_text_multiple: 'Choose 5 styles',
            max_selected_options: '5',
            disable_search_threshold: 10
    }).change(function(){
        console.log('style works');
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