<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gig extends Model
{
	/**
	 * 
	 */
    protected $table = "gigs";

    /**
     * 
     */
    protected $fillable = [
    	'title', 'start', 'end', 'url', 'allDay'
    ];

    /**
     * 
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function request()
    {
        return $this->hasOne('App\Ask');
    }
}
