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
                    <div><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                        Reject
                    </button>
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#successModal">
                        Confirm
                    </button></div>
                    @include('flash::message')
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

                    <!-- <button id="paypal" class="btn btn-block btn-default">
                        PayPal
                    </button>

                    <div id="paypalDetails">
                    <div class="col-md-10 col-md-offset-1">
                        ....PAYPAL....
                    </div>
                    </div> -->

                    <button id="transfer" class="btn btn-block btn-default">
                        Bank Transfer
                    </button>

                    <div id="transferDetails">
                    <div class="col-md-10 col-md-offset-1">
                        <button id='linkButton'>Open Plaid Link</button>
                    </div>
                    </div>

                    <button id="cash" class="btn btn-block btn-default">
                        Cash
                    </button>

                    <div id="cashDetails">
                    <div class="col-md-10 col-md-offset-1">
                        <center><h2 style="color: orange;"><strong>For this option you have to pay at least 12% of ${{$price}} (${{$price*(0.12)}})</strong></h2></center>
                        <script src="https://js.stripe.com/v3/"></script>
                        <form action="{{ route('general.return.confirmed', $id) }}" method="post" id="payment-form-cash">
                            {{ csrf_field() }}
                            <div class="form-row">
                                <label for="name-element">
                                    Name
                                </label>
                                <input id="_c_name" type="text" class="form-control" name="_c_name" placeholder="Name" required>
                            </div>
                            <div class="form-row">
                                <label for="address-element">
                                    Billing Address
                                </label>
                                <input id="_c_address" type="text" class="form-control" name="_c_address" placeholder="Address" required>
                            </div>
                            <div class="form-row">
                                <label for="card-element-cash">
                                    Credit or debit card
                                </label>
                                <div id="card-element-cash"></div>
                                <div id="card-errors-cash" role="alert"></div>
                            </div>
                            <div class="checkbox">
                                <label><input name="_c_save" type="checkbox" value="save">Save information</label>
                            </div>
                            <button type="submit">Submit Payment</button>
                        </form>
                    </div>
                    </div>

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
  env: 'sandbox',
  clientName: 'Stripe/Plaid Test',
  key: '[Plaid key]',
  product: ['auth'],
  selectAccount: true,
  onSuccess: function(public_token, metadata) {
    // Send the public_token and account ID to your app server.
    console.log('public_token: ' + public_token);
    console.log('account ID: ' + metadata.account_id);
  },
  onExit: function(err, metadata) {
    // The user exited the Link flow.
    if (err != null) {
      // The user encountered a Plaid API error prior to exiting.
    }
  },
});

// Trigger the Link UI
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
    var stripeCash = Stripe('{{$p_key}}');

    // Create an instance of Elements
    var elements = stripe.elements();
    var elementsCash = stripeCash.elements();

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
    var cardCash = elementsCash.create('card', {style: style});

    // Add an instance of the card Element into the `card-element` <div>
    card.mount('#card-element');
    cardCash.mount('#card-element-cash');

    // Handle real-time validation errors from the card Element.
    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    cardCash.addEventListener('change', function(event) {
        var displayErrorCash = document.getElementById('card-errors-cash');
        if (event.error) {
            displayErrorCash.textContent = event.error.message;
        } else {
            displayErrorCash.textContent = '';
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

    var formCash = document.getElementById('payment-form-cash');
    formCash.addEventListener('submit', function(event) {
        event.preventDefault();

        stripeCash.createToken(cardCash).then(function(result) {
            if (result.error) {
                // Inform the user if there was an error
                var errorElementCash = document.getElementById('card-errors-cash');
                errorElementCash.textContent = result.error.message;
            } else {
                // Send the token to your server
                stripeTokenHandlerCash(result.token);
            }
        });
    });

    function stripeTokenHandlerCash(token) {
        // Insert the token ID into the form so it gets submitted to the server
        var formCash = document.getElementById('payment-form-cash');
        var hiddenInputCash = document.createElement('input');
        hiddenInputCash.setAttribute('type', 'hidden');
        hiddenInputCash.setAttribute('name', '_c_stripeToken');
        hiddenInputCash.setAttribute('value', token.id);
        formCash.appendChild(hiddenInputCash);

        // Submit the form
        formformCash.submit();
    }

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
</script>
@endsection