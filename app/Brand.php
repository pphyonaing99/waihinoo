<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
    	'name',
    	'category_id',
    	'supplier_id',
    	'country_of_origin',
    	'show_flag',
    ];
}
