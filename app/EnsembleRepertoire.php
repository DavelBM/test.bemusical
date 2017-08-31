<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnsembleRepertoire extends Model
{
    protected $table = "ensemble_repertoires";

    protected $fillable = [
    	'repertoire_example', 'visible'
    ];

    public function ensemble()
    {
        return $this->belongsTo('App\Ensemble');
    }
}
