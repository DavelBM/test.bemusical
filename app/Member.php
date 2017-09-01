<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = "members";

    protected $fillable = [
    	'user_id', 'name', 'instrument', 'slug', 'token', 'email', 'confirmation' 
    ];

    public function ensemble()
    {
        return $this->belongsTo('App\Ensemble');
    }

    // public function user()
    // {
    //     return $this->hasOne('App\User');
    // }
}
