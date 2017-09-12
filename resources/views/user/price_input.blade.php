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
                
                <div class="panel-heading">We almost finish, please fill the input</div>

                <div class="panel-body">
                    <strong>Tell us your price</strong>
                        <form class="form-horizontal" method="POST" action="{{ route('general.request.send_price') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                                <label for="price" class="col-md-4 control-label">$</label>

                                <div class="col-md-6">
                                    <input id="price" type="number" step="0.01" class="form-control" name="price" placeholder="This price will be send it to your next client" required>

                                    @if ($errors->has('price'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('price') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <input id="token" type="hidden" class="form-control" name="token" value="{{$token}}">

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Send information
                                    </button>
                                </div>
                            </div>
                        </form>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
