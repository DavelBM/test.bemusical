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
                    <strong>Name:</strong>{{$client->name}} <br>
                    <strong>Email:</strong>{{$client->email}} <br>
                    <strong>My Address:</strong> {{$client->address}}<br>
                    <strong>Company:</strong>{{$client->company}} <br>
                    <strong>My phone:</strong> {{$client->phone}}<br>
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
@endsection
