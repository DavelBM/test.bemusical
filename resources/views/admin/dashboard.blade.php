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
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#passModal">
                    Change my password
                </button>
                <a href="{{ route('admin.manage_user') }}" class="btn btn-primary">
                    members
                </a>
                @if($asks_count != 0)
                    <a href="{{ route('admin.general.request') }}" class="btn btn-primary">
                        general requests <span class="badge">{{$asks_count}}</span>
                    </a>
                @else
                    <a href="{{ route('admin.general.request') }}" class="btn btn-primary">
                        general requests
                    </a>
                @endif
                    <a href="{{ route('admin.payments') }}" class="btn btn-primary">
                        Payments
                    </a>
                </div>

                    <div class="row panel-body">
                        <strong>e-mail*:</strong> {{$me->email}}
                        <button type="button" class="btn btn-xs btn-warning" onclick="changeEmail(this); return false;">Change my email</button>
                        @if(Auth::user()->permission=='higher')
                        <a href="{{ route('admin.create') }}" type="button" class="btn btn-primary btn-block">New Admin</a>
                        @endif
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
                                            @if(Auth::user()->permission=='higher')
                                                <td>
                                                    <a href="{{ route('admin.destroy', $admin->id) }}" class="btn btn-warning">Delete</a>
                                                </td>
                                            @else
                                                <td>
                                                    OK
                                                </td>
                                            @endif
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
<!-- Modal Password -->
<div class="modal fade" id="passModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update password
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h5>
            </div>

            <div class="modal-body">
                
                {!! Form::open(['route' => ['admin.updatePassAdmin', $me->id], 'id' => 'pass-form', 'method' => 'PUT']) !!}

                    <div class="row form-group{{ $errors->has('old_password') ? ' has-error' : '' }}">
                        <label for="old_password" class="col-md-4 control-label">Current password</label>

                        <div class="col-md-6">
                            <input id="old_password" type="password" class="form-control" name="old_password" required>

                            @if ($errors->has('old_password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('old_password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-4 control-label">New password</label>

                        <div class="col-md-6">
                            <input id="password" type="password" class="form-control" name="password" required>

                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row form-group">
                        <label for="password-confirm" class="col-md-4 control-label">Confirm new password</label>

                        <div class="col-md-6">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>

                {!! Form::close() !!}

            </div>

            <div class="modal-footer">
                <a href="{{ route('admin.updatePassAdmin', $me->id) }}"
                   class="btn btn-primary" 
                   onclick="event.preventDefault();
                   document.getElementById('pass-form').submit();">Update password</a>
            </div>
        </div>       
    </div>
</div>
<!-- /Modal Password -->
<!-- Announcement -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update email
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h5>
            </div>

            <div class="modal-body">
                
            <center><h3><strong>We already sent you an Email to change your password, you have 30 minutes to do it</strong></h3></center>

            </div>
        </div>       
    </div>
</div>
<!-- /Announcement -->
@endsection

@section('js')
<script type="text/javascript">
    function changeEmail(){
        $.ajax({
            type: "POST",
            url: "/admin/change/email",
            data: {
                "_token": "{{ csrf_token() }}",
            },
            dataType: 'json',
            beforeSend: function(){
                $("#emailModal").modal();
            },
            success: function(response){
                setTimeout(function(){
                    $('#emailModal').modal('hide');
                }, 2000);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                 console.log(XMLHttpRequest);
            }
        });
    }
</script>
@endsection