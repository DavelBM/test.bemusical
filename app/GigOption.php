<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GigOption extends Model
{
    /**
	 * 
	 */
    protected $table = "gig_options";

    /**
     * 
     */
    protected $fillable = [
    	'listDay', 'listWeek', 'month', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'start', 'end', 'time_before_event', 'time_after_event', 'defaultView',
    ];

    /**
     * 
     */
    public function user()
    {
        return $this->hasOne('App\User');
    }
    
}
