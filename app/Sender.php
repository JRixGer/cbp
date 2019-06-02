<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class Sender extends Model
{
    protected $table = "senders";

    protected $fillable = ["first_name","last_name","contact_no","email","has_business","referral","marketing_emails","import_number"];


    public function sender_business_address(){
        return $this->hasOne("cbp\SenderBusiness","sender_id","id");
    }

    public function sender_physical_address(){
        return $this->hasOne("cbp\SenderPhysicalAddress","sender_id","id");
    }

    public function sender_mailing_address(){
        return $this->hasOne("cbp\SenderMailingAddress","sender_id","id");
    }

    public function sender_power_of_atty(){
        return $this->hasOne("cbp\SenderPowerOfAtty","sender_id","id");
    }


}
