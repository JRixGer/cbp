<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class PostageRate extends Model
{
    //
    protected $table = "postage_rates";
    protected $fillable = ["description","currency","value","est_delivery_time","package_type","service_code","other_cost"];

}
