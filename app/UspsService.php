<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class UspsService extends Model
{
    //
    protected $table = "usps_services";
    protected $fillable = ["desc","service_code","package_type"];

}
