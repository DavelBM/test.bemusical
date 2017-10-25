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
                                Hi {{$c_name[0]}}, we have recommendations for you
                                @if(!App\Client::where('email', $c_email[0])->exists())
                                <strong>This is an invitation to be part of BeMusical.us.</strong>
                                <a href="{{ URL::to('/client/register') }}">Register</a>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                            We have some recommendations based of your query for your event on {{$c_name[0]}}:<br>
                            @for($i=0; $i < count($name); $i++)
                            	<div class="col-sm-6 col-md-4">
                                    <div class="thumbnail">
                                        @if($type[$i] == 'ensemble')
                                        @if($image[$i] != 'null')
                                            <a class="btn" href="{{ URL::to('/'.$slug[$i]) }}">
                                                <img alt="100%x200" data-src="holder.js/100%x200" src="{{ asset("images/ensemble/$image[$i]") }}" alt="{{$image[$i]}}" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                            </a>
                                        @else
                                            <a class="btn" href="{{ URL::to('/'.$slug[$i]) }}">
                                                <img alt="100%x200" data-src="holder.js/100%x200" src="{{ asset("images/profile/no-image.png") }}" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                            </a>
                                        @endif 
                                        @elseif($type[$i] == 'soloist')
                                        @if($image[$i] != 'null')
                                            <a class="btn" href="{{ URL::to('/'.$slug[$i]) }}">
                                                <img alt="100%x200" data-src="holder.js/100%x200" src="{{ asset("images/profile/$image[$i]") }}" alt="{{$image[$i]}}" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                            </a>
                                        @else
                                            <a class="btn" href="{{ URL::to('/'.$slug[$i]) }}">
                                                <img alt="100%x200" data-src="holder.js/100%x200" src="{{ asset("images/profile/no-image.png") }}" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
                                            </a>
                                        @endif
                                        @endif
                                        <div class="caption">
                                                <h3>{{$name[$i]}}</h3>
                                                <p>works in {{$location[$i]}}</p>
                                                <p><a href="{{ URL::to('/'.$slug[$i]) }}" class="btn btn-primary" role="button">See profile</a></p>
                                        </div>
                                    </div>
                                </div>
                            @endfor
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
