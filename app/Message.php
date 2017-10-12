<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = "messages";

    protected $fillable = [
    	'user_id', 'message', 'message_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
