<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    protected $fillable = [
    	'name',
    	'photo',
    	'category_id',
    	'brand_id',
    	'supplier_id',
    	'serial_number',
    	'model_number',
    	'color',
    	'size',
    	'instock_qty',
    	'purchase_price',
    	'purchase_currency',
    	'sales_price',
    	'sales_currency',
    	'exchange_rate',
    	'discount_flag',
    	'discount_percent',
    	'foc_item_flag',
    	'custom_discount_flag',
    	'custom_discount_id',
    	'specification_description',
    	'defect_flag',
    ];
}
