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
                                <img src="{{ asset("images/ensemble/$ensemble->profile_picture") }}" class="img-circle float-left" alt="{{$ensemble->profile_picture}}" width="236" height="194">
                            @else
                                <img src="{{ asset("images/profile/no-image.png") }}" class="img-circle float-left" alt="No image">
                            @endif
                        </div>
                        <div class="col-md-7">
                            @php
                                if ((!strpos($ensemble->address, 'id:') and !strpos($ensemble->address, 'address:') and !strpos($ensemble->address, 'lat:') and !strpos($ensemble->address, 'long:')) or $ensemble->address==null) {
                                    $ensemble->address = "id:no-addres|address:no-address|lat:0|long:0";
                                }
                                $data = explode("|", $ensemble->address);
                                $data_address = explode("address:", $data[1]);
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

                            <!-- Form for upload profile picture -->
                            {!! Form::open(['route' => ['ensemble.updateImage', $ensemble->id], 'method' => 'PUT', 'files' => true]) !!}
            
                            {!! Form::file('image') !!}
                            @if ($errors->has('image'))
                                <span class="help-block">
                                    <strong style="color: red;">{{ $errors->first('image') }}</strong>
                                </span>
                            @endif
                            {!! Form::submit('Update image', ['class' => 'btn btn-primary']) !!}
            
                            {!! Form::close() !!}
                            <!-- /Form for upload profile picture -->

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
                            <label for="member" class="col-md-2 control-label">member url:</label>

                            <div class="col-md-6">
                                <input id="member" type="text" class="form-control" name="member" placeholder="bemusical.us/exaple-here-REQUEST" required>
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
                    OR
                    {!! Form::open(['route' => 'ensemble.not.member', 'method' => 'POST']) !!}
                        <br>
                        <div class="row form-group">
                            <label for="notmember" class="col-md-2 control-label">email:</label>

                            <div class="col-md-6">
                                <input id="notmember" type="email" class="form-control" name="notmember" placeholder="not-bemusical-user@example.com-REQUEST" required>
                            </div>

                            <div class="form-group">
                                {!! Form::submit('Add member', ['class' => 'btn btn-primary']) !!}
                            </div>
                         @if($errors->has('notmember'))
                            <span class="help-block">
                                <strong style="color: red;">{{ $errors->first('notmember') }}</strong>
                            </span>
                        @endif
                        </div>
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
                    <div class="row">
                        <!-- Displaying images -->
                        @foreach($images as $image)
                        <div class="col-md-12">
                            <img src="{{ asset("images/general/$image") }}" class="img-rounded" alt="{{$image}}" width="304" height="236"><a href="{{ route('ensemble.image.destroy', $image) }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                        </div>
                        @endforeach
                        <!-- /Displaying images -->
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Save Bio images -->
                            {!! Form::open(['route'=> 'ensemble.images', 'method' => 'POST', 'files'=>'true', 'id' => 'my-dropzone' , 'class' => 'dropzone']) !!}
                                <div class="dz-message" style="height:50px;">
                                    Drop your files here (5 images)
                                </div>
                                <div class="dropzone-previews"></div>
                                <!-- <button type="submit" class="btn btn-success" id="submit">Save images</button> -->
                            {!! Form::close() !!}
                            <!-- /Save Bio images -->
                        </div>
                    </div>
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
                    <div class="row form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                        {!! Form::label('location', 'Location', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('location', $ensemble->location, ['class'=>'form-control', 'placeholder'=>'Usually, where do you work?', 'required']) !!}
                            @if ($errors->has('location'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('location') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
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

                    <input id="place-id" type="hidden" name="place_id" required>
                    <input id="place-address" type="hidden" name="place_address" required>
                    <input id="place-geometry" type="hidden" name="place_geometry" required>
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&v=3.exp&libraries=places"></script>
@endsection

@section('script')

    //Api(choosen) for display and select tags
    $("#select-tag").chosen({
            placeholder_text_multiple: 'Choose 5 tags',
            max_selected_options: '5',
            disable_search_threshold: 10
    });

    //Api(choosen) for display and select instruments
    $("#select-instrument").chosen({
            placeholder_text_multiple: 'Choose 5 instruments',
            max_selected_options: '5',
            disable_search_threshold: 10
    });

    //Api(choosen) for display and select styles
    $("#select-style").chosen({
            placeholder_text_multiple: 'Choose 5 styles',
            max_selected_options: '5',
            disable_search_threshold: 10
    });

    //Dropzone is for dropping images
    Dropzone.options.myDropzone = {
            //autoProcessQueue: false,
            autoProcessQueue: true,
            uploadMultiple: true,
            maxFilezise: 10,
            maxFiles: 5,
            
            init: function() {
                //var submitBtn = document.querySelector("#submit");
                //myDropzone = this;
                
                //submitBtn.addEventListener("click", function(e){
                //    e.preventDefault();
                //    e.stopPropagation();
                //    myDropzone.processQueue();
                //});
                                
                this.on("complete", function(file) {
                    myDropzone.removeFile(file);
                });
 
                this.on("success", myDropzone.processQueue.bind(myDropzone));
            }
        };
    //--Dropzone is for dropping images--//

    //////////////Maps////////////////////
    function initialize() {

    var input = document.getElementById('searchTextField');
    var autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }

        document.getElementById('place-id').value = place.place_id;
        document.getElementById('place-geometry').value = place.geometry.location;
        document.getElementById('place-address').value = place.formatted_address;
      });
    }

    google.maps.event.addDomListener(window, 'load', initialize);
    //////////////----////////////////////
@endsection