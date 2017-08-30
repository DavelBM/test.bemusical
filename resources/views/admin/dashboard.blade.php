@extends('layouts.app')

@section('logout')
    @if(Auth::guard('admin')->check())
        <a href="{{ url('/admin/logout') }}">Logout admin</a>
    @endif
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                
                <div class="panel-heading">Welcome {{ Auth::user()->name }}, currently we have {{$number_of_members}} 
                <a href="{{ route('admin.manage_user') }}" class="btn">
                members
                </a>
                </div>

                    <div class="row panel-body">
                        <a href="{{ route('admin.create') }}" type="button" class="btn btn-primary btn-block">New Admin</a>
                        <table class="table table-striped">
                            <thead>
                                <th>ID</th>
                                <th>Name</th>
                                <th>E-Mail</th>
                                <th>Type</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @foreach($admins as $admin)
                                    @if($admin->id != Auth::user()->id)
                                        <tr>
                                            <td>{{ $admin->id }}</td>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            @if($admin->permission=='higher')
                                                <td>Super Admin</td>
                                            @else
                                                <td>Admin</td>
                                            @endif
                                            <td>
                                                <a href="{{ route('admin.destroy', $admin->id) }}" class="btn btn-warning">Delete</a>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{{ $admin->id }}</td>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            @if($admin->permission=='higher')
                                                <td>Super Admin</td>
                                            @else
                                                <td>Admin</td>
                                            @endif
                                            <td>
                                                ME
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        {!! $admins->render() !!}
                    </div>

                    <div class="row panel-body">
                        <table class="table table-striped">
                            <thead>
                                <th>Add instruments</th>
                                <th>Add tags</th>
                                <th>Add styles</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        {!! Form::open(['route' => 'option.instrument', 'method' => 'POST']) !!}
                                            <div class="row form-group{{ $errors->has('instrument') ? ' has-error' : '' }}">
                                                <div class="col-md-6">
                                                    {!! Form::text('instrument', null, ['class'=>'form-control', 'placeholder'=>"Instrument's name", 'required']) !!}
                                                    @if ($errors->has('instrument'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('instrument') }}</strong>
                                                        </span>
                                                    @endif
                                                    <div class="form-group">
                                                            {!! Form::submit('Add', ['class' => 'btn btn-primary btn-block']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        {!! Form::close() !!}
                                    </td>

                                    <td>
                                        {!! Form::open(['route' => 'option.tag', 'method' => 'POST']) !!}
                                            <div class="row form-group{{ $errors->has('tag') ? ' has-error' : '' }}">
                                                <div class="col-md-6">
                                                    {!! Form::text('tag', null, ['class'=>'form-control', 'placeholder'=>"Tag's name", 'required']) !!}
                                                    @if ($errors->has('tag'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('tag') }}</strong>
                                                        </span>
                                                    @endif
                                                    <div class="form-group">
                                                            {!! Form::submit('Add', ['class' => 'btn btn-primary btn-block']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        {!! Form::close() !!}
                                    </td>

                                    <td>
                                        {!! Form::open(['route' => 'option.style', 'method' => 'POST']) !!}
                                            <div class="row form-group{{ $errors->has('style') ? ' has-error' : '' }}">
                                                <div class="col-md-6">
                                                    {!! Form::text('style', null, ['class'=>'form-control', 'placeholder'=>"Style's name", 'required']) !!}
                                                    @if ($errors->has('style'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('style') }}</strong>
                                                        </span>
                                                    @endif
                                                    <div class="form-group">
                                                            {!! Form::submit('Add', ['class' => 'btn btn-primary btn-block']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @foreach($instruments as $instrument)
                                            {{$instrument->name}}
                                            <a style="float: right !important;" href="{{ route('instrument.destroy', $instrument->id) }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a><br><br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($tags as $tag)  
                                            {{$tag->name}}<a style="float: right !important;" href="{{ route('tag.destroy', $tag->id) }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a><br><br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($styles as $style)
                                            {{$style->name}}<a style="float: right !important;" href="{{ route('style.destroy', $style->id) }}" class="btn btn-danger"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a><br><br>
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
