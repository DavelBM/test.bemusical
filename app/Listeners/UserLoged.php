<?php

namespace App\Listeners;

use App\User;
use Carbon\Carbon;
use App\Events\UserLogedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserLoged
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
     * @param  UserLoged  $event
     * @return void
     */
    public function handle($event)
    {
        User::where('id', $event->user->id)->update(['last_login_at' => Carbon::now()->toDateTimeString()]);
    }
}
