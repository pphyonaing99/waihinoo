<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    protected $fillable = [
    	'name',
    	'payment_amount',
    	'status',
    	'due_date',
    	'payment_id',
    ];
}
