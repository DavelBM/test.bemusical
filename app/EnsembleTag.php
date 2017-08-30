<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnsembleTag extends Model
{
    protected $table = "ensemble_tag";

    protected $fillable = ['tag_id', 'user_id'];
}
