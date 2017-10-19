<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Update Your Email Address {{$name}}</h2>

        <div>
            You can change your email in the next url. It expire in 30 minutes
            {{ URL::to('/update/email/'.$token) }}.<br/>

        </div>

    </body>
</html>