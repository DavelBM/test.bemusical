<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ensemble extends Model
{
    protected $table = "ensembles";

    protected $fillable = [
    	'slug', 'name', 'email', 'manager_name', 'type', 'about', 'profile_picture', 'summary', 'address', 'phone', 'location', 'mile_radious'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function ensemble_repertoires()
    {
        return $this->hasMany('App\EnsembleRepertoire');
    }

    public function members()
    {
        return $this->hasMany('App\Member');
    }

        public function ensemble_tags()
    {
        return $this->belongsToMany('App\Tag');
    }

    public function ensemble_styles()
    {
        return $this->belongsToMany('App\Style');
    }

    public function ensemble_instruments()
    {
        return $this->belongsToMany('App\Instrument');
    }

    public function ensemble_images()
    {
        return $this->hasMany('App\Ensemble_image');
    }

    public function ensemble_videos()
    {
        return $this->hasMany('App\Ensemble_video');
    }

    public function ensemble_songs()
    {
        return $this->hasMany('App\Ensemble_song');
    }
}
