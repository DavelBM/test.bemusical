@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                
                <div class="panel-heading">Requests</div>

                <div class="panel-body">
                    @foreach($general_asks as $ask)
                        @php
                            $dt = explode("|", $ask->date);
                            $get_data_address = explode("|", $ask->address);
                            $id_place = explode("id:", $get_data_address[0]);
                            $name_place = explode("address:", $get_data_address[1]);
                            $lat_place = explode("lat:", $get_data_address[2]);
                            $lng_place = explode("long:", $get_data_address[3]);
                        @endphp
                        @if($ask->assigned == 0)
                            @if($ask->read == 0)
                            <button id="trigger{{$ask->id}}" class="btn btn-block btn-default">
                                <span class="badge">
                                    new!
                                </span>
                                On {{$dt[1]}}<span class="glyphicon glyphicon-menu-down"></span>
                            </button>
                            @else
                            <button id="trigger{{$ask->id}}" class="btn btn-block btn-default">
                                On {{$dt[1]}}<span class="glyphicon glyphicon-menu-down"></span>
                            </button>
                            @endif

                            <div id="details{{$ask->id}}">
                                <hr>
                                <p>Name: <strong>{{$ask->name}}</strong></p>
                                <p>Email: <strong>{{$ask->email}}</strong></p>
                                <p>Company: <strong>{{$ask->company}}</strong></p>
                                @if($ask->phone != null)
                                    <p>Phone: <strong>{{$ask->phone}}</strong></p>
                                @endif
                                <p>Date: <strong>{{$dt[1]}}</strong></p>
                                <p>Address: <strong>{{$name_place[1]}}</strong> <a href="{{URL::to('/admin/maps/id='.$id_place[1].'&lat='.$lat_place[1].'&lng='.$lng_place[1])}}">see on google maps</a></p>
                                <p>Length: <strong>{{$ask->duration}}</strong></p>
                                <p>Type: <strong>{{$ask->type}}</strong></p>
                                <p>Comments: <strong>{{$ask->comment}}</strong></p>

                                <div>
                                    assign to 

                                    <div class="col-md-6">
                                        {!! Form::open(['route' => 'user.instrument', 'method' => 'POST']) !!}
                                <div class="form-group col-md-12">
                                    {!! Form::label('instruments', 'Instruments', ['class' => 'control-label']) !!}<br>

                                    {!! Form::select('emails[]', $emails, $emails, ['id'=>'select-instrument','class'=>'form-control', 'multiple', 'required']) !!}
                                </div>

                                <div class="form-group">
                                        {!! Form::submit('Add', ['class' => 'btn btn-primary btn-block']) !!}
                                </div>
                            {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    
    $("#select-instrument").chosen({
            placeholder_text_multiple: 'Choose 5 instruments',
            max_selected_options: '5',
            disable_search_threshold: 10
    });
   

    $(document).ready(function(){
        @foreach($general_asks as $ask)
            $("#details{{$ask->id}}").hide();
            $("#trigger{{$ask->id}}").click(function(){
                $("#details{{$ask->id}}").slideToggle();
            });
        @endforeach
    });
@endsection
