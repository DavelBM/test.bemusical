<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = "tags";

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
