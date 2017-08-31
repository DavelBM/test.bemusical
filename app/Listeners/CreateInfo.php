<?php

namespace App\Listeners;

use App\Events\NewUser;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User_info;

class CreateInfo
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewUser  $event
     * @return void
     */
    public function handle(NewUser $event)
    {
        // $info = new User_info();
        // $info->user_id         = $event->user->id;
        // $info->first_name      = 'null';
        // $info->last_name       = 'null';
        // $info->about           = 'null';
        // $info->profile_picture = 'null';
        // $info->bio             = 'null';
        // $info->address         = 'null';
        // $info->phone           = 0;
        // $info->location        = 'null';
        // $info->degree          = 'null';
        // $info->mile_radious    = 0;
        // $info->save();
    }
}
