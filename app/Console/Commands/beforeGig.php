<?php

namespace App\Console\Commands;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\User;
use App\Phone;
use App\Payment;
use App\Ask;
use Mail;
use Log;

class beforeGig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This sends a gig reminder by text message to musicians';

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
        Log::info('--------------------------Gig Reminder---------------------------------');
        $requests = Ask::where('accepted_price', 1)->where('available', 1)->where('nonavailable', 0)->where('price', '!=', null)->get();
        foreach ($requests as $request) {
            $now = Carbon::now();
            $date_e = explode('|', $request->date);
            $nextgig = Carbon::parse($date_e[0]);
            $elapsed_time = $now->diffInHours($nextgig);
            if ($nextgig->isFuture()) {
                if ($elapsed_time == 24) {
                    $data = ['email' => $request->user->email, 'message' => 'next gig'];
                    $this->SendTextMessage('Bemusical reminder: gig in 24 hours', $request->user->id);
                    $this->_email($data);
                }
            }
        }
    }

    public function SendTextMessage($text_message, $user_id)
    {
        $phone = Phone::where('user_id', $user_id);
        if ($phone->first()->confirmed == 1) {
            $code_country = $phone->first()->country_code;
            $phone_number = $code_country.$phone->first()->phone;

            $sid = "ACf29b73d8c11a7d9d84656693aac302f5";
            $token = "d340b51f8ff42b20daeb1607d0459713";
            $client = new \Twilio\Rest\Client($sid, $token);
            
            $message = $client->messages->create(
                $phone_number,
                array(
                    'from' => '+16502156754',
                    'body' => $text_message
                )
            );
        }
    }

    public function _email($data)
    {
        Mail::send('email.admin.reminder_gig', $data, function($message) use ($data){
            $message->from('support@bemusical.us');
            $message->to($data['email']);
            $message->subject($data['message']);
        });
    }
}
