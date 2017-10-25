<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Hi, {{$name}}</h2>

        <div>
            @if($num == 5)
                We already bloked your account, please send us an email if you want to reactive your account to support@bemusical.us
            @else
                We have been missing you, comeback soon.
                {{ route('login') }}.<br/>
            @endif
        </div>

    </body>
</html>