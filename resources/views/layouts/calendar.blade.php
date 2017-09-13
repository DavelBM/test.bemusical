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
                    <div id='loading'>loading...</div>
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
                    <div class="col-md-2"><label for="views">Views:</label></div>
                    <div class="col-md-3">
                        <div>
                            <input type="text" class="form-control" id="view_listDay" name="view_listDay" placeholder="Name day list" value="list day">
                            <p id="options_view_listDay"></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div>
                            <input type="text" class="form-control" id="view_listWeek" name="view_listWeek" placeholder="Name week list" value="list week">
                            <p id="options_view_listWeek"></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div>
                            <input type="text" class="form-control" id="view_month" name="view_month" placeholder="Name month list" value="month">
                            <p id="options_view_month"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
@endsection

@section('script')

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
                listDay: { buttonText: 'list day' },
                listWeek: { buttonText: 'list week' },
                month: { buttonText: 'month' }
            },

            dayClick: function(date, jsEvent, view) {
                //alert('Clicked on: ' + date.format());
                //alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
                //alert('Current view: ' + view.name);

                // change the day's background color just for fun
                $(this).css('background-color', 'gray'); 
                console.log('agregar algo para: '+date.format());
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
        }    
    });

    $("#view_listWeek").blur(function() {
        var view_listWeek = document.getElementById('view_listWeek').value;

        if(!$(this).val()){
            $('#options_view_listWeek').empty();
            document.getElementById("options_view_listWeek").innerHTML = '<strong style="color: red;">this is not a string</strong>';
        }else{
            $('#options_view_listWeek').empty();
        }

    });

    $("#view_month").blur(function() {
        var view_month = document.getElementById('view_month').value;

        if(!$(this).val()){
            $('#options_view_month').empty();
            document.getElementById("options_view_month").innerHTML = '<strong style="color: red;">this is not a string</strong>';
        }else{
            $('#options_view_listWeek').empty();
        }
        
    });
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