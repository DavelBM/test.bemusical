<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = "payments";

    protected $fillable = [
    	'request_id', 'email', 'phone', '_billing_address', '_billing_zip', '_id_costumer', '_id_card', '_id_token', '_id_charge', 'amount', 'payed', 'type', 
    ];

    public function request()
    {
        return $this->hasOne('App\Ask');
    }
}
