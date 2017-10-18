@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                
                <div class="panel-heading">CHAT</div>

                <div class="panel-body">
                    <div id="vue-app">
                        <input id="user_id_chating" value="{{$id}}" type="hidden">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="panel panel-primary">
                                    <div class="panel-body-chat">
                                        <admin-chat-log :adminmessages="adminmessages"></admin-chat-log>          
                                    </div>
                                    <admin-chat-composer v-on:adminmessagesent="addadminMessage"></admin-chat-composer>
                                </div>
                            </div>
                        </div>
                        <!-- <form action="{{ route('admin.post.messages', $id) }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                            <input name="message">
                            <input name="time">
                            <input type="submit">
                        </form> -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
