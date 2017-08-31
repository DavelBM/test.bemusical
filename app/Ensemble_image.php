<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ensemble_image extends Model
{
    protected $table = "ensemble_images";

    protected $fillable = [
    	'name'
    ];

    public function ensemble()
    {
        return $this->belongsTo('App\Ensemble');
    }
}
