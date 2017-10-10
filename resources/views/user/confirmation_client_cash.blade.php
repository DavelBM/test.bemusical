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
                    @include('flash::message')

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
                        </form><hr>
                    </div>
                    </div>

                    <div><center><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                        Reject
                    </button>
                     <a href="{{route('general.return.answer.price', $token)}}" id="cash" class="btn btn-success">
                        Switch method payment
                    </a></center></div>
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
<script type="text/javascript">

    function rejectModal(){
        $("#successModal").modal('hide');
        $("#rejectModal").modal('show');
    }

    // Create a Stripe client
    var stripeCash = Stripe('{{$p_key}}');

    // Create an instance of Elements
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
    var cardCash = elementsCash.create('card', {style: style});

    // Add an instance of the card Element into the `card-element` <div>
    cardCash.mount('#card-element-cash');

    // Handle real-time validation errors from the card Element.
    cardCash.addEventListener('change', function(event) {
        var displayErrorCash = document.getElementById('card-errors-cash');
        if (event.error) {
            displayErrorCash.textContent = event.error.message;
        } else {
            displayErrorCash.textContent = '';
        }
    });

    // Create a token or display an error when the form is submitted.
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
        formCash.submit();
    }
</script>
@endsection