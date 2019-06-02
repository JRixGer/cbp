<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentItem extends Model
{
    //
	use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = "shipment_items";
    protected $fillable = ["shipment_id","pallet_id","bag_id","description","value","qty","origin_country","note"];
    
    public function shipment()
    {
    	return $this->belongsTo('cbp\Shipment');
    }    
}
