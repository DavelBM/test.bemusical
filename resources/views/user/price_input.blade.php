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
                
                <div class="panel-heading"><center>Send your quote to {{$name}}</center></div>

                <div class="panel-body">
                    <center><strong>Gig's date</strong><br>
                    <p>{{$day}}</p>
                    <strong>Lenght</strong><br>
                    <p>{{$lenght}}</p></center><hr>
                    <form id="sendPrice" class="form-horizontal" method="POST" action="{{ route('general.request.send_price') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                            <label for="price" class="col-md-4 control-label">$</label>

                            <div class="col-md-6">
                                <input id="price" type="number" step="0.01" class="form-control" name="price" placeholder="This price will be send it to your next client" required>

                                @if ($errors->has('price'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('price') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <input id="token" type="hidden" class="form-control" name="token" value="{{$token}}">

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button onclick="confirmation_price(this)" type="button" class="btn btn-primary" data-toggle="modal" data-target="#priceModal">
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
    <div class="modal fade" id="priceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </h5>
                </div>

                <div class="modal-body">
                    <h3><center><strong>You are about to send a quote to {{$name}} for</strong></center></h3>
                    <h1><center><strong id="priceSent"></strong></center></h1>
                    <h5><center id="info_quote" class="text-muted"></center></h5>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('general.request.send_price') }}"
                       class="btn btn-primary" 
                       onclick="event.preventDefault();
                       document.getElementById('sendPrice').submit();">Continue</a>
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
        $('#sendPrice').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) { 
                e.preventDefault();
                return false;
            }
        });

        function confirmation_price(){
            var price = document.getElementById('price').value;
            var priceperhour = price/{{$time_hours}};
            document.getElementById("priceSent").innerHTML = '$'+price;
            
            if(priceperhour < 80){
            document.getElementById("info_quote").innerHTML = 'In our experience, this is cheaper than other quotes from pro musicians. Please consider raising your quote to at least $80 per hour ($'+priceperhour+').';
                $('#button_send_price').hide();
            }else if(priceperhour > 150){
                document.getElementById("info_quote").innerHTML = 'In our experience, this is more expensive than quotes from other pro musicians. Please consider lowering your quote to at least $150 per hour($'+priceperhour+').';
                $('#button_send_price').hide();
            }else{
                document.getElementById("info_quote").innerHTML = 'You are earning $'+priceperhour+' per hour.';
                $('#button_send_price').show();
            }
        }
    </script>
@endsection