<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentStatusHistory extends Model
{


	use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = "shipment_status_history";
    
    public function shipment()
    {
    	return $this->belongsTo('cbp\Shipment');
    }   

    // public function shipment_status()
    // {
    // 	return $this->belongsTo('cbp\ShipmentStatus');
    // }      
}
