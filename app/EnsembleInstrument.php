<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnsembleInstrument extends Model
{
    protected $table = "ensemble_instrument";

    protected $fillable = ['instrument_id', 'ensemble_id'];
}
