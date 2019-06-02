<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class SenderPhysicalAddress extends Model
{
    protected $table = 'sender_physical_addresses';
    protected $fillable = ["sender_id","address_1","address_2","city","province","postal","country"];
}
