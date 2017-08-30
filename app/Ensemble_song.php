<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ensemble_song extends Model
{
    protected $table = "ensemble_songs";

    protected $fillable = [
    	'platform', 'code',
    ];

    public function ensemble()
    {
        return $this->belongsTo('App\Uensemble');
    }
}
