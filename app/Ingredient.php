<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
    	'product_id',
    	'raw_material_id',
    	'unit_name',
    	'amount',
    ];
}
