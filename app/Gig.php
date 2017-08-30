<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gig extends Model
{
    protected $table = "gigs";

    protected $fillable = [
    	'address', 'time', 'date', 'details', 'price', 'who', 'whos_company'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
