@extends('layouts.app')

@section('logout')
    @if(Auth::guard('client')->check())
        <a href="{{ url('/client/logout') }}">Logout client</a>
    @endif
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                
                <div class="panel-heading">dashboard CLIENT</div>
                    
                <div class="panel-body">
                    @include('flash::message')
                    @if(!$errors->isEmpty())
                        <span class="help-block">
                            <strong style="color: red;">We had a problem while we was updating the info, check again</strong>
                        </span>
                    @endif
                    <strong>Name:</strong>{{$info->name}} <br>
                    <strong>Email:</strong>{{$client->email}} <br>
                    <strong>My Address:</strong> {{$info->address}}, {{$info->zip}}<br>
                    <strong>Company:</strong>{{$info->company}} <br>
                    <strong>My phone:</strong> {{$info->phone}}<br>

                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateInfo">
                      Update Info
                    </button>
                    <br>
                    <br>
                    Payment info saved
                    @php
                    if($info->id_costumer != null){
                        $id_costumer = $info->id_costumer;
                        $data_costumer = $stripe->customers()->find($id_costumer);
                        $account_number = $data_costumer['sources']['data'][0]['last4'];
                        try {
                            $bank_name = $data_costumer['sources']['data'][0]['brand'];
                            $d_bank = [
                                'name'   => 'Card: '.$bank_name,
                                'number' => '**** **** **** '.$account_number
                            ];
                        } catch (\Exception $e) {
                            $bank_name = $data_costumer['sources']['data'][0]['bank_name'];
                            $d_bank = [
                                'name'   => 'Bank: '.$bank_name,
                                'number' => '****'.$account_number
                            ];
                        }
                    }else{
                        $d_bank = [
                            'name'   => 'no',
                            'number' => 'info'
                        ];
                    }
                    @endphp
                    <br>{{$d_bank['name']}}
                    <br>{{$d_bank['number']}}
                </div>
                <hr>
                <div class="panel-body">
                    <center><h2><strong><i>Today gigs</i></strong></h2></center><br>
                    <table class="table table-hover table-sprite">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Address</th>
                                <th>Quote</th>
                                <th>User</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prequests as $prequest)                              
                                @php
                                    $exploded_date = explode("|", $prequest->date);
                                    $date = explode(' ', $exploded_date[0]);
                                    $day = $date[0];
                                    $time = $date[1];

                                    $ft = Carbon\Carbon::parse($time);
                                    $check_fromday = Carbon\Carbon::parse($exploded_date[0]);
                                    $check_now = Carbon\Carbon::now();
                                    $tt = Carbon\Carbon::parse($time);
                                    $to_time = $tt->addMinutes($prequest->duration);
                                    $duration_event = $ft->format('h:i A').' - '.$tt->format('h:i A');
                                @endphp
                                @if($check_fromday->isToday())
                                @if($prequest->payment != null)
                                    <tr>
                                        <td>
                                            {{$prequest->id}}   
                                        </td>
                                        <td>
                                            {{$exploded_date[1]}}<br>
                                            {{$check_fromday->diffForHumans()}}  
                                        </td>
                                        <td>
                                            {{$duration_event}}   
                                        </td>
                                        <td>
                                            @php
                                                $_address_gig = explode("|", $prequest->address);
                                                $address_gig = explode("address:", $_address_gig[1]);
                                            @endphp
                                            {{$address_gig[1]}}   
                                        </td>
                                        <td>
                                            @php
                                                switch ($prequest->payment->type) {
                                                    case 'stripe':
                                                        $message_payment = 'You paid with credit card ';
                                                        break;
                                                    
                                                    case 'cash':
                                                        $message_payment = 'You paid with credit card the amount of $'.$prequest->payment->amount;
                                                        break;
                                                    
                                                    case 'transfer':
                                                        $message_payment = 'You paid with bank transfer';
                                                        break;
                                                    
                                                    default:
                                                        # code...
                                                        break;
                                                }
                                            @endphp
                                            ${{$prequest->price}}<br>
                                            {{$message_payment}} 
                                        </td>
                                        <td>
                                            @php
                                                switch ($prequest->user->type) {
                                                    case 'soloist':
                                                        $information = $prequest->user->info;
                                                        $name_info = $information->first_name.' '.$information->last_name;
                                                        break;

                                                    case 'ensemble':
                                                        $information = $prequest->user->ensemble;
                                                        $name_info = $information->name;
                                                        break;
                                                }
                                            @endphp
                                            <a href="{{ URL::to('/'.$information->slug) }}">{{$name_info}} </a>  
                                        </td>
                                        <td>
                                            @php
                                                if($prequest->payment->_id_costumer != null){
                                                    switch ($prequest->payment->type) {
                                                        case 'stripe':
                                                            $id_costumer = $prequest->payment->_id_costumer;
                                                            $data_costumer = $stripe->customers()->find($id_costumer);
                                                            // dd($data_costumer);
                                                            $bank_name = $data_costumer['sources']['data'][0]['brand'];
                                                            $account_number = $data_costumer['sources']['data'][0]['last4'];
                                                            $data_bank = [
                                                                'name'   => $bank_name,
                                                                'number' => '**** **** **** '.$account_number
                                                            ];
                                                            break;
                                                        
                                                        case 'cash':
                                                            $id_costumer = $prequest->payment->_id_costumer;
                                                            $data_costumer = $stripe->customers()->find($id_costumer);
                                                            // dd($data_costumer);
                                                            $bank_name = $data_costumer['sources']['data'][0]['brand'];
                                                            $account_number = $data_costumer['sources']['data'][0]['last4'];
                                                            $data_bank = [
                                                                'name'   => $bank_name,
                                                                'number' => '**** **** **** '.$account_number
                                                            ];
                                                            break;
                                                        
                                                        case 'transfer':
                                                            $id_costumer = $prequest->payment->_id_costumer;
                                                            $data_costumer = $stripe->customers()->find($id_costumer);
                                                            // dd($data_costumer);
                                                            $bank_name = $data_costumer['sources']['data'][0]['bank_name'];
                                                            $account_number = $data_costumer['sources']['data'][0]['last4'];
                                                            $data_bank = [
                                                                'name'   => $bank_name,
                                                                'number' => '***'.$account_number
                                                            ];
                                                            break;
                                                        
                                                        default:
                                                            # code...
                                                            break;
                                                    }
                                                }else{
                                                    $data_bank = [
                                                        'name'   => 'did not save',
                                                        'number' => 'your information'
                                                    ];
                                                }
                                            @endphp
                                            {{$data_bank['name']}}<br>
                                            {{$data_bank['number']}}
                                        </td>
                                    </tr>
                                @endif
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    <center><h2><strong><i>Upcoming gigs</i></strong></h2></center><br>
                    <table class="table table-hover table-sprite">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Address</th>
                                <th>Quote</th>
                                <th>User</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prequests as $prequest)                              
                                @php
                                    $exploded_date = explode("|", $prequest->date);
                                    $date = explode(' ', $exploded_date[0]);
                                    $day = $date[0];
                                    $time = $date[1];

                                    $ft = Carbon\Carbon::parse($time);
                                    $check_fromday = Carbon\Carbon::parse($exploded_date[0]);
                                    $check_now = Carbon\Carbon::now();
                                    $tt = Carbon\Carbon::parse($time);
                                    $to_time = $tt->addMinutes($prequest->duration);
                                    $duration_event = $ft->format('h:i A').' - '.$tt->format('h:i A');
                                @endphp
                                @if($check_fromday->isFuture())
                                @if($prequest->payment != null)
                                    <tr>
                                        <td>
                                            {{$prequest->id}}   
                                        </td>
                                        <td>
                                            {{$exploded_date[1]}}<br>
                                            {{$check_fromday->diffForHumans()}}  
                                        </td>
                                        <td>
                                            {{$duration_event}}   
                                        </td>
                                        <td>
                                            @php
                                                $_address_gig = explode("|", $prequest->address);
                                                $address_gig = explode("address:", $_address_gig[1]);
                                            @endphp
                                            {{$address_gig[1]}}   
                                        </td>
                                        <td>
                                            @php
                                                switch ($prequest->payment->type) {
                                                    case 'stripe':
                                                        $message_payment = 'You paid with credit card ';
                                                        break;
                                                    
                                                    case 'cash':
                                                        $message_payment = 'You paid with credit card the amount of $'.$prequest->payment->amount;
                                                        break;
                                                    
                                                    case 'transfer':
                                                        $message_payment = 'You paid with bank transfer';
                                                        break;
                                                    
                                                    default:
                                                        # code...
                                                        break;
                                                }
                                            @endphp
                                            ${{$prequest->price}}<br>
                                            {{$message_payment}} 
                                        </td>
                                        <td>
                                            @php
                                                switch ($prequest->user->type) {
                                                    case 'soloist':
                                                        $information = $prequest->user->info;
                                                        $name_info = $information->first_name.' '.$information->last_name;
                                                        break;

                                                    case 'ensemble':
                                                        $information = $prequest->user->ensemble;
                                                        $name_info = $information->name;
                                                        break;
                                                }
                                            @endphp
                                            <a href="{{ URL::to('/'.$information->slug) }}">{{$name_info}} </a>  
                                        </td>
                                        <td>
                                            @php
                                                if($prequest->payment->_id_costumer != null){
                                                    switch ($prequest->payment->type) {
                                                        case 'stripe':
                                                            $id_costumer = $prequest->payment->_id_costumer;
                                                            $data_costumer = $stripe->customers()->find($id_costumer);
                                                            // dd($data_costumer);
                                                            $bank_name = $data_costumer['sources']['data'][0]['brand'];
                                                            $account_number = $data_costumer['sources']['data'][0]['last4'];
                                                            $data_bank = [
                                                                'name'   => $bank_name,
                                                                'number' => '**** **** **** '.$account_number
                                                            ];
                                                            break;
                                                        
                                                        case 'cash':
                                                            $id_costumer = $prequest->payment->_id_costumer;
                                                            $data_costumer = $stripe->customers()->find($id_costumer);
                                                            // dd($data_costumer);
                                                            $bank_name = $data_costumer['sources']['data'][0]['brand'];
                                                            $account_number = $data_costumer['sources']['data'][0]['last4'];
                                                            $data_bank = [
                                                                'name'   => $bank_name,
                                                                'number' => '**** **** **** '.$account_number
                                                            ];
                                                            break;
                                                        
                                                        case 'transfer':
                                                            $id_costumer = $prequest->payment->_id_costumer;
                                                            $data_costumer = $stripe->customers()->find($id_costumer);
                                                            // dd($data_costumer);
                                                            $bank_name = $data_costumer['sources']['data'][0]['bank_name'];
                                                            $account_number = $data_costumer['sources']['data'][0]['last4'];
                                                            $data_bank = [
                                                                'name'   => $bank_name,
                                                                'number' => '***'.$account_number
                                                            ];
                                                            break;
                                                        
                                                        default:
                                                            # code...
                                                            break;
                                                    }
                                                }else{
                                                    $data_bank = [
                                                        'name'   => 'did not save',
                                                        'number' => 'your information'
                                                    ];
                                                }
                                            @endphp
                                            {{$data_bank['name']}}<br>
                                            {{$data_bank['number']}}
                                        </td>
                                    </tr>
                                @endif
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    <center><h2><strong><i>Previous gigs</i></strong></h2></center><br>
                    <table class="table table-hover table-sprite">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Address</th>
                                <th>Quote</th>
                                <th>User</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prequests as $prequest)                              
                                @php
                                    $exploded_date = explode("|", $prequest->date);
                                    $date = explode(' ', $exploded_date[0]);
                                    $day = $date[0];
                                    $time = $date[1];

                                    $ft = Carbon\Carbon::parse($time);
                                    $check_fromday = Carbon\Carbon::parse($exploded_date[0]);
                                    $check_now = Carbon\Carbon::now();
                                    $tt = Carbon\Carbon::parse($time);
                                    $to_time = $tt->addMinutes($prequest->duration);
                                    $duration_event = $ft->format('h:i A').' - '.$tt->format('h:i A');
                                @endphp
                                @if($check_fromday->isPast())
                                @if($prequest->payment != null)
                                    <tr>
                                        <td>
                                            {{$prequest->id}}   
                                        </td>
                                        <td>
                                            {{$exploded_date[1]}}<br>
                                            {{$check_fromday->diffForHumans()}}   
                                        </td>
                                        <td>
                                            {{$duration_event}}   
                                        </td>
                                        <td>
                                            @php
                                                $_address_gig = explode("|", $prequest->address);
                                                $address_gig = explode("address:", $_address_gig[1]);
                                            @endphp
                                            {{$address_gig[1]}}   
                                        </td>
                                        <td>
                                            @php
                                                switch ($prequest->payment->type) {
                                                    case 'stripe':
                                                        $message_payment = 'You paid with credit card ';
                                                        break;
                                                    
                                                    case 'cash':
                                                        $message_payment = 'You paid with credit card the amount of $'.$prequest->payment->amount;
                                                        break;
                                                    
                                                    case 'transfer':
                                                        $message_payment = 'You paid with bank transfer';
                                                        break;
                                                    
                                                    default:
                                                        # code...
                                                        break;
                                                }
                                            @endphp
                                            ${{$prequest->price}}<br>
                                            {{$message_payment}} 
                                        </td>
                                        <td>
                                            @php
                                                switch ($prequest->user->type) {
                                                    case 'soloist':
                                                        $information = $prequest->user->info;
                                                        $name_info = $information->first_name.' '.$information->last_name;
                                                        break;

                                                    case 'ensemble':
                                                        $information = $prequest->user->ensemble;
                                                        $name_info = $information->name;
                                                        break;
                                                }
                                            @endphp
                                            <a href="{{ URL::to('/'.$information->slug) }}">{{$name_info}} </a>  
                                        </td>
                                        <td>
                                            @php
                                                if($prequest->payment->_id_costumer != null){
                                                    switch ($prequest->payment->type) {
                                                        case 'stripe':
                                                            $id_costumer = $prequest->payment->_id_costumer;
                                                            $data_costumer = $stripe->customers()->find($id_costumer);
                                                            // dd($data_costumer);
                                                            $bank_name = $data_costumer['sources']['data'][0]['brand'];
                                                            $account_number = $data_costumer['sources']['data'][0]['last4'];
                                                            $data_bank = [
                                                                'name'   => $bank_name,
                                                                'number' => '**** **** **** '.$account_number
                                                            ];
                                                            break;
                                                        
                                                        case 'cash':
                                                            $id_costumer = $prequest->payment->_id_costumer;
                                                            $data_costumer = $stripe->customers()->find($id_costumer);
                                                            // dd($data_costumer);
                                                            $bank_name = $data_costumer['sources']['data'][0]['brand'];
                                                            $account_number = $data_costumer['sources']['data'][0]['last4'];
                                                            $data_bank = [
                                                                'name'   => $bank_name,
                                                                'number' => '**** **** **** '.$account_number
                                                            ];
                                                            break;
                                                        
                                                        case 'transfer':
                                                            $id_costumer = $prequest->payment->_id_costumer;
                                                            $data_costumer = $stripe->customers()->find($id_costumer);
                                                            // dd($data_costumer);
                                                            $bank_name = $data_costumer['sources']['data'][0]['bank_name'];
                                                            $account_number = $data_costumer['sources']['data'][0]['last4'];
                                                            $data_bank = [
                                                                'name'   => $bank_name,
                                                                'number' => '***'.$account_number
                                                            ];
                                                            break;
                                                        
                                                        default:
                                                            # code...
                                                            break;
                                                    }
                                                }else{
                                                    $data_bank = [
                                                        'name'   => 'did not save',
                                                        'number' => 'your information'
                                                    ];
                                                }
                                            @endphp
                                            {{$data_bank['name']}}<br>
                                            {{$data_bank['number']}}
                                        </td>
                                    </tr>
                                @endif
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <hr>
                <div class="panel-body">
                    <center><h2><strong><i>Gigs Requests</i></strong></h2></center><br>
                    <table class="table table-hover table-sprite">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Address</th>
                                <th>Qoute</th>
                                <th>User</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                @if(!($request->available != 0 and $request->nonavailable == 0 and $request->price != null and $request->accepted_price != 0))
                                <tr>
                                    <td>
                                        {{$request->id}}   
                                    </td>
                                    <td>
                                        @php
                                            $exploded_date = explode("|", $request->date);
                                        @endphp
                                        {{$exploded_date[1]}}   
                                    </td>
                                    <td>
                                        @php
                                            $date = explode(' ', $exploded_date[0]);
                                            $day = $date[0];
                                            $time = $date[1];

                                            $ft = Carbon\Carbon::parse($time);
                                            $tt = Carbon\Carbon::parse($time);
                                            $to_time = $tt->addMinutes($request->duration);
                                            $duration_event = $ft->format('h:i A').' - '.$tt->format('h:i A');
                                        @endphp
                                        {{$duration_event}}   
                                    </td>
                                    <td>
                                        @php
                                            $_address_gig = explode("|", $request->address);
                                            $address_gig = explode("address:", $_address_gig[1]);
                                        @endphp
                                        {{$address_gig[1]}}   
                                    </td>
                                    <td>
                                        @if($request->available != 0 and $request->nonavailable == 0 and $request->price != null and $request->accepted_price == 0)
                                            <strong>${{$request->price}}</strong><br>
                                            <a class="btn btn-primary btn-sm" href="{{ URL::to('/return/answer/price/'.$request->token) }}">Method payment</a>
                                        @else
                                            ${{$request->price}}
                                        @endif   
                                    </td>
                                    <td>
                                        @php
                                            switch ($request->user->type) {
                                                case 'soloist':
                                                    $information = $request->user->info;
                                                    $name_info = $information->first_name.' '.$information->last_name;
                                                    break;

                                                case 'ensemble':
                                                    $information = $request->user->ensemble;
                                                    $name_info = $information->name;
                                                    break;
                                            }
                                        @endphp
                                        <a href="{{ URL::to('/'.$information->slug) }}">{{$name_info}} </a>  
                                    </td>
                                    <td>
                                        @if($request->available == 0 and $request->nonavailable == 0 and $request->price == null and $request->accepted_price == 0)
                                            Waiting
                                        @elseif($request->available != 0 and $request->nonavailable == 0 and $request->price != null and $request->accepted_price == 0)
                                            Quote received
                                        @elseif($request->available == 0 and $request->nonavailable != 0 and $request->accepted_price == 0)
                                            Cancelled
                                        @endif  
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <hr>
                <div class="panel-body">
                    Preferences<br>
                    @foreach($grequests as $grequest)
                        {{$grequest->type_of}}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="updateInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update your info
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h5>
            </div>
            @php
                if(strpos($info->name, ' ') !== false){
                    $info_name = explode(' ', $info->name);
                    $info->name = $info_name[0];
                    if (count($info_name) > 2){
                        $info_last_name = $info_name[1].' '.$info_name[2];
                    }else{
                        $info_last_name = $info_name[1];
                    }
                }else{
                    $info_last_name = '';
                }
            @endphp
            <div class="modal-body">
                {!! Form::open(['route' => ['client.update', $info->id], 'id' => 'update-form', 'method' => 'POST']) !!}
                    <div class="row form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                        {!! Form::label('first_name', 'First Name', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('first_name', $info->name, ['class'=>'form-control', 'placeholder'=>'Type your first name', 'required']) !!}
                            @if ($errors->has('first_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                        {!! Form::label('last_name', 'Last Name', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('last_name', $info_last_name, ['class'=>'form-control', 'placeholder'=>'Type your last name', 'required']) !!}
                            @if ($errors->has('last_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('last_name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="address" class="col-md-4 control-label">My address<label>

                        <div class="col-md-12">
                            <input id="address" type="text" class="form-control" name="address" placeholder="My address" value="{{$info->address}}" required>
                            @if ($errors->has('address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                            @endif
                            <input id="zip" type="number" class="form-control" name="zip" placeholder="zip" value="{{$info->zip}}" required>
                            @if ($errors->has('zip'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('zip') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                        {!! Form::label('company', 'Company', ['class' => 'col-md-4 control-label']) !!}
                        <div class="col-md-6">
                            {!! Form::text('company', $info->company, ['class'=>'form-control', 'placeholder'=>'Tell us something amazing', 'required']) !!}
                            @if ($errors->has('bio'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('company') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="row form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone" class="col-md-4 control-label">Phone</label>

                        <div class="col-md-6">
                            <input id="phone" type="number" placeholder="Phone number" value="{{$info->phone}}" class="form-control" name="phone" required>
                        </div>
                        @if ($errors->has('phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                        @endif
                    </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <a href="{{ route('client.update', $info->id) }}"
                   class="btn btn-primary" 
                   onclick="event.preventDefault();
                   document.getElementById('update-form').submit();">Update data</a>
            </div>
        </div>     
        </div>
    </div>
</div>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('js')
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('vendor/fullcalendar/lib/moment.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
@endsection
