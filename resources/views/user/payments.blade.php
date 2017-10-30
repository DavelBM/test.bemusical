@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                
                <div class="panel-heading">Payments</div>
                @include('flash::message')
                <div class="panel-body">
                    @if(!empty($payments))
                    @foreach($payments as $request)
                        @php
                            $gig_event = explode('|', $request->date);
                            $dt = \Carbon\Carbon::parse($request->payment['created_at'])->toDayDateTimeString();

                            switch ($request->payment->type) {
                                case 'stripe':
                                    $message_payment = 'Credit card ';
                                    break;
                                
                                case 'cash':
                                    $cash = $request->price-$request->payment->amount;
                                    $message_payment = 'Credit card $'.$request->payment->amount.' and cash $'.$cash;
                                    break;
                                
                                case 'transfer':
                                    $message_payment = 'Bank transfer';
                                    break;
                                
                                default:
                                    # code...
                                    break;
                            }
                        @endphp
                        <button id="trigger{{$request->id}}" class="btn btn-block btn-default">
                            Payed on {{$dt}}<span class="glyphicon glyphicon-menu-down"></span>
                        </button>

                        <div id="details{{$request->id}}">
                            <hr>
                            <p>From: <strong>{{$request->email}}</strong></p>
                            <p>To: <strong>{{$request->user->email}}</strong></p>
                            <p>Company: <strong>{{$request->company}}</strong></p>
                            @if( !empty($request->phone) )
                                <p>Phone: {{$request->phone}} <strong></strong></p>
                            @endif
                            <p>Date of event: <strong>{{$gig_event[1]}}</strong></p>
                            <p>Amount: ${{$request->payment->amount}}<strong></strong></p>
                            <p>Type: <strong>{{$message_payment}}</strong></p>
                        </div>
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
        @foreach($payments as $request)
            $("#details{{$request->id}}").hide();
            $("#trigger{{$request->id}}").click(function(){
                $("#details{{$request->id}}").slideToggle();
            });
        @endforeach
    });
@endsection
