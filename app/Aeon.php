<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aeon extends Model
{
    protected $fillable = [
    	'applicant_name',
    	'nrc',
    	'details_document',
    	'job_position',
    	'salary',
    	'job_reference_letter',
    	'reporter_reference_letter',
    	'installment_plan',
        'product_id',
    ];
}
