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
                @if($time >= 15 and $status == 'OK')
                <div class="panel-heading"><center>Error</center></div>

                <div class="panel-body">
                    THIS TOKEN ALREADY EXPIRED
                </div>
                @elseif($status == 'ERROR')
                <div class="panel-heading"><center>Error</center></div>

                <div class="panel-body">
                    SOMETHING WRONG HAPPEND
                </div>
                @else
                <div class="panel-heading"><center>Update your email</center></div>

                <div class="panel-body">
                    <form id="sendEmail" class="form-horizontal" method="POST" action="{{ route('admin.updating.email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                            <label for="price" class="col-md-4 control-label"></label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control" name="email" placeholder="My new email" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button onclick="confirmation_email(this)" type="button" class="btn btn-primary" data-toggle="modal" data-target="#emailModal">
                                    Send
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Confirmation Send Price -->
    <div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </h5>
                </div>

                <div class="modal-body">
                    <h3><center><strong>Is this your new email?</strong></center></h3>
                    <h1><center><strong id="emailSent"></strong></center></h1>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('updating.email') }}"
                       class="btn btn-primary" 
                       onclick="event.preventDefault();
                       document.getElementById('sendEmail').submit();">Continue</a>
                       <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">Close</button>
                </div>
            </div>       
        </div>
    </div>
    <!-- /Modal Confirmation Send Price -->
</div>

@endsection

@section('js')
    <script type="text/javascript">
        $('#sendEmail').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) { 
                e.preventDefault();
                return false;
            }
        });

        function confirmation_email(){
            var email = document.getElementById('email').value;
            document.getElementById("emailSent").innerHTML = email;
        }
    </script>
@endsection