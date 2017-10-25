<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\NewUser' => [
            'App\Listeners\SendVerifierMail',
            //'App\Listeners\CreateInfo',
        ],

        //'App\Events\UserLogedEvent' => [
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\UserLoged'
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
