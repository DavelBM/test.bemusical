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
                    
                    <div class="panel-heading">
                        <strong><center>PAYOUTS</center></strong>
                    </div>
        
                    <div class="panel-body">
                        Page
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')

@endsection
