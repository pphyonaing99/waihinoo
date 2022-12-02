<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
    	'product_id',
    	'size',
    	'sell_price',
    ];
}
