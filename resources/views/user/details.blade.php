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
                    $addrID = explode(":", $address[0]);
                    $addrNAME = explode(":", $address[1]);
                @endphp
                <a class="btn btn-primary btn-block" type="button" href="{{ URL::to('/dashboard') }}">Return Dashboard</a>
                <div class="panel-body">
                    <div class="col-md-8">
                        Name: <strong>{{$request->name}}</strong><br>
                        Music requested: <strong>{{$request->event_type}}</strong><br>
                        Date of performance: <strong>{{$dt[1]}}</strong><br>
                        Length of performance : <strong>{{$request->duration}} minutes</strong><br>
                        Location of event : <strong>{{$addrNAME[1]}}</strong><br>
                    </div>
                    <div class="col-md-4">
                        @if($request->available == 0 and $request->nonavailable == 0)
                            <a class="btn btn-success" type="button" href="{{ URL::to('/specified/request/invitation/'.$request->token.'1') }}">Accept</a>
                            <a class="btn btn-danger" type="button" href="{{ URL::to('/specified/request/invitation/'.$request->token.'0') }}">Decline</a>
                        @else
                            @if($request->available == 1)
                                <p style="color:green;">ACCEPTED!</p>
                            @elseif($request->nonavailable == 1)
                                <p style="color:red;">DENIED!</p>
                            @endif
                        @endif
                    </div>
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
@endsection

@section('js')
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&libraries=places&callback=initMap">
    </script>
@endsection

@section('script')

    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: -33.866, lng: 151.196},
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

 @endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
@endsection

@section('js')
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&libraries=places&callback=initAutocomplete"
         async defer></script>
@endsection
