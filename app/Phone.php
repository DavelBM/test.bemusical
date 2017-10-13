<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $table = "phones";

    protected $fillable = [
    	'user_id', 'phone', 'country', 'country_code', 'confirmed', 'token', 'times', 'message_id', 'times_token'
    ];

    public function user()
    {
        return $this->hasOne('App\User');
    }
}
