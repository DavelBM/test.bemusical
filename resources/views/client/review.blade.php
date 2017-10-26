@extends('layouts.app')

@section('logout')
    @if(Auth::guard('web')->check())
        <a href="{{ url('/client/logout') }}">Logout client</a>
    @endif
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                
                <div class="panel-heading"><center>Please send a review to <a href="{{ URL::to('/'.$slug) }}">{{$user}}</a></center></div>

                <div class="panel-body">
                    <center><strong>Gig's date</strong><br>
                    <p>{{$date[1]}}</p></center><hr>
                    <form id="sendReview" class="form-horizontal" method="POST" action="{{ route('client.store_review') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('score') ? ' has-error' : '' }}">
                            <label for="score" class="col-md-4 control-label">Score</label>

                            <label class="radio-inline"><input type="radio" name="score" value="3">Excelent</label>
                            <label class="radio-inline"><input type="radio" name="score" value="2">Good</label>
                            <label class="radio-inline"><input type="radio" name="score" value="1">Something went wrong</label>

                            @if ($errors->has('score'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('score') }}</strong>
                                </span>
                            @endif

                        </div>

                        <div class="form-group{{ $errors->has('review') ? ' has-error' : '' }}">
                            <label for="review" class="col-md-4 control-label">Please tell us how was the musician performance?</label>

                            <div class="col-md-6">
                                <textarea rows="5" id="review" type="text" class="form-control" name="review" placeholder="Describe the performace" required></textarea>

                                @if ($errors->has('review'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('review') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <input id="token" type="hidden" class="form-control" name="token" value="{{$token}}">

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button onclick="confirmation_review(this)" type="button" class="btn btn-primary" data-toggle="modal" data-target="#reviewModal">
                                    Send
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmation Send Price -->
    <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </h5>
                </div>

                <div class="modal-body">
                    <h3><center><strong id="titleModal"></strong></center></h3>
                    <h3><center><i><strong class="text-muted" id="reviewSent">REVIEW</strong></i></center></h3>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('client.store_review') }}"
                       class="btn btn-primary" 
                       id="button_send_review" 
                       onclick="event.preventDefault();
                       document.getElementById('sendReview').submit();">Continue</a>
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
        $('#sendReview').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) { 
                e.preventDefault();
                return false;
            }
        });

        function confirmation_review(){
            var review = '"'+document.getElementById('review').value+'"';
            
            if(review != '""'){
                document.getElementById("titleModal").innerHTML = "You are about to send a review to {{$user}}:";
                document.getElementById("reviewSent").innerHTML = review;
                $('#button_send_review').show();
            }else{
                document.getElementById("titleModal").innerHTML = "";
                document.getElementById("reviewSent").innerHTML = 'Type a review please';
                $('#button_send_review').hide();
            }
        }
    </script>
@endsection