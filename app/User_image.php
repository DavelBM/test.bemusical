<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_image extends Model
{
    protected $table = "user_images";

    protected $fillable = [
    	'name'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
