<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
    	'remaining_amount',
    	'paid',
    	'customer_id',
    	'customer_phone',
    	'aeon_id',
    	'status',
    ];
}
