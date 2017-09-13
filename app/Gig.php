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
    	'name', 'start_time', 'end_time', 'all_day', 'details'
    ];

    /**
     * 
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
