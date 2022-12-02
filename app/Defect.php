<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    protected $fillable = [
            'product_id','imei_number','comment','defect_date','user_id','accessory_id','product_flag','qty'
        ];
}
