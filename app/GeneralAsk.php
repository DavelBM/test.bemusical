<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralAsk extends Model
{
    protected $table = "general_requests";

    protected $fillable = [
    	'name', 'email', 'company', 'phone', 'date', 'address', 'duration', 'comment', 'type', 'read',
    ];
}
