<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    //
    protected $table = "carriers";
    protected $fillable = ["name"];

}
