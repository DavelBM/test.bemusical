<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = "members";

    protected $fillable = [
    	'instrument', 'name'
    ];

    public function ensemble()
    {
        return $this->belongsTo('App\Ensemble');
    }

    // public function user()
    // {
    //     return $this->hasOne('App\User');
    // }
}
