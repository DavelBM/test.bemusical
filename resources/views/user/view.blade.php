@extends('layouts.app')

@section('logout')
    @if(Auth::guard('web')->check())
        <a href="{{ url('/user/logout') }}">Logout user</a>
    @endif
@endsection

@php
    if (!strpos($info->info->address, 'id:') and !strpos($info->info->address, 'address:') and !strpos($info->info->address, 'lat:') and !strpos($info->info->address, 'long:')) {
        $info->info->address = 'id:no-addres|address:no-address|lat:0|long:0';
    }
    $get_data = explode("|", $info->info->address);
    $get_address_place = explode("address:", $get_data[1]);
    $address_place = $get_address_place[1];

    $start = "";
    $end = "";
    $start = $option->start;
    $end = $option->end;
    $start_exploded = explode(':', $start);
    $end_exploded = explode(':', $end);
@endphp

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                
                <div class="panel-heading">public USER page</div>

                <div class="panel-body">
                    @if(!$errors->isEmpty())
                        <span class="help-block">
                            <strong style="color: red;">We had a problem while we was send your request, check again</strong>
                        </span>
                    @endif
                    @include('flash::message')
                    <div class="col-md-5">
                        
                        @php
                           $image = $info->info->profile_picture; 
                        @endphp
                        @if($image != 'null')
                            <img src="{{ asset("images/profile/$image") }}" class="img-circle float-left" alt="{{$info->info->profile_picture}}" width="250" height="250">
                        @else
                            <img src="{{ asset("images/profile/no-image.png") }}" class="img-circle float-left" alt="No image">
                        @endif
                        
                    </div>
                    <div class="col-md-7">
                        <!-- Displaying data -->
                        <strong>Name:</strong> {{$info->info->first_name." ".$info->info->last_name}}<br>
                        <strong>Degree:</strong> {{$info->info->degree}}<br>
                        <strong>Bio:</strong> {{$info->info->bio}}<br>
                        <strong>Location:</strong> {{$info->info->location}}<br>
                        <strong>Mile Radius:</strong> {{$info->info->mile_radious}} miles<br>
                        <strong>About Me:</strong> {{$info->info->about}}<br>
                        <!-- /Displaying data -->
                    </div>
                </div>
                <div class="panel-body">
                    <div class="col-md-4">
                        <strong>TAGS</strong><br>
                        @foreach($info->user_tags as $tag)
                            {{$tag->name}}<br>
                        @endforeach
                    </div>
                    <div class="col-md-4">
                        <strong>STYLES</strong><br>
                        @foreach($info->user_styles as $style)
                            {{$style->name}}<br>
                        @endforeach
                    </div>
                    <div class="col-md-4">
                        <strong>INSTRUMENTS</strong><br>
                        @foreach($info->user_instruments as $instrument)
                            {{$instrument->name}}<br>
                        @endforeach
                    </div>
                </div>
                <div class="panel-body">
                    <div class="col-md-12">
                        @foreach($info->user_images as $image)
                                <img src="{{ asset("images/general/$image->name") }}" class="img-rounded" alt="{{$image->name}}" width="304" height="236">
                        @endforeach
                    </div>
                </div>
                <div class="panel-body">
                    <strong>VIDEOS</strong>
                    <div class="col-md-12">
                    @foreach($info->user_videos as $video)
                        @if($video->platform == 'youtube')
                            <iframe width="100%" height="315" src="https://www.youtube.com/embed/{{$video->code}}" frameborder="0" allowfullscreen></iframe>
                        @elseif($video->platform == 'vimeo')
                            <iframe src="https://player.vimeo.com/video/{{$video->code}}" width="100%" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                        @endif
                    @endforeach
                    </div>
                </div>
                <div class="panel-body">
                    <strong>SONGS</strong>
                    <div class="col-md-12">
                    @foreach($info->user_songs as $song)
                        @if($song->platform == 'spotify')
                            <iframe src="https://open.spotify.com/embed?uri=spotify:track:{{$song->code}}&theme=white&view=coverart" 
                            frameborder="0" allowtransparency="true"></iframe>
                        @elseif($song->platform == 'soundcloud')
                            <iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/{{$song->code}}&amp;color=0066cc&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe>
                        @endif
                    @endforeach                        
                    </div>
                </div>
                <div class="panel-body">
                    <strong>Repertoir</strong>
                    <div class="col-md-12">
                    @foreach($info->user_repertoires as $repertoire)
                        @if($repertoire->visible)
                            *{{ $repertoire->repertoire_example }}<br>
                        @endif
                    @endforeach                        
                    </div>
                </div>
                
                <hr>

                <div class="panel-body">
                    <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#formRequest">Do you want to hire {{$info->info->first_name}}?</button>
                </div>
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
                {!! Form::open(['route' => 'specific.request', 'method' => 'POST']) !!}

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

                    <div class="row form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                        <label for="company" class="col-md-4 control-label">Company</label>

                        <div class="col-md-6">
                            <input id="company" type="text" class="form-control" name="company" placeholder="Your company" value="{{ old('company') }}" required>

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
                            <input id="phone" type="number" class="form-control" name="phone" placeholder="This makes the process faster" value="{{ old('phone') }}">
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
                            <input id="event_type" type="text" class="form-control" name="event_type" placeholder="What kind of music do you require?" value="{{ old('event_type') }}" required>
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
                            <!-- <input id="day" type="text" class="form-control" placeholder="Select date" class="textbox-n"  onfocus="(this.type='date')" name="day"> --> 
                            <input id="day" type="text" class="form-control" placeholder="Select date" type="date" name="day" value="{{ old('day') }}">
                            <!-- <input id="day" type="text" class="form-control" placeholder="Select date" name="day">
                            <select id="day" type="text" class="form-control" placeholder="Select date" name="day" required> -->

                            </select>
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
                            <select id="time" class="time form-control" name="time" required></select>
                           <!--  <select id="time" class="time form-control" name="time" required> -->
                                <?php

                                    // $start = "";
                                    // $end = "";
                                    // // if ($option->start == null or $option->end == null) {
                                    // //     $start = "08:00";
                                    // //     $end = "22:00";
                                    // //     $start_exploded = explode(':', $start);
                                    // //     $end_exploded = explode(':', $end);
                                    // // }else{
                                    //     $start = $option->start;
                                    //     $end = $option->end;
                                    //     $start_exploded = explode(':', $start);
                                    //     $end_exploded = explode(':', $end);
                                    //}

                                    // $tStart = strtotime($start);
                                    // $tEnd = strtotime($end);
                                    // $tNow = $tStart;

                                    // while($tNow <= $tEnd){
                                ?>
                                <?php
                                        // <option value="{{date('H:i',$tNow)}}">{{date('h:i A',$tNow)}}</option>
                                ?>
                                <?php
                                    //     $tNow = strtotime('+15 minutes',$tNow);
                                    // }
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
                        <label for="address" class="col-md-4 control-label">Location of event<p class="text-muted">Powered by google</p></label>

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
                    <input id="distance-google" type="hidden" name="distance_google" required>
                    <input id="user_id" type="hidden" class="form-control" name="user_id" value="{{$info->id}}">

                    <div class="form-group">
                        {!! Form::submit('Ask availability', ['class' => 'btn btn-primary', 'id' => 'btn-status']) !!}
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<!-- /ModalForm -->
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('css/jquery.timepicker.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('js')
    <script src="{{ asset('js/main.js') }}"></script>
    <!-- <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.js') }}"></script> -->
    <script src="{{ asset('vendor/fullcalendar/lib/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <!-- <script type="text/javascript" src="{{ asset('js/jquery.timepicker.min.js') }}"></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI&v=3.exp&libraries=places"></script>

    <script type="text/javascript">
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

            var distanceService = new google.maps.DistanceMatrixService();
            distanceService.getDistanceMatrix({
                origins: [place.formatted_address],
                destinations: ['{{$address_place}}'],
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.IMPERIAL
            },
            function (response, status) {
                if (status !== google.maps.DistanceMatrixStatus.OK) {
                    console.log('Error:', status);
                } else {
                    document.getElementById('distance-google').value = response.rows[0].elements[0].distance.text;
                    document.getElementById("btn-status").disabled = false;
                }
            });
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

    document.getElementById("btn-status").disabled = true;

    var datetime =  currentdate.getFullYear()+"-"+newmonth+"-"+newday;
    document.getElementById('day').setAttribute("min", datetime);
    //////////////------------////////////////////

    var get_date = new Date();
    var month = get_date.getMonth()+1;
    var day = get_date.getDate();

    var output_date = get_date.getFullYear()+'-'+(month<10 ? '0' : '')+month+'-'+(day<10 ? '0' : '')+day;

    $("#day").val(output_date);

    $('#day').datetimepicker({
        'format' : 'YYYY-MM-DD',
        'minDate': output_date,
        'daysOfWeekDisabled': [@if($option->monday == 0)1,@endif @if($option->tuesday == 0)2,@endif @if($option->wednesday == 0)3,@endif @if($option->thursday == 0)4,@endif @if($option->friday == 0)5,@endif @if($option->saturday == 0)6,@endif @if($option->sunday == 0)0,@endif],

        'disabledDates': [@foreach($dates as $date)"{{$date}}",@endforeach],
    }).on('changeDate', function(ev){                 
        $('#day').datepicker('hide');
    });


    $("#time").attr("disabled", "disabled");
    var selectList = $('#time');
    selectList.append('<option>(Pick an hour)</option>');
    var time_select;
    var timeBeforeEvent = {{$option->time_before_event}};
    var timeAfterEvent = {{$option->time_after_event}};

    $("#day").on("dp.change", function(e) {
        /* Asking for information to db */
        $.get( "/allow/times/date="+$('#day').val()+"&id={{$info->id}}", function(data) {
            if (data[0].length > 0) { 
                
                var dates_getting = [];
                var dates_deleting = [];
                $("#time").removeAttr("disabled");     
                $('#time').empty();

                /* Creating hours for select dropdown */
                for(var hour = {{$start_exploded[0]}}; hour <= {{$end_exploded[0]}}; hour++){
                    var ampmhour = ((hour + 11) % 12 + 1);
                    var a = hour > 11 ? "PM" : "AM";

                    loopMinute:
                    /* Creating minutes for select dropdown */
                    for (var minute = 0; minute < 60; minute = minute+15){
                        if(hour == {{$end_exploded[0]}})
                        {
                            if (minute > {{$end_exploded[1]}}) {
                                break;
                            }
                        }  

                        /* Helper variable */
                        time_select = (aZero(hour)+':'+aZero(minute));   
                        var currentTime= moment(time_select, "HH:mm");

                        loopDataLength:
                        for(var i = 0; i < data[0].length; i++) {
                            /*Here we compare tha start time and the end time of the evento to print it*/
                            var startTime = moment(data[0][i], "HH:mm").subtract(timeBeforeEvent, 'm');
                            var endTime = moment(data[1][i], "HH:mm").add(timeAfterEvent, 'm');
                            var isBetween = currentTime.isBetween(startTime, endTime);

                            /* If time called "time_select" for select exist in array received from db called "data", we break this for. We have to wait to the next iteration.*/
                            if (($.inArray(time_select, data[0]) > -1)) {break loopDataLength;}
        
                            /* We push to an array all data received */
                            //if(data != time_select){
                            dates_getting.push('<option value="'+time_select+'" >'+ampmhour+':'+aZero(minute)+' '+a+'</option>');

                            /*We select all the times busy to push them in a array*/
                            if(isBetween){
                                dates_deleting.push('<option value="'+time_select+'" >'+ampmhour+':'+aZero(minute)+' '+a+'</option>');
                            }
                            //}         
                        }
                    }        
                }

                /* With this we ensure that the array does not contain any repited element */
                var allTimes = [];
                $.each(dates_getting, function(i, el){
                    if($.inArray(el, allTimes) === -1) allTimes.push(el);
                });

                /* Times selected to create an array with blocked times */
                var blockedTimes = [];
                $.each(dates_deleting, function(i, el){
                    if($.inArray(el, blockedTimes) === -1) blockedTimes.push(el);
                });

                /* Compare both arrays to create one, without the blocked times */
                allTimes = allTimes.filter(function(val) {
                  return blockedTimes.indexOf(val) == -1;
                });
                /* Printing the array in DOM with the values of data except the times that the user is not available */
                selectList.append(allTimes);

            }else{

                var dates_getting = [];
                $("#time").removeAttr("disabled");     
                $('#time').empty();

                /* Creating hours for select dropdown */
                for(var hour = {{$start_exploded[0]}}; hour <= {{$end_exploded[0]}}; hour++){
                    var ampmhour = ((hour + 11) % 12 + 1);
                    var a = hour > 11 ? "PM" : "AM";

                    loopMinute:
                    /* Creating minutes for select dropdown */
                    for (var minute = 0; minute < 60; minute = minute+15){
                        if(hour == {{$end_exploded[0]}})
                        {
                            if (minute > {{$end_exploded[1]}}) {
                                break;
                            }
                        }  

                        /* Helper variable */
                        time_select = (aZero(hour)+':'+aZero(minute));   
                        var currentTime= moment(time_select, "HH:mm");

                        dates_getting.push('<option value="'+time_select+'" >'+ampmhour+':'+aZero(minute)+' '+a+'</option>');
                    }        
                }

                /* With this we ensure that the array does not contain any repited element */
                var allTimes = [];
                $.each(dates_getting, function(i, el){
                    if($.inArray(el, allTimes) === -1) allTimes.push(el);
                });

                /* Printing the array in DOM with the values of data except the times that the user is not available */
                selectList.append(allTimes);

            }
        });
    });

    /* helper function to add a zero to times */
    function aZero(n) {
      return n.toString().length == 1 ?  n = '0' + n: n;
    }

</script>
@endsection
