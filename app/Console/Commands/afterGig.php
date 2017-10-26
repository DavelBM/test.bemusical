<?php

namespace App\Console\Commands;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\User;
use App\Phone;
use App\Payment;
use Hashids\Hashids;
use App\Ask;
use App\Review;
use Mail;
use Log;

class afterGig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:review';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an invitation after gig to review to musician';

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
        Log::info('--------------------------Client_Review---------------------------------');
        $asks = Ask::where('accepted_price', 1)->where('available', 1)->where('nonavailable', 0)->where('price', '!=', null)->get();
        foreach ($asks as $ask) {
            $now = Carbon::now();
            $date_e = explode('|', $ask->date);
            $previousgig = Carbon::parse($date_e[0]);
            $elapsed_time = $now->diffInHours($previousgig);
            $hashids = new Hashids();
            if ($previousgig->isPast()) {
                if ($elapsed_time == 20) {
                    $id = $hashids->encode(time());
                    $data = [
                        'name'    => $this->_type($ask->user->type, $ask->user->id),
                        'token'   => $id,
                        'email'   => $ask->email,
                        'message' => 'Bemusical reviews'
                    ];
                    $this->_email($data);
                    Review::create([
                        'score' => 0,
                        'review' => 'null',
                        'client_id' => $ask->id,
                        'user_id' => $ask->user->id,
                        'visible' => 0,
                        'token' => $id
                    ]);
                }
                // Log::info($id);
                // Log::info($ask->user->email);
                // Log::info($ask->id);
                // Log::info($elapsed_time);
            }
        }
    }

    public function _email($data)
    {
        Mail::send('email.client.review', $data, function($message) use ($data){
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
}
