<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\User;
use Mail;
use Log;

class lastLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:llogin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get timestamp of last login, and send email for not login';

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
        $users = User::where('last_login_at', '!=', '0000-00-00 00:00:00')->where('login_reminder', '!=', 5)->where('active', 1)->get();
        
        Log::info('--------------------------Reminder---------------------------------');
        foreach ($users as $user) {
            $now = Carbon::now();
            $llogin = Carbon::parse($user->last_login_at);
            $elapsed_time = $now->diffInDays($llogin);
            
            switch ($elapsed_time) {
                case 30:
                    $data = [
                        'name'    => $this->_type($user->type, $user->id),
                        'email'   => $user->email,
                        'message' => 'You have not logged for 30 days',
                        'num'     => 1
                    ];
                    $this->_email($data);
                    $this->_update($user->id, $data['num']);
                    break;
                
                case 38:
                    $data = [
                        'name'    => $this->_type($user->type, $user->id),
                        'email'   => $user->email,
                        'message' => 'You have not logged for more than 30 days',
                        'num'     => 2
                    ];
                    $this->_email($data);
                    $this->_update($user->id, $data['num']);
                    break;
                
                case 46:
                    $data = [
                        'name'    => $this->_type($user->type, $user->id),
                        'email'   => $user->email,
                        'message' => 'You have not logged for more than 30 days',
                        'num'     => 3
                    ];
                    $this->_email($data);
                    $this->_update($user->id, $data['num']);
                    break;
                
                case 54:
                    $data = [
                        'name'    => $this->_type($user->type, $user->id),
                        'email'   => $user->email,
                        'message' => 'You may be bloked. Login!',
                        'num'     => 4
                    ];
                    $this->_email($data);
                    $this->_update($user->id, $data['num']);
                    break;
                
                case 62:
                    $data = [
                        'name'    => $this->_type($user->type, $user->id),
                        'email'   => $user->email,
                        'message' => 'We have been bloked you profile.',
                        'num'     => 5
                    ];
                    $this->_email($data);
                    $this->_update($user->id, $data['num']);
                    break;
            }
        }
    }

    public function _email($data)
    {
        Mail::send('email.admin.reminder_log', $data, function($message) use ($data){
            $message->from('support@bemusical.us');
            $message->to($data['email']);
            $message->subject($data['message']);
        });
    }

    public function _type($type, $id)
    {
        $user = User::where('id', $id)->first();
        if ($type == 'soloist') {
            $info = $user->info->first_name.' '.$user->info->last_name;
        } elseif ($type == 'ensemble') {
            $info = $user->ensemble->name;
        }

        return $info;
    }

    public function _update($id, $num)
    {
        if ($num == 5) {
            User::where('id', $id)
            ->update([
                'login_reminder' => $num,
                'active' => 0,
            ]);
        } else {
            User::where('id', $id)
            ->update([
                'login_reminder' => $num
            ]);
        }
    }
}
