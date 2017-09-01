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
                    <strong>Tell us, what instrument do you play?</strong>
                        <form class="form-horizontal" method="POST" action="{{ route('member.add.instrument') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('instrument') ? ' has-error' : '' }}">
                                <label for="instrument" class="col-md-4 control-label">Instrument</label>

                                <div class="col-md-6">
                                    <input id="instrument" type="instrument" class="form-control" name="instrument" value="{{ old('instrument') }}" placeholder="example: piano, guitar, bass, microphone." required>

                                    @if ($errors->has('instrument'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('instrument') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <input id="id" type="hidden" class="form-control" name="id" value="{{$id}}">

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
