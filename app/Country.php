<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    //
    protected $table = "countries";
    protected $fillable = ["countryCode","countryName"];

}
