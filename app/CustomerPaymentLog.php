<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerPaymentLog extends Model
{
    protected $fillable = [
    	'customer_id',
    	'voucher_id',
    	'total_credit_amount',
    	'total_paid_amount',
    	'remaining_amount',
    	'payment_due_date',
    ];
}
