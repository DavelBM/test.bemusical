@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                
                <div class="panel-heading">Requests</div>
                @include('flash::message')
                <div class="panel-body">
                    @if(!empty($general_asks))
                    @foreach($general_asks as $ask)
                        @php
                            $dt = \Carbon\Carbon::parse($ask->date)->toDayDateTimeString();
                             // = explode("|", );
                            $get_data_address = explode("|", $ask->address);
                            $id_place = explode("id:", $get_data_address[0]);
                            $name_place = explode("address:", $get_data_address[1]);
                            $lat_place = explode("lat:", $get_data_address[2]);
                            $lng_place = explode("long:", $get_data_address[3]);
                        @endphp
                        @if($ask->assined == 0)
                            @if($ask->read == 0)
                            <button id="trigger{{$ask->id}}" class="btn btn-block btn-default" onclick="update(this)">
                                <span class="badge">
                                    new!
                                </span>
                                On {{$dt}}<span class="glyphicon glyphicon-menu-down"></span>
                            </button>
                            @else
                            <button id="trigger{{$ask->id}}" class="btn btn-block btn-default">
                                On {{$dt}}<span class="glyphicon glyphicon-menu-down"></span>
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
                                <p>Date: <strong>{{$dt}}</strong></p>
                                <p>Address: <strong>{{$name_place[1]}}</strong> <a href="{{URL::to('/admin/maps/id='.$id_place[1].'&lat='.$lat_place[1].'&lng='.$lng_place[1])}}">see on google maps</a></p>
                                <p>Length: <strong>{{$ask->duration}}</strong></p>
                                <p>Type: <strong>{{$ask->type}}</strong></p>
                                <p>Comments: <strong>{{$ask->comment}}</strong></p>

                                <div>
                                    <div class="col-md-6">
                                        {!! Form::open(['route' => 'admin.assign_user', 'method' => 'POST']) !!}
                                            <div class="form-group col-md-12">
                                                {!! Form::label('users', 'Users', ['class' => 'control-label']) !!}<br>

                                                <input type="text" name="email" list="email">
                                                <datalist id="email">
                                                    @for($i=0; $i < count($emails); $i++)
                                                        <option value="{{$emails[$i]}}">
                                                    @endfor
                                                </datalist>
                                                <!-- {!! Form::select('emails[]', $emails, $emails, ['id'=>'select'.$ask->id,'class'=>'form-control', 'multiple', 'required']) !!} -->
                                            </div>
                                            <div class="form-group col-md-12">
                                                <input id="type" type="text" class="form-control" name="type" placeholder="type of music" required>
                                            </div>
                                            <input type="hidden" name="id_request" value="{{$ask->id}}">

                                            <div class="form-group">
                                                    {!! Form::submit('Give to', ['class' => 'btn btn-primary btn-block']) !!}
                                            </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    $(document).ready(function(){
        @foreach($general_asks as $ask)
            //$("#select{{$ask->id}}").chosen({
            //        placeholder_text_multiple: 'ASSIGN TO',
            //        max_selected_options: '1'
            //});

            $("#details{{$ask->id}}").hide();
            $("#trigger{{$ask->id}}").click(function(){
                $("#details{{$ask->id}}").slideToggle();
            });
        @endforeach
    });

    function update(data) {
        var request_id = data.id;
        var id_split = request_id.split("trigger");
        var id = id_split[1];
        $.get('/admin/general/requests/update/' + id);
    }
@endsection
