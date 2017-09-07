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
                <a href="{{ route('admin.general.request') }}" class="btn btn-primary">
                    return to requests
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                    return dashboard
                </a>
                <div class="panel-heading">Maps</div>
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('script')

    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: {{$lat}}, lng: {{$lng}}},
          zoom: 15
        });

        var infowindow = new google.maps.InfoWindow();
        var service = new google.maps.places.PlacesService(map);

        service.getDetails({
          placeId: '{{$id}}'
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
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&libraries=places&callback=initMap">
    </script>
@endsection