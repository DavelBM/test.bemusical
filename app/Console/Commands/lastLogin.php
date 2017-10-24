<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\User;
use Log;

class lastLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'llogin:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get timestamp of last login';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::where('last_login_at', '!=', '0000-00-00 00:00:00')->get();
        
        Log::info('--------------------------------------------------------------');
        foreach ($users as $user) {
            $now = Carbon::now();
            $llogin = Carbon::parse($user->last_login_at);
            $elapsed_time = $now->diffInDays($llogin);

            Log::info($elapsed_time);
            Log::info($llogin);
        }
    }
}
