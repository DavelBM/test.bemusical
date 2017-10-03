<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>A Simple Responsive HTML Email</title>
        <style type="text/css">
        body {margin: 0; padding: 0; min-width: 100%!important;}
            .content {width: 100%; max-width: 600px;}
            .header {padding: 40px 30px 20px 30px;}  
            .footer {padding: 20px 30px 15px 30px;}
            .footercopy {font-family: sans-serif; font-size: 14px; color: #ffffff;}
            .footercopy a {color: #ffffff; text-decoration: underline;}
        </style>
    </head>
    <body yahoo bgcolor="#f6f8f1">
        <table width="100%" bgcolor="#f6f8f1" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <table class="content" align="center" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td class="header" bgcolor="#c7d8a7">
                                This email was sent it to {{$email}}, if you are not please ignore this
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Hi {{$user_name}},
                                <br>
                                We are excited to inform you that somebody wants your services in his/her event.
                                <br>
                                <p>
                                Name: <strong>{{$name}}</strong><br>
                                On: <strong>{{$date}}</strong><br>
                                Lenght : <strong>{{$duration}}</strong><br>
                                Distance : <strong>{{$distance}} miles</strong><br>
                                Notes from client: <strong>{{$event}}</strong><br>
                                Are you available?<br>
                                <a class="btn btn-primary" type="button" href="{{ URL::to('/price/'.$token.'1') }}">YES</a><br>
                                <a class="btn btn-primary" type="button" href="{{ URL::to('/specified/request/invitation/'.$token.'0') }}">NO</a> <br>
                                </p>
                                <!-- Do you want to accept? Are you available? 
                                <p>
                                <a class="btn btn-primary" type="button" href="{{ URL::to('/specified/request/invitation/'.$token.'1') }}">Yes, I am</a> <br> or follow this link <br>
                                {{ URL::to('/specified/request/invitation/'.$token.'1') }}<br>
                                </p>
                                <p>
                                <a class="btn btn-primary" type="button" href="{{ URL::to('/specified/request/invitation/'.$token.'0') }}">No, I am not</a> <br> or follow this link <br>
                                {{ URL::to('/specified/request/invitation/'.$token.'0') }}
                                </p> -->
                            </td>
                        </tr>
                        <tr>
                            <td class="footer" bgcolor="#44525f">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td align="center" class="footercopy">
                                            &amp;reg; BeMusical.us. San Francisco, California 2017<br/>
                                            <!-- <a href="#"><font color="#ffffff">Unsubscribe</font></a> from this newsletter instantly. --> You do not need to replay to this mail
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="padding: 20px 0 0 0;">
                                            <table border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td width="37" style="text-align: center; padding: 0 10px 0 10px;">
                                                        <a href="http://www.facebook.com/">
                                                            <img src="https://facebookbrand.com/wp-content/themes/fb-branding/prj-fb-branding/assets/images/fb-art.png" width="37" height="37" alt="Facebook" border="0" />
                                                        </a>
                                                    </td>
                                                    <td width="37" style="text-align: center; padding: 0 10px 0 10px;">
                                                        <a href="http://www.twitter.com/">
                                                            <img src="https://cdn1.iconfinder.com/data/icons/iconza-circle-social/64/697029-twitter-512.png" width="37" height="37" alt="Twitter" border="0" />
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
