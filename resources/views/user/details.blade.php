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
                
                <div class="panel-heading">Details</div>
                @php
                    $dt = explode("|", $request->date);
                    $address = explode("|", $request->address);
                    $addrID = explode("id:", $address[0]);
                    $addrNAME = explode("address:", $address[1]);
                    $addrLAT = explode("lat:", $address[2]);
                    $addrLNG = explode("long:", $address[3]);
                @endphp
                <a class="btn btn-primary btn-block" type="button" href="{{ URL::to('/dashboard') }}">Return Dashboard</a>
                <div class="panel-body">
                    <center><div class="col-md-8">
                        <strong>Send your quote to {{$request->name}}</strong><br><br>
                        Gig's date: <strong>{{$dt[1]}}</strong><br>
                        <?php
                            $exploded_date = explode('|', $request->date);
                            $date = explode(' ', $exploded_date[0]);
                            $day = $date[0];
                            $time = $date[1];

                            $d = Carbon\Carbon::parse($day)->format('F j, Y');
                            $ft = Carbon\Carbon::parse($time);
                            $tt = Carbon\Carbon::parse($time);
                            $to_time = $tt->addMinutes($request->duration);
                            $duration_event = $ft->format('h:i A').' - '.$tt->format('h:i A');
                            $time_hours = $request->duration/60;
                        ?>
                        Length of performance : <strong>{{$duration_event}}</strong><br>
                        Notes from client: <strong>{{$request->event_type}}</strong><br>
                        Location: <strong>{{$addrNAME[1]}}</strong><br>
                    </div>

                    <div class="col-md-4">
                        @if($request->available == 0 and $request->nonavailable == 0 and $request->price == null and $request->accepted_price == 0)
                            <form id="sendPrice" class="form-horizontal" method="POST" action="{{ route('general.request.send_price') }}">
                            {{ csrf_field() }}
                                If you take this, the price will be send it to your next client
                                <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">

                                    <label for="price" class="col-md-2 control-label">$</label>

                                    <div class="col-md-10">
                                        <input id="price" type="number" step="0.01" class="form-control" name="price" required>
                                        @if ($errors->has('price'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('price') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <input id="token" type="hidden" class="form-control" name="token" value="{{$request->token}}">

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button onclick="
                                        $('#button_send_price').attr('class', 'btn btn-primary disable');
                                        confirmation_price(this);" type="button" class="btn btn-primary" data-toggle="modal" data-target="#priceModal">
                                            Send
                                        </button>
                                    </div>
                                </div>

                            </form>
                            <a class="btn btn-danger" type="button" href="{{ URL::to('/specified/request/invitation/'.$request->token.'0') }}">Decline</a>
                        @elseif($request->available != 0 and $request->nonavailable == 0 and $request->price != null and $request->accepted_price == 0)
                            <p style="color:green;">ACCEPTED!</p>
                            <p>For ${{$request->price}}</p>
                            <p><strong>The client does not answer yet</strong></p>
                        @elseif($request->available == 0 and $request->nonavailable != 0 and $request->accepted_price == 0)
                            <p style="color:red;">CANCELLED!</p>
                        @elseif($request->available != 0 and $request->nonavailable == 0 and $request->price != null and $request->accepted_price != 0)
                            <p style="color:green;">CONFIRMED BY CLIENT!</p>
                            <p><strong>bemusical wish the best for you!</strong></p>
                            <p>${{$request->price}}</p>
                        @endif
                    </div>
                    <div id="map"></div>
                    </center>
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
                    <h3><center><strong>You are about to send a quote to {{$request->name}} for</strong></center></h3>
                    <h1><center><strong id="priceSent"></strong></center></h1>
                    <h5><center id="info_quote" class="text-muted"></center></h5>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('general.request.send_price') }}"
                       id="button_send_price" 
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

@section('css')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
@endsection

@section('js')
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&libraries=places&callback=initMap">
</script>

<script type="text/javascript">

    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: {{$addrLAT[1]}}, lng: {{$addrLNG[1]}}},
          zoom: 15
        });

        var infowindow = new google.maps.InfoWindow();
        var service = new google.maps.places.PlacesService(map);

        service.getDetails({
          placeId: '{{$addrID[1]}}'
        }, function(place, status) {
          if (status === google.maps.places.PlacesServiceStatus.OK) {
            var marker = new google.maps.Marker({
              map: map,
              position: place.geometry.location
            });
            google.maps.event.addListener(marker, 'click', function() {
              infowindow.setContent('<div><strong>' + place.name + '</strong><br>' +
                'Place ID: ' + place.place_id + '<br>' +
                place.formatted_address + '</div>');
              infowindow.open(map, this);
            });
          }
        });
      }

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

@section('css')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
@endsection

@section('js')
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&libraries=places&callback=initAutocomplete"
         async defer></script>
@endsection
