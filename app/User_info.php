<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_info extends Model
{
    protected $table = "infos";

    protected $fillable = [
    	'slug', 'first_name', 'last_name', 'about', 'profile_picture', 'bio', 'address', 'phone', 'location', 'degree', 'mile_radious'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
