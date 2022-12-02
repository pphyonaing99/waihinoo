<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
    	'supplier_id',
        'product_flag',
    	'product_id',
    	'purchase_quantity',
    	'purchase_by',
        'purchase_type',
    	'purchase_date',
    	'timetick',
    	'description',
    	'exchange_rate',
    	'amount',
    	'total_amount',
    	'currency_type',
    ];
}
