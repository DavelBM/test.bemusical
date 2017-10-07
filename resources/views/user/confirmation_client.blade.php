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
                    <script src='https://js.stripe.com/v2/' type='text/javascript'></script>
                    <form accept-charset="UTF-8" action="{{ route('general.return.confirmed') }}" class="require-validation"
                        data-cc-on-file="false"
                        data-stripe-publishable-key="{{$p_key}}"
                        id="payment-form" method="post">
                        {{ csrf_field() }}
                        <div class='form-row'>
                            <div class='col-xs-12 form-group required'>
                                <label class='control-label'>Name on Card</label> <input
                                    class='form-control' size='4' type='text'>
                            </div>
                        </div>
                        <div class='form-row'>
                            <div class='col-xs-12 form-group card required'>
                                <label class='control-label'>Card Number</label> <input
                                    autocomplete='off' class='form-control card-number' size='20'
                                    type='text'>
                            </div>
                        </div>
                        <div class='form-row'>
                            <div class='col-xs-4 form-group cvc required'>
                                <label class='control-label'>CVC</label> <input autocomplete='off'
                                    class='form-control card-cvc' placeholder='ex. 311' size='4'
                                    type='text'>
                            </div>
                            <div class='col-xs-4 form-group expiration required'>
                                <label class='control-label'>Expiration</label> <input
                                    class='form-control card-expiry-month' placeholder='MM' size='2'
                                    type='text'>
                            </div>
                            <div class='col-xs-4 form-group expiration required'>
                                <label class='control-label'> </label> <input
                                    class='form-control card-expiry-year' placeholder='YYYY' size='4'
                                    type='text'>
                            </div>
                        </div>
                        <div class='form-row'>
                            <div class='col-md-12'>
                                <div class='form-control total btn btn-info'>
                                    Total: <span class='amount'>${{$price}}</span>
                                </div>
                            </div>
                        </div>
                        <div class='form-row'>
                            <div class='col-md-12 form-group'>
                                <button class='form-control btn btn-primary submit-button'
                                    type='submit' style="margin-top: 10px;">Pay »</button>
                            </div>
                        </div>
                        <div class='form-row'>
                            <div class='col-md-12 error form-group hide'>
                                <div class='alert-danger alert'>Please correct the errors and try
                                    again.</div>
                            </div>
                        </div>
                    </form>
                    <div>
                        <a style="color: red;" onclick="rejectModal()">Cancel request booking</a>
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

@section('js')
    <script type="text/javascript">
        function rejectModal(){
            $("#successModal").modal('hide');
            $("#rejectModal").modal('show');
        }

$(function() {
  $('form.require-validation').bind('submit', function(e) {
    var $form         = $(e.target).closest('form'),
        inputSelector = ['input[type=email]', 'input[type=password]',
                         'input[type=text]', 'input[type=file]',
                         'textarea'].join(', '),
        $inputs       = $form.find('.required').find(inputSelector),
        $errorMessage = $form.find('div.error'),
        valid         = true;

    $errorMessage.addClass('hide');
    $('.has-error').removeClass('has-error');
    $inputs.each(function(i, el) {
      var $input = $(el);
      if ($input.val() === '') {
        $input.parent().addClass('has-error');
        $errorMessage.removeClass('hide');
        e.preventDefault(); // cancel on first error
      }
    });
  });
});

$(function() {
  var $form = $("#payment-form");

  $form.on('submit', function(e) {
    if (!$form.data('cc-on-file')) {
      e.preventDefault();
      Stripe.setPublishableKey($form.data('stripe-publishable-key'));
      Stripe.createToken({
        number: $('.card-number').val(),
        cvc: $('.card-cvc').val(),
        exp_month: $('.card-expiry-month').val(),
        exp_year: $('.card-expiry-year').val()
      }, stripeResponseHandler);
    }
  });

  function stripeResponseHandler(status, response) {
    if (response.error) {
      $('.error')
        .removeClass('hide')
        .find('.alert')
        .text(response.error.message);
    } else {
      // token contains id, last4, and card type
      var token = response['id'];
      // insert the token into the form so it gets submitted to the server
      $form.find('input[type=text]').empty();
      $form.append("<input type='hidden' name='reservation[stripe_token]' value='" + token + "'/>");
      $form.get(0).submit();
    }
  }
})
    </script>
@endsection