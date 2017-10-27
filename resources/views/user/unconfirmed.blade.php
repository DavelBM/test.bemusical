@extends('layouts.app')

@section('logout')
    @if(Auth::guard('web')->check())
        <a href="{{ url('/user/logout') }}">Logout user</a>
    @endif
@endsection

@section('content')
<div class="container">
    <div class="row">
    @if($errors->has('token'))
        <span class="help-block">
            <strong style="color: red;">{{ $errors->first('token') }}</strong>
        </span>
    @endif
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                PLEASE CONFIRM YOUR ACCOUNT, WE ALREADY SENT YOU AN EMAIL TO {{$info->email}}<br>
                This mail can delay 3 minutes or more, please be cool with us.
            </div>
        </div>
    </div>
</div>

@endsection
