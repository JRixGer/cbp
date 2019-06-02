<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class ParcelRate extends Model
{
    protected $table = "parcel_rates";

    protected $fillable = ["weight_lbs","rate","type"];


}
