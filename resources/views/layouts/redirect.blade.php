@extends('layouts.app')

@section('logout')
    @if(Auth::guard('web')->check())
        <a href="{{ url('/user/logout') }}">Logout user</a>
    @endif
@endsection

@if (Auth::check()) 
   @section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        
                        <div class="panel-heading"><strong><center>WE ALREADY VERIFIED YOUR ACCOUNT</center></strong></div>
            
                        <div class="panel-body">
                            Redirecting to your <strong>DASHBOARD</strong>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('js')
        <script type="text/JavaScript">
            @if(Auth::user()->type == 'soloist')
                setTimeout("location.href = '{{ route('user.dashboard') }}';", 2000);
            @elseif(Auth::user()->type == 'ensemble')
                setTimeout("location.href = '{{ route('ensemble.dashboard') }}';", 2000);
            @endif
        </script>
    @endsection 
@else
    @section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        
                        <div class="panel-heading"><strong><center>WE ALREADY VERIFIED YOUR ACCOUNT</center></strong></div>
            
                        <div class="panel-body">
                            Redirecting to <strong>LOGIN</strong> page
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('js')
        <script type="text/JavaScript">
            setTimeout("location.href = '{{ URL::to('login') }}';", 2000);
        </script>
    @endsection
@endif
