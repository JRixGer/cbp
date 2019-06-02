<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class SenderMailingAddress extends Model
{
    protected $table = 'sender_mailing_addresses';
    protected $fillable = ["sender_id","sender_business_id","address_1","address_2","city","province","postal","country"];
}
