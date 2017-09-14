<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ask extends Model
{
    protected $table = "requests";

    protected $fillable = [
    	'date', 'address', 'duration', 'available', 'name', 'email', 'phone', 'event_type'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function gig()
    {
        return $this->hasOne('App\Gig');
    }
}
