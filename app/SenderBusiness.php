<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class SenderBusiness extends Model
{
    protected $table = 'sender_businesses';
    protected $fillable = ["sender_id","sender_mailing_address_id","business_name","business_number","import_number","business_location","address_1","address_2","city","province","postal","country"];
}
