@extends('layouts.app')

@section('content')
<div class="flex-center position-ref full-height">
    <div class="content">
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
            </div>
            <input id="place-id-principal" type="hidden" name="place_id" required>
            <input id="place-address-principal" type="hidden" name="place_address" required>
            <input id="place-geometry-principal" type="hidden" name="place_geometry" required>
            <br>
            <button type="submit" class="btn btn-block btn-primary">Search</button>
        </form>
        @if ($errors->has('distance'))
            <span class="help-block">
                <strong style="color: red;">{{ $errors->first('distance') }}</strong>
            </span>
        @else
            @if(!$errors->isEmpty())
                <span class="help-block">
                    <strong style="color: red;">We had a problem while we was sending your request, check again</strong>
                </span>
            @endif
        @endif
        @include('flash::message')
        <div class="title m-b-md">
            BeMusical.us
            <div class="row col-md-6 col-md-offset-3">
                <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#formRequest">Do you need a service?</button>
            </div>
        </div>
    </div>
</div>

<!-- ModalForm -->
<div id="formRequest" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Fill the spaces please.</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'general.request', 'method' => 'POST']) !!}

                    <div class="row form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-4 control-label">Name</label>

                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control" name="name" placeholder="Your full name" value="{{ old('name') }}" required>

                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-4 control-label">Email</label>

                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control" name="email" placeholder="Your email" value="{{ old('email') }}" required>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="address" class="col-md-4 control-label">Location of event</label>

                        <div class="col-md-6">
                            <input id="searchTextField" type="text" class="form-control" name="address" value="{{ old('address') }}" required>
                            @if ($errors->has('address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                            @endif
                            @if ($errors->has('place_id'))
                                <span class="help-block">
                                    <strong style="color: red;">Please choose a place with google suggestions</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="day" class="col-md-4 control-label">Day of performance</label>

                        <div class="col-md-6">
                            <input id="day1" type="text" class="form-control" placeholder="Select date" type="date" name="day" value="{{ old('day') }}" aria-describedby="basic-addon2" required>
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="time" class="col-md-4 control-label">Time of performance</label>

                        <div class="col-md-6">
                            <!-- <input id="time" class="time form-control" name="time" placeholder="Select time" required> -->
                            <select id="time" class="time form-control" name="time" required>
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
                            @if ($errors->has('time'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('time') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="duration" class="col-md-4 control-label">Length of performance</label>

                        <div class="col-md-6">

                            <select id="duration" class="form-control" name="duration" placeholder="Minutes" required>
                                <option value="60">min. 1 hr</option>
                                <option value="90">1 hr 30 min</option>
                                <option value="120">2 hrs</option>
                                <option value="150">2 hrs 30 min</option>
                                <option value="180">3 hrs</option>
                                <option value="210">3 hr 30 min</option>
                                <option value="240">4 hrs</option>
                                <option value="270">4 hr 30 min</option>
                                <option value="300">max. 5 hrs</option>
                            </select>
                            <!-- <input id="duration" type="number" class="form-control" name="duration" placeholder="Minutes" required> -->
                            @if ($errors->has('duration'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('duration') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="type" class="col-md-4 control-label">Type</label>

                        <div class="col-md-6">
                            <label><input type="checkbox" name="soloist" value="soloist">Soloist</label>
                            <label><input type="checkbox" name="ensemble" value="ensemble">Ensemble</label>
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="tags" class="col-md-4 control-label">Tags</label>
                        <div class="col-md-6">
                            @php
                                $tags = \App\Tag::orderBy('name', 'DES')->select('id', 'name')->get();
                            @endphp
                            @foreach($tags as $tag)
                                <label><input type="checkbox" name="tags[]" value="{{$tag->id}}">{{$tag->name}}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="instruments" class="col-md-4 control-label">Instruments</label>
                        <div class="col-md-6">
                            @php
                                $instruments = \App\Instrument::orderBy('name', 'DES')->select('id', 'name')->get();
                            @endphp
                            @foreach($instruments as $instrument)
                                <label><input type="checkbox" name="instruments[]" value="{{$instrument->id}}">{{$instrument->name}}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="styles" class="col-md-4 control-label">Styles</label>
                        <div class="col-md-6">
                            @php
                                $styles = \App\Style::orderBy('name', 'DES')->select('id', 'name')->get();
                            @endphp
                            @foreach($styles as $style)
                                <label><input type="checkbox" name="styles[]" value="{{$style->id}}">{{$style->name}}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="comment" class="col-md-4 control-label">Comments</label>

                        <div class="col-md-6">
                            <textarea id="comment" type="text" class="form-control" name="comment" placeholder="Do you have any special requirement?" value="{{ old('comment') }}" rows="5" ></textarea>
                            @if ($errors->has('comment'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('comment') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <input id="place-id" type="hidden" name="place_id" value="{{ old('place_id') }}" required>
                    <input id="place-address" type="hidden" name="place_address" value="{{ old('place_address') }}" required>
                    <input id="place-geometry" type="hidden" name="place_geometry" value="{{ old('place_geometry') }}" required>

                    <div class="form-group">
                        {!! Form::submit('Ask for service', ['class' => 'btn btn-primary']) !!}
                    </div>

                {!! Form::close() !!}
            </div>
        </div>

    </div>
</div>
<!-- /ModalForm -->
@endsection

@section('css')
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
    <style>
            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
@endsection

@section('js')
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('vendor/fullcalendar/lib/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&v=3.exp&libraries=places"></script>

    <script type="text/javascript">
    
    $(function() {
        var get_date = new Date();
        var month = get_date.getMonth()+1;
        var day = get_date.getDate();

        var output_date = get_date.getFullYear()+'-'+(month<10 ? '0' : '')+month+'-'+(day<10 ? '0' : '')+day;

        $("#day1").val(output_date);

        $('#day1').datetimepicker({
            'format' : 'YYYY-MM-DD',
            'minDate': output_date,
            @if(!$errors->isEmpty())
            'date': '{{ old("day") }}',
            @endif
        }).on('changeDate', function(ev){                 
            $('#day1').datepicker('hide');
        });
    });

    $('#searchTextFieldPrincipal').keypress(function(e){
        if ( e.which == 13 ) // Enter key = keycode 13
        {
            $(this).next().focus();  //Use whatever selector necessary to focus the 'next' input
            return false;
        }
    });

    $('#searchTextField').keypress(function(e){
        if ( e.which == 13 ) // Enter key = keycode 13
        {
            $(this).next().focus();  //Use whatever selector necessary to focus the 'next' input
            return false;
        }
    });
    //////////////Maps////////////////////
    function initialize() {

        var input = document.getElementById('searchTextField');
        var inputPrincipal = document.getElementById('searchTextFieldPrincipal');
        var autocomplete = new google.maps.places.Autocomplete(input);
        var autocompletePrincipal = new google.maps.places.Autocomplete(inputPrincipal);

        (function pacSelectFirst(input){
            // store the original event binding function
            var _addEventListener = (input.addEventListener) ? input.addEventListener : input.attachEvent;

            function addEventListenerWrapper(type, listener) {
            // Simulate a 'down arrow' keypress on hitting 'return' when no pac suggestion is selected,
            // and then trigger the original listener.

            if (type == "keydown") {
              var orig_listener = listener;
              listener = function (event) {
                var suggestion_selected = $(".pac-item-selected").length > 0;
                if (event.which == 13 && !suggestion_selected) {
                  var simulated_downarrow = $.Event("keydown", {keyCode:40, which:40})
                  orig_listener.apply(input, [simulated_downarrow]);
                }

                orig_listener.apply(input, [event]);
              };
            }

            // add the modified listener
            _addEventListener.apply(input, [type, listener]);
          }

          if (input.addEventListener)
            input.addEventListener = addEventListenerWrapper;
          else if (input.attachEvent)
            input.attachEvent = addEventListenerWrapper;

        })(inputPrincipal);

        (function pacSelectFirst(input){
            // store the original event binding function
            var _addEventListener = (input.addEventListener) ? input.addEventListener : input.attachEvent;

            function addEventListenerWrapper(type, listener) {
            // Simulate a 'down arrow' keypress on hitting 'return' when no pac suggestion is selected,
            // and then trigger the original listener.

            if (type == "keydown") {
              var orig_listener = listener;
              listener = function (event) {
                var suggestion_selected = $(".pac-item-selected").length > 0;
                if (event.which == 13 && !suggestion_selected) {
                  var simulated_downarrow = $.Event("keydown", {keyCode:40, which:40})
                  orig_listener.apply(input, [simulated_downarrow]);
                }

                orig_listener.apply(input, [event]);
              };
            }

            // add the modified listener
            _addEventListener.apply(input, [type, listener]);
          }

          if (input.addEventListener)
            input.addEventListener = addEventListenerWrapper;
          else if (input.attachEvent)
            input.attachEvent = addEventListenerWrapper;

        })(input);

        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }

            document.getElementById('place-id').value = place.place_id;
            document.getElementById('place-geometry').value = place.geometry.location;
            document.getElementById('place-address').value = place.formatted_address;
        });
        

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
