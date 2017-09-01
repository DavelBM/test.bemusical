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
                
                <div class="panel-heading">We almost finish, please fill the form</div>

                <div class="panel-body">
                        <form id="registerNewMember" class="form-horizontal" method="POST" action="{{ route('member.new') }}">
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
                            </div>

                            <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                <label for="first_name" class="col-md-4 control-label">First Name</label>

                                <div class="col-md-6">
                                    <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required>

                                    @if ($errors->has('first_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                <label for="last_name" class="col-md-4 control-label">Last Name</label>

                                <div class="col-md-6">
                                    <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>

                                    @if ($errors->has('last_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('last_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button onclick="details_soloist(this)" type="button" class="btn btn-primary" data-toggle="modal" data-target="#registrationModal">
                                        Send information
                                    </button>
                                </div>
                            </div>

                            <input id="type" type="hidden" class="form-control" name="type" value="soloist">

                            <input id="id" type="hidden" class="form-control" name="id" value="{{$id}}">

                        </form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmation Soloist -->
<div class="modal fade" id="registrationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Soloist Confirmation
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </h5>
            </div>

            <div class="modal-body">
                If you proceed, you won't be able to change your URL (bemusical.us/<strong id="url_soloist"></strong>).
                <p id="url_soloist_danger_message" style="color: red;"></p>
                <p id="url_soloist_success_message" style="color: green;"></p>
            </div>

            <div class="modal-footer">
                <a href="{{ route('member.new') }}"
                   class="btn btn-primary" 
                   onclick="event.preventDefault();
                   document.getElementById('registerNewMember').submit();">Continue</a>
                   <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">Change my name</button>
            </div>
        </div>       
    </div>
</div>
<!-- /Modal Confirmation Soloist -->
@endsection


@section('script')
   
    //check and return if slug value exist for soloists
    function details_soloist(urlS) {
        var first_name = document.getElementById('first_name').value; 
        var last_name = document.getElementById('last_name').value;
        var urlS = first_name+' '+last_name;

        $.get('/review/' + urlS, function(slug) {
            document.getElementById("url_soloist").innerHTML = slug[0];
            if (slug[1] == true) {
                document.getElementById("url_soloist_danger_message").innerHTML = "The ensemble's name already exist";
                $('#url_soloist_success_message').empty(); 
            }else{
                document.getElementById("url_soloist_success_message").innerHTML = "That's a new name";
                $('#url_soloist_danger_message').empty(); 
            }
        })
    };   

@endsection