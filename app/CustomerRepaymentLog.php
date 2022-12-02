<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerRepaymentLog extends Model
{
    protected $fillable = [
    	'customer_id',
    	'customer_payment_log_id',
    	'paid_amount',
    	'paid_timetick',
    ];
}
