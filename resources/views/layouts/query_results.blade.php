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
                                    <input id="searchTextFieldPrincipal" type="text" class="form-control" name="place" value="{{$place_r}}" aria-describedby="basic-addon1" required>
                                </div>
                                <div class="input-group">
                                    <span class="glyphicon glyphicon-calendar input-group-addon" id="basic-addon2"> </span>
                                    <input id="day" type="text" class="form-control" placeholder="Select date" type="date" name="day" value="{{$date_r}}" aria-describedby="basic-addon2" required>
                                </div>
                            </div>
                            <input id="place-id-principal" type="hidden" name="place_id" value="{{$place_id}}">
                            <input id="place-address-principal" type="hidden" name="place_address" value="{{$place_address}}">
                            <input id="place-geometry-principal" type="hidden" name="place_geometry" value="{{$place_geometry}}">

                            <button type="submit" class="btn btn-default">New Search</button>
                            <button class="btn btn-default" onclick="allUsers()">All users available in that area</button>
                        </form>
                    </div>
                    <div class="row"><center>RESULTS for: {{$address}} on {{$date}}</center></div>

                    <strong><center>filters---filters---filters---filters---filters---filters---filters---filters---filters---filters---filters---filters</center></strong>
                    <form id="filter" action="/filter/results/" method="post">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-4">
                            <span class="glyphicon glyphicon-time" id="basic-addon3"> </span>
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
                        <div class="col-md-4">
                            <span class="glyphicon glyphicon-dashboard" id="basic-addon4"></span>
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
                        <div class="col-md-4">
                            <div class="radio">
                                <label><input type="checkbox" name="soloist" value="soloist">Soloist</label>
                            </div>
                            <div class="radio">
                                <label><input type="checkbox" name="ensemble" value="ensemble">Ensemble</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            tags<br>
                            @foreach($tags as $tag)
                                <label><input type="checkbox" name="tags[]" value="{{$tag->id}}">{{$tag->name}}</label>
                            @endforeach
                        </div>
                        <div class="col-md-4">
                            instruments<br>
                            @foreach($instruments as $instrument)
                                <label><input type="checkbox" name="instruments[]" value="{{$instrument->id}}">{{$instrument->name}}</label>
                            @endforeach
                        </div>
                        <div class="col-md-4">
                            styles<br>
                            @foreach($styles as $style)
                                <label><input type="checkbox" name="styles[]" value="{{$style->id}}">{{$style->name}}</label>
                            @endforeach
                        </div>
                    </div>
                    @foreach($users as $user)
                      <input type="hidden" name="users[]" value="{{$user}}">
                    @endforeach
                        <input id="day" type="hidden" name="day" value="{{$date_r}}">
                        <input class="btn btn-primary" type="submit" value="Update">
                    </form>
                </div>

                <div class="panel-body">
                    <strong id="message" style="color: red;"></strong>
                    <div id="response" class="row">

                    </div>
                    <div id="displayUsers" class="row">
                        @for($i=0; $i < count($users); $i++)
                            <?php
                                $user = \App\User::where('email','=',$users[$i])->first();
                                if ($user->type == 'ensemble') {
                                    $pic = $user->ensemble->profile_picture;
                                }elseif ($user->type == 'soloist') {
                                    $pic = $user->info->profile_picture;
                                }                            
                            ?>
                            <div class="col-sm-6 col-md-4">
                                <div class="thumbnail">
                                    @if($user->type == 'ensemble')
                                        @if($pic != 'null')
                                            <a class="btn" href="{{ URL::to('/'.$user->ensemble->slug) }}">
                                                <img alt="100%x200" data-src="holder.js/100%x200" src="{{ asset("images/ensemble/$pic") }}" alt="{{$pic}}" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                            </a>
                                        @else
                                            <a class="btn" href="{{ URL::to('/'.$user->info->slug) }}">
                                                <img alt="100%x200" data-src="holder.js/100%x200" src="{{ asset("images/profile/no-image.png") }}" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                            </a>
                                        @endif 
                                    @elseif($user->type == 'soloist')
                                        @if($pic != 'null')
                                            <a class="btn" href="{{ URL::to('/'.$user->info->slug) }}">
                                                <img alt="100%x200" data-src="holder.js/100%x200" src="{{ asset("images/profile/$pic") }}" alt="{{$pic}}" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                            </a>
                                        @else
                                            <a class="btn" href="{{ URL::to('/'.$user->info->slug) }}">
                                                <img alt="100%x200" data-src="holder.js/100%x200" src="{{ asset("images/profile/no-image.png") }}" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                            </a>
                                        @endif                                        
                                    @endif
                                    <div class="caption">
                                        @if($user->type == 'ensemble')
                                            <h3>{{$user->ensemble->name}}</h3>
                                        @elseif($user->type == 'soloist')
                                            <h3>{{$user->info->first_name.' '.$user->info->last_name}}</h3>
                                        @endif
                                        @if($user->type == 'ensemble')
                                            <p>{{$user->ensemble->summary}}</p>
                                        @elseif($user->type == 'soloist')
                                            <p>{{$user->info->bio}}</p>
                                        @endif
                                        @if($user->type == 'ensemble')
                                            <p><a href="{{ URL::to('/'.$user->ensemble->slug) }}" class="btn btn-primary" role="button">See profile</a> </p>
                                        @elseif($user->type == 'soloist')
                                            <p><a href="{{ URL::to('/'.$user->info->slug) }}" class="btn btn-primary" role="button">See profile</a> </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endfor
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
            @if($date_r != null)
            'date': '{{$date_r}}',
            @endif
        }).on('changeDate', function(ev){                 
            $('#day').datepicker('hide');
        });

        //$("#day").datepicker("date", "02-17-2012");
    });

    $(function() {
        // $("#filter").on("submit", function(e) {
        //     e.preventDefault();
        //     // $.ajax({
        //     //     url: $(this).attr("action"),
        //     //     type: 'POST',
        //     //     data: $(this).serialize(),
        //     //     // beforeSend: function() {
        //     //     //     $("#message").html("loading...").removeAttr('class','hdiv');
        //     //     //     $('#response').attr('class','hdiv');
        //     //     //     $('#displayUsers').attr('class','hdiv');
        //     //     // },
        //     //     // success: function(data) {
        //     //     //     $("#message").attr('class','hdiv');
        //     //     //     $('#response').removeAttr('class','hdiv').html(data);
        //     //     // }
        //     //     beforeSend: function() {
        //     //         $("#message").html("loading...").show();
        //     //         $('#response').hide();
        //     //         $('#displayUsers').hide();
        //     //     },
        //     //     success: function(data) {
        //     //         $("#message").hide();
        //     //         $('#response').html(data).show();
        //     //     }
        //     // });
        // });
    });

    function allUsers(){
        $('#displayUsers').show();
        $('#response').hide();
    }
    </script>
@endsection

