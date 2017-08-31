<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ensemble_video extends Model
{
    protected $table = "ensemble_videos";

    protected $fillable = [
    	'platform', 'code',
    ];

    public function ensemble()
    {
        return $this->belongsTo('App\Ensemble');
    }
}
