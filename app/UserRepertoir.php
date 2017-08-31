<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRepertoir extends Model
{
    protected $table = "user_repertoires";

    protected $fillable = [
    	'repertoire_example', 'visible'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
