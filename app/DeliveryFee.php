<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class DeliveryFee extends Model
{
    //
    protected $table = "delivery_fees";
    protected $fillable = ["note","min_weight","max_weight","price","letter_mail_price","is_active"];

}
