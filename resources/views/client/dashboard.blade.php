@extends('layouts.app')

@section('logout')
    @if(Auth::guard('client')->check())
        <a href="{{ url('/client/logout') }}">Logout client</a>
    @endif
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                
                <div class="panel-heading">dashboard CLIENT</div>
                    
                <div class="panel-body">
                    <strong>Name:</strong>{{$info->name}} <br>
                    <strong>Email:</strong>{{$client->email}} <br>
                    <strong>My Address:</strong> {{$info->address}}, {{$info->zip}}<br>
                    <strong>Company:</strong>{{$info->company}} <br>
                    <strong>My phone:</strong> {{$info->phone}}<br>

                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateInfo">
                      Update Info
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="updateInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update your info
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h5>
            </div>
            @php
                if(strpos($info->name, ' ') !== false){
                    $info_name = explode(' ', $info->name);
                    $info->name = $info_name[0];
                    $info_last_name = $info_name[1];
                }else{
                    $info_last_name = '';
                }
            @endphp
            <div class="modal-body">
                {!! Form::open(['route' => ['client.update', $info->id], 'id' => 'update-form', 'method' => 'POST']) !!}
                    <div class="row form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                        {!! Form::label('first_name', 'First Name', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('first_name', $info->name, ['class'=>'form-control', 'placeholder'=>'Type your first name', 'required']) !!}
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
                            {!! Form::text('last_name', $info_last_name, ['class'=>'form-control', 'placeholder'=>'Type your last name', 'required']) !!}
                            @if ($errors->has('last_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('last_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="address" class="col-md-4 control-label">My address<label>

                        <div class="col-md-5">
                            <input id="address" type="text" class="form-control" name="address" placeholder="My address" required>
                            @if ($errors->has('address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-md-1">
                            <input id="zip" type="number" class="form-control" name="zip" placeholder="zip" required>
                            @if ($errors->has('zip'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('zip') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                        {!! Form::label('company', 'Company', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('company', $info->company, ['class'=>'form-control', 'placeholder'=>'Tell us something amazing', 'required']) !!}
                            @if ($errors->has('bio'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('company') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone" class="col-md-4 control-label">Phone</label>

                        <div class="col-md-6">
                            <input id="phone" type="number" class="form-control" name="phone" required>
                        </div>
                        @if ($errors->has('phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                        @endif
                    </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <a href="{{ route('client.update', $info->id) }}"
                   class="btn btn-primary" 
                   onclick="event.preventDefault();
                   document.getElementById('update-form').submit();">Update data</a>
            </div>
        </div>     
        </div>
    </div>
</div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('js')
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('vendor/fullcalendar/lib/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
@endsection
