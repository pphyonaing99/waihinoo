<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductWithImei extends Model
{
    protected $fillable = [
    	'product_id',
    	'color',
    	'imei_number',
    	'internal_storage',
    	'ram',
    	'cpu',
    	'camera',
    	'purchase_price',
    	'sales_price',
    	'sold_flag',
    	'defect_flag',
    ];
}
