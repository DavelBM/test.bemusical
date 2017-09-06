@extends('layouts.app')

@section('content')
<div class="flex-center position-ref full-height">
    <div class="content">
        <div class="title m-b-md">
            BeMusical.us
            <div class="row col-md-6 col-md-offset-3">
                <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#formRequest">Do you need a service?</button>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- ModalForm -->
<div id="formRequest" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Fill the spaces please.</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'specific.request', 'method' => 'POST']) !!}

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
                        <label for="event_type" class="col-md-4 control-label">Music</label>

                        <div class="col-md-6">
                            <input id="event_type" type="text" class="form-control" name="event_type" placeholder="What kind of music do you require?" required>
                            @if ($errors->has('event_type'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('event_type') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="day" class="col-md-4 control-label">Day of performance</label>

                        <div class="col-md-6">
                            <input id="day" type="text" class="form-control" placeholder="Select date" class="textbox-n"  onfocus="(this.type='date')" name="day"> 
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
                            <input id="time" type="text" class="time form-control" name="time" placeholder="Select time" required>
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
                                <option value="30">30 min</option>
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

                    <input id="place-id" type="hidden" name="place_id" required>
                    <input id="place-address" type="hidden" name="place_address" required>
                    <input id="place-geometry" type="hidden" name="place_geometry" required>
                    <input id="user_id" type="hidden" class="form-control" name="user_id" value="0">

                    <!-- <div class="form-group">
                        {!! Form::submit('Ask for service', ['class' => 'btn btn-primary']) !!}
                    </div> -->

                {!! Form::close() !!}
            </div>
        </div>

    </div>
</div>
<!-- /ModalForm -->

@section('css')
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.timepicker.css') }}">
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
    <script type="text/javascript" src="{{ asset('js/jquery.timepicker.min.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&v=3.exp&libraries=places"></script>
@endsection

@section('script')
    //////////////Maps////////////////////
    function initialize() {

    var input = document.getElementById('searchTextField');
    var autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }

        document.getElementById('place-id').value = place.place_id;
        document.getElementById('place-geometry').value = place.geometry.location;
        document.getElementById('place-address').value = place.formatted_address;
      });
    }

    google.maps.event.addDomListener(window, 'load', initialize);
    //////////////----////////////////////
    //////////////Getting Date////////////////////
    var currentdate = new Date(); 

    var todaymonth = (currentdate.getMonth()+1);
    if(todaymonth == "1"){
        var newmonth = '0'+todaymonth;
    }else if(todaymonth == "2"){
        var newmonth = '0'+todaymonth;
    }else if(todaymonth == "3"){
        var newmonth = '0'+todaymonth;
    }else if(todaymonth == "4"){
        var newmonth = '0'+todaymonth;
    }else if(todaymonth == "5"){
        var newmonth = '0'+todaymonth;
    }else if(todaymonth == "6"){
        var newmonth = '0'+todaymonth;
    }else if(todaymonth == "7"){
        var newmonth = '0'+todaymonth;
    }else if(todaymonth == "8"){
        var newmonth = '0'+todaymonth;
    }else if(todaymonth == "9"){
        var newmonth = '0'+todaymonth;
    }

    var todayday = currentdate.getDate();
    if(todayday == "1"){
        var newday = '0'+todayday;
    }else if(todayday == "2"){
        var newday = '0'+todayday;
    }else if(todayday == "3"){
        var newday = '0'+todayday;
    }else if(todayday == "4"){
        var newday = '0'+todayday;
    }else if(todayday == "5"){
        var newday = '0'+todayday;
    }else if(todayday == "6"){
        var newday = '0'+todayday;
    }else if(todayday == "7"){
        var newday = '0'+todayday;
    }else if(todayday == "8"){
        var newday = '0'+todayday;
    }else if(todayday == "9"){
        var newday = '0'+todayday;
    }

    var datetime =  currentdate.getFullYear()+"-"+newmonth+"-"+newday;
    document.getElementById('day').setAttribute("min", datetime);
    //////////////------------////////////////////
    $(function() {
        $('#time').timepicker({ 
            'timeFormat': 'H:i',
            'step': 15, 
        });
    });
@endsection
