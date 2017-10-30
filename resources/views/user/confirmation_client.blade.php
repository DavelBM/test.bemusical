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
                @php
                    $_s_price = explode('.', $price);
                    $_price = $_s_price[0].$_s_price[1];
                @endphp
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
                    <div><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                        Reject
                    </button>
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#successModal">
                        Confirm
                    </button></div>
                    @include('flash::message')
                    <h3><p id="statusPayment" style="color: gray;"></p></h3>
                    <h4><p id="s" style="color: red;"></p></h4>
                    <!-- <form action="{{ route('general.return.confirmed', $id) }}" method="post">
                            {{ csrf_field() }}
                        <input name="public_token">
                        <input name="account_ID">
                        <button type="submit">Submit</button>
                    </form> -->
                    @php
                        $stripe = new \Cartalyst\Stripe\Stripe('sk_test_e7FsM5lCe5UwmUEB4djNWmtz');
                        $charge = $stripe->charges()->all();
                        print_r($charge);
                    @endphp
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

                        <div class="form-group{{ $errors->has('reject') ? ' has-error' : '' }}">
                            <div class="col-md-6 col-md-offset-4">
                                <input id="reject" type="text" class="form-control" name="reject" placeholder="text input" required>

                                @if ($errors->has('reject'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('reject') }}</strong>
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

                    <button id="stripe" class="btn btn-block btn-default">
                        Credit or debit card
                    </button>

                    <div id="stripeDetails">
                    <div class="col-md-10 col-md-offset-1">
                        <script src="https://js.stripe.com/v3/"></script>
                        <form action="{{ route('general.return.confirmed', $id) }}" method="post" id="payment-form">
                            {{ csrf_field() }}
                            <div class="form-row">
                                <label for="name-element">
                                    Name
                                </label>
                                <input id="_s_name" type="text" class="form-control" name="_s_name" placeholder="Name" required>
                            </div>
                            <div class="form-row">
                                <label for="address-element">
                                    Billing Address
                                </label>
                                <input id="_s_address" type="text" class="form-control" name="_s_address" placeholder="Address" required>
                            </div>
                            <div class="form-row">
                                <label for="card-element">
                                    Credit or debit card
                                </label>
                                <div id="card-element"></div>
                                <div id="card-errors" role="alert"></div>
                            </div>
                            <div class="checkbox">
                                <label><input name="_s_save" type="checkbox" value="save">Save information</label>
                            </div>
                            <button type="submit">Submit Payment</button>
                        </form>
                    </div>
                    </div>

                    <div id="payment-request-button"></div>

                    <button id="linkButton" class="btn btn-block btn-default">
                        Bank Transfer
                    </button>

                    <a href="{{route('general.return.answer.price.cash', $token)}}" id="cash" class="btn btn-block btn-default">
                        Cash
                    </a>

                    <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <hr>
                        <center><a style="color: red;" onclick="rejectModal()">Cancel request booking</a></center>
                    </div>
                    </div>
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

@section('css')
<style type="text/css">
 /**
 * The CSS shown here will not be introduced in the Quickstart guide, but shows
 * how you can use CSS to style your Element's container.
 */
.StripeElement {
  background-color: white;
  padding: 8px 12px;
  border-radius: 4px;
  border: 1px solid transparent;
  box-shadow: 0 1px 3px 0 #e6ebf1;
  -webkit-transition: box-shadow 150ms ease;
  transition: box-shadow 150ms ease;
}

.StripeElement--focus {
  box-shadow: 0 1px 3px 0 #cfd7df;
}

.StripeElement--invalid {
  border-color: #fa755a;
}

.StripeElement--webkit-autofill {
  background-color: #fefde5 !important;
}

</style>
@endsection

@section('js')
<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
<script>
    var linkHandler = Plaid.create({
        apiVersion: 'v2',
        env: 'sandbox',
        clientName: 'Stripe/Plaid Test',
        key: 'bbbb161e192cf8202379b91f186ab7',
        product: ['auth'],
        selectAccount: true,
        forceIframe: true,
        onSuccess: function(public_token, metadata) {
            $.ajax({
                type: "POST",
                url: "/return/answer/confirmed/{{$id}}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "public_token": public_token,
                    "account_ID": metadata.account_id,
                },
                dataType: 'json',
                beforeSend: function(){
                    $('#successModal').modal('hide');
                    $('#statusPayment').show();
                    $('#statusPayment').empty();
                    $('<p/>').html('receiving information... please wait').appendTo($('#statusPayment'));
                },
                success: function(response){
                    $('#statusPayment').show();
                    $('#statusPayment').empty();
                    $.each(response.info, function (index, info) {
                        if (info.status == 'OK') {
                            $('<p/>').html(info.message).appendTo($('#statusPayment'));
                            var myVar = setInterval(myTimer, 2000);
                            function myTimer() {
                                location.href = "{{ url('/') }}/"+info.slug;
                            }
                        } else if (info.status == 'ERROR') {
                            $('<p/>').html(info.message).appendTo($('#statusPayment'));
                        }
                    });
                },
                error: function(xhr){
                    $('#statusPayment').show();
                    $('#statusPayment').empty();
                    $('<p/>').html('error with our servers, change your method payment').appendTo($('#statusPayment'));
                }
            });
        },
        onExit: function(err, metadata) {
            
            if (err != null) {

            }
        },
    });

document.getElementById('linkButton').onclick = function() {
  linkHandler.open();
};
</script>
<script type="text/javascript">

    $(document).ready(function(){
        $("#stripeDetails").hide();
        $("#stripe").click(function(){
            $("#stripeDetails").slideToggle();
            $("#paypalDetails").hide();
            $("#transferDetails").hide();
            $("#cashDetails").hide();
        });

        $("#paypalDetails").hide();
        $("#paypal").click(function(){
            $("#paypalDetails").slideToggle();
            $("#stripeDetails").hide();
            $("#transferDetails").hide();
            $("#cashDetails").hide();
        });

        $("#transferDetails").hide();
        $("#transfer").click(function(){
            $("#transferDetails").slideToggle();
            $("#stripeDetails").hide();
            $("#paypalDetails").hide();
            $("#cashDetails").hide();
        });

        $("#cashDetails").hide();
        $("#cash").click(function(){
            $("#cashDetails").slideToggle();
            $("#stripeDetails").hide();
            $("#paypalDetails").hide();
            $("#transferDetails").hide();
        });
    });

    function rejectModal(){
        $("#successModal").modal('hide');
        $("#rejectModal").modal('show');
    }

    // Create a Stripe client
    var stripe = Stripe('{{$p_key}}');

    // Create an instance of Elements
    var elements = stripe.elements();

    // Custom styling can be passed to options when creating an Element.
    // (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
        base: {
            color: '#32325d',
            lineHeight: '24px',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    // Create an instance of the card Element
    var card = elements.create('card', {style: style});

    // Add an instance of the card Element into the `card-element` <div>
    card.mount('#card-element');

    // Handle real-time validation errors from the card Element.
    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    // Create a token or display an error when the form is submitted.
    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        stripe.createToken(card).then(function(result) {
            if (result.error) {
                // Inform the user if there was an error
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                // Send the token to your server
                stripeTokenHandler(result.token);
            }
        });
    });

    function stripeTokenHandler(token) {
        // Insert the token ID into the form so it gets submitted to the server
        var form = document.getElementById('payment-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        form.appendChild(hiddenInput);

        // Submit the form
        form.submit();
    }


    ///////////////////////////////////////////////////////////////

    var paymentRequest = stripe.paymentRequest({
        country: 'US',
        currency: 'usd',
        total: {
            label: '{{$id}}. Bemusical: Payment to {{$name_user}}',
            amount: {{$_price}},
        },
    });

    var elements = stripe.elements();
    var prButton = elements.create('paymentRequestButton', {
        paymentRequest: paymentRequest,
    });
    // Check the availability of the Payment Request API first.
    paymentRequest.canMakePayment().then(function(result) {
        if (result) {
            prButton.mount('#payment-request-button');
        } else {
            document.getElementById('#payment-request-button').style.display = 'none';
        }
    });

    paymentRequest.on('token', function(ev) {
        $.ajax({
            type: "POST",
            url: "/return/answer/confirmed/{{$id}}",
            data: {
                "_token": "{{ csrf_token() }}",
                "app_token": ev.token.id
            },
            dataType: 'json',
            beforeSend: function(){
                console.log('enviando informacion');
            },
            success: function(response){
                $.each(response.info, function (index, info) {
                    if (info.status == 'OK') {
                        console.log('todo OK');
                         ev.complete('success');
                    } else {
                        console.log('algo mal');
                        ev.complete('fail');
                    }
                });
            },
            error: function(xhr){
                console.log('tuvimos un error');
            }
        });
    // paymentRequest.on('token', function(ev) {
    //     $.ajax({
    //         type: "POST",
    //         url: "/return/answer/confirmed/{{$id}}",
    //         data: {
    //             "_token": "{{ csrf_token() }}",
    //             "app_token": ev.token.id
    //         },
    //         dataType: 'json',
    //         beforeSend: function(){
    //             $('#successModal').modal('hide');
    //             $('#statusPayment').show();
    //             $('#statusPayment').empty();
    //             $('<p/>').html('receiving information... please wait').appendTo($('#statusPayment'));
    //         },
    //         success: function(response){
    //             $.each(response.info, function (index, info) {
    //                 if (info.status == 'OK') {
    //                     ev.complete('success');
    //                     $('<p/>').html(info.message).appendTo($('#statusPayment'));
    //                     var myVar = setInterval(myTimer, 1000);
    //                     function myTimer() {
    //                         location.href = "{{ url('/') }}/"+info.slug;
    //                     }
    //                 } else {
    //                     ev.complete('fail');
    //                     $('<p/>').html(info.message).appendTo($('#statusPayment'));
    //                 }
    //             });
    //         },
    //         error: function(xhr){
    //             $('#statusPayment').show();
    //             $('#statusPayment').empty();
    //             $('<p/>').html('error with our servers, change your method payment').appendTo($('#statusPayment'));
    //         }
    //     });
    });
    /////////////////////////////////////////////////////////////
</script>
@endsection