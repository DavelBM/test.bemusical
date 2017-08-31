<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTag extends Model
{
    protected $table = "tag_user"; 

    protected $fillable = ['tag_id', 'user_id'];
}
