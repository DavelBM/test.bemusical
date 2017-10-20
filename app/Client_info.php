<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client_info extends Model
{
	protected $table = "info_clients";

    protected $fillable = [
    	'id_costumer', 'client_id', 'name', 'address', 'company', 'phone', 'zip'
    ];

    public function client()
    {
        return $this->belongsTo('App\Client');
    }
}
