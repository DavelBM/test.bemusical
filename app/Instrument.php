<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instrument extends Model
{
    protected $table = "instruments";

    protected $fillable = [
    	'name'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function ensembles()
    {
        return $this->belongsToMany('App\Ensemble');
    }
}
