<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'confirmed', 'token', 'username', 'active', 'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // protected $events = [
    //     'created' => Events\NewUser::class
    // ];

    public function info()
    {
        return $this->hasOne('App\User_info');
    }

    public function ensemble()
    {
        return $this->hasOne('App\Ensemble');
    }

    public function member()
    {
        return $this->hasOne('App\Member');
    }

    public function user_videos()
    {
        return $this->hasMany('App\User_video');
    }

    public function user_songs()
    {
        return $this->hasMany('App\User_song');
    }

    public function user_tags()
    {
        return $this->belongsToMany('App\Tag');
    }

    public function user_styles()
    {
        return $this->belongsToMany('App\Style');
    }

    public function user_instruments()
    {
        return $this->belongsToMany('App\Instrument');
    }

    public function user_images()
    {
        return $this->hasMany('App\User_image');
    }

    public function user_repertoires()
    {
        return $this->hasMany('App\UserRepertoir');
    }

    public function reviews()
    {
        return $this->hasMany('App\Review');
    }

    public function asks()
    {
        return $this->hasMany('App\Ask');
    }

    public function gigs()
    {
        return $this->hasMany('App\Gig');
    }
}
