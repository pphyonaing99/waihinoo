<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductProfit extends Model
{
    protected $fillable = [
    	'voucher_id',
    	'product_id',
    	'color',
    	'total_amount',
    	'imei_number',
    	'date'
    ];
}
