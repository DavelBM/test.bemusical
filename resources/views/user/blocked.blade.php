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
                
                <div class="panel-heading">YOUR ACCOUNT IS BLOCKED</div>

                <div class="panel-body">
                    YOUR ACCOUNT IS BLOCKED
                </div>

                <div class="panel-body">
                    YOUR ACCOUNT IS BLOCKED
                </div>

                <div class="panel-body">
                    YOUR ACCOUNT IS BLOCKED
                </div>

                <div class="panel-body">
                    YOUR ACCOUNT IS BLOCKED
                </div>

                <div class="panel-body">
                    YOUR ACCOUNT IS BLOCKED
                </div>

                <div class="panel-body">
                    YOUR ACCOUNT IS BLOCKED
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
