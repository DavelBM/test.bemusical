<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_video extends Model
{
    protected $table = "user_videos";

    protected $fillable = [
    	'platform', 'code',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
