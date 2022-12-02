<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
    	'name',
    	'phone',
    	'address',
    	'brand_id',
    	'email',
    	'credit_amount',
        'payable',
    	'repayment_period',
    	'repayment_date',
    	'cashback',
        'user_id',
        'cashback',
    ];
}
