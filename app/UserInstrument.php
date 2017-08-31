<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserInstrument extends Model
{
    protected $table = "instrument_user";

    protected $fillable = ['instrument_id', 'user_id'];
}
