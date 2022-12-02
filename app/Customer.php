<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
    	'name',
    	'address',
    	'phone',
    	'advance_balance',
    	'credit_balance',
    	'frequent_item',
    	'email',
    	'created_by',
    	'credit_flag',
    	'credit_limit',
    	'allow_credit_limit',
        'allow_credit_period',
        'user_id',
    ];
}
