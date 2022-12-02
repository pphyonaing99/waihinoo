<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
    	'voucher_number',
    	'customer_id',
    	'user_id',
    	'sold_by',
    	'item_list',
        'accessory_list',
    	'total_amount',
    	'tax',
    	'total_discount',
    	'voucher_grand_total',
    	'credit',
    	'total_quantity',
        'payment_type',
    	'print_flag',
    	'date',
    	'cashback_flag',
    	'cashback_type',
    	'cashback_amount',
    ];

    public function getItemListAttribute($value){
        return json_decode($value);
    }
    public function getAccessoryListAttribute($value){
        return json_decode($value);
    }

    public function accessory(){
        return $this->getAccessoryListAttribute->belongsTo('App\Accessory');
    }
    
}
