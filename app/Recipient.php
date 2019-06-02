<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    protected $table = "recipients";

    protected $fillable = ["first_name","last_name","company","contact_no","email"];

    public function recipient_address(){
    	return $this->hasOne("\cbp\RecipientAddress","recipient_id","id");
    }


}
