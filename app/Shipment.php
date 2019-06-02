<?php

namespace cbp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
	use SoftDeletes;
    protected $table = "shipments";
    protected $dates = ['deleted_at'];
    protected $fillable = ["sender_id","shipment_code","order_id","tracking_no","recipient_id","length","width","height","weight","require_signature","letter_mail","import_batch","cbp_address_id",'recipient_address_id'];
	
	protected static function boot() {
	    parent::boot();

	    static::deleting(function($shipment) {
         foreach ($shipment->shipmentitems()->get() as $shipitem) {
            $shipitem->delete();
         }
         // foreach ($shipment->shipmentstatus()->get() as $shipstatus) {
         //    $shipstatus->delete();
         // }         
      });
	}    


	public function shipmentitems()
    {
      return $this->hasMany('cbp\ShipmentItem');
    }
	
	public function shipmentstatus()
    {
      return $this->hasMany('cbp\ShipmentStatusHistory');
    }

    public function recipient()
    {
      return $this->hasOne('cbp\Recipient','id','recipient_id');
    }

    public function recipient_address()
    {
      return $this->hasOne('cbp\RecipientAddress','id','recipient_address_id');
    }

    public function shipfrom()
    {
      return $this->hasOne('cbp\CBPAddress','id','cbp_address_id');
    }

    public function postage_rate()
    {
      return $this->hasOne('cbp\PostageRate','id','postage_rate_id');
    }
}
