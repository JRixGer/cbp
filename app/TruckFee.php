<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class TruckFee extends Model
{
    //
    protected $table = "zone_skipping_fees";
    protected $fillable = ["postal_code","amount"];

}
