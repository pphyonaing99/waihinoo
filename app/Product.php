<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
    	'name',
    	'category_id',
    	'brand_id',
    	'supplier_id',
    	'imei_number',
    	'model_number',
    	'color',
    	'size',
    	'instock_qty',
    	'purchase_price',
    	'purchase_currency',
    	'sales_price',
    	'sales_currency',
    	'discount_flag',
    	'discount_percent',
    	'gift_flag',
    	'gift_item_id',
    	'custom_discount_flag',
    	'custom_discount_id',
    	'specification_description',
    	'photo',
        'series_flag',
    ];

    public function getImeiNumberAttribute($value){
        return json_decode($value);
    }
}
