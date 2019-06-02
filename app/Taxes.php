<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class Taxes extends Model
{
    //
    protected $table = "taxes_setup";
    protected $fillable = ["province","tax_percent"];

}
