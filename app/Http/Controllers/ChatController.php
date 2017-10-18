<?php

namespace App\Http\Controllers;

use URL;
use Auth;
use App\User;
use App\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Events\MessagePosted;
use App\Events\AdminMessagePosted;

class ChatController extends Controller
{
    public function messages()
    {
    	$info = [];
    	$messages = [];
        $user_id = Auth::user()->id;
        $user = User::where('id', $user_id)->firstOrFail();
        $messages_query = Message::where('user_id', $user_id)->orderBy('id', 'desc')->take(10)->get();
        
        foreach ($messages_query as $message) {
        	if ($message->admin == 1) {
                $info = array(
                    'message' => $message->message,
                    'name'    => 'BeMusical adviser',
                    'image'   => URL::to('/images/admin/admin.png'),
                    'time'    => Carbon::parse($message->time)->format('F j, Y h:i A')
                );
            }else{
                if ($user->type == 'soloist') {
                    $info = array(
                        'message' => $message->message,
                        'name'    => $user->info->first_name.' '.$user->info->last_name,
                        'image'   => URL::to('/images/profile/').'/'.$user->info->profile_picture,
                        'time'    => Carbon::parse($message->time)->format('F j, Y h:i A')
                    );
                }elseif($user->type == 'ensemble'){
                    $info = array(
                        'message' => $message->message,
                        'name'    => $user->ensemble->name,
                        'image'   => URL::to('/images/ensemble/').'/'.$user->ensemble->profile_picture,
                        'time'    => Carbon::parse($message->time)->format('F j, Y h:i A')
                    );
                }
            }
        	array_push($messages, $info);
        }
        $r_messages = array_reverse($messages);
        return $r_messages;
    }

    public function post_messages()
    {
    	$info_message = [];
    	$user = Auth::user();
    	$message = request()->get('message');
    	$time = request()->get('time');

    	$user->messages()->create([
    		'message' => $message,
    		'time'    => $time,
    	]);

    	$info_message = array(
    		'message' => $message,
    		'name'    => $user->info->first_name.' '.$user->info->last_name,
    		'image'   => URL::to('/images/profile/').'/'.$user->info->profile_picture,
    		'time'    => $time
    	);
    	broadcast(new MessagePosted($info_message))->toOthers();
    	// event(new MessagePosted($info_message));

    	return ['status' => 'OK'];
    }

    public function messages_admin($id)
    {
        $info = [];
        $messages = [];
        $user = User::where('id', $id)->firstOrFail();
        
        foreach ($user->messages as $message) {
            if ($message->admin == 1) {
                $info = array(
                    'message' => $message->message,
                    'name'    => 'BeMusical adviser',
                    'image'   => URL::to('/images/admin/admin.png'),
                    'time'    => Carbon::parse($message->time)->format('F j, Y h:i A')
                );
            }else{
                if ($user->type == 'soloist') {
                    $info = array(
                        'message' => $message->message,
                        'name'    => $user->info->first_name.' '.$user->info->last_name,
                        'image'   => URL::to('/images/profile/').'/'.$user->info->profile_picture,
                        'time'    => Carbon::parse($message->time)->format('F j, Y h:i A')
                    );
                }elseif($user->type == 'ensemble'){
                    $info = array(
                        'message' => $message->message,
                        'name'    => $user->ensemble->name,
                        'image'   => URL::to('/images/ensemble/').'/'.$user->ensemble->profile_picture,
                        'time'    => Carbon::parse($message->time)->format('F j, Y h:i A')
                    );
                }
            }
            array_push($messages, $info);
        }

        return $messages;
    }

    public function post_messages_admin($id)
    {
        $info_message = [];
        $user = User::where('id', $id)->firstOrFail();
        $message = request()->get('message');
        $time = request()->get('time');

        $user->messages()->create([
            'message' => $message,
            'time'    => $time,
            'admin'   => 1,
        ]);

        $info_message = array(
            'message' => $message,
            'name'    => 'BeMusical adviser',
            'image'   => URL::to('/images/admin/admin.png'),
            'time'    => $time
        );

        broadcast(new AdminMessagePosted($info_message))->toOthers();
        // event(new AdminMessagePosted($info_message));

        return ['status' => 'OK'];
    }

    public function admin_chat($id)
    {
        return view('admin.admin_chat')->with('id', $id);
    }
}
