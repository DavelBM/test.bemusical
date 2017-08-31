<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserStyle extends Model
{
    protected $table = "style_user";

    protected $fillable = ['style_id', 'user_id'];
}
