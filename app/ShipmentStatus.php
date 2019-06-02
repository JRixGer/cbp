<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentStatus extends Model
{

	use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = "shipment_status";
    
    public function shipment()
    {
    	return $this->belongsTo('cbp\Shipment');
    }      
}
