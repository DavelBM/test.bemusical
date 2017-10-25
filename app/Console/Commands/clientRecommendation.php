<?php

namespace App\Console\Commands;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Console\Command;
use App\GeneralAsk;
use Carbon\Carbon;
use App\Ask;
use App\User;
use Mail;
use Log;

class clientRecommendation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:recommendation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send recommendations to clients';

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
        $_gasks = GeneralAsk::where('email_sent', 0)->get();
        
        Log::info('--------------------------Recommendations---------------------------------');
        foreach ($_gasks as $general) {
            //compare schedules beetwen now and the request time, then send mail
            $info = [];
            $name_array         = [];
            $type_array         = [];
            $image_array        = [];
            $slug_array         = [];
            $location_array     = [];
            $email_array        = [];
            $client_email_array = [];
            $client_name_array  = [];

            $search = Carbon::parse($general->created_at);
            $now = Carbon::now();
            $elapsed_time = $now->diffInHours($search);
            try{
                $last_ask = Ask::orderBy('id', 'DES')->where('email', $general->email)->firstOrFail();
                $elapsed_time_ask = Carbon::parse($last_ask->created_at)->diffInHours($search);
                if ($elapsed_time_ask > 5) 
                {
                    if ($elapsed_time == 5) 
                    {
                        $array_users = explode(',', $general->original_array);
                        for ($i=0; $i < count($array_users); $i++) { 
                            array_push($info, $this->_type($array_users[$i], $general->email, $general->name));
                            if ($i == 4) {
                                break;
                            }
                        }

                        for ($i=0; $i < count($info); $i++) { 
                            array_push($name_array, $info[$i]['name']);
                            array_push($type_array, $info[$i]['type']);
                            array_push($image_array, $info[$i]['image']);       
                            array_push($slug_array, $info[$i]['slug']);      
                            array_push($location_array, $info[$i]['location']);
                            array_push($email_array, $info[$i]['email']);
                            array_push($client_email_array, $info[$i]['client_email']);
                            array_push($client_name_array, $info[$i]['client_name']);
                        }
                        
                        $data = [
                            'name'     => $name_array,
                            'type'     => $type_array,
                            'image'    => $image_array,
                            'slug'     => $slug_array,
                            'location' => $location_array,
                            'email'    => $email_array,
                            'c_email'  => $client_email_array,
                            'c_name'   => $client_name_array
                        ];
                        $this->_email($data);
                        $this->_update($general->id);
                    }
                }
            }catch(ModelNotFoundException $e) {
                if ($elapsed_time == 5) 
                {
                    $array_users = explode(',', $general->original_array);
                    for ($i=0; $i < count($array_users); $i++) { 
                        array_push($info, $this->_type($array_users[$i], $general->email, $general->name));
                        if ($i == 4) {
                            break;
                        }
                    }

                    for ($i=0; $i < count($info); $i++) { 
                        array_push($name_array, $info[$i]['name']);
                        array_push($type_array, $info[$i]['type']);
                        array_push($image_array, $info[$i]['image']);       
                        array_push($slug_array, $info[$i]['slug']);      
                        array_push($location_array, $info[$i]['location']);
                        array_push($email_array, $info[$i]['email']);
                        array_push($client_email_array, $info[$i]['client_email']);
                        array_push($client_name_array, $info[$i]['client_name']);
                    }
                    
                    $data = [
                        'name'     => $name_array,
                        'type'     => $type_array,
                        'image'    => $image_array,
                        'slug'     => $slug_array,
                        'location' => $location_array,
                        'email'    => $email_array,
                        'c_email'  => $client_email_array,
                        'c_name'   => $client_name_array
                    ];
                    $this->_email($data);
                    $this->_update($general->id);
                }
            }
        }
    }

    public function _email($data)
    {
        Mail::send('email.admin.recommendations', $data, function($message) use ($data){
            $message->from('support@bemusical.us');
            $message->to($data['c_email'][0]);
            $message->subject('We have some recomendations');
        });
    }

    public function _type($id, $c_email, $c_name)
    {
        $user = User::where('id', $id)->first();
        if ($user->type == 'soloist') {
            $name     = $user->info->first_name.' '.$user->info->last_name;
            $image    = $user->info->profile_picture;
            $slug     = $user->info->slug;
            $location = $user->info->location;
            $email    = $user->email;
        } elseif ($user->type == 'ensemble') {
            $name     = $user->ensemble->name;
            $image    = $user->ensemble->profile_picture;
            $slug     = $user->ensemble->slug;
            $location = $user->ensemble->location;
            $email    = $user->email;
        }

        return ['name' => $name, 'type' => $user->type, 'image' => $image, 'slug' => $slug, 'location' => $location, 'email' => $email, 'client_email' => $c_email, 'client_name' => $c_name];
    }

    public function _update($id)
    {
        GeneralAsk::where('id', $id)
        ->update([
            'email_sent' => 1
        ]);
    }
}
