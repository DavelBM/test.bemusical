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
        $requests = Ask::where('accepted_price', 1)->where('available', 1)->where('nonavailable', 0)->where('price', '!=', null)->get();
        foreach ($requests as $request) {
            $now = Carbon::now();
            $date_e = explode('|', $request->date);
            $nextgig = Carbon::parse($date_e[0]);
            $elapsed_time = $now->diffInHours($nextgig);
            if ($nextgig->isFuture()) {
                $hashids = new Hashids();
                $id = $hashids->encode(time());
                Log::info($id);
                Log::info($request->user->email);
                // if ($elapsed_time == 24) {
                //     $data = ['email' => $request->user->email, 'message' => 'next gig'];
                //     $this->SendTextMessage('Bemusical reminder: gig in 24 hours', $request->user->id);
                //     $this->_email($data);
                // }
                Log::info($request->id);
                Log::info($elapsed_time);
            }
        }
    }

    public function _email($data)
    {
        Mail::send('email.admin.review_gig', $data, function($message) use ($data){
            $message->from('support@bemusical.us');
            $message->to($data['email']);
            $message->subject($data['message']);
        });
    }
}
