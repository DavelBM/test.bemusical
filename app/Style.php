<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    protected $table = "styles";

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
