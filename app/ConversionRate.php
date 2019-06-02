<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class ConversionRate extends Model
{
    //
    protected $table = "conversion_rates";
    protected $fillable = ["rate","currency"];

}
