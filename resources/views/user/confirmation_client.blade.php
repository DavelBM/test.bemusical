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
                
                <div class="panel-heading"><center>Hi, {{$name}}</center></div>

                <div class="panel-body">
                    <center><p>Here are the details</p>
                    <strong>User</strong><br>
                    <p><a href="{{ url('/'.$slug_user) }}">{{$name_user}}</a></p>
                    <strong>Gig's date</strong><br>
                    <p>{{$day}}</p>
                    <strong>Lenght</strong><br>
                    <p>{{$lenght}}</p>
                    <strong>Price</strong><br>
                    <p>$ {{$price}}</p></center><hr>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                        Reject
                    </button>
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#successModal">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </h5>
                </div>

                <div class="modal-body">
                    <h3><center><strong>Oh no, we skipped a beat</strong></center></h3>
                    <h5 class="text-muted"><center>Please let us know why are you rejecting this quote</center></h5>
                    <form id="rejectForm" class="form-horizontal" method="POST" action="{{ route('general.return.reject') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                            <div class="col-md-6 col-md-offset-4">
                                <input id="reject" type="text" class="form-control" name="reject" placeholder="text input" required>

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
                                <button type="submit" class="btn btn-danger">
                                    Reject qoute
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">Close</button>
                </div>
            </div>       
        </div>
    </div>
    <!-- /Modal Reject -->

    <!-- Modal Confirmation -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </h5>
                </div>

                <div class="modal-body">
                    <form action="/your-server-side-code" method="POST">
                        <script
                            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                            data-key="{{$p_key}}"
                            data-amount="999"
                            data-name="Stripe.com"
                            data-description="Widget"
                            data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                            data-locale="auto"
                            data-zip-code="true">
                        </script>
                    </form>
                    <div><a style="color: red;" onclick="rejectModal()">Cancel request booking</a></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">Close</button>
                </div>
            </div>       
        </div>
    </div>
    <!-- /Modal Confirmation -->
</div>

@endsection

@section('js')
    <script type="text/javascript">
        function rejectModal(){
            $("#successModal").modal('hide');
            $("#rejectModal").modal('show');
        }
    </script>
@endsection