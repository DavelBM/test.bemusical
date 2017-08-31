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
                
                <div class="panel-heading">Admin the users</div>

                <div class="panel-body">
                    <table class="table table-striped">
                            <thead>
                                <th>ID</th>
                                <th>email</th>
                                <th>block users</th>
                                <th>public</th>
                                <th>Asking Reviews</th>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->email }}</td>

                                        @if($user->active == 1)
                                            <td>
                                                <a href="{{ route('admin.blockuser', $user->id) }}" class="btn btn-danger">Block</a>
                                            </td>
                                        @else
                                            <td>
                                                <a href="{{ route('admin.unlockuser', $user->id) }}" class="btn btn-success">Unlock</a>
                                            </td>
                                        @endif

                                        <!-- @if($user->active == 1)
                                            <td><strong style="color: green;">OK</strong></td>
                                        @else
                                            <td><strong style="color: red;">Bloked</strong></td>
                                        @endif -->

                                        @if($user->visible == 1)
                                            <td>
                                                <a href="{{ route('admin.nonvisible', $user->id) }}" class="btn btn-danger">Non-Visible</a>
                                            </td>
                                        @else
                                            <td>
                                                <a href="{{ route('admin.visible', $user->id) }}" class="btn btn-success">Visible</a>
                                            </td>
                                        @endif

                                        @if($user->ask_review == 1 and $user->visible == 0)
                                            <td>
                                                CHECK<br> PROFILE
                                            </td>
                                        @elseif($user->ask_review == 1 and $user->visible == 1)
                                            <td>
                                                ALREADY<br> CHECKED
                                            </td>
                                        @elseif($user->ask_review == 0 and $user->visible == 0)
                                            <td>
                                                WAITING
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {!! $users->render() !!}
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
