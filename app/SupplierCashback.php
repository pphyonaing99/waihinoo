<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierCashback extends Model
{
    protected $fillable = [
    	'voucher_number',
    	'supplier_id',
    	'product_id',
    	'item_flag',
    	'cashback',
    	'date',
    ];
}
