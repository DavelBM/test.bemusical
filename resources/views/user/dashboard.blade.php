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
                    </div>
                @endif
        
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
                                <img src="{{ asset("images/profile/$info->profile_picture") }}" class="img-circle float-left" alt="{{$info->profile_picture}}" width="250" height="250">
                            @else
                                <img src="{{ asset("images/profile/no-image.png") }}" class="img-circle float-left" alt="No image">
                            @endif
                        </div>
                        <div class="col-md-7">
                            <!-- Displaying data -->
                            <strong>Name:</strong> {{$info->first_name." ".$info->last_name}}<br>
                            <strong>url*:</strong> bemusical.us/{{$info->slug}}<br>
                            <strong>e-mail*:</strong> {{$info->user->email}}<br>
                            <strong>Bio summary:</strong> {{$info->bio}}<br>
                            <strong>My Address:</strong> {{$info->address}}<br>
                            <strong>My phone:</strong> {{$info->phone}}<br>
                            <strong>Location:</strong> {{$info->location}}<br>
                            <strong>Degree:</strong> {{$info->degree}}<br>
                            <strong>Mile Radius:</strong> {{$info->mile_radious}} miles<br>
                            <strong>About Me:</strong> {!!$info->about!!}<br>
                            <!-- /Displaying data -->

                            <!-- Form for upload profile picture -->
                            {!! Form::open(['route' => ['user.updateImage', $info->id], 'method' => 'PUT', 'files' => true]) !!}
            
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
                    <div class="row">
                        <!-- Add instruments -->
                        <div class="col-md-3">
                            {!! Form::open(['route' => 'user.instrument', 'method' => 'POST']) !!}
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
                            {!! Form::open(['route' => 'user.style', 'method' => 'POST']) !!}
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
                            {!! Form::open(['route' => 'user.tag', 'method' => 'POST']) !!}
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
                            <img src="{{ asset("images/general/$image") }}" class="img-rounded" alt="{{$image}}" width="304" height="236"><a href="{{ route('user.image.destroy', $image) }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                        </div>
                        @endforeach
                        <!-- /Displaying images -->
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Save Bio images -->
                            {!! Form::open(['route'=> 'user.images', 'method' => 'POST', 'files'=>'true', 'id' => 'my-dropzone' , 'class' => 'dropzone']) !!}
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
                    {!! Form::open(['route' => 'option.video', 'method' => 'POST']) !!}

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

                    {!! Form::open(['route' => 'option.song', 'method' => 'POST']) !!}

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
                            <a href="{{ route('user.delete.video', $video->id) }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
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
                                <a href="{{ route('user.delete.song', $song->id) }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                        @endforeach                        
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    {!! Form::open(['route' => 'user.repertoir', 'method' => 'POST']) !!}

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
                            *{{ $repertoir->repertoire_example }}<a href="{{ route('user.repertoir.destroy', $repertoir->id) }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
                            @if(!$repertoir->visible and $total_repertoires < 5)
                                <a href="{{ route('user.repertoir.update', $repertoir->id) }}" class="btn btn-success">Make it visible</a>
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
                        {!! Form::label('address', 'Address', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('address', $info->address, ['class'=>'form-control', 'placeholder'=>'Tell us something amazing', 'required']) !!}
                            @if ($errors->has('address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        {!! Form::label('phone', 'Phone', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::number('phone', $info->phone, ['class'=>'form-control', 'placeholder'=>"What's your contact number", 'required']) !!}
                            @if ($errors->has('phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
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
                    <div class="row form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                        {!! Form::label('location', 'Location', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('location', $info->location, ['class'=>'form-control', 'placeholder'=>'Usually, where do you work?', 'required']) !!}
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
 
                this.on("success", 
                    myDropzone.processQueue.bind(myDropzone)
                );
            }
        };
    //--Dropzone is for dropping images--//
@endsection