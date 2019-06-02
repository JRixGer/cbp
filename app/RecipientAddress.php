<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class RecipientAddress extends Model
{
    //
    protected $table = "recipient_addresses";
    protected $fillable = ["recipient_id","address_1","address_2","city","ProvState","province","postal","country","is_active"];

}