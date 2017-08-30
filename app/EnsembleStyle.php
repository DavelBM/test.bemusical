<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnsembleStyle extends Model
{
    protected $table = "ensemble_style";

    protected $fillable = ['style_id', 'ensemble_id'];
}
