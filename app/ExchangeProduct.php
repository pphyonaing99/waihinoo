<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExchangeProduct extends Model
{
    protected $fillable = [
            'voucher_number','product_id','imei_number','product_name','accessory_id','comment','exchange_date','supplier_id','accessory_id','accessory_name','product_flag','qty','status'
        ];
}
