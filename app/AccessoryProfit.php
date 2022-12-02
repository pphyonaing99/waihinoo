<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessoryProfit extends Model
{
    protected $fillable = [
    	'voucher_id',
    	'accessory_id',
    	'color',
    	'total_amount',
    	'date',
    ];
}
