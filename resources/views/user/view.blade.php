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
                
                <div class="panel-heading">public USER page</div>

                <div class="panel-body">
                    <div class="col-md-5">
                        
                        @php
                           $image = $info->info->profile_picture; 
                        @endphp
                        <img src="{{ asset("images/profile/$image") }}" class="img-circle float-left" alt="{{$info->info->profile_picture}}" width="250" height="250">
                        
                    </div>
                    <div class="col-md-7">
                        <!-- Displaying data -->
                        <strong>Name:</strong> {{$info->info->first_name." ".$info->last_name}}<br>
                        <strong>Degree:</strong> {{$info->info->degree}}<br>
                        <strong>Bio:</strong> {{$info->info->bio}}<br>
                        <strong>Location:</strong> {{$info->info->location}}<br>
                        <strong>Mile Radius:</strong> {{$info->info->mile_radious}} miles<br>
                        <strong>About Me:</strong> {{$info->info->about}}<br>
                        <!-- /Displaying data -->
                    </div>
                </div>
                <div class="panel-body">
                    <div class="col-md-4">
                        <strong>TAGS</strong><br>
                        @foreach($info->user_tags as $tag)
                            {{$tag->name}}<br>
                        @endforeach
                    </div>
                    <div class="col-md-4">
                        <strong>STYLES</strong><br>
                        @foreach($info->user_styles as $style)
                            {{$style->name}}<br>
                        @endforeach
                    </div>
                    <div class="col-md-4">
                        <strong>INSTRUMENTS</strong><br>
                        @foreach($info->user_instruments as $instrument)
                            {{$instrument->name}}<br>
                        @endforeach
                    </div>
                </div>
                <div class="panel-body">
                    <div class="col-md-12">
                        @foreach($info->user_images as $image)
                                <img src="{{ asset("images/general/$image->name") }}" class="img-rounded" alt="{{$image->name}}" width="304" height="236">
                        @endforeach
                    </div>
                </div>
                <div class="panel-body">
                    <strong>VIDEOS</strong>
                    <div class="col-md-12">
                    @foreach($info->user_videos as $video)
                        @if($video->platform == 'youtube')
                            <iframe width="100%" height="315" src="https://www.youtube.com/embed/{{$video->code}}" frameborder="0" allowfullscreen></iframe>
                        @elseif($video->platform == 'vimeo')
                            <iframe src="https://player.vimeo.com/video/{{$video->code}}" width="100%" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                        @endif
                    @endforeach
                    </div>
                </div>
                <div class="panel-body">
                    <strong>SONGS</strong>
                    <div class="col-md-12">
                    @foreach($info->user_songs as $song)
                        @if($song->platform == 'spotify')
                            <iframe src="https://open.spotify.com/embed?uri=spotify:track:{{$song->code}}&theme=white&view=coverart" 
                            frameborder="0" allowtransparency="true"></iframe>
                        @elseif($song->platform == 'soundcloud')
                            <iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/{{$song->code}}&amp;color=0066cc&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe>
                        @endif
                    @endforeach                        
                    </div>
                </div>
                <div class="panel-body">
                    <strong>Repertoir</strong>
                    <div class="col-md-12">
                    @foreach($info->user_repertoires as $repertoire)
                        @if($repertoire->visible)
                            *{{ $repertoire->repertoire_example }}<br>
                        @endif
                    @endforeach                        
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
