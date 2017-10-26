<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <div>
            {{$name}} follow this link and tell us how was your event<br>
            {{ URL::to('review/' . $token) }}<br>
            <strong>This link expires in 15 days</strong>
        </div>

    </body>
</html>