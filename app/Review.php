<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = "reviews";

    protected $fillable = [
    	'score', 'review', 'who', 'whos_company', 'visible'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
