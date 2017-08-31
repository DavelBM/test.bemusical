<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_song extends Model
{
    protected $table = "user_songs";

    protected $fillable = [
    	'platform', 'code',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
