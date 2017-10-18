<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'message', 'admin', 'user_id', 'time'
    ];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function admin()
    {
    	return $this->belongsTo('App\Admin');
    }
}
