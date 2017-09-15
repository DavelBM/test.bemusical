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
                <!-- Button trigger modal -->
                <div class="panel-heading">
                    Here you can block, add and malipulate your dates
                    <div class="btn-group">
                        <a class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                            Options
                        </a>
                        <a class="btn btn-primary" href="{{ route('user.dashboard') }}">Return to dashboard</a>
                    </div>
                </div>
                @include('flash::message')
                <div class="panel-body">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Modify your calendar</h4>
            </div>
            <div class="modal-body">
                <div class="row form-group">
                    <div class="col-md-2"><label for="views">Buttons names:</label></div>
                    <div class="col-md-3">
                        <div>
                            <input type="text" class="form-control" id="view_listDay" name="view_listDay" placeholder="Name day list" value="{{$option->listDay}}">
                            <p id="options_view_listDay"></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div>
                            <input type="text" class="form-control" id="view_listWeek" name="view_listWeek" placeholder="Name week list" value="{{$option->listWeek}}">
                            <p id="options_view_listWeek"></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div>
                            <input type="text" class="form-control" id="view_month" name="view_month" placeholder="Name month list" value="{{$option->month}}">
                            <p id="options_view_month"></p>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3"><label for="views">Business Days:</label></div>
                    <div class="col-md-9">

                        @if($option->sunday == 1 and $option->monday == 1 and $option->tuesday == 1 and $option->wednesday == 1 and $option->thursday == 1 and $option->friday == 1 and $option->saturday == 1)<div style="color:green">You are working every day</div>@endif

                        <label><input name="monday" type="checkbox" onclick="if(this.checked){mondayFunctionTrue()}else{mondayFunctionFalse()}" @if($option->monday) checked @endif>Mon</label>
                        <label><input name="tuesday" type="checkbox" onclick="if(this.checked){tuesdayFunctionTrue()}else{tuesdayFunctionFalse()}"@if($option->tuesday) checked @endif>Tue</label>
                        <label><input name="wednesday" type="checkbox" onclick="if(this.checked){wednesdayFunctionTrue()}else{wednesdayFunctionFalse()}"@if($option->wednesday) checked @endif>Wed</label>
                        <label><input name="thursday" type="checkbox" onclick="if(this.checked){thursdayFunctionTrue()}else{thursdayFunctionFalse()}"@if($option->thursday) checked @endif>Thu</label>
                        <label><input name="friday" type="checkbox" onclick="if(this.checked){fridayFunctionTrue()}else{fridayFunctionFalse()}"@if($option->friday) checked @endif>Fri</label>
                        <label><input name="saturday" type="checkbox" onclick="if(this.checked){saturdayFunctionTrue()}else{saturdayFunctionFalse()}"@if($option->saturday) checked @endif>Sat</label>
                        <label><input name="sunday" type="checkbox" onclick="if(this.checked){sundayFunctionTrue()}else{sundayFunctionFalse()}"@if($option->sunday) checked @endif>Sun</label>
                        <p id="options_business_days"></p>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3"><label for="views">Business Hours:</label></div>
                    <div class="col-md-9">
                        <div class="col-md-6">
                            <label>Start at:<input class="form-control" name="start_business_hours" type="time" id="start_business_hours" value="{{$option->start}}"></label>
                        </div>
                        <div class="col-md-6">
                            <label>End at:<input class="form-control" name="end_business_hours" type="time" id="end_business_hours" value="{{$option->end}}"></label>
                        </div>
                        <p id="options_business_hours"></p>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3"><label for="views">Dead times:</label></div>
                    <div class="col-md-9">
                        <div class="col-md-6">
                            <label>Before events:<input class="form-control" name="dead_time_before" type="number" id="dead_time_before" value="{{$option-> time_before_event}}"> minutes</label>
                        </div>
                        <div class="col-md-6">
                            <label>After events:<input class="form-control" name="dead_time_after" type="number" id="dead_time_after" value="{{$option-> time_after_event}}"> minutes</label>
                        </div>
                        <p><strong class="text-muted small">By default you will have 30 minutes as min to accept any requests if you need more time fill this inputs with the minutes that you need</strong></p>
                        <p id="options_dead_time"></p>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-3"><label for="views">Default View:</label></div>
                    <div class="col-md-9">
                        <select class="form-control" id="default_view" name="default_view" onchange="showDefaultView()">
                            @if($option->defaultView == 'month')
                                <option value="month">Month</option>
                                <option value="listDay">Days</option>
                                <option value="listWeek">Weeks</option>
                            @elseif($option->defaultView == 'listDay')
                                <option value="listDay">Days</option>
                                <option value="month">Month</option>
                                <option value="listWeek">Weeks</option>
                            @elseif($option->defaultView == 'listWeek')
                                <option value="listWeek">Weeks</option>
                                <option value="month">Month</option>
                                <option value="listDay">Days</option>
                            @endif
                        </select>
                        <p id="options_default_view"></p>
                    </div>
                </div>
 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="calendarModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4 id="modalTitle" class="modal-title">Choose your option</h4>
            </div>
            <div id="modalBody" class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ route('user.block.day') }}">
                            {{ csrf_field() }}

                    <div class="form-group">
                        <div class="col-md-4 col-md-offset-4">
                            <center>You picked <p id="date-format-fullcalendar"></p></center>      
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="title" class="col-md-4 control-label">Name the event</label>
                        <div class="col-md-6">
                            <input class="form-control" name="title" type="text" id="title" placeholder="What are you going to do?" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="block" class="col-md-4 control-label">Block</label>

                        <div class="col-md-8">
                            <div class="col-md-6">
                                <label>From:<input class="form-control" name="start" type="time" id="start" value="{{$option->start}}"></label>
                            </div>
                            <div class="col-md-6">
                                <label>To:<input class="form-control" name="end" type="time" id="end" value="{{$option->end}}"></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4 col-md-offset-4">
                            <center>
                                <button type="submit" class="btn btn-primary">
                                    Continue
                                </button><br>
                                OR
                            </center>
                        </div>
                    </div>

                    <input type="hidden" id="user_id"  name="user_id" value="{{$user}}">
                    <input type="hidden" id="dateFullcalendarPartDay" name="date" value="">
                    <input type="hidden" id="fullOrPart" name="fullOrPart" value="part">

                </form>

                <form class="form-horizontal" method="POST" action="{{ route('user.block.day') }}">
                            {{ csrf_field() }}
                    <input type="hidden" id="user_id"  name="user_id" value="{{$user}}">
                    <input type="hidden" id="dateFullcalendarFullDay" name="date" value="">
                    <input type="hidden" id="fullOrPart" name="fullOrPart" value="full">
                    <div class="form-group">
                        <div class="col-md-4 col-md-offset-4">
                            <center>
                                <button type="submit" class="btn btn-danger">
                                    Block all day
                                </button>
                            </center>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/fullcalendar/fullcalendar.min.css') }}">
    <style type="text/css">
            
        #loading {
            display: none;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .modal {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1050;
            display: none;
            overflow: hidden;
            -webkit-overflow-scrolling: touch;
            outline: 0;
        }

        .modal-backdrop {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            background-color: #000;
        }
        .modal-backdrop.fade {
            filter: alpha(opacity=0);
            opacity: 0;
        }
        .modal-backdrop.in {
            filter: alpha(opacity=50);
            opacity: .5;
        }

    </style>
@endsection

@section('js')
    <script src="{{ asset('vendor/fullcalendar/lib/moment.min.js')}}"></script>
    <script src="{{ asset('vendor/fullcalendar/fullcalendar.min.js')}}"></script>
    <script src="{{ asset('vendor/fullcalendar/gcal.min.js')}}"></script>
    
    <script type="text/javascript">

    $('#myModal').on('hidden.bs.modal', function () {
        location.reload();
    });

    var urlcalendar = "{{ url('/') }}";

    $(document).ready(function() {
        
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'listDay,listWeek,month'
            },

            views: {
                listDay: { buttonText: '{{$option->listDay}}' },
                listWeek: { buttonText: '{{$option->listWeek}}' },
                month: { buttonText: '{{$option->month}}' }
            },

            businessHours: {
                
                dow: [ @if($option->sunday)0,@endif @if($option->monday)1,@endif @if($option->tuesday)2,@endif @if($option->wednesday)3,@endif @if($option->thursday)4,@endif @if($option->friday)5,@endif @if($option->saturday)6,@endif ],

                start: '{{$option->start}}',
                end: '{{$option->end}}',
            },

            defaultView: '{{$option->defaultView}}',
            weekNumbers: true,
            weekNumbersWithinDays: true,
            weekNumberCalculation: 'ISO',

            dayClick: function(date, jsEvent, view) {
                //$(this).css('background-color', '#f3f3f3');
                var date_picked = date.format();
                var date_string = moment(date_picked).format('LL');
                document.getElementById("date-format-fullcalendar").innerHTML = date_string;
                document.getElementById("dateFullcalendarFullDay").value = date_picked;
                document.getElementById("dateFullcalendarPartDay").value = date_picked;
                
                $('#modalTitle').html(event.title);
                $('#modalBody').html(event.description);
                $('#eventUrl').attr('href',event.url);
                $('#calendarModal').modal();

            },

            navLinks: true, // can click day/week names to navigate views
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: urlcalendar + '/events/data',  

        });
        
    });

    $("#view_listDay").blur(function() {
        var view_listDay = document.getElementById('view_listDay').value;

        if(!$(this).val()){
            $('#options_view_listDay').empty();
            document.getElementById("options_view_listDay").innerHTML = '<strong style="color: red;">this is not a string</strong>';
        }else{
            $('#options_view_listDay').empty();
            $.get('/add/calendar/options/value:'+view_listDay+'|id:view_listDay');
            document.getElementById("options_view_listDay").innerHTML = '<strong style="color: green;">Saved!</strong>';
        }    
    });

    $("#view_listWeek").blur(function() {
        var view_listWeek = document.getElementById('view_listWeek').value;

        if(!$(this).val()){
            $('#options_view_listWeek').empty();
            document.getElementById("options_view_listWeek").innerHTML = '<strong style="color: red;">this is not a string</strong>';
        }else{
            $('#options_view_listWeek').empty();
            $.get('/add/calendar/options/value:'+view_listWeek+'|id:view_listWeek');
            document.getElementById("options_view_listWeek").innerHTML = '<strong style="color: green;">Saved!</strong>';
        }

    });

    $("#view_month").blur(function() {
        var view_month = document.getElementById('view_month').value;

        if(!$(this).val()){
            $('#options_view_month').empty();
            document.getElementById("options_view_month").innerHTML = '<strong style="color: red;">this is not a string</strong>';
        }else{
            $('#options_view_listWeek').empty();
            $.get('/add/calendar/options/value:'+view_month+'|id:view_month');
            document.getElementById("options_view_month").innerHTML = '<strong style="color: green;">Saved!</strong>';
        }
        
    });

    function mondayFunctionTrue() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:true|id:monday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: green;">Monday work saved!</strong>';
    }

    function mondayFunctionFalse() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:false|id:monday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: red;">No Monday work!</strong>';
    }

    function tuesdayFunctionTrue() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:true|id:tuesday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: green;">Tuesday work saved!</strong>';
    }

    function tuesdayFunctionFalse() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:false|id:tuesday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: red;">No Tuesday work!</strong>';
    }

    function wednesdayFunctionTrue() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:true|id:wednesday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: green;">Wednesday work saved!</strong>';
    }

    function wednesdayFunctionFalse() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:false|id:wednesday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: red;">No Wednesday work!</strong>';
    }

    function thursdayFunctionTrue() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:true|id:thursday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: green;">Thursday work saved!</strong>';
    }

    function thursdayFunctionFalse() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:false|id:thursday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: red;">No Thursday work!</strong>';
    }

    function fridayFunctionTrue() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:true|id:friday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: green;">Friday work saved!</strong>';
    }

    function fridayFunctionFalse() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:false|id:friday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: red;">No Friday work!</strong>';
    }

    function saturdayFunctionTrue() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:true|id:saturday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: green;">Saturday work saved!</strong>';
    }

    function saturdayFunctionFalse() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:false|id:saturday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: red;">No Saturday work!</strong>';
    }

    function sundayFunctionTrue() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:true|id:sunday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: green;">Sunday work saved!</strong>';
    }

    function sundayFunctionFalse() {
        $('#options_business_days').empty();
        $.get('/add/calendar/options/value:false|id:sunday');
        document.getElementById("options_business_days").innerHTML = '<strong style="color: red;">No Sunday work!</strong>';
    }

    function showDefaultView() {
       var default_view = document.getElementById("default_view").value;
        $('#options_default_view').empty();
        $.get('/add/calendar/options/value:'+default_view+'|id:default_view', function(response) {
            console.log(response);
        });
        document.getElementById("options_default_view").innerHTML = '<strong style="color: green;">'+default_view+' will be your default view</strong>';

    }
    
    $("#start_business_hours").blur(function() {
        var start_business_hours = document.getElementById("start_business_hours").value;

        if($(this).val()){
            $('#options_business_hours').empty();
            $.get('/add/calendar/options/value:'+start_business_hours+'|id:start_business_hours');
            document.getElementById("options_business_hours").innerHTML = '<strong style="color: green;">The resquests could be send from: '+start_business_hours+':00 hrs</strong>';
        }
        
    });

    $("#end_business_hours").blur(function() {
        var end_business_hours = document.getElementById("end_business_hours").value;

        if($(this).val()){
            $('#options_business_hours').empty();
            $.get('/add/calendar/options/value:'+end_business_hours+'|id:end_business_hours');
            document.getElementById("options_business_hours").innerHTML = '<strong style="color: green;">The resquests could be send to: '+end_business_hours+' hrs</strong>';
        }
        
    });

    $("#dead_time_before").blur(function() {
        var dead_time_before = document.getElementById("dead_time_before").value;

        if($(this).val()){
            $('#options_dead_time').empty();
            if(dead_time_before <= 30){
                document.getElementById("options_dead_time").innerHTML = '<strong style="color: orange;">You cannot do it in less than 30 minutes</strong>';
                document.getElementById("dead_time_before").value = 30;
            }else{
                document.getElementById("options_dead_time").innerHTML = '<strong style="color: green;">You will have '+dead_time_before+' minutes to get to the events</strong>';
            }
            $.get('/add/calendar/options/value:'+dead_time_before+'|id:dead_time_before');
        }
        
    });

    $("#dead_time_after").blur(function() {
        var dead_time_after = document.getElementById("dead_time_after").value;

        if($(this).val()){
            $('#options_dead_time').empty();
            if(dead_time_after <= 30){
                document.getElementById("options_dead_time").innerHTML = '<strong style="color: orange;">You cannot do it in less than 30 minutes</strong>';
                document.getElementById("dead_time_after").value = 30;
            }else{
                document.getElementById("options_dead_time").innerHTML = '<strong style="color: green;">You will have '+dead_time_after+' minutes to get to another place after the events</strong>';
            }
            $.get('/add/calendar/options/value:'+dead_time_after+'|id:dead_time_after');
        }
        
    });
    </script>

@endsection

<?php
// @section('script')

//     $(document).ready(function() {
    
//         $('#calendar').fullCalendar({

//             header: {
//                 left: 'prev,next today',
//                 center: 'title',
//                 right: 'month,listYear'
//             },

//             displayEventTime: false, // don't show the time column in list view

//             // THIS KEY WON'T WORK IN PRODUCTION!!!
//             // To make your own Google API key, follow the directions here:
//             // http://fullcalendar.io/docs/google_calendar/
//             //googleCalendarApiKey: 'AIzaSyAiSpxjqWzkCFUzn6l1H-Lh-6mNA8OnKzI',
//             googleCalendarApiKey: 'AIzaSyDcnW6WejpTOCffshGDDb4neIrXVUA1EAE',
        
//             // US Holidays
//             events: 'en.usa#holiday@group.v.calendar.google.com',
//             //events: 'cjairmjuarez@gmail.com',
            
//             eventClick: function(event) {
//                 // opens events in a popup window
//                 window.open(event.url, 'gcalevent', 'width=700,height=600');
//                 return false;
//             },
            
//             loading: function(bool) {
//                 $('#loading').toggle(bool);
//             }
            
//         });
        
//     });
// @endsection
?>