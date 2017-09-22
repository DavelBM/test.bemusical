@extends('layouts.app')

@section('logout')
    @if(Auth::guard('web')->check())
        <a href="{{ url('/user/logout') }}">Logout user</a>
    @endif
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <form class="navbar-form navbar-left" method="POST" action="{{ route('query.results') }}" role="search">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="glyphicon glyphicon-map-marker input-group-addon" id="basic-addon1"> </span>
                                    <input id="searchTextFieldPrincipal" type="text" class="form-control" name="place" aria-describedby="basic-addon1" required>
                                </div>
                                <div class="input-group">
                                    <span class="glyphicon glyphicon-calendar input-group-addon" id="basic-addon2"> </span>
                                    <input id="day" type="text" class="form-control" placeholder="Select date" type="date" name="day" value="{{ old('day') }}" aria-describedby="basic-addon2" required>
                                </div>
                                <div class="input-group">
                                    <span class="glyphicon glyphicon-time input-group-addon" id="basic-addon3"> </span>
                                    <select id="time" class="time form-control" name="time" aria-describedby="basic-addon3" required>
                                        <?php
                                            $start = "08:00";
                                            $end = "22:00";
                                            $tStart = strtotime($start);
                                            $tEnd = strtotime($end);
                                            $tNow = $tStart;

                                            while($tNow <= $tEnd){
                                        ?>
                                            <option value="{{date('H:i',$tNow)}}">{{date('h:i A',$tNow)}}</option>
                                        <?php
                                                $tNow = strtotime('+15 minutes',$tNow);
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <span class="glyphicon glyphicon-dashboard input-group-addon" id="basic-addon4">Duracion</span>
                                    <select id="duration" class="form-control" name="duration" aria-describedby="basic-addon4" placeholder="Minutes" required>
                                        <option value="60">1 hr</option>
                                        <option value="90">1 hr 30 min</option>
                                        <option value="120">2 hrs</option>
                                        <option value="150">2 hrs 30 min</option>
                                        <option value="180">3 hrs</option>
                                        <option value="210">3 hr 30 min</option>
                                        <option value="240">4 hrs</option>
                                        <option value="270">4 hr 30 min</option>
                                        <option value="300">5 hrs</option>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <div class="radio">
                                        <label><input type="radio" name="typeOf" value="soloist">Soloist</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="typeOf" value="ensemble">Ensemble</label>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <span class="glyphicon glyphicon-list-alt input-group-addon" id="basic-addon4"></span>
                                    <input id="text" type="text" class="form-control" type="text" name="text" value="{{ old('duration') }}" aria-describedby="basic-addon4">
                                </div>
                            </div>
                            <input id="place-id-principal" type="hidden" name="place_id" required>
                            <input id="place-address-principal" type="hidden" name="place_address" required>
                            <input id="place-geometry-principal" type="hidden" name="place_geometry" required>
                            <input id="distance-google-principal" type="hidden" name="distance_google" required>

                            <button type="submit" class="btn btn-default">Search</button>
                        </form>
                    </div>
                    <div class="row"><center>RESULTS for: {{$address}}. {{$date}}, {{$time}} hrs</center></div>
                </div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <div class="thumbnail">
                                <img alt="100%x200" data-src="holder.js/100%x200" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNTY4IiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDU2OCAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzEwMCV4MjAwCkNyZWF0ZWQgd2l0aCBIb2xkZXIuanMgMi42LjAuCkxlYXJuIG1vcmUgYXQgaHR0cDovL2hvbGRlcmpzLmNvbQooYykgMjAxMi0yMDE1IEl2YW4gTWFsb3BpbnNreSAtIGh0dHA6Ly9pbXNreS5jbwotLT48ZGVmcz48c3R5bGUgdHlwZT0idGV4dC9jc3MiPjwhW0NEQVRBWyNob2xkZXJfMTVlYTA3MzUyNTQgdGV4dCB7IGZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToyOHB0IH0gXV0+PC9zdHlsZT48L2RlZnM+PGcgaWQ9ImhvbGRlcl8xNWVhMDczNTI1NCI+PHJlY3Qgd2lkdGg9IjU2OCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSIyMTEuMDA3ODEyNSIgeT0iMTEyLjYiPjU2OHgyMDA8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                <div class="caption">
                                    <h3>Thumbnail label</h3>
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.{{$address}}</p>

                                    <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="thumbnail">
                                <img alt="100%x200" data-src="holder.js/100%x200" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNTY4IiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDU2OCAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzEwMCV4MjAwCkNyZWF0ZWQgd2l0aCBIb2xkZXIuanMgMi42LjAuCkxlYXJuIG1vcmUgYXQgaHR0cDovL2hvbGRlcmpzLmNvbQooYykgMjAxMi0yMDE1IEl2YW4gTWFsb3BpbnNreSAtIGh0dHA6Ly9pbXNreS5jbwotLT48ZGVmcz48c3R5bGUgdHlwZT0idGV4dC9jc3MiPjwhW0NEQVRBWyNob2xkZXJfMTVlYTA3MzUyNTQgdGV4dCB7IGZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToyOHB0IH0gXV0+PC9zdHlsZT48L2RlZnM+PGcgaWQ9ImhvbGRlcl8xNWVhMDczNTI1NCI+PHJlY3Qgd2lkdGg9IjU2OCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSIyMTEuMDA3ODEyNSIgeT0iMTEyLjYiPjU2OHgyMDA8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                <div class="caption">
                                    <h3>Thumbnail label</h3>
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.{{$address}}</p>

                                    <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
                                </div>
                            </div>
                        </div><div class="col-sm-6 col-md-4">
                            <div class="thumbnail">
                                <img alt="100%x200" data-src="holder.js/100%x200" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNTY4IiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDU2OCAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzEwMCV4MjAwCkNyZWF0ZWQgd2l0aCBIb2xkZXIuanMgMi42LjAuCkxlYXJuIG1vcmUgYXQgaHR0cDovL2hvbGRlcmpzLmNvbQooYykgMjAxMi0yMDE1IEl2YW4gTWFsb3BpbnNreSAtIGh0dHA6Ly9pbXNreS5jbwotLT48ZGVmcz48c3R5bGUgdHlwZT0idGV4dC9jc3MiPjwhW0NEQVRBWyNob2xkZXJfMTVlYTA3MzUyNTQgdGV4dCB7IGZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToyOHB0IH0gXV0+PC9zdHlsZT48L2RlZnM+PGcgaWQ9ImhvbGRlcl8xNWVhMDczNTI1NCI+PHJlY3Qgd2lkdGg9IjU2OCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSIyMTEuMDA3ODEyNSIgeT0iMTEyLjYiPjU2OHgyMDA8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                <div class="caption">
                                    <h3>Thumbnail label</h3>
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.{{$address}}</p>

                                    <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <div class="thumbnail">
                                <img alt="100%x200" data-src="holder.js/100%x200" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNTY4IiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDU2OCAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzEwMCV4MjAwCkNyZWF0ZWQgd2l0aCBIb2xkZXIuanMgMi42LjAuCkxlYXJuIG1vcmUgYXQgaHR0cDovL2hvbGRlcmpzLmNvbQooYykgMjAxMi0yMDE1IEl2YW4gTWFsb3BpbnNreSAtIGh0dHA6Ly9pbXNreS5jbwotLT48ZGVmcz48c3R5bGUgdHlwZT0idGV4dC9jc3MiPjwhW0NEQVRBWyNob2xkZXJfMTVlYTA3MzUyNTQgdGV4dCB7IGZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToyOHB0IH0gXV0+PC9zdHlsZT48L2RlZnM+PGcgaWQ9ImhvbGRlcl8xNWVhMDczNTI1NCI+PHJlY3Qgd2lkdGg9IjU2OCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSIyMTEuMDA3ODEyNSIgeT0iMTEyLjYiPjU2OHgyMDA8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                <div class="caption">
                                    <h3>Thumbnail label</h3>
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.{{$address}}</p>

                                    <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
                                </div>
                            </div>
                        </div><div class="col-sm-6 col-md-4">
                            <div class="thumbnail">
                                <img alt="100%x200" data-src="holder.js/100%x200" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNTY4IiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDU2OCAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzEwMCV4MjAwCkNyZWF0ZWQgd2l0aCBIb2xkZXIuanMgMi42LjAuCkxlYXJuIG1vcmUgYXQgaHR0cDovL2hvbGRlcmpzLmNvbQooYykgMjAxMi0yMDE1IEl2YW4gTWFsb3BpbnNreSAtIGh0dHA6Ly9pbXNreS5jbwotLT48ZGVmcz48c3R5bGUgdHlwZT0idGV4dC9jc3MiPjwhW0NEQVRBWyNob2xkZXJfMTVlYTA3MzUyNTQgdGV4dCB7IGZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToyOHB0IH0gXV0+PC9zdHlsZT48L2RlZnM+PGcgaWQ9ImhvbGRlcl8xNWVhMDczNTI1NCI+PHJlY3Qgd2lkdGg9IjU2OCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSIyMTEuMDA3ODEyNSIgeT0iMTEyLjYiPjU2OHgyMDA8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                <div class="caption">
                                    <h3>Thumbnail label</h3>
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.{{$address}}</p>

                                    <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
                                </div>
                            </div>
                        </div><div class="col-sm-6 col-md-4">
                            <div class="thumbnail">
                                <img alt="100%x200" data-src="holder.js/100%x200" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iNTY4IiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDU2OCAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzEwMCV4MjAwCkNyZWF0ZWQgd2l0aCBIb2xkZXIuanMgMi42LjAuCkxlYXJuIG1vcmUgYXQgaHR0cDovL2hvbGRlcmpzLmNvbQooYykgMjAxMi0yMDE1IEl2YW4gTWFsb3BpbnNreSAtIGh0dHA6Ly9pbXNreS5jbwotLT48ZGVmcz48c3R5bGUgdHlwZT0idGV4dC9jc3MiPjwhW0NEQVRBWyNob2xkZXJfMTVlYTA3MzUyNTQgdGV4dCB7IGZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToyOHB0IH0gXV0+PC9zdHlsZT48L2RlZnM+PGcgaWQ9ImhvbGRlcl8xNWVhMDczNTI1NCI+PHJlY3Qgd2lkdGg9IjU2OCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSIyMTEuMDA3ODEyNSIgeT0iMTEyLjYiPjU2OHgyMDA8L3RleHQ+PC9nPjwvZz48L3N2Zz4=" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                <div class="caption">
                                    <h3>Thumbnail label</h3>
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.{{$address}}</p>

                                    <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('js')
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('vendor/fullcalendar/lib/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&v=3.exp&libraries=places"></script>

    <script type="text/javascript">
    //////////////Maps////////////////////
    function initialize() {

        var inputPrincipal = document.getElementById('searchTextFieldPrincipal');
        var autocompletePrincipal = new google.maps.places.Autocomplete(inputPrincipal);

        autocompletePrincipal.addListener('place_changed', function() {
            var placePrincipal = autocompletePrincipal.getPlace();
            if (!placePrincipal.geometry) {
                return;
            }

            document.getElementById('place-id-principal').value = placePrincipal.place_id;
            document.getElementById('place-geometry-principal').value = placePrincipal.geometry.location;
            document.getElementById('place-address-principal').value = placePrincipal.formatted_address;
        });
    }

    google.maps.event.addDomListener(window, 'load', initialize);
    //////////////----////////////////////

    $(function() {
        var get_date = new Date();
        var month = get_date.getMonth()+1;
        var day = get_date.getDate();

        var output_date = get_date.getFullYear()+'-'+(month<10 ? '0' : '')+month+'-'+(day<10 ? '0' : '')+day;

        $("#day").val(output_date);

        $('#day').datetimepicker({
            'format' : 'YYYY-MM-DD',
            'minDate': output_date,
        }).on('changeDate', function(ev){                 
            $('#day').datepicker('hide');
        });
    });
    </script>
@endsection

