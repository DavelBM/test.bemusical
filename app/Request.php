<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $table = "requests";

    protected $fillable = [
    	'date', 'address', 'duration', 'available'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
