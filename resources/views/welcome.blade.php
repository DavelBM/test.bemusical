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
                <!-- <div class="input-group">
                    <span class="glyphicon glyphicon-time input-group-addon" id="basic-addon3"> </span>
                    <select id="time" class="time form-control" name="time" aria-describedby="basic-addon3" required>
                        <?php
                            // $start = "08:00";
                            // $end = "22:00";
                            // $tStart = strtotime($start);
                            // $tEnd = strtotime($end);
                            // $tNow = $tStart;

                            // while($tNow <= $tEnd){
                        
                            // <option value="{{date('H:i',$tNow)}}">{{date('h:i A',$tNow)}}</option>
                        
                            //     $tNow = strtotime('+15 minutes',$tNow);
                            // }
                        ?>
                    </select>
                </div> -->
                <!-- <div class="input-group">
                    <span class="glyphicon glyphicon-dashboard input-group-addon" id="basic-addon4">Duracion</span>
                    <select id="duration" class="form-control" name="duration" aria-describedby="basic-addon4" placeholder="Minutes" required>
                        <option value="0">Select the duration</option>
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
                </div> -->
                <div class="input-group">
                    <div class="radio">
                        <label><input type="checkbox" name="soloist" value="soloist">Soloist</label>
                    </div>
                    <div class="radio">
                        <label><input type="checkbox" name="ensemble" value="ensemble">Ensemble</label>
                    </div>
                </div>
               <!--  <div class="input-group">
                    <span class="glyphicon glyphicon-list-alt input-group-addon" id="basic-addon4"></span>
                    <input id="text" type="text" class="form-control" type="text" name="text" value="{{ old('duration') }}" aria-describedby="basic-addon4">
                </div> -->
            </div>
            <input id="place-id-principal" type="hidden" name="place_id" required>
            <input id="place-address-principal" type="hidden" name="place_address" required>
            <input id="place-geometry-principal" type="hidden" name="place_geometry" required>
            <input id="distance-google-principal" type="hidden" name="distance_google" required>
            <input id="time" type="hidden" name="time" value="08:00" required>
            <input id="duration" type="hidden" name="duration" value="60" required>
            <br>
            <button type="submit" class="btn btn-block btn-primary">Search</button>
        </form>
        @if(!$errors->isEmpty())
            <span class="help-block">
                <strong style="color: red;">We had a problem while we was sending your request, check again</strong>
            </span>
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
                            <input id="name" type="text" class="form-control" name="name" placeholder="Your full name" required>

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
                            <input id="email" type="email" class="form-control" name="email" placeholder="Your email" required>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                        <label for="company" class="col-md-4 control-label">Company</label>

                        <div class="col-md-6">
                            <input id="company" type="text" class="form-control" name="company" placeholder="Your company" required>

                            @if ($errors->has('company'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('company') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="phone" class="col-md-4 control-label">Phone (optional)</label>

                        <div class="col-md-6">
                            <input id="phone" type="number" class="form-control" name="phone" placeholder="This makes the process faster">
                            @if ($errors->has('phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="day" class="col-md-4 control-label">Day of performance</label>

                        <div class="col-md-6">
                            <!-- <input id="day" type="text" class="form-control" placeholder="Select date" class="textbox-n"  onfocus="(this.type='date')" name="day">  -->
                            <input id="day" type="text" class="form-control" placeholder="Select date" type="date" name="day">
                            @if ($errors->has('day'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('day') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="time" class="col-md-4 control-label">Time of performance</label>

                        <div class="col-md-6">
                            <!-- <input id="time" class="time form-control" name="time" placeholder="Select time" required> -->
                            <select id="time" class="time form-control" name="time" required>
                                <option value="0:00">Select time</option>
                                <option value="8:00">8:00AM</option>
                                <option value="8:15">8:15AM</option>
                                <option value="8:30">8:30AM</option>
                                <option value="8:45">8:45AM</option>
                                <option value="9:00">9:00AM</option>
                                <option value="9:15">9:15AM</option>
                                <option value="9:30">9:30AM</option>
                                <option value="9:45">9:45AM</option>
                                <option value="10:00">10:00AM</option>
                                <option value="10:15">10:15AM</option>
                                <option value="10:30">10:30AM</option>
                                <option value="10:45">10:45AM</option>
                                <option value="11:00">11:00AM</option>
                                <option value="11:15">11:15AM</option>
                                <option value="11:30">11:30AM</option>
                                <option value="11:45">11:45AM</option>
                                <option value="12:00">12:00PM</option>
                                <option value="12:15">12:15PM</option>
                                <option value="12:30">12:30PM</option>
                                <option value="12:45">12:45PM</option>
                                <option value="13:00">1:00PM</option>
                                <option value="13:15">1:15PM</option>
                                <option value="13:30">1:30PM</option>
                                <option value="13:45">1:45PM</option>
                                <option value="14:00">2:00PM</option>
                                <option value="14:15">2:15PM</option>
                                <option value="14:30">2:30PM</option>
                                <option value="14:45">2:45PM</option>
                                <option value="15:00">3:00PM</option>
                                <option value="15:15">3:15PM</option>
                                <option value="15:30">3:30PM</option>
                                <option value="15:45">3:45PM</option>
                                <option value="16:00">4:00PM</option>
                                <option value="16:15">4:15PM</option>
                                <option value="16:30">4:30PM</option>
                                <option value="16:45">4:45PM</option>
                                <option value="17:00">5:00PM</option>
                                <option value="17:15">5:15PM</option>
                                <option value="17:30">5:30PM</option>
                                <option value="17:45">5:45PM</option>
                                <option value="18:00">6:00PM</option>
                                <option value="18:15">6:15PM</option>
                                <option value="18:30">6:30PM</option>
                                <option value="18:45">6:45PM</option>
                                <option value="19:00">7:00PM</option>
                                <option value="19:15">7:15PM</option>
                                <option value="19:30">7:30PM</option>
                                <option value="19:45">7:45PM</option>
                                <option value="20:00">8:00PM</option>
                                <option value="20:15">8:15PM</option>
                                <option value="20:30">8:30PM</option>
                                <option value="20:45">8:45PM</option>
                                <option value="21:00">9:00PM</option>
                                <option value="21:15">9:15PM</option>
                                <option value="21:30">9:30PM</option>
                                <option value="21:45">9:45PM</option>
                                <option value="22:00">10:00PM</option>
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
                                <option value="0">Select the duration</option>
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
                            <!-- <input id="duration" type="number" class="form-control" name="duration" placeholder="Minutes" required> -->
                            @if ($errors->has('duration'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('duration') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="address" class="col-md-4 control-label">Location of event</label>

                        <div class="col-md-6">
                            <input id="searchTextField" type="text" class="form-control" name="address" required>
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
                        <label for="type" class="col-md-4 control-label">Type</label>

                        <div class="col-md-6">
                            <label class="radio-inline"><input type="radio" name="type" value="soloist">Soloist</label>
                            <label class="radio-inline"><input type="radio" name="type" value="ensemble">Ensemble</label>
                            @if ($errors->has('type'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('type') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="comment" class="col-md-4 control-label">Comments</label>

                        <div class="col-md-6">
                            <textarea id="comment" type="text" class="form-control" name="comment" placeholder="Do you have any special requirement?"  rows="10" ></textarea>
                            @if ($errors->has('comment'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('comment') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <input id="place-id" type="hidden" name="place_id" required>
                    <input id="place-address" type="hidden" name="place_address" required>
                    <input id="place-geometry" type="hidden" name="place_geometry" required>

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
    <!-- <link rel="stylesheet" href="{{ asset('css/jquery.timepicker.css') }}"> -->
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
    <!-- <script type="text/javascript" src="{{ asset('js/jquery.timepicker.min.js') }}"></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&v=3.exp&libraries=places"></script>

    <script type="text/javascript">
    //////////////Maps////////////////////
    function initialize() {

        var input = document.getElementById('searchTextField');
        var inputPrincipal = document.getElementById('searchTextFieldPrincipal');
        var autocomplete = new google.maps.places.Autocomplete(input);
        var autocompletePrincipal = new google.maps.places.Autocomplete(inputPrincipal);

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
