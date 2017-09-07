@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                
                <div class="panel-heading">Requests</div>

                <div class="panel-body">
                    <button class="btn btn-block btn-default">Toggle between hiding and showing the paragraphs</button>
                    <div id="details">
                        <hr>
                        This is a paragraph with little content.
                        <br>
                        This is another small paragraph.
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

    $(document).ready(function(){
        $("#details").hide();
        $("button").click(function(){
            $("#details").slideToggle();
            //$("#details").fadeToggle("slow");
            //$("#details").toggle();
        });
    });
@endsection
