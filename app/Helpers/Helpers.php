<?php

//////////////////////////////////////
// global functions here 
//////////////////////////////////////
use \cbp\Services\Rocketship; 
use \cbp\Services\ShipStation;
use \cbp\Services\ShippingLabel;
use \cbp\Services\PackingSlip;
use \cbp\Services\DHLGLobal; 
use \cbp\Http\Requests\RecipientRegistrationRequest;
use \cbp\Http\Requests\ParcelDimensionsRequest;


use \cbp\ParcelRate;
use \cbp\ShipmentStatus;
use \cbp\ShipmentStatusHistory;
use \cbp\Shipment;
use \cbp\ShipmentsArchived;
use \cbp\ShipmentItemsArchived;
use \cbp\Taxes;
use \cbp\Carrier;
use \cbp\ConversionRate;


function generateCode($id, $length = 15) 
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $id.$randomString;
}

function generateSequence($prefix, $linkId, $withOrderId = false) 
{


    if($prefix =='PDL')
    {
        $tbl_seq = 'postage_and_delivery_sequence';
        $tbl_trans = 'shipments';
    }
    if($prefix =='PGO')
    {
        $tbl_seq = 'postage_and_delivery_sequence';
        $tbl_trans = 'shipments';
    }    
    else if($prefix =='DEL')
    {
        $tbl_seq = 'delivery_sequence';
        $tbl_trans = 'shipments';
    }
    else if($prefix =='OB')
    {
        $tbl_seq = 'order_sequence';
        $tbl_trans = 'shipments';
    }

    $nextId = DB::table($tbl_seq)->insertGetId(['link_id' => $prefix]);
    if($withOrderId)
    {
        $nextOrderId = DB::table('order_sequence')->insertGetId(['link_id' => $prefix]);
        $updateId = DB::table($tbl_trans)->where('id', $linkId)->update(['internal_ship_id' => $prefix.$nextId, 'internal_order_id' => $prefix.$nextOrderId]);
    }
    else
    {
        if($prefix =='OB')
            $updateId = DB::table($tbl_trans)->where('id', $linkId)->update(['internal_order_id' => $prefix.$nextId]);
        else
            $updateId = DB::table($tbl_trans)->where('id', $linkId)->update(['internal_ship_id' => $prefix.$nextId]);
    }
}

function generateAndGetSequence($prefix) 
{


    if($prefix =='PDL')
        $tbl_seq = 'postage_and_delivery_sequence';
    else if($prefix =='DEL')
        $tbl_seq = 'delivery_sequence';
    else if($prefix =='OB' || $prefix =='OBG')
        $tbl_seq = 'order_sequence';

    return $prefix.DB::table($tbl_seq)->insertGetId(['link_id' => $prefix]);

}

function generateAndGetGroupedId() 
{
    return DB::table('grouped_orders')->insertGetId(['grouped_orders' => 'grouped_orders']);
}

function unitConvesion($value, $unitFrom, $unitTo){


        $unitFrom = strtoupper($unitFrom);
        $unitTo = strtoupper($unitTo);

        if(($unitFrom == "IN" && $unitTo == "CM")){
            return (float)number_format((float)$value * 2.54, 2);
        }

        if(($unitFrom == "CM" && $unitTo == "IN")){
            return (float)number_format((float)$value * 0.39, 2);
        }


        if(($unitFrom == "LBS" && $unitTo == "KG")){
            return (float)number_format((float)$value * 0.453592,2);
        }

        if(($unitFrom == "KG" && $unitTo == "LBS")){
            return (float)number_format((float)$value * 2.2,2);
        }

        if(($unitFrom == "G" && $unitTo == "KG")){
            return (float)number_format((float)$value * 0.001,2);
        }

        if(($unitFrom == "G" && $unitTo == "LBS")){
            return (float)number_format((float)$value * 0.00220462,2);
        }

}



function storeCSVTemp($f, $sender_id, $import_batch) 
{

    $yes = array('Y','YES','Yes','yes');
    $no = array('N','NO','No','no');
    $csv = $f['csv'];
    $pd = 'PD';
    $do = 'DO';
    $shipment_type = '';
    if (strpos($f['fileName'], $pd) !== false) 
        $shipment_type = 'PD';
    else if(strpos($f['fileName'], $do) !== false) 
        $shipment_type = 'DO';

    for($i = 0; $i < sizeof($csv); $i++)
    {
        if(!($csv[$i]["FirstName"] == "FirstName" || $csv[$i]["LastName"] == "LastName" || $csv[$i]["BusinessName"] == "BusinessName"))
        {

            $marks = array(100, 65, 70, 87); 
  
            if (in_array($csv[$i]["isSignatureReq"], $yes)) 
                $isSignatureReq = 1;
            else
                $isSignatureReq = 0;

            if (in_array($csv[$i]["LetterMail"], $yes)) 
                $LetterMail = 1;
            else
                $LetterMail = 0;

            DB::table('csv_import_temp')->insert([
                'shipment_type' => $shipment_type,
                'sender_id' => $sender_id,
                'import_batch' => $import_batch,
                'firstName' => $csv[$i]["FirstName"],
                'lastName' => $csv[$i]["LastName"],
                'company' => $csv[$i]["BusinessName"],
                'phone' => (@$csv[$i]["Phone"])? @$csv[$i]["Phone"]:"",
                'businessName' => $csv[$i]["BusinessName"],     
                'email' => (@$csv[$i]["Email"])? @$csv[$i]["Email"]:"",
                'addressLine1' => $csv[$i]["AddressLine1"],
                'addressLine2' => $csv[$i]["AddressLine2"],
                'city' => $csv[$i]["City"],
                'provState' => $csv[$i]["ProvState"],
                'postalZipCode' => $csv[$i]["PostalZipCode"],
                'intlProvState' => $csv[$i]["IntlProvState"],
                'country' => $csv[$i]["Country"],
                
                'length' => (@$csv[$i]["Length"])? @$csv[$i]["Length"]:"",
                'width' => (@$csv[$i]["Width"])? @$csv[$i]["Width"]:"",
                'height' => (@$csv[$i]["Height"])? @$csv[$i]["Height"]:"",
                'weight' =>  $csv[$i]["Weight"],
                'unit_size' =>  "IN",
                'unit_weight' =>  "LBS",

                'isSignatureReq' => $isSignatureReq,
                'letterMail' => $LetterMail,
                'tracking' => (@$csv[$i]["Tracking"])? $csv[$i]["Tracking"]:"",

                'item1' => $csv[$i]["Item1"],
                'qty1' => $csv[$i]["Qty1"],
                'itemValue1' => $csv[$i]["ItemValue1"],
                'originCountry1' => $csv[$i]["OriginCountry1"],

                'item2' => $csv[$i]["Item2"],
                'qty2' => $csv[$i]["Qty2"],
                'itemValue2' => $csv[$i]["ItemValue2"],
                'originCountry2' => $csv[$i]["OriginCountry2"],
                
                'item3' => $csv[$i]["Item3"],
                'qty3' => $csv[$i]["Qty3"],
                'itemValue3' => $csv[$i]["ItemValue3"],
                'originCountry3' => $csv[$i]["OriginCountry3"],         

                'item4' => $csv[$i]["Item4"],
                'qty4' => $csv[$i]["Qty4"],
                'itemValue4' => $csv[$i]["ItemValue4"],
                'originCountry4' => $csv[$i]["OriginCountry4"],         


                'item5' => $csv[$i]["Item5"],
                'qty5' => $csv[$i]["Qty5"],
                'itemValue5' => $csv[$i]["ItemValue5"],
                'originCountry5' => $csv[$i]["OriginCountry5"],         


                'item6' => $csv[$i]["Item6"],
                'qty6' => $csv[$i]["Qty6"],
                'itemValue6' => $csv[$i]["ItemValue6"],
                'originCountry6' => $csv[$i]["OriginCountry6"],         


                'item7' => $csv[$i]["Item7"],
                'qty7' => $csv[$i]["Qty7"],
                'itemValue7' => $csv[$i]["ItemValue7"],
                'originCountry7' => $csv[$i]["OriginCountry7"],         


                'item8' => $csv[$i]["Item8"],
                'qty8' => $csv[$i]["Qty8"],
                'itemValue8' => $csv[$i]["ItemValue8"],
                'originCountry8' => $csv[$i]["OriginCountry8"],         


                'item9' => $csv[$i]["Item9"],
                'qty9' => $csv[$i]["Qty9"],
                'itemValue9' => $csv[$i]["ItemValue9"],
                'originCountry9' => $csv[$i]["OriginCountry9"],         


                'item10' => $csv[$i]["Item10"],
                'qty10' => $csv[$i]["Qty10"],
                'itemValue10' => $csv[$i]["ItemValue10"],
                'originCountry10' => $csv[$i]["OriginCountry10"],           


                'item11' => $csv[$i]["Item11"],
                'qty11' => $csv[$i]["Qty11"],
                'itemValue11' => $csv[$i]["ItemValue11"],
                'originCountry11' => $csv[$i]["OriginCountry11"],           


                'item12' => $csv[$i]["Item12"],
                'qty12' => $csv[$i]["Qty12"],
                'itemValue12' => $csv[$i]["ItemValue12"],
                'originCountry12' => $csv[$i]["OriginCountry12"],           


                'item13' => $csv[$i]["Item13"],
                'qty13' => $csv[$i]["Qty13"],
                'itemValue13' => $csv[$i]["ItemValue13"],
                'originCountry13' => $csv[$i]["OriginCountry13"],           


                'item14' => $csv[$i]["Item14"],
                'qty14' => $csv[$i]["Qty14"],
                'itemValue14' => $csv[$i]["ItemValue14"],
                'originCountry14' => $csv[$i]["OriginCountry14"],           


                'item15' => $csv[$i]["Item15"],
                'qty15' => $csv[$i]["Qty15"],
                'itemValue15' => $csv[$i]["ItemValue15"],
                'originCountry15' => $csv[$i]["OriginCountry15"],           


                'item16' => $csv[$i]["Item16"],
                'qty16' => $csv[$i]["Qty16"],
                'itemValue16' => $csv[$i]["ItemValue16"],
                'originCountry16' => $csv[$i]["OriginCountry16"],           


                'item17' => $csv[$i]["Item17"],
                'qty17' => $csv[$i]["Qty17"],
                'itemValue17' => $csv[$i]["ItemValue17"],
                'originCountry17' => $csv[$i]["OriginCountry17"],           


                'item18' => $csv[$i]["Item18"],
                'qty18' => $csv[$i]["Qty18"],
                'itemValue18' => $csv[$i]["ItemValue18"],
                'originCountry18' => $csv[$i]["OriginCountry18"],           


                'item19' => $csv[$i]["Item19"],
                'qty19' => $csv[$i]["Qty19"],
                'itemValue19' => $csv[$i]["ItemValue19"],
                'originCountry19' => $csv[$i]["OriginCountry19"],           


                'item20' => $csv[$i]["Item20"],
                'qty20' => $csv[$i]["Qty20"],
                'itemValue20' => $csv[$i]["ItemValue20"],
                'originCountry20' => $csv[$i]["OriginCountry20"]            

             ]);
        }

    }

}

function orderItems($sender_id, $import_batch)
{    


    $qry = DB::table('shipments')
        ->selectRaw(
            'shipment_items.shipment_id as shipment_id,
            shipment_items.description as Item,
            shipment_items.qty as Qty,
            shipment_items.value as ItemValue,
            shipment_items.origin_country as OriginCountry'
           )
        ->leftjoin('shipment_items', 'shipment_items.shipment_id', '=', 'shipments.id')
        ->where('shipments.sender_id', '=', $sender_id)
        ->where('shipments.import_batch', '=', $import_batch)
        ->orderby('shipments.recipient_id','shipments.shipment_code')
        ->get();
    
    $items = array();
    $temp_id = 0;
    $temp_items = '';
    $j = 0;
    $t = 0;
    $t_amt = 0;
    foreach ($qry as $key => $i) 
    {   
        $temp_items .= '&bull; '.$i->Qty.' '.$i->Item.($i->Qty>1? 's':'').' at $'.$i->ItemValue.'CAD, Made in '.$i->OriginCountry.'<br>';
        $t += $i->Qty;
        $t_amt += $i->ItemValue;
        if($temp_id != $i->shipment_id)
        {
            $t_mess = '&nbsp&nbsp<b>Total: '.$t .' items, $'. $t_amt.'CAD</b>';
            $items[$i->shipment_id] = $temp_items . $t_mess;    
            $temp_items = '';
            $t = 0;
            $t_amt = 0;
        }

        $temp_id = $i->shipment_id;
    }

    return $items;
                          
}        


function orderSummary($sender_id, $import_batch)
{    

    return DB::table('shipments')
        ->select(
            'shipments.id as shipment_id',
            'shipments.internal_ship_id as internal_ship_id',
            'shipments.sender_id as sender_id',
            'shipments.shipment_code as shipment_code',
            'shipments.shipment_type as shipment_type',
            'shipments.order_id as order_id',
            'shipments.internal_order_id as internal_order_id',
            'shipments.tracking_no as Tracking',
            'shipments.recipient_id as recipient_id',
            'shipments.length as Length',
            'shipments.width as Width',
            'shipments.height as Height',
            'shipments.weight as Weight',
            'shipments.size_unit as SizeUnit',
            'shipments.weight_unit as WeightUnit',
            'shipments.require_signature as isSignatureReq',
            'shipments.letter_mail as LetterMail',
            'shipments.import_batch as import_batch',
            'shipments.note as note',
            
            'shipments.carrier_id as CarrierId',
            'shipments.carrier as Carrier',
            'shipments.carrier_desc as CarrierDesc',
            'shipments.duration as Duration',

            'postage_rates.description as postage_desc',

            DB::raw("IF(shipments.shipment_type='DO',shipments.currency,postage_rates.currency) as Currency"),

            'postage_rates.est_delivery_time as est_delivery_time',
            'postage_rates.package_type as package_type',
            'postage_rates.service_code as service_code',
            'postage_rates.other_cost as other_cost',

            'shipments.insurance_cover as InsuranceCover',

            'shipments.coupon_code as coupon_code',
            'shipments.coupon_type as coupon_type',
            'shipments.coupon_amount as coupon_amount',

            'shipments.insurance_cover_amount as InsuranceCoverAmount',    
            'shipments.mark_up as MarkUp',
            'shipments.postage_fee as PostageFee',
            'shipments.truck_fee as TruckFee',

            DB::raw("IF(shipments.shipment_type='DO',shipments.delivery_fee,shipments.total_fee) as TotalFees"),

            'shipments.amount_paid as AmountPaid',
            'shipments.delivery_fee as DeliveryFee',
            'shipments.note as Note',
            'shipments.import_status as import_status',

            'cbp_addresses.address_1 as cbp_address_1',
            'cbp_addresses.city as cbp_city',
            'cbp_addresses.postal as cbp_postal',
            'cbp_addresses.province as cbp_province',
            'cbp_addresses.country as cbp_country',
            
            'recipients.first_name as FirstName',
            'recipients.last_name as LastName',
            'recipients.contact_no as Phone',
            'recipients.email as Email',
            'recipients.company as BusinessName',
            'recipients.id as recipients_id',
            'recipient_addresses.address_1 as AddressLine1',
            'recipient_addresses.address_2 as AddressLine2',
            'recipient_addresses.city as City',
            'recipient_addresses.province as ProvState',
            'recipient_addresses.postal as PostalZipCode',
            'recipient_addresses.intl_prov_state as IntlProvState',
            'recipient_addresses.country as Country',
            'recipient_addresses.id as recipient_addresses_id',
            'shipments.postage_rate_id as postage_rate_id'
           )
        ->leftjoin('cbp_addresses', 'cbp_addresses.id', '=', 'shipments.cbp_address_id')
        ->leftjoin('recipient_addresses', 'recipient_addresses.id', '=', 'shipments.recipient_address_id')
        ->leftjoin('recipients', 'recipients.id', '=', 'recipient_addresses.recipient_id')
        ->leftjoin('postage_rates', 'postage_rates.id', '=', 'shipments.postage_rate_id')
        ->where('shipments.sender_id', '=', $sender_id)
        ->where('shipments.import_batch', '=', $import_batch)
        ->orderby('shipments.recipient_id','shipments.shipment_code')
        ->get();
                          
}        

function getDownloadData($sender_id, $ids)
{    

    return DB::table('shipments')
            ->select(
                'shipments.internal_ship_id as ShipmentId',
                'shipments.internal_order_id as InternalOrderId',
                'shipments.carrier as ServiceType',
                'cbp_addresses.country as FromCountry',
                'recipients.first_name as RecipientFirstName',
                'recipients.last_name as RecipientLastName',
                'recipients.company as RecipientBusinessName',
                'recipient_addresses.address_1 as RecipientAddressLine1',
                'recipient_addresses.address_2 as RecipientAddressLine2',
                'recipient_addresses.city as RecipientCity',
                'recipient_addresses.province as RecipientProvState',
                'recipient_addresses.postal as RecipientPostalZipCode',
                'recipient_addresses.country as RecipientCountry',
                'recipients.email as RecipientEmail',
                'recipients.contact_no as RecipientPhone',
                'shipments.length as Length',
                'shipments.width as Width',
                'shipments.height as Height',
                'shipments.weight as Weight',
                'shipments.size_unit as SizeUnit',
                'shipments.weight_unit as WeightUnit',
                DB::raw("IF(shipments.require_signature=1,'Y','N') as Signature"),                
                DB::raw("IF(shipments.letter_mail='1','Y','N') as LetterMail"),
                'shipments.carrier_id as Carrier',
                'shipments.insurance_cover_amount as InsuranceAmount',                
                'shipments.postage_fee as PostageFee',
                'shipments.delivery_fee as DeliveryFee',
                DB::raw("IF(shipments.shipment_type='DO',shipments.delivery_fee,shipments.total_fee) as TotalFee"),
                DB::raw("(SUM(shipment_items.qty)) as Items"),
                DB::raw("(SUM(shipment_items.value * shipment_items.qty)  ) as ItemsValue")
           )
        ->leftjoin('cbp_addresses', 'cbp_addresses.id', '=', 'shipments.cbp_address_id')        
        ->leftjoin('senders', 'senders.id', '=', 'shipments.sender_id')
        ->leftjoin('sender_physical_addresses', 'sender_physical_addresses.sender_id', '=', 'senders.id')
        ->leftjoin('recipient_addresses', 'recipient_addresses.id', '=', 'shipments.recipient_address_id')
        ->leftjoin('recipients', 'recipients.id', '=', 'recipient_addresses.recipient_id')
        ->leftjoin('postage_rates', 'postage_rates.id', '=', 'shipments.postage_rate_id')
        ->leftjoin('shipment_status', 'shipment_status.id', '=', 'shipments.shipment_status_id')
        ->leftjoin('shipment_items', 'shipment_items.shipment_id', '=', 'shipments.id')
        ->where('shipments.sender_id', '=', $sender_id)
        ->whereIn('shipments.id', $ids)
        ->where('shipment_status.name', '<>', 'Void')
        ->where('recipient_addresses.is_active', '<>', 0)
        ->where('shipments.note', '<>', 'Imported with error')
        ->where('shipments.note', '<>', 'Update with error')
        ->orderby('shipments.created_at', 'DESC')
        ->groupby('shipments.id')->get();
                         
}         

function getStatusCode($value){
    $qry = DB::table('shipment_status')->select('*')->where('name','=',$value)
                    ->get();

    return $qry[0]->id; 

}    

function updateShipment($shipId, $value){
    
    // get the status code here
    $shipment_status= new ShipmentStatusHistory;
    $shipment_status->shipment_id = $shipId;
    $shipment_status->shipment_status_id = $value;
    $status = $shipment_status->save(); 

    $status = DB::table('shipments')
            ->where('id', $shipId)
            ->update(['shipment_status_id' => $value]);    

    return $status;
}

function updateShipmentAmount($shipId, $value){

    $status = DB::table('shipments')
        ->where('id', $shipId)
        ->update(['total_fee' => 0.0,'amount_paid'=>$value ]);    

    return $status;
}  


function getSenderInfo($id){
    $from_qry = DB::table('senders')->select('senders.id as s_id','senders.*','sender_physical_addresses.*')->where('senders.id','=',$id)
                    ->leftjoin('sender_physical_addresses', 'sender_physical_addresses.sender_id', '=', 'senders.id')
                    ->get();

    $sender['sender_id'] = $from_qry[0]->s_id; 
    $sender['first_name'] = $from_qry[0]->first_name; 
    $sender['last_name'] = $from_qry[0]->last_name;
    $sender['contact_no'] = $from_qry[0]->contact_no;
    $sender['email'] = $from_qry[0]->email;
    $sender['address_1'] = $from_qry[0]->address_1;
    $sender['address_2'] = $from_qry[0]->address_2;
    $sender['province'] = $from_qry[0]->province;
    $sender['city'] = $from_qry[0]->city;
    $sender['postal'] = $from_qry[0]->postal;
    $sender['country'] = $from_qry[0]->country;

    return $sender;
}

function getToInfo($d){

    $caCountry = array("CA", "CAN", "CANADA");
    $usCountry = array("US", "USA", "AMERICA");

    if (in_array(strtoupper($d->country), $usCountry))
        $country = "US";
    else if (in_array(strtoupper($d->country), $caCountry))
        $country = "CA";
    else
        $country = $d->country;


    $to['first_name'] = $d->firstName;
    $to['last_name'] = $d->lastName;
    $to['contact_no'] = $d->phone;
    $to['address_1'] = $d->addressLine1;
    $to['address_2'] = $d->addressLine2;
    $to['city'] = $d->city;
    $to['postal'] = $d->postalZipCode;
    $to['province'] = $d->provState;
    $to['intl_prov_state'] = $d->intlProvState;
    $to['country'] = $country;

    return $to;
}

function getDimInfo($d){

    $dimension['length'] = $d->length;
    $dimension['width'] = $d->width;
    $dimension['height'] = $d->height;
    $dimension['weight'] = $d->weight;
    
    return $dimension;
}

function getPostageInfo($shipArr){

    $postage['carrier'] = (@$shipArr[0]['postage_type'])? $shipArr[0]['postage_type']:"";
    $postage['desc'] = (@$shipArr[0]['desc'])? $shipArr[0]['desc']:"";
    $postage['rate'] = (@$shipArr[0]['rate'])? $shipArr[0]['rate']:"0.00";
    $postage['currency'] = (@$shipArr[0]['currency'])? $shipArr[0]['currency']:"CAD";
    $postage['service_code'] = (@$shipArr[0]['service_code'])? $shipArr[0]['service_code']:"";
    $postage['est_delivery_time'] = (@$shipArr[0]['est_delivery_time'])? $shipArr[0]['est_delivery_time']:"";
    $postage['package_type'] = (@$shipArr[0]['package_type'])? $shipArr[0]['package_type']:"";
    $postage['rate_detail'] = (@$shipArr[0]['rate_detail'])? $shipArr[0]['rate_detail']:"";
    $postage['postage_type'] = (@$shipArr[0]['postage_type'])? $shipArr[0]['postage_type']:"";
    $postage['postage_fee'] = (@$shipArr[0]['total'])? $shipArr[0]['total']:"0.00";
    $postage['est_delivery_no_ofdays'] = (@$shipArr[0]['estimated_delivery'])? $shipArr[0]['estimated_delivery']:"";
    $postage['truck_fee'] = (@$shipArr[0]['truck_fee'])? $shipArr[0]['truck_fee']:"0.00";
    $postage['markup'] = (@$shipArr[0]['markup'])? $shipArr[0]['markup']:"0.00";
    $postage['cbp_delivery_fee'] = (@$shipArr[0]['cbp_delivery_fee'])? $shipArr[0]['cbp_delivery_fee']:"";

    return $postage;
}

function getShipInfo($import_batch_Ids, $senderId, $w)
{

    $q = DB::table('shipments')
        ->selectRaw(
            'shipments.id as shipment_id,
            shipments.internal_ship_id as internal_ship_id,
            shipments.sender_id as sender_id,
            shipments.shipment_code as shipment_code,
            shipments.shipment_type as shipment_type,
            shipments.cbp_address_id as cbp_address_id,
            shipments.order_id as order_id,
            shipments.internal_order_id as internal_order_id,
            shipments.tracking_no as Tracking,
            shipments.recipient_id as recipient_id,
            shipments.length as length,
            shipments.width as width,
            shipments.height as height,
            shipments.weight as weight,
            shipments.require_signature as isSignatureReq,
            shipments.letter_mail as LetterMail,
            shipments.import_batch as import_batch,
            
            shipments.carrier as Carrier,
            shipments.carrier_id as carrier_id,
            shipments.carrier_desc as CarrierDesc,
            shipments.duration as Duration,

            senders.first_name as from_first_name,
            senders.last_name as from_last_name,
            senders.contact_no as from_contact_no,
            senders.email as from_email,
            sender_physical_addresses.address_1 as from_address_1,
            sender_physical_addresses.address_2 as from_address_2,
            sender_physical_addresses.province as from_province,
            sender_physical_addresses.city as from_city,
            sender_physical_addresses.postal as from_postal,
            sender_physical_addresses.country as from_country,

            postage_rates.description as postage_desc,
            postage_rates.currency as Currency,
            postage_rates.value as value,
            postage_rates.est_delivery_time as est_delivery_time,
            postage_rates.package_type as package_type,
            postage_rates.service_code as service_code,
            postage_rates.other_cost as other_cost,
            postage_rates.postage_type as postage_type,

            shipments.insurance_cover as InsuranceCover,
            shipments.insurance_cover_amount as InsuranceCoverAmount,                
            shipments.note as Note,
            shipments.import_status as import_status,

            shipments.coupon_code as coupon_code,
            shipments.coupon_type as coupon_type,
            shipments.coupon_amount as coupon_amount,

            shipments.mark_up as mark_up,
            shipments.postage_fee as postage_fee,
            shipments.truck_fee as truck_fee,
            shipments.total_fee as total_fee,
            shipments.amount_paid as amount_paid,
            shipments.delivery_fee as delivery_fee,

            cbp_addresses.address_1 as cbp_address_1,
            cbp_addresses.city as cbp_city,
            cbp_addresses.postal as cbp_postal,
            cbp_addresses.province as cbp_province,
            cbp_addresses.country as cbp_country,

            recipients.first_name as to_first_name,
            recipients.last_name as to_last_name,
            recipients.contact_no as to_contact_no,
            recipients.email as to_email,
            recipients.company as to_company,
            recipients.id as recipients_id,
            recipient_addresses.address_1 as to_address_1,
            recipient_addresses.address_2 as to_address_2,
            recipient_addresses.city as to_city,
            recipient_addresses.province as to_province,
            recipient_addresses.postal as to_postal,
            recipient_addresses.intl_prov_state as to_intl_prov_state,
            recipient_addresses.country as to_country,
            recipient_addresses.id as recipient_addresses_id,
            shipments.postage_rate_id as postage_rate_id'
           )
        ->leftjoin('cbp_addresses', 'cbp_addresses.id', '=', 'shipments.cbp_address_id')   
        ->leftjoin('senders', 'senders.id', '=', 'shipments.sender_id')
        ->leftjoin('sender_physical_addresses', 'sender_physical_addresses.sender_id', '=', 'senders.id')
        ->leftjoin('recipient_addresses', 'recipient_addresses.id', '=', 'shipments.recipient_address_id')
        ->leftjoin('recipients', 'recipients.id', '=', 'recipient_addresses.recipient_id')
        ->leftjoin('postage_rates', 'postage_rates.id', '=', 'shipments.postage_rate_id')
        ->leftjoin('shipment_status', 'shipment_status.id', '=', 'shipments.shipment_status_id')
        ->where('shipments.sender_id', '=', $senderId)
        ->where('shipment_status.name', '<>', 'Void')
        ->where('shipments.note', '<>', 'Imported with error')
        ->where('shipments.note', '<>', 'Update with error')
        ->where('recipient_addresses.is_active', '<>', 0);
        

    if($w == 'bcode')
        $qry = $q->where('shipments.import_batch', '=', $import_batch_Ids)->orderby('shipments.recipient_id','shipments.shipment_code')->get();
    else if($w == 'ids')
        $qry = $q->whereIn('shipments.id', $import_batch_Ids)->orderby('shipments.recipient_id','shipments.shipment_code')->get();


    $shipInfo = array();
    


    for($i = 0; $i < sizeof($qry); $i++) 
    {    

        $shipInfo['ship_from_address_model'] = getCBPAddressById($qry[$i]->cbp_address_id); 
        
        $shipInfo['shipment_type'] = $qry[$i]->shipment_type;
        $shipInfo['shipment_code'] = $qry[$i]->shipment_code;
        $shipInfo['unit_type'] = "imperial";    
        $shipInfo['signature_require_model'] = "0";    
        $shipInfo['cbp_ship_id'] = $qry[$i]->shipment_id;
        $shipInfo['total_fee'] = $qry[$i]->total_fee;//kuldeep
        $shipInfo['delivery_fee'] = $qry[$i]->delivery_fee;//kuldeep

        $sender['sender_id'] = $qry[$i]->sender_id; 
        
        $sender['first_name'] = $qry[$i]->from_first_name; 
        $sender['last_name'] = $qry[$i]->from_last_name;
        $sender['contact_no'] = $qry[$i]->from_contact_no;
        $sender['email'] = $qry[$i]->from_email;
        $sender['address_1'] = $qry[$i]->from_address_1;
        $sender['address_2'] = $qry[$i]->from_address_2;
        $sender['province'] = $qry[$i]->from_province;
        $sender['city'] = $qry[$i]->from_city;
        $sender['postal'] = $qry[$i]->from_postal;
        $sender['country'] = $qry[$i]->from_country;
        $shipInfo['ship_from_address_model'] = $sender; 
  
        $to['first_name'] = $qry[$i]->to_first_name;
        $to['last_name'] = $qry[$i]->to_last_name;
        $to['contact_no'] = $qry[$i]->to_contact_no;
        $to['address_1'] = $qry[$i]->to_address_1;
        $to['address_2'] = $qry[$i]->to_address_2;
        $to['city'] = $qry[$i]->to_city;
        $to['postal'] = $qry[$i]->to_postal;
        $to['province'] = $qry[$i]->to_province;
        $to['intl_prov_state'] = $qry[$i]->to_intl_prov_state;
        $to['country'] = $qry[$i]->to_country;
        $shipInfo['recipient_model'] = $to;       


        $dimension['length'] = $qry[$i]->length;
        $dimension['width'] = $qry[$i]->width;
        $dimension['height'] = $qry[$i]->height;
        $dimension['weight'] = $qry[$i]->weight;
        $shipInfo['parcel_dimensions_model'] = $dimension;

        $shipInfo['parcel_letter_status']= strtolower($qry[$i]->LetterMail);

        $postageDetails['postage'] = empty($qry[$i]->postage)? 0:$qry[$i]->postage;
        $postageDetails['carrier'] = $qry[$i]->Carrier;
        $postageDetails['carrier_id'] = $qry[$i]->carrier_id;
        $postageDetails['desc'] = $qry[$i]->postage_desc;
        $postageDetails['carrier_desc'] = $qry[$i]->CarrierDesc;
        $postageDetails['currency'] = $qry[$i]->Currency;
        $postageDetails['total'] = $qry[$i]->value;
        $postageDetails['rate'] = $qry[$i]->value;
        $postageDetails['billing_weight'] = $qry[$i]->weight;
        $postageDetails['est_delivery_time'] = $qry[$i]->est_delivery_time;
        $postageDetails['postage_type'] = $qry[$i]->postage_type;
        $postageDetails['service_code'] = $qry[$i]->service_code;
        $postageDetails['truck_fee'] = $qry[$i]->truck_fee;
        $postageDetails['cbp_delivery_fee'] = $qry[$i]->delivery_fee;

        $shipInfo['postage_option_model']['details'] = $postageDetails;

        $qry_items = DB::table('shipments')
            ->selectRaw(
                'shipment_items.description,
                shipment_items.qty,
                shipment_items.value,
                shipment_items.origin_country'
               )
            ->leftjoin('shipment_items', 'shipment_items.shipment_id', '=', 'shipments.id')
            ->where('shipments.id', '=', $qry[$i]->shipment_id)
            ->get();
        
        for($q = 0; $q < sizeof($qry_items); $q++) 
        {   
            $item[$q]['description'] = $qry_items[$q]->description;
            $item[$q]['quantity'] = $qry_items[$q]->qty;
            $item[$q]['value'] = $qry_items[$q]->value;
            $item[$q]['country'] = $qry_items[$q]->origin_country;

        }
        $shipInfo['item_information_model'] = $item;

        $idsArr = [
            'shipment_id'=>$qry[$i]->shipment_id, 
            'recipient_id'=>$qry[$i]->recipients_id, 
            'recipient_name'=>$qry[$i]->to_first_name." ".$qry[$i]->to_last_name,
        ];
        $shipInfo['idsArr'] = $idsArr;
        $batchShipInfo[$i] = $shipInfo;    
    }

    return $batchShipInfo;


}


function getCBPAddress($loc = 'CA', $type = null, $carrier = null){
    
    if($type == 'PD' && $carrier == "USPS")
    {

        $from_qry = DB::table('cbp_addresses')->select('*')->where('country','=','US')->get();
        $sender['cbp_address_id'] = $from_qry[0]->id;
        $sender['address_1'] = $from_qry[0]->address_1;
        $sender['address_2'] = $from_qry[0]->address_2;
        $sender['province'] = $from_qry[0]->province;
        $sender['city'] = $from_qry[0]->city;
        $sender['postal'] = $from_qry[0]->postal;
        $sender['country'] = $from_qry[0]->country;        
    }
    else if($type == 'DO')
    {
        $from_qry = DB::table('cbp_addresses')->select('*')->where('country','=','CA')->get();

        $sender['cbp_address_id'] = $from_qry[0]->id;
        $sender['address_1'] = $from_qry[0]->address_1;
        $sender['address_2'] = $from_qry[0]->address_2;
        $sender['province'] = $from_qry[0]->province;
        $sender['city'] = $from_qry[0]->city;
        $sender['postal'] = $from_qry[0]->postal;
        $sender['country'] = $from_qry[0]->country;        
    }
    else
    {
        $from_qry = DB::table('cbp_addresses')->select('*')->where('country','=',$loc)->get();

        $sender['cbp_address_id'] = $from_qry[0]->id;
        $sender['address_1'] = $from_qry[0]->address_1;
        $sender['address_2'] = $from_qry[0]->address_2;
        $sender['province'] = $from_qry[0]->province;
        $sender['city'] = $from_qry[0]->city;
        $sender['postal'] = $from_qry[0]->postal;
        $sender['country'] = $from_qry[0]->country;        
    }
    return $sender;
}

function getCBPAddressById($id){
    $from_qry = DB::table('cbp_addresses')->select('*')->where('id','=',$id)->get();

    $sender['cbp_address_id'] = $from_qry[0]->id;
    $sender['address_1'] = $from_qry[0]->address_1;
    $sender['address_2'] = $from_qry[0]->address_2;
    $sender['province'] = $from_qry[0]->province;
    $sender['city'] = $from_qry[0]->city;
    $sender['postal'] = $from_qry[0]->postal;
    $sender['country'] = $from_qry[0]->country;

    return $sender;
}

function getMarkup($id){
    if(empty($id))
        return 0;    
    else
    {
        $carrier_qry = DB::table('carriers')->select('*')->where('carrier_id','=',$id)->get();
        return ($carrier_qry[0]->mark_up > 0)? $carrier_qry[0]->mark_up:0;
    }
}


function transliterateString($txt) {
    $transliterationTable = array('á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 'а' => 'a', 'А' => 'a', 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'd', 'е' => 'e', 'Е' => 'E', 'ё' => 'e', 'Ё' => 'E', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o', 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja');
    return str_replace(array_keys($transliterationTable), array_values($transliterationTable), $txt);
}

function processArchive($ids){

    // process the archiving here
    // copy the relevant data to separate table
    
    DB::beginTransaction();

    $qry = DB::table('shipments')
        ->selectRaw('*')
        ->whereIn('id', $ids)
        ->orderBy('id')
        ->get();
        
    for($q = 0; $q < sizeof($qry); $q++) 
    {   

        $shipmentsArchived = new ShipmentsArchived;
        $shipmentsArchived->id = $qry[$q]->id;
        $shipmentsArchived->shipment_code = $qry[$q]->shipment_code;
        $shipmentsArchived->sender_id = $qry[$q]->sender_id;
        $shipmentsArchived->tracking_no = $qry[$q]->tracking_no;
        $shipmentsArchived->order_id = $qry[$q]->order_id;
        $shipmentsArchived->recipient_id = $qry[$q]->recipient_id;
        $shipmentsArchived->carrier = $qry[$q]->carrier;
        $shipmentsArchived->postage_rate_id = $qry[$q]->postage_rate_id;
        $shipmentsArchived->shipment_date = $qry[$q]->shipment_date;
        $shipmentsArchived->length = $qry[$q]->length;
        $shipmentsArchived->width = $qry[$q]->width;
        $shipmentsArchived->height = $qry[$q]->height;
        $shipmentsArchived->size_unit = $qry[$q]->size_unit;
        $shipmentsArchived->weight = $qry[$q]->weight;
        $shipmentsArchived->weight_unit = $qry[$q]->weight_unit;
        $shipmentsArchived->require_signature = $qry[$q]->require_signature;
        $shipmentsArchived->delivery_fee = $qry[$q]->delivery_fee;
        $shipmentsArchived->amount_paid = $qry[$q]->amount_paid;
        $shipmentsArchived->shipment_status_id = $qry[$q]->shipment_status_id;
        $shipmentsArchived->received = $qry[$q]->received;
        $shipmentsArchived->picked_up = $qry[$q]->picked_up;
        $shipmentsArchived->in_transit = $qry[$q]->in_transit;
        $shipmentsArchived->duration = $qry[$q]->duration;
        $shipmentsArchived->delivered = $qry[$q]->delivered;
        $shipmentsArchived->note = $qry[$q]->note;
        $shipmentsArchived->letter_mail = $qry[$q]->letter_mail;
        $shipmentsArchived->created_at = $qry[$q]->created_at;
        $shipmentsArchived->updated_at = $qry[$q]->updated_at;
        $shipmentsArchived->deleted_at = $qry[$q]->deleted_at;
        $shipmentsArchived->import_batch = $qry[$q]->import_batch;
        $shipmentsArchived->import_status = $qry[$q]->import_status;
        $shipmentsArchived->carrier_desc = $qry[$q]->carrier_desc;
        $shipmentsArchived->currency = $qry[$q]->currency;
        $shipmentsArchived->shipment_type = $qry[$q]->shipment_type;
        $shipmentsArchived->insurance_cover = $qry[$q]->insurance_cover;
        $shipmentsArchived->insurance_cover_amount = $qry[$q]->insurance_cover_amount;
        $shipmentsArchived->cbp_address_id = $qry[$q]->cbp_address_id;
        $shipmentsArchived->letter_option = $qry[$q]->letter_option;
        $shipmentsArchived->postage_fee = $qry[$q]->postage_fee;
        $shipmentsArchived->total_fee = $qry[$q]->total_fee;
        $shipmentsArchived->postage = $qry[$q]->postage;
        $shipmentsArchived->truck_fee = $qry[$q]->truck_fee;
        $shipmentsArchived->recipient_address_id = $qry[$q]->recipient_address_id;
        $shipmentsArchived->bag_id = $qry[$q]->bag_id;
        $shipmentsArchived->pallet_id = $qry[$q]->pallet_id;
        $shipmentsArchived->cbme = $qry[$q]->cbme;
        $shipmentsArchived->carrier_id = $qry[$q]->carrier_id;
        $shipmentsArchived->mark_up = $qry[$q]->mark_up;
        $shipmentsArchived->coupon_code = $qry[$q]->coupon_code;
        $shipmentsArchived->coupon_type = $qry[$q]->coupon_type;
        $shipmentsArchived->coupon_amount = $qry[$q]->coupon_amount;
        $res1 = $shipmentsArchived->save();

    }

    $qryItems = DB::table('shipment_items')
        ->selectRaw('*')
        ->whereIn('shipment_id', $ids)
        ->orderBy('shipment_id')
        ->get();

    for($q = 0; $q < sizeof($qryItems); $q++) 
    {   

        $shipmentItemsArchived = new ShipmentItemsArchived;
        $shipmentItemsArchived->id = $qryItems[$q]->id;
        $shipmentItemsArchived->shipment_id = $qryItems[$q]->shipment_id;
        $shipmentItemsArchived->pallet_id = $qryItems[$q]->pallet_id;
        $shipmentItemsArchived->bag_id = $qryItems[$q]->bag_id;
        $shipmentItemsArchived->description = $qryItems[$q]->description;
        $shipmentItemsArchived->value = $qryItems[$q]->value;
        $shipmentItemsArchived->qty = $qryItems[$q]->qty;
        $shipmentItemsArchived->origin_country = $qryItems[$q]->origin_country;
        $shipmentItemsArchived->note = $qryItems[$q]->note;
        $shipmentItemsArchived->created_at = $qryItems[$q]->created_at;
        $shipmentItemsArchived->updated_at = $qryItems[$q]->updated_at;
        $shipmentItemsArchived->deleted_at = $qryItems[$q]->deleted_at;
        $res2 = $shipmentItemsArchived->save();

    }      

    $res3 = DB::table('shipment_items')
        ->whereIn('shipment_id', $ids)
        ->delete();

    $res4 = DB::table('shipments')
        ->whereIn('id', $ids)
        ->delete();

    if($res1 && $res2 && $res3 && $res4)
    {
        DB::commit();
        return true;
    }
    else
    {
        DB::rollback();
        return false;
    }

}

function processFee($d){

    $qry = DB::table('shipments')
        ->selectRaw(
            'shipments.id,
            shipments.shipment_type,
            shipments.insurance_cover_amount,    
            shipments.total_fee'
        )
        ->where('import_batch', '=', $d['importBatch'])
        ->get();
    
    for($q = 0; $q < sizeof($qry); $q++) 
    {   

        $fee = $qry[$q]->total_fee + $qry[$q]->insurance_cover_amount;

        if($d['coupon']['couponType'] = '%')
            $total_fee = $fee - ($fee * ($d['coupon']['couponAmount']/100));
        else
            $total_fee = $fee - $d['coupon']['couponAmount'];

        $status = DB::table('shipments')
            ->where('id', $qry[$q]->id)
            ->update(
                ['coupon_code' => $d['coupon']['couponCode'], 
                'coupon_type' => $d['coupon']['couponType'], 
                'coupon_amount' => $d['coupon']['couponAmount'], 
                'total_fee' => round($total_fee,2)]
            );

    }

}


function updatePaidShipments($batchIds, $fromWhere) 
{

    $prefix = 'OB';
    $nextOrderId = generateAndGetSequence($prefix);

    if($fromWhere == 'from_allshipment')
    {

        if(is_array($batchIds))
        {
            for($i=0; $i<sizeof($batchIds); $i++)    
                $shipIds[$batchIds[$i][0]] = $batchIds[$i][0];
        }
        else
        {
            $batchIds = explode(',', $batchIds);
            for($i=0; $i<sizeof($batchIds); $i++)    
                $shipIds[$batchIds[$i]] = $batchIds[$i];
        }

        foreach ($shipIds as $shipId) 
                $ids[] = $shipId; 

        $qry = DB::table('shipments')
            ->selectRaw(
                'shipments.id,
                shipments.shipment_type,
                shipments.insurance_cover_amount,    
                shipments.delivery_fee,
                shipments.coupon_code,
                shipments.coupon_type,
                shipments.coupon_amount,
                shipments.total_fee'
            )
            ->whereIn('id', $ids)
            ->get();

    }
    else if($fromWhere == 'from_import')
    {
        $qry = DB::table('shipments')
            ->selectRaw(
                'shipments.id,
                shipments.shipment_type,
                shipments.insurance_cover_amount,    
                shipments.delivery_fee,
                shipments.coupon_code,
                shipments.coupon_type,
                shipments.coupon_amount,                
                shipments.total_fee'
            )
            ->where('import_batch', '=', $batchIds)
            ->get();
    }else{


        //singleshipment
        //update of payment and status is handled in singleshipment createShipmentDO
        return true;

    }

    $statusCode = getStatusCode('Ready');
    for($q = 0; $q < sizeof($qry); $q++) 
    {   

        $shipment_status= new ShipmentStatusHistory;
        $shipment_status->shipment_id = $qry[$q]->id;
        $shipment_status->shipment_status_id = $statusCode;
        $status = $shipment_status->save(); 

        $amount_paid = $qry[$q]->total_fee + $qry[$q]->insurance_cover_amount;

        if($qry[$q]->shipment_type == 'DO')
            $amount_paid = $qry[$q]->delivery_fee;

        if($qry[$q]->coupon_amount > 0)
        {
            if($qry[$q]->coupon_type == '%')
                $amount_paid = $amount_paid - ($amount_paid * ($qry[$q]->coupon_amount/100));
            else
                $amount_paid = $amount_paid - $qry[$q]->coupon_amount;
        }

        $status = DB::table('shipments')
            ->where('id', $qry[$q]->id)
            ->update(['shipment_status_id' => $statusCode, 'internal_order_id' => $nextOrderId, 'amount_paid' => round($amount_paid,2)]);


        handleReports(NULL, $qry[$q]->id, NULL, 'bol');

    }
}

function udpateShipmentDetails($data=[], $id, $manifest, $shipmentDate = NULL){  // to dos

    //dd($data);
    
    $m = Shipment::find($id);
    if($data['shipment_type'] == "PD"){

        if($data['postage_option_model']['details']['carrier_id'] == "CANADA POST"){
            //$m->tracking_no = generateCode($id,15);
        }else{
            $m->shipment_code = @$manifest['shipmentId'];
            $m->tracking_no = @$manifest['trackingNumber'];
            $m->shipment_date = $shipmentDate;
        }

        $m->order_id = @$manifest['orderId'];
        $m->cbme = "CBMESHIP".@$id;

        
    }
    else{
        
        $m->order_id = @$manifest['orderId'];
        //$m->shipment_code = generateCode($id,7);
        //$m->tracking_no = generateCode($id,15);
        $m->cbme = "CBMECBD".$id;
        
    }
    $m->save();

}

function handleInternalLabelCreation($shipmentId){
    $shipping_label = new ShippingLabel;
    $shipping_label->generate($shipmentId);
}

function handleInternalLabelCreationDHL($label, $packageID){
    $shipping_label = new ShippingLabel;
    $shipping_label->generateFromImage($label,  $packageID);
}

function handleReports($data, $shipmentId=null, $imageData=null, $report=null){

    $packing_slip = new PackingSlip;

    if($report == "bol")
        $packing_slip->generate($data, $shipmentId);
}

function splitInsurance($normal_rates, $insured_rates, $service_code, $package_type=null){
    

    $_normal_rate = 0;
    if(@$normal_rates['data']['rates']){
        foreach ($normal_rates['data']['rates'] as $key => $value) {
            if($value['service_code'] == $service_code  && $value['package_type'] == $package_type){
                if(@$value['negotiated_rate']){
                    $_normal_rate = $value['negotiated_rate'];
                }else{
                    $_normal_rate = $value['rate'];
                }

                break;
            }
        }
    }


    $_insured_rate = 0;
    if(@$insured_rates['data']['rates']){
        foreach ($insured_rates['data']['rates'] as $key => $value) {
            if($value['service_code'] == $service_code  && $value['package_type'] == $package_type){
                if(@$value['negotiated_rate']){
                    $_insured_rate = $value['negotiated_rate'];
                }else{
                    $_insured_rate = $value['rate'];
                }

                break;

            }
        }
    }

    return number_format($_insured_rate - $_normal_rate,2);
}


function splitSignature($normal_rates, $signature_rates, $service_code, $package_type=null){
    
    $_normal_rate = 0;
    if(@$normal_rates['data']['rates']){
        foreach ($normal_rates['data']['rates'] as $key => $value) {
            if($value['service_code'] == $service_code && $value['package_type'] == $package_type){
                if(@$value['negotiated_rate']){
                    $_normal_rate = $value['negotiated_rate'];
                }else{
                    $_normal_rate = $value['rate'];
                }

                break;
            }

        }
    }


    $_signature_rate = 0;
    if(@$signature_rates['data']['rates']){
        foreach ($signature_rates['data']['rates'] as $key => $value) {
            if($value['service_code'] == $service_code  && $value['package_type'] == $package_type){
                // dd($value);

                if(@$value['negotiated_rate']){
                    $_signature_rate = $value['negotiated_rate'];
                }else{
                    $_signature_rate = $value['rate'];
                }

                break;
            }
        }
    }

    return number_format($_signature_rate - $_normal_rate,2);
}

////////////////////////

function handleDeliveryOnlyRates($d){
    // dd($d);
    // $letterRate = 0.25;
    // dd($rate);
    if(@$d['parcel_letter_status'] == "yes"){
        $rate = DeliveryFee::where("max_weight","<",30)->first()->letter_mail_price;
        return ['status'=>true, "rate"=>$rate];
    }else{

        $weight = (float)$d['parcel_dimensions_model']['weight'];
        // dd($weight);
         $rate = DB::table('delivery_fees')
                ->where('min_weight', '<=', $weight)
                ->where('max_weight', '>=', $weight)
                ->get()->first();
        // dd($rate->rate);
        // dd($rate->toArray());
        if($rate){
            $_rate = $rate->price;
            return ['status'=>true, "rate"=>$_rate];
            
        }else{
            return ['status'=>false, "message"=>"Rate Error"];

        }
            
    }
}





function handlePostageDeliveryRates($d){
    $show_all_rates = env("SHOW_ALL_RATES");
    $from_country = $d['ship_from_address_model']['country'];
    $to_country = $d['recipient_model']['country'];

  
    if($from_country == "CA" && $to_country == "CA"){

            // $rate1 = $rs->getCanadaPostRates($d);
            // $rate2 = $rs->getUPSCARates($d);
            // $rate3 = $rs->getFedExRates($d);

        
        try {

            $rs = new Rocketship;
            // $rate2 = $rs->getUPSCARates($d);
            // dd($rate2);


            $rate1 = $rs->getCanadaPostRates($d);


            $d['_signature'] = "false";
            $d['_insurance'] = "false";
            $rate1_normal = $rs->getCanadaPostRates($d);
            // dd($rate1_normal);

            $d['_signature'] = "true";
            $d['_insurance'] = "false";
            $rate1_signature = $rs->getCanadaPostRates($d);
            // dd($rate1_signature);

            $d['_signature'] = "false";
            $d['_insurance'] = "true";
            $rate1_insurance = $rs->getCanadaPostRates($d);
            // dd($rate1_insurance);
            // dd($rate1);


            unset($d['_signature']);
            unset($d['_insurance']);


            $rate2 = $rs->getUPSCARates($d);


            $d['_signature'] = "false";
            $d['_insurance'] = "false";
            $rate2_normal = $rs->getUPSCARates($d);


            $d['_signature'] = "true";
            $d['_insurance'] = "false";
            $rate2_signature = $rs->getUPSCARates($d);
            // dd($rate1_signature);

            $d['_signature'] = "false";
            $d['_insurance'] = "true";
            $rate2_insurance = $rs->getUPSCARates($d);

            // dd($rate2);


            unset($d['_signature']);
            unset($d['_insurance']);

            $rate3 = $rs->getFedExRates($d);
            // dd($rate3);

            $d['_signature'] = "false";
            $d['_insurance'] = "false";
            $rate3_normal = $rs->getFedExRates($d);
            // dd($rate3_normal);


            $d['_signature'] = "true";
            $d['_insurance'] = "false";
            $rate3_signature = $rs->getFedExRates($d);
            // dd($rate3_signature);

            $d['_signature'] = "false";
            $d['_insurance'] = "true";

            $rate3_insurance = $rs->getFedExRates($d);
            // dd($rate3_insurance);
            
            // dd($rate3);

            unset($d['_signature']);
            unset($d['_insurance']);

            //include zoneskip
            $rate4 = $rs->getCanadaPostRates($d, true);

            $d['_signature'] = "false";
            $d['_insurance'] = "false";
            $rate4_normal = $rs->getCanadaPostRates($d, true);

            $d['_signature'] = "true";
            $d['_insurance'] = "false";
            $rate4_signature = $rs->getCanadaPostRates($d, true);
            // dd($rate1_signature);

            $d['_signature'] = "false";
            $d['_insurance'] = "true";
            $rate4_insurance = $rs->getCanadaPostRates($d, true);

            $sign_insurance = [
                'rate1_normal' => $rate1_normal,
                'rate1_signature' => $rate1_signature,
                'rate1_insurance' => $rate1_insurance,

                'rate2_normal' => $rate2_normal,
                'rate2_signature' => $rate2_signature,
                'rate2_insurance' => $rate2_insurance,

                'rate3_normal' => $rate3_normal,
                'rate3_signature' => $rate3_signature,
                'rate3_insurance' => $rate3_insurance,

                'rate4_normal' => $rate4_normal,
                'rate4_signature' => $rate4_signature,
                'rate4_insurance' => $rate4_insurance,
            ];
        

            $rates = [];

            $CBP_Saver_CACA = CBP_Saver_CACA($sign_insurance, $rate1, $rate2, $rate3);
            $CBP_Saver_CACA_ZoneSkipped = CBP_Saver_CACA_ZoneSkipped($sign_insurance, $rate4);
            $CBP_Expedited_CACA = CBP_Expedited_CACA($sign_insurance, $rate1, $rate2);
            $CBP_Express_CACA = CBP_Express_CACA($sign_insurance, $rate1, $rate2);


            if($show_all_rates){

                if($CBP_Saver_CACA) $rate1 = $CBP_Saver_CACA;
                  if($CBP_Expedited_CACA) $rate2 = $CBP_Expedited_CACA;
                  if($CBP_Express_CACA) $rate3 = $CBP_Express_CACA;
                  if($CBP_Saver_CACA_ZoneSkipped) $rate4 = $CBP_Saver_CACA_ZoneSkipped;

                  $rates = array_merge($rate1, $rate2, $rate3, $rate4);

            }else{

              if($CBP_Saver_CACA) $rates[] = $CBP_Saver_CACA;
              if($CBP_Saver_CACA_ZoneSkipped) $rates[] = $CBP_Saver_CACA_ZoneSkipped;
              if($CBP_Expedited_CACA) $rates[] = $CBP_Expedited_CACA;
              if($CBP_Express_CACA) $rates[] = $CBP_Express_CACA;

            }

            // dd($rates);

            // $_rate1 = renameCBP($rate1['data']['rates']);
            // $a = insertCarrier($_rate1,"CANADA POST");
            // $b = insertCarrier($rate2['data']['rates'],"UPS");
            // $c = insertCarrier($rate3['data']['rates'],"FedEx");

            // dd($rate1);
            // $data = $rate1;
            // $data['data']['rates'] = ratesCompare($a, $b, $c);
            // dd($data['data']['rates']);
            $res =  $rates;


        } catch (\Exception $e) {
             return ['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS"'];
            
        }

        // dd($res);

    }else if($from_country == "CA" && $to_country == "US"){


        try {

            $rs = new Rocketship;
            $rate1 = $rs->getUPSCARates($d);
            // $rate2 = $rs->getSTAMPSRates($d);

            $d['_signature'] = "false";
            $d['_insurance'] = "false";
            $rate1_normal = $rs->getUPSCARates($d);
            // dd($rate1_normal);

            $d['_signature'] = "true";
            $d['_insurance'] = "false";
            $rate1_signature = $rs->getUPSCARates($d);
            // dd($rate1_signature);

            $d['_signature'] = "false";
            $d['_insurance'] = "true";
            $rate1_insurance = $rs->getUPSCARates($d);
            // dd($rate1_insurance);

            $sign_insurance = [
                'rate1_normal' => $rate1_normal,
                'rate1_signature' => $rate1_signature,
                'rate1_insurance' => $rate1_insurance,
            ];



            $rates = [];

            $CBP_Saver_CAUS = CBP_Saver_CAUS($sign_insurance, $rate1);
            $CBP_Expedited_CAUS = CBP_Expedited_CAUS($sign_insurance, $rate1);
            $CBP_Express_CAUS = CBP_Express_CAUS($sign_insurance, $rate1);


            if($show_all_rates){

              if($CBP_Saver_CAUS) $rate1 = $CBP_Saver_CAUS;
              if($CBP_Expedited_CAUS) $rate2 = $CBP_Expedited_CAUS;
              if($CBP_Express_CAUS) $rate3 = $CBP_Express_CAUS;

              $rates = array_merge($rate1, $rate2, $rate3);
            
            }else{

                if($CBP_Saver_CAUS) $rates[] = $CBP_Saver_CAUS;
                if($CBP_Expedited_CAUS) $rates[] = $CBP_Expedited_CAUS;
                if($CBP_Express_CAUS) $rates[] = $CBP_Express_CAUS;

            }

            $res =  $rates;

        } catch (\Exception $e) {
             return ['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS"'];
            
        }
    }elseif($from_country == "CA" &&  !in_array($to_country, ["CA","US"])){
            
        
        try {
            $rs = new Rocketship;
            $rate1 = $rs->getDHLRates($d);
            // dd($rate1);
            // echo "<pre>";
            // print_r($rate1);
            // exit;
            $rate2 = $rs->getUPSCARates($d);

            $d['_signature'] = "false";
            $d['_insurance'] = "false";
            $rate2_normal = $rs->getUPSCARates($d);


            $d['_signature'] = "true";
            $d['_insurance'] = "false";
            $rate2_signature = $rs->getUPSCARates($d);
            // dd($rate1_signature);

            $d['_signature'] = "false";
            $d['_insurance'] = "true";
            $rate2_insurance = $rs->getUPSCARates($d);

            $sign_insurance = [
                // 'rate1_normal' => $rate1_normal,
                // 'rate1_signature' => $rate1_signature,
                // 'rate1_insurance' => $rate1_insurance,

                'rate2_normal' => $rate2_normal,
                'rate2_signature' => $rate2_signature,
                'rate2_insurance' => $rate2_insurance,
            ];
            $rates = [];

            // $CBP_Saver_CAInt = CBP_Saver_CAInt($rate1);
            $CBP_Expedited_CAInt = CBP_Expedited_CAInt($sign_insurance, $rate2);
            $CBP_Express_CAInt = CBP_Express_CAInt($sign_insurance, $rate2);

        
            if($show_all_rates){

              // if($CBP_Saver_CAInt) $rate1 = $CBP_Saver_CAInt;
              if($CBP_Expedited_CAInt) $rate2 = $CBP_Expedited_CAInt;
              if($CBP_Express_CAInt) $rate3 = $CBP_Express_CAInt;
              
              // $rates = array_merge($rate1, $rate2, $rate3);
              $rates = array_merge( $rate2, $rate3);

            }else{

                if($CBP_Saver_CAInt) $rates[] = $CBP_Saver_CAInt;
              if($CBP_Expedited_CAInt) $rates[] = $CBP_Expedited_CAInt;
              if($CBP_Express_CAInt) $rates[] = $CBP_Express_CAInt;

            }

            $res =  $rates;
            // $rate1 = $rs->getUPSCARates($d);
            // $rate2 = $rs->getDHLRates($d);
            // // dd($rate2);

            // $a = insertCarrier($rate1['data']['rates'],"UPS");
            // $b = insertCarrier($rate2['data']['rates'],"DHL");
            // // dd($b);

            // // dd($rate1);
            // $data = $rate1;
            // $data['data']['rates'] = ratesCompare($a, $b);

            // $data['data']['rates'] = sortRates( $data['data']['rates']);
            // $data['data']['rates'] = insertCarrier( $data['data']['rates'], "UPS");
            // $res = $data;
        } catch (\Exception $e) {
             return ['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS" and declare item details'];
        }

    }elseif($from_country == "US" && $to_country == "US"){
        try { 
            $rates = [];

                if($show_all_rates){
                    if(CBP_Saver_USUS($d)) $rates = CBP_Saver_USUS($d);

                }else{
                    if(CBP_Saver_USUS($d)) $rates[] = CBP_Saver_USUS($d);
                }

            $res =  $rates;
            
            // // $rs->getSTAMPSRates($d);
            // $data = $rs->getSTAMPSRates($d);
            // $data['data']['rates'] = sortRates( $data['data']['rates']);
            // $data['data']['rates'] = insertCarrier( $data['data']['rates'], "USPS");
            // // dd($d['parcel_types']['parcel_type']);
            // $data['data']['rates'] = filterUSPSRates($data['data']['rates'],  $d['usps_options_model']['upsps_option'], $d['parcel_types']['parcel_type']);
            // // dd($data['data']['rates']);
            // $res = $data;
        } catch (\Exception $e) {
            return ['status'=>false,"message"=>'No rates available. Please review your shipment details"'];
        }
        
    }elseif($from_country == "US" && $to_country != "US"){

            
        try {

            $rates = [];
            if($show_all_rates){
                if(CBP_Saver_USInt($d)) $rates = CBP_Saver_USInt($d);
            }else{
                if(CBP_Saver_USInt($d)) $rates[] = CBP_Saver_USInt($d);

            }
            $res =  $rates;

            // $data = $rs->getUPSUSRates($d);
            // // dd($data);
            // $data['data']['rates'] = sortRates( $data['data']['rates']);
            // $data['data']['rates'] = insertCarrier( $data['data']['rates'], "UPS");
            // $res = $data;

        } catch (\Exception $e) {
            return ['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS"'];
            
        }

    }



    $res = postageRateTriggers($d, $rates);
    // 

    // $rs->getUFedExRates($d);
    // $ss->test($d);
    // dd($res);

    return ['status'=>true,"response"=>$res];
}


// function getRates(Request $r){
//     $d = $r->toArray();
//     $rs =  new Rocketship;
//     $ss =  new ShipStation;

//     // dd($ss->createShipment($d));

//     $from_country = $d['ship_from_address_model']['country'];
//     $to_country = $d['recipient_model']['country'];


//     $rates = [];
//     if($from_country == "CA" && $to_country == "CA"){
            
//         try {
//             if(CBP_Saver_CACA($d))  $rates[] = CBP_Saver_CACA($d);
//             if(CBP_Expedited_CACA($d))  $rates[] = CBP_Expedited_CACA($d);
//             if(CBP_Express_CACA($d))  $rates[] = CBP_Express_CACA($d);

//         } catch (\Exception $e) {
//              return ['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS"']);
            
//         }

//         // dd($res);

//     }else if($from_country == "CA" && $to_country == "US"){
//         try {

//             if(CBP_Saver_CAUS($d)) $rates[] = CBP_Saver_CAUS($d);
//             if(CBP_Expedited_CAUS($d)) $rates[] = CBP_Expedited_CAUS($d);
//             if(CBP_Express_CAUS($d)) $rates[] = CBP_Express_CAUS($d);


//         } catch (\Exception $e) {
//              return ['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS"']);
            
//         }

//     }elseif($from_country == "CA" && $to_country != "US"){
            
//         try {
//             if(CBP_Saver_CAInt($d)) $rates[] = CBP_Saver_CAInt($d);
//             if(CBP_Expedited_CAInt($d)) $rates[] = CBP_Expedited_CAInt($d);
//             if(CBP_Express_CAInt($d)) $rates[] = CBP_Express_CAInt($d);

//         } catch (\Exception $e) {
//              return ['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS" and declare item details']);
//         }

//     }elseif($from_country == "US" && $to_country == "US"){

//             if(CBP_Saver_USUS($d)) $rates[] = CBP_Saver_USUS($d);

//         try {
//         } catch (\Exception $e) {
//             return ['status'=>false,"message"=>'No rates available. Please review your shipment details"']);
//         }
        
//     }elseif($from_country == "US" && $to_country != "US"){

//             if(CBP_Saver_USInt($d)) $rates[] = CBP_Saver_USInt($d);

//         try {
//         } catch (\Exception $e) {
//             return ['status'=>false,"message"=>'No rates available. Please review your shipment details"']);
//         }
        
//     }

//     // $res['data']['rates'] = postageRateTriggers($d, $res['data']['rates']);
//     // 

//     // $rs->getUFedExRates($d);
//     // $ss->test($d);
//     // dd($res);

//     return ['status'=>true,"response"=>$rates]);
// }


function sortShipstationRates($rates){
    $show_all_rates = env("SHOW_ALL_RATES");

    // dd($rates);
    //SORT ACCORDING TO COST
    for ($i=0; $i < count($rates) ; $i++) { 
        for ($z=0; $z < count($rates) ; $z++) { 
            if($z < count($rates) - 1){
                if($rates[$z]['rate'] > $rates[$z + 1]['rate']){
                    $hold =  $rates[$z];
                    $rates[$z] = $rates[$z + 1];
                    $rates[$z + 1] = $hold;
                }
            }
        }

    }

    // dd($rates);


    if($show_all_rates){
        //show all
        return $rates;
    }else{

        //show lowest price
        if(@$rates[0]){
            return $rates[0];
        }else{
            return [];
        }
        
    }

}

function filterRocketshipRatesByServiceCode($rates, $serviceCode, $carrier=null, $packageType=null){
    $selected = [];
    foreach ($rates as $key => $value) {
        // dd($value);

        if(@$packageType){
            if($value['service_code'] == $serviceCode && $value['package_type'] == $packageType){
                $value['carrier'] =  $carrier;
                $selected = $value;
            }
        }else{

            if($value['service_code'] == $serviceCode){
                $value['carrier'] =  $carrier;
                $selected = $value;
            }
        }
    }

    return $selected;
}

function filterUSPSByServiceCodeAndPackageType($rates, $serviceCode, $packageType, $carrier=null){
    $selected = [];
    foreach ($rates as $key => $value) {
        // dd($value);
        if($value['service_code'] == $serviceCode && $value['package_type'] == $packageType){
            $value['carrier'] =  $carrier;
            $selected = $value;
        }
    }

    return $selected;
}


function expedited_zone_skip($d){
    $rs = new Rocketship;
    $rate1 = $rs->getCanadaPostRates($d);
}


function CBP_Saver_CACA($sign_insurance, $rate1, $rate2, $rate3){
    $show_all_rates = env("SHOW_ALL_RATES");
    // $rs = new Rocketship;
    // $rate1 = $rs->getCanadaPostRates($data);
    // $rate2 = $rs->getUPSCARates($data);
    // $rate3 = $rs->getFedExRates($data);
    $rates = [];


    if(@$rate1['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate1['data']['rates'],"DOM.EP", "CANADA POST"); //Expedited Parcel
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "DOM.EP","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "DOM.EP","");

            if($d['insurance_fee'] && $d['signature_fee']){
                $d['rate'] = number_format($d['rate'] + $d['signature_fee'], 2 ); 
            }

            $rates[] = $d; 
        }
    }

    if(@$rate2['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate2['data']['rates'],"11","UPS"); //UPS Standard
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate2_normal'], $sign_insurance['rate2_insurance'], "11","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate2_normal'], $sign_insurance['rate2_signature'], "11","");
            $rates[] = $d; 
        }

    }
    if(@$rate3['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate3['data']['rates'],"FEDEX_GROUND","FEDEX"); //edEx Ground®
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate3_normal'], $sign_insurance['rate3_insurance'], "FEDEX_GROUND","YOUR_PACKAGING");
            $d['signature_fee'] = splitSignature($sign_insurance['rate3_normal'], $sign_insurance['rate3_signature'], "FEDEX_GROUND","YOUR_PACKAGING");

            $rates[] = $d; 
        }
    }

   $_rates = sortShipstationRates($rates);

    // dd($_rates);
    // $rate2 = $rs->getUPSCARates($d);
    // $rate3 = $rs->getFedExRates($d);


   //  $ss =  new ShipStation;
   //  $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_standard");
   //  $CANADA_rate = $ss->getShipStationsRatesCA($data,  "canada_post", "expedited_parcel");
   //  $FEDEX_rate = $ss->getShipStationsRatesCA($data,  "fedex", "fedex_ground");

    // $rates = [];
   // if($UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;
   // if($CANADA_rate[0]['shipmentCost']) $rates[] = $CANADA_rate;
   // if($FEDEX_rate[0]['shipmentCost']) $rates[] = $FEDEX_rate;

   // $_rates = sortShipstationRates($rates);


   //display all rates

   if($show_all_rates){
       foreach ($_rates as $key => $value) {
           # code...

            if($value["carrier"] == "FEDEX"){

                if(@$value['rate_detail']){
                    foreach (@$value['rate_detail'] as $k => $v) {
                        if($v['type'] == "SIGNATURE_OPTION"){
                            $_rates[$key]['signature_fee'] = $v['amount'];   
                        }
                    }
                }
            }

            $_rates[$key]['postage_type'] = "CBP Saver";   
            $_rates[$key]['postage_type_code'] = "cbp_saver";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "5 days or more";


            // print_r("<pre>");
            // print_r($_rates);
            // exit;
       }
   }else{

       //hold display lowest single rate
       if($_rates){
            $_rates['postage_type'] = "CBP Saver";   
            $_rates['postage_type_code'] = "cbp_saver";   
            $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
            $_rates['estimated_delivery'] = "5 days or more";
       }
   }


   
   return $_rates;


}


function CBP_Saver_CACA_ZoneSkipped($sign_insurance, $rate){
    $show_all_rates = env("SHOW_ALL_RATES");

    // $rs = new Rocketship;
    // $rate1 = $rs->getCanadaPostRates($data);
    // $rate2 = $rs->getUPSCARates($data);
    // $rate3 = $rs->getFedExRates($data);
    $rates = [];

    if(@$rate['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate['data']['rates'],"DOM.EP", "CANADA POST"); //Expedited Parcel
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate4_normal'], $sign_insurance['rate4_insurance'], "DOM.EP","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate4_normal'], $sign_insurance['rate4_signature'], "DOM.EP","");
            if($d['insurance_fee'] && $d['signature_fee']){
                $d['rate'] = number_format($d['rate'] + $d['signature_fee'], 2 ); 
            }
            $rates[] = $d; 
        }
    }

   

   $_rates = sortShipstationRates($rates);


   //display all rates

   if($show_all_rates){
       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Saver + Zone Skip";   
            $_rates[$key]['postage_type_code'] = "cbp_saver_zone_skip";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "5 days or more";
            $_rates[$key]['zone_skipped'] = true;


            // print_r("<pre>");
            // print_r($_rates);
            // exit;
       }
   }else{

       //hold display lowest single rate
       if($_rates){
            $_rates['postage_type'] = "CBP Saver + Zone Skip";   
            $_rates['postage_type_code'] = "cbp_saver_zone_skip";   
            $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
            $_rates['estimated_delivery'] = "5 days or more";
            $_rates[$key]['zone_skipped'] = true;
       }
   }


   
   return $_rates;


}

function CBP_Saver_CAUS($sign_insurance, $rate1){
    $show_all_rates = env("SHOW_ALL_RATES");

    // $rs = new Rocketship;
    // $rate1 = $rs->getUPSCARates($data);
    // $rate2 = $rs->getDHLRates($data);
    // dd($rate2);
    $rates = [];
    if(@$rate1['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate1['data']['rates'],"11","UPS"); //UPS Standard
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "11","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "11","");
            $rates[] = $d; 
        }
    }

    // if(@$rate2['data']['rates']){
    //     $d = filterRocketshipRatesByServiceCode($rate2['data']['rates'],"H","DHL"); //ECONOMY SELECT
    //     if($d) { $rates[] = $d; }

    // }


   $_rates = sortShipstationRates($rates);
   // dd($_rates);
    // $ss =  new ShipStation;
    // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_standard_international");
    // $DHL_rate = $ss->getShipStationsRatesUS($data,  "dhl_global_mail", "globalmail_packet_standard");

    // $rates = [];
    // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;
    // if(@$DHL_rate[0]['shipmentCost']) $rates[] = $DHL_rate;

    // $_rates = sortShipstationRates($rates);

   if($show_all_rates){
       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Saver";   
            $_rates[$key]['postage_type_code'] = "cbp_saver";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "5 days or more";
       }
   }else{

       //hold display lowest single rate
       if($_rates){
            $_rates['postage_type'] = "CBP Saver";   
            $_rates['postage_type_code'] = "cbp_saver";   
            $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
            $_rates['estimated_delivery'] = "5 days or more";
       }
   }

    return $_rates;

}

function CBP_Saver_USUS($data){
    $show_all_rates = env("SHOW_ALL_RATES");

    $rs = new Rocketship;

    $rate1 = $rs->getUPSUSRates($data);
    $data['_signature'] = "false";
    $data['_insurance'] = "false";
    $rate1_normal = $rs->getUPSUSRates($data);
    // dd($rate1_normal);

    $data['_signature'] = "true";
    $data['_insurance'] = "false";
    $rate1_signature = $rs->getUPSUSRates($data);
    // dd($rate1_signature);

    $data['_signature'] = "false";
    $data['_insurance'] = "true";
    $rate1_insurance = $rs->getUPSUSRates($data);


    
    // dd($rate1);

    unset($data['_signature']);
    unset($data['_insurance']);

    $rate2 = $rs->getSTAMPSRates($data);
    
    $data['_signature'] = "false";
    $data['_insurance'] = "false";
    $rate2_normal = $rs->getSTAMPSRates($data);


    $data['_signature'] = "true";
    $data['_insurance'] = "false";
    $rate2_signature = $rs->getSTAMPSRates($data);
    // dd($rate1_signature);

    $data['_signature'] = "false";
    $data['_insurance'] = "true";
    $rate2_insurance = $rs->getSTAMPSRates($data);


    
    if($data['unit_type'] == "imperial"){
        $weight  =  @$data['parcel_dimensions_model']['weight'];
        $length  =  @$data['parcel_dimensions_model']['length'];
        $width  =  @$data['parcel_dimensions_model']['width'];
        $height  =  @$data['parcel_dimensions_model']['height'];
    }else{
        $weight  =  unitConvesion(@$data['parcel_dimensions_model']['weight'], "G","LBS" );
        $length  =  unitConvesion(@$data['parcel_dimensions_model']['length'], "CM","IN" );
        $width  =  unitConvesion(@$data['parcel_dimensions_model']['width'], "CM","IN" );
        $height  =  unitConvesion(@$data['parcel_dimensions_model']['height'], "CM","IN" );
    }


    $rates = [];
    if(@$rate1['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate1['data']['rates'],"03","UPS"); //UPS Ground
        if($d) { 
            $d['insurance_fee'] = splitInsurance($rate1_normal, $rate1_insurance, "03","");
            $d['signature_fee'] = splitSignature($rate1_normal, $rate1_signature, "03","");
            $rates[] = $d; 
        }
    }

    if(@$rate2['data']['rates'] && $weight <=70){

        if($data['parcel_types']['parcel_type'] == "Box" && $data['parcel_types']['usps_box_status'] == "no"){

            $dimsWeight = $length + ($width * 2) + ($height * 2);

            // print_r($dimsWeight);
            // exit;
            //USPS FIRST CLASS
            $d = filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-FC", "Large Envelope or Flat","USPS"); //USPS First class
           
            if($d) { 
                $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance, "US-FC","Large Envelope or Flat");
                $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature, "US-FC","Large Envelope or Flat");
                $rates[] = $d; 
            }

            $d = filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-FC", "Package","USPS"); //USPS First class
            if($d) { 
                $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-FC", "Package");
                $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-FC", "Package");
                $rates[] = $d; 
            }

            $d = filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-FC", "Thick Envelope","USPS"); //USPS First class
            if($d) { 
                $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-FC", "Thick Envelope");
                $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-FC", "Thick Envelope");
                $rates[] = $d; 
            }



            //USPS PRIORITY

            if($dimsWeight <= 84){

                $d = filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-PM", "Large Envelope or Flat","USPS"); //USPS PRIORITY PACKAGE
                if($d) { 
                    $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-PM", "Large Envelope or Flat");
                    $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-PM", "Large Envelope or Flat");
                    $rates[] = $d; 
                }

                $d = filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-PM", "Package","USPS"); //USPS PRIORITY PACKAGE
                if($d) { 
                    $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-PM", "Package");
                    $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-PM", "Package");
                    $rates[] = $d; 
                }

                $d = filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-PM", "Thick Envelope","USPS"); //USPS PRIORITY PACKAGE
                if($d) { 
                    $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-PM", "Thick Envelope");
                    $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-PM", "Thick Envelope");
                    $rates[] = $d; 
                }

            }else if($dimsWeight > 84 && $dimsWeight <= 108){

                 $d = filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-PM", "Large Package","USPS"); //USPS PRIORITY PACKAGE
                if($d) { 
                    $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-PM", "Large Package");
                    $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-PM", "Large Package");
                    $rates[] = $d; 
                }
            }


            if($dimsWeight <= 108){
                //USPS EPRESS MAIL
                $d = filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-XM", "Large Envelope or Flat","USPS");
                if($d) { 
                    $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-XM", "Large Envelope or Flat");
                    $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-XM", "Large Envelope or Flat");
                    $rates[] = $d; 
                }

                $d = filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-XM", "Package","USPS"); 
                if($d) { 
                    $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-XM", "Package");
                    $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-XM", "Package");
                    $rates[] = $d; 
                }

                $d = filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-XM", "Thick Envelope","USPS");
                if($d) { 
                    $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-XM", "Thick Envelope");
                    $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-XM", "Thick Envelope");
                    $rates[] = $d; 
                }
            }


        }else if($data['parcel_types']['parcel_type'] == "Box" && $data['parcel_types']['usps_box_status'] == "yes"){
            $rates = []; // this will not include rates from USPS
            $options = explode("|", $data['usps_options_model']['usps_options']);
            $d = filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],$options[0], $options[1],"USPS"); //USPS PRIORITY PACKAGE
            if($d) { 
                $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,$options[0], $options[1]);
                $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,$options[0], $options[1]);
                $rates[] = $d; 
            }

        }else if($data['parcel_types']['parcel_type'] == "Letter" ){

            $options = explode("|", $data['usps_options_model']['usps_options']);
            $d = filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],$options[0], $options[1],"USPS"); //USPS PRIORITY PACKAGE
            if($d) { 
                $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,$options[0], $options[1]);
                $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,$options[0], $options[1]);
                $rates[] = $d; 
            }

        }else{ $d = []; }

    
        

    }


   $_rates = sortShipstationRates($rates);


   if($show_all_rates){
       foreach ($_rates as $key => $value) {
           # code...
            // echo "<pre>";
            // print_r($value);
            // exit;          
            // if($value["carrier"] == "USPS"){
            //     if(@$value['rate_detail']){
            //         $_rates[$key]['signature_fee'] = $value['rate_detail'][0]['amount'];   
            //     }
            // }

            $_rates[$key]['postage_type'] = "CBP Saver";   
            $_rates[$key]['postage_type_code'] = "cbp_saver";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "5 days or more";
       }
   }else{

        // if($_rates["carrier"] == "USPS"){
        //     if(@$value['rate_detail']){
        //         $_rates[$key]['signature_fee'] = $value['rate_detail'][0]['amount'];   
        //     }
        // }

       //hold display lowest single rate
       if($_rates){
            $_rates['postage_type'] = "CBP Saver";   
            $_rates['postage_type_code'] = "cbp_saver";   
            $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
            $_rates['estimated_delivery'] = "5 days or more";
       }
   }

    return $_rates;

    // $ss =  new ShipStation;
    // $USPS_rate = $ss->getShipStationsRatesUS($data,  "stamps_com", "usps_priority_mail");
    // $UPS_rate = $ss->getShipStationsRatesUS($data,  "ups", "ups_standard");

    // $rates = [];
    // if(@$USPS_rate[0]['shipmentCost']) $rates[] = $USPS_rate;
    // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;

    // $_rates = sortShipstationRates($rates);

    // if($_rates){
    //     $_rates['postageType'] = "CBP Saver";
    //     $_rates['postageCurrency'] = "USD";
    //     $_rates['total'] = $_rates['shipmentCost'];
    //     $_rates['estimated_delivery'] = "2 days or more";

    // }

    // return $_rates;

}

function CBP_Saver_CAInt($sign_insurance, $rate1){
    $show_all_rates = env("SHOW_ALL_RATES");
    // $rs = new Rocketship;
    // $rate1 = $rs->getDHLRates($data);
    // dd($rate1);
   
    $rates = [];
    if(@$rate1['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate1['data']['rates'],"P", "DHL"); //EXPRESS WORLDWIDE
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "P","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "P","");
            $rates[] = $d; 
        }
    }


   $_rates = sortShipstationRates($rates);



   if($show_all_rates){
       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Saver";   
            $_rates[$key]['postage_type_code'] = "cbp_saver";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "5 days or more";
       }
   }else{

       //hold display lowest single rate
       if($_rates){
            $_rates['postage_type'] = "CBP Saver";   
            $_rates['postage_type_code'] = "cbp_saver";   
            $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
            $_rates['estimated_delivery'] = "5 days or more";
       }
   }

    return $_rates;


    // $ss =  new ShipStation;
    // $DHL_rate = $ss->getShipStationsRatesUS($data,  "dhl_global_mail", "globalmail_packet_standard");

    // $rates = [];
    // if(@$DHL_rate[0]['shipmentCost']) $rates[] = $DHL_rate;

    // $_rates = sortShipstationRates($rates);

    // if($_rates){
    //     $_rates['postageType'] = "CBP Saver";
    //     $_rates['postageCurrency'] = "CAD";
    //     $_rates['total'] = $_rates['shipmentCost'];
    //     $_rates['estimated_delivery'] = "2 days or more";

    // }

    // return $_rates;

}

function CBP_Saver_USInt($data){
    $show_all_rates = env("SHOW_ALL_RATES");

    $rs = new Rocketship;
    $rate1 = $rs->getSTAMPSRates($data);
    $data['_signature'] = "false";
    $data['_insurance'] = "false";
    $rate1_normal = $rs->getSTAMPSRates($data);
    // dd($rate1_normal);

    $data['_signature'] = "true";
    $data['_insurance'] = "false";
    $rate1_signature = $rs->getSTAMPSRates($data);
    // dd($rate1_signature);

    $data['_signature'] = "false";
    $data['_insurance'] = "true";
    $rate1_insurance = $rs->getSTAMPSRates($data);
    // dd($rate1_insurance);
    // echo "<pre>";
    // print_r($rate1);
    // exit;
    $rates = [];
    if(@$rate1['data']['rates']){
        $d = filterUSPSByServiceCodeAndPackageType($rate1['data']['rates'],"US-PMI","Package","USPS"); //USPS Priority Mail International
        if($d) { 
            $d['insurance_fee'] = splitInsurance($rate1_normal, $rate1_insurance, "US-PMI","Package");
            $d['signature_fee'] = splitSignature($rate1_normal, $rate1_signature, "US-PMI","Package");
            $rates[] = $d; 
        }


        $d = filterUSPSByServiceCodeAndPackageType($rate1['data']['rates'],"US-EMI","Package","USPS"); //USPS Priority Mail International
        if($d) { 
            $d['insurance_fee'] = splitInsurance($rate1_normal, $rate1_insurance, "US-EMI","Package");
            $d['signature_fee'] = splitSignature($rate1_normal, $rate1_signature, "US-EMI","Package");
            $rates[] = $d; 
        }


        $d = filterUSPSByServiceCodeAndPackageType($rate1['data']['rates'],"US-FCI","Package","USPS"); //USPS First Class Mail International
        if($d) { 
            $d['insurance_fee'] = splitInsurance($rate1_normal, $rate1_insurance, "US-FCI","Package");
            $d['signature_fee'] = splitSignature($rate1_normal, $rate1_signature, "US-FCI","Package");
            $rates[] = $d; 
        }
    }


   $_rates = sortShipstationRates($rates);



   if($show_all_rates){
       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Saver";   
            $_rates[$key]['postage_type_code'] = "cbp_saver";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "5 days or more";
       }
   }else{

       //hold display lowest single rate
       if($_rates){
            $_rates['postage_type'] = "CBP Saver";   
            $_rates['postage_type_code'] = "cbp_saver";   
            $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
            $_rates['estimated_delivery'] = "5 days or more";
       }
   }

    return $_rates;

    // $ss =  new ShipStation;
    // $USPS_rate = $ss->getShipStationsRatesUS($data,  "stamps_com", "usps_priority_mail_international");

    // $rates = [];
    // if(@$USPS_rate[0]['shipmentCost']) $rates[] = $USPS_rate;

    // $_rates = sortShipstationRates($rates);

    // if($_rates){
    //     $_rates['postageType'] = "CBP Saver";
    //     $_rates['postageCurrency'] = "USD";
    //     $_rates['total'] = $_rates['shipmentCost'];
    //     $_rates['estimated_delivery'] = "2 days or more";
    // }

    // return $_rates;

}




function CBP_Expedited_CACA($sign_insurance, $rate1, $rate2){
    $show_all_rates = env("SHOW_ALL_RATES");
    // $rs = new Rocketship;
    // $rate1 = $rs->getCanadaPostRates($data);
    // $rate2 = $rs->getUPSCARates($data);

    $rates = [];
    if(@$rate1['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate1['data']['rates'],"DOM.XP","CANADA POST"); //Canada Post Xpresspost
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "DOM.XP","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "DOM.XP","");
            $rates[] = $d; 
        }
    }

    if(@$rate2['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate2['data']['rates'],"13",'UPS'); //UPS Express Saver / UPS Next Day Air Saver
        // dd($d);
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate2_normal'], $sign_insurance['rate2_insurance'], "13","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate2_normal'], $sign_insurance['rate2_signature'], "13","");
            $rates[] = $d; 
        }

    }


   $_rates = sortShipstationRates($rates);




   if($show_all_rates){
       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Expedited";   
            $_rates[$key]['postage_type_code'] = "cbp_expedited";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "4-5 days";
       }
   }else{

       //hold display lowest single rate
       if($_rates){
             $_rates['postage_type'] = "CBP Expedited";   
            $_rates['postage_type_code'] = "cbp_expedited";   
            $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
            $_rates['estimated_delivery'] = "4-5 days";
       }
   }
   
   return $_rates;

    // $ss =  new ShipStation;
    // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_next_day_air_saver");
    // $CANADA_rate = $ss->getShipStationsRatesCA($data,  "canada_post", "xpresspost");

    // $rates = [];
    // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;
    // if(@$CANADA_rate[0]['shipmentCost']) $rates[] = $CANADA_rate;

    // $_rates = sortShipstationRates($rates);

    // if($_rates){
    //     $_rates['postageType'] = "CBP Expedited";
    //     $_rates['postageCurrency'] = "CAD";
    //     $_rates['total'] = $_rates['shipmentCost'];
    //     $_rates['estimated_delivery'] = "1 to 2 days";
    // }

    // return $_rates;

}

function CBP_Expedited_CAUS($sign_insurance, $rate1){
    $show_all_rates = env("SHOW_ALL_RATES");

    // $rs = new Rocketship;
    // $rate1 = $rs->getUPSCARates($data);
    // dd($rate1);
    $rates = [];

    if(@$rate1['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate1['data']['rates'],"07",'UPS'); //UPS Express Saver / UPS Next Day Air Saver
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "07","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "07","");
            $rates[] = $d; 
        }

    }


   $_rates = sortShipstationRates($rates);


   if($show_all_rates){
       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Expedited";   
            $_rates[$key]['postage_type_code'] = "cbp_expedited";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "1-3 days";
       }
   }else{

       //hold display lowest single rate
       if($_rates){
             $_rates['postage_type'] = "CBP Expedited";   
            $_rates['postage_type_code'] = "cbp_expedited";   
            $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
            $_rates['estimated_delivery'] = "1-3 days";
       }
   }

   return $_rates;

    // $ss =  new ShipStation;
    // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_next_day_air_international");
    // $rates = [];
    // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;

    // $_rates = sortShipstationRates($rates);

    // if($_rates){
    //     $_rates['postageType'] = "CBP Expedited";
    //     $_rates['postageCurrency'] = "CAD";
    //     $_rates['total'] = $_rates['shipmentCost'];
    //     $_rates['estimated_delivery'] = "1 to 2 days";
    // }

    // return $_rates;

}

function CBP_Expedited_CAInt($sign_insurance, $rate1){
    $show_all_rates = env("SHOW_ALL_RATES");
    // $rs = new Rocketship;
    // $rate1 = $rs->getUPSCARates($data);

    $rates = [];
    if(@$rate1['data']['rates']){
         $d = filterRocketshipRatesByServiceCode($rate1['data']['rates'],"08",'UPS'); //UPS Worldwide Expedited
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate2_normal'], $sign_insurance['rate2_insurance'], "08","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate2_normal'], $sign_insurance['rate2_signature'], "08","");
            $rates[] = $d; 
        }

    }


   $_rates = sortShipstationRates($rates);


   if($show_all_rates){
       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Expedited";   
            $_rates[$key]['postage_type_code'] = "cbp_expedited";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "4-5 days";
       }
   }else{

       //hold display lowest single rate
       if($_rates){
             $_rates['postage_type'] = "CBP Expedited";   
            $_rates['postage_type_code'] = "cbp_expedited";   
            $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
            $_rates['estimated_delivery'] = "4-5 days";
       }
   }
   
   return $_rates;

    // $ss =  new ShipStation;
    // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_worldwide_expedited");

    // $rates = [];
    // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;

    // $_rates = sortShipstationRates($rates);

    // if($_rates){
    //     $_rates['postageType'] = "CBP Expedited";
    //     $_rates['postageCurrency'] = "CAD";
    //     $_rates['total'] = $_rates['shipmentCost'];
    //     $_rates['estimated_delivery'] = "1 to 2 days";
    // }

    // return $_rates;

}


function CBP_Express_CACA($sign_insurance, $rate1, $rate2){
    $show_all_rates = env("SHOW_ALL_RATES");
    // $rs = new Rocketship;
    // $rate1 = $rs->getCanadaPostRates($data);
    // $rate2 = $rs->getUPSCARates($data);

    // dd($rate2);

    $rates = [];
    if(@$rate1['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate1['data']['rates'],"DOM.PC","CANADA POST"); //Canada Post priority
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "DOM.PC","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "DOM.PC","");
            $rates[] = $d; 
        }
    }

    if(@$rate2['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate2['data']['rates'],"13","UPS"); //UPS Express Saver / UPS Next Day Air Saver
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate2_normal'], $sign_insurance['rate2_insurance'], "13","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate2_normal'], $sign_insurance['rate2_signature'], "13","");
            $rates[] = $d; 
        }

    }


   $_rates = sortShipstationRates($rates);


   if($show_all_rates){
       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Express";   
            $_rates[$key]['postage_type_code'] = "cbp_express";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "1-3 days";
       }
   }else{

       //hold display lowest single rate
       if($_rates){
             $_rates['postage_type'] = "CBP Express";   
            $_rates['postage_type_code'] = "cbp_express";   
            $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
            $_rates['estimated_delivery'] = "1-3 days";
       }
   }
   
   return $_rates;

    // $ss =  new ShipStation;
    // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_next_day_air_saver");
    // $CANADA_rate = $ss->getShipStationsRatesCA($data,  "canada_post", "priority");

    // $rates = [];
    // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;
    // if(@$CANADA_rate[0]['shipmentCost']) $rates[] = $CANADA_rate;

    // $_rates = sortShipstationRates($rates);

    // if($_rates){
    //     $_rates['postageType'] = "CBP Express";
    //     $_rates['postageCurrency'] = "CAD";
    //     $_rates['total'] = $_rates['shipmentCost'];
    //     $_rates['estimated_delivery'] = "less than a day";
    // }

    // return $_rates;

}

function CBP_Express_CAUS($sign_insurance, $rate1){
    $show_all_rates = env("SHOW_ALL_RATES");
    // $rs = new Rocketship;
    // $rate1 = $rs->getUPSCARates($data);

    // dd($rate1);

    $rates = [];


    if(@$rate1['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate1['data']['rates'],"65","UPS"); //UPS Worldwide Saver
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "65","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "65","");
            $rates[] = $d; 
        }

        $d = filterRocketshipRatesByServiceCode($rate1['data']['rates'],"07","UPS"); //UPS Worldwide Express
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "07","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "07","");
            $rates[] = $d; 
        }
        // dd($d);

    }


   $_rates = sortShipstationRates($rates);



   if($show_all_rates){
       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Express";   
            $_rates[$key]['postage_type_code'] = "cbp_express";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "less than a day";
       }
   }else{

       //hold display lowest single rate
       if($_rates){
             $_rates['postage_type'] = "CBP Express";   
            $_rates['postage_type_code'] = "cbp_express";   
            $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
            $_rates['estimated_delivery'] = "less than a day";
       }
   }
   
   return $_rates;

    // $ss =  new ShipStation;
    // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_next_day_air_international");

    // $rates = [];
    // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;

    // $_rates = sortShipstationRates($rates);

    // if($_rates){
    //     $_rates['postageType'] = "CBP Express";
    //     $_rates['postageCurrency'] = "CAD";
    //     $_rates['total'] = $_rates['shipmentCost'];
    //     $_rates['estimated_delivery'] = "less than a day";
    // }

    // return $_rates;

}

function CBP_Express_CAInt($sign_insurance, $rate1){
    $show_all_rates = env("SHOW_ALL_RATES");
    // $rs = new Rocketship;
    // $rate1 = $rs->getUPSCARates($data);

    // dd($rate1);

    $rates = [];


    if(@$rate1['data']['rates']){
        $d = filterRocketshipRatesByServiceCode($rate1['data']['rates'],"65","UPS"); //UPS Express Saver / UPS Next Day Air Saver
        if($d) { 
            $d['insurance_fee'] = splitInsurance($sign_insurance['rate2_normal'], $sign_insurance['rate2_insurance'], "65","");
            $d['signature_fee'] = splitSignature($sign_insurance['rate2_normal'], $sign_insurance['rate2_signature'], "65","");
            $rates[] = $d; 
        }

        // $d = filterRocketshipRatesByServiceCode($rate1['data']['rates'],"54","UPS"); // UPS Worldwide Express Plus
        // if($d) { $rates[] = $d; }


    }


   $_rates = sortShipstationRates($rates);



   if($show_all_rates){
       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Express";   
            $_rates[$key]['postage_type_code'] = "cbp_express";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "1-3 days";
       }
   }else{

       //hold display lowest single rate
       if($_rates){
             $_rates['postage_type'] = "CBP Express";   
            $_rates['postage_type_code'] = "cbp_express";   
            $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
            $_rates['estimated_delivery'] = "1-3 days";
       }
   }
   
   return $_rates;

    // $ss =  new ShipStation;
    // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_worldwide_saver");

    // $rates = [];
    // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;

    // $_rates = sortShipstationRates($rates);

    // if($_rates){
    //     $_rates['postageType'] = "CBP Express";
    //     $_rates['postageCurrency'] = "CAD";
    //     $_rates['total'] = $_rates['shipmentCost'];
    //     $_rates['estimated_delivery'] = "less than a day";
    // }

    // return $_rates;

}


function filterUSPSRates($rates, $selectedOption, $parcel_type){
    $show_all_rates = env("SHOW_ALL_RATES");
    $arr = [];
    foreach ($rates as $key => $value) {
        if($value['desc'] == $selectedOption){
            if($parcel_type == "Box" && (strpos($value['package_type'], 'Box') !== false ||  strpos($value['package_type'], 'Package') !== false)){
                $arr[] = $value;
            }else if($parcel_type == "Letter" && strpos($value['package_type'], 'Envelope') !== false){
                $arr[] = $value;
            }else{
                // $arr[] = $value;
            }
        }

    }

    return $arr;
}


function uspsOptions(Request $r){
    $show_all_rates = env("SHOW_ALL_RATES");
    $d = $r->toArray();


    $rates = uspsServices();

    $arr = [];
    foreach ($rates as $key => $value) {
        if(!in_array($value['desc'], $arr)){

            if($r['unit_type'] =="imperial"){
                $box_weight = @$d['usps_options_model']['weight'];
                $letter_weight = @$d['parcel_dimensions_model']['weight'];
            }else{
                $box_weight = unitConvesion(@$d['usps_options_model']['weight'], "G", "LBS");
                $letter_weight = unitConvesion(@$d['parcel_dimensions_model']['weight'], "G", "LBS");
                
            }

            if($d['parcel_types']['parcel_type'] == "Box"){

                if (strpos($value['package_type'], 'Box') ||  strpos($value['package_type'], 'Envelope')) {

                    if( !in_array($value['desc'], ["USPS Media Mail","USPS First Class Mail","USPS Parcel Select","USPS Library Mail"])){
                        // array_push($arr, $value['desc']);

                            if(strpos($value['package_type'], 'Large Envelope') === false && strpos($value['package_type'], 'Thick Envelope') === false){
                                $arr[$value['desc']][] = $value;
                            }

                    }

                }


            }else{

                if (strpos($value['package_type'], 'Envelope') !== false && strpos($value['package_type'], 'Thick Envelope') === false && strpos($value['package_type'], 'Padded Envelope') === false ) {
                    if($value['desc'] != "USPS Media Mail" && $value['desc'] != "USPS Library Mail" ){
                        // array_push($arr, $value['desc']);

                        if($letter_weight <= 0.218125){

                            // if($value['desc'] == "USPS First Class Mail"){
                                $arr[$value['desc']][] = $value;
                            // }
                        }else{

                            if($value['desc'] != "USPS First Class Mail"){
                                $arr[$value['desc']][] = $value;
                            }
                        }
                    }
                }
            }
        }
    }




    // dd($arr);

    return ['status'=>true,  "options"=>$arr ];
    
}

function uspsServices(){
    return UspsService::get()->toArray();
}

function getConversionRate($rate, $currency){
    $data = ConversionRate::where("currency",$currency)->get()->first();

    if($data){
        return $rate * $data->rate;
    }else{
        return $rate;
    }
}

function handleMarkup($carrier){
    $data = Carrier::where("carrier_id",$carrier)->get()->first();
    if($data){
        return $data->mark_up;
    }else{
        return 0;
    }
}




function postageRateTriggers($data, $rates){

    //TEMPORARY VALUE. TO BE CONFIGURED IN STAFF PORTAL
    $postageMarkupPercentage = 0;//1;
    $truckFee =  0;//0.1;  //$/lb
    $tax = 0;

    // --END--
    // dd($deliveryFee);


    $shipFromCountry = $data['ship_from_address_model']['country'];
    $recipientCountry = $data['recipient_model']['country'];
    $recipientPostal = $data['recipient_model']['postal'];
    $recipientProvince = $data['recipient_model']['province'];

    $weight = @$data['parcel_dimensions_model']['weight'];
    $length = @$data['parcel_dimensions_model']['length'];
    $width = @$data['parcel_dimensions_model']['width'];
    $height = @$data['parcel_dimensions_model']['height'];
    $unit_type = @$data['unit_type'];

    //POSTAL CODES APPLICABLE FOR ZONE SKIP RATE PER LBS;
    $zoneSkipRateCode=['T','R','V'];
    $zoneSkipRateCodeName=['ab','alberta','mb','manitoba','bc','british columbia'];


    //PROVINCIAL TAX PERCENTAGE
    $taxPercentage = getTaxPercentage($recipientPostal, $recipientProvince);

    //CA-CA
    if($shipFromCountry == "CA" && $recipientCountry == "CA"){
        // echo "<pre>";
        // print_r($rates);
        // exit;
        foreach ($rates as $key=>$value) {
            $postageMarkupPercentage = handleMarkup($value['carrier']);

            $rate = getConversionRate($value['total'],$value['currency']);

            if($postageMarkupPercentage){
                $markup = $rate * ($postageMarkupPercentage / 100);
            }else{
                $markup = 0;
            }
           
            if($value['carrier'] == "CANADA POST"){ 

                


                //ADD TRUCK FEES FOR THE ZONE SKIP POSTAL
                if(@$value['zone_skipped']){
                    // dd($value);
                    $truck_fee = calculateTruckFee($length, $width, $height, $weight, $unit_type, $recipientPostal[0]);

                    $truck_fee_tax = $truck_fee * $taxPercentage;

                    $rate_tax = $rate * $taxPercentage;
                    $markup_tax = $markup * $taxPercentage;
                    $rate_before_tax = $rate - $rate_tax;
                    $total_tax = $rate_tax + $markup_tax + $truck_fee_tax;



                    $cbp_delivery_fee = 0;
                    // $total = $rate + $markup + $truck_fee;
                    $total = $rate_before_tax + $markup + $total_tax + $truck_fee;

                    $rates = rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $total_tax, $total);
                }else{


                    $cbp_delivery_fee = 0;

                    $rate_tax = $rate * $taxPercentage;
                    $markup_tax = $markup * $taxPercentage;
                    $rate_before_tax = $rate - $rate_tax;
                    $total_tax =$rate_tax + $markup_tax;

                    $truck_fee = 0;
                    $total = $rate_before_tax + $markup + $total_tax;
                    $rates = rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $total_tax, $total);


                }

            }else if($value['carrier'] == "UPS"){

                // $rate_tax = $value['taxes'][0]['amount'];
                $rate_tax = $rate * $taxPercentage;
                $markup_tax = $markup * $taxPercentage;
                $rate_before_tax = $rate - $rate_tax;
                $total_tax =$rate_tax + $markup_tax;

                $cbp_delivery_fee = 0;
                $truck_fee = 0;
                $total = $rate_before_tax + $markup + $total_tax;
                $rates = rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $total_tax, $total);

            }else if($value['carrier'] == "FEDEX"){

                // $rate_tax = $value['taxes'][0]['amount'];
                $rate_tax = $rate * $taxPercentage;
                $markup_tax = $markup * $taxPercentage;
                $rate_before_tax = $rate - $rate_tax;
                $total_tax =$rate_tax + $markup_tax;

                $cbp_delivery_fee = 0;
                $truck_fee = 0;
                $total = $rate_before_tax + $markup + $total_tax;
                $rates = rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $total_tax, $total);

            }
        }


    }
    //CA - INTERNATIONAL
    else if ($shipFromCountry == "CA" && $recipientCountry != "CA"){

        // echo "<pre>";
        // print_r($rates);
        // exit;
        foreach ($rates as $key=>$value) {
            $postageMarkupPercentage = handleMarkup(@$value['carrier']);

            $rate = getConversionRate($value['total'],$value['currency']);
            
            if($postageMarkupPercentage){
                $markup = $rate * ($postageMarkupPercentage / 100);
            }else{
                $markup = 0;
            }

            $cbp_delivery_fee = 0;
            $tax = 0;
            if($recipientCountry == "US"){

                if(@$data['parcel_types']['parcel_type'] == "Letter"){
                    $weight = @$data['usps_options_model']['weight'];
                    if($data['unit_type'] == "metric"){
                        $weight = unitConvesion($weight, "G", "LBS");
                    }

                    $cbp_delivery_fee = handleSingleShipmentDeliveryFee($weight,"Letter");

                }else{
                    if(@$data['parcel_types']['usps_box_status'] == "yes"){
                        $weight = @$data['usps_options_model']['weight'];
                    }else{
                        $weight = @$data['parcel_dimensions_model']['weight'];

                    }

                    if($data['unit_type'] == "metric"){
                        $weight = unitConvesion($value, "G", "LBS");
                    }

                    $df = handleSingleShipmentDeliveryFee($weight,"Box");
                    $cbp_delivery_fee = $df;
                }

                $tax = 0.13 * $cbp_delivery_fee;

            }

            $truck_fee = 0;
            $total = $rate + $markup + $cbp_delivery_fee + $tax;
            $rates = rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $tax, $total);
        }
    }
    
    // US-US
    else if($shipFromCountry == "US" && $recipientCountry == "US"){

        // dd($rates);
        foreach ($rates as $key=>$value) {

            // $postageMarkupPercentage = handleMarkup($value['carrier']);
            $rate = getConversionRate($value['total'],$value['currency']);
            $markup = 0;

            $deliveryFee = 0;
            if(@$data['parcel_types']['parcel_type'] == "Letter"){
                $weight = @$data['usps_options_model']['weight'];
                if($data['unit_type'] == "metric"){
                    $weight = unitConvesion($weight, "G", "LBS");
                }
                $deliveryFee = handleSingleShipmentDeliveryFee($weight,"Letter");

            }else{
                if(@$data['parcel_types']['usps_box_status'] == "yes"){
                    $weight = @$data['usps_options_model']['weight'];
                }else{
                    $weight = @$data['parcel_dimensions_model']['weight'];

                }

                if($data['unit_type'] == "metric"){
                    $weight = unitConvesion($weight, "G", "LBS");
                }


                $df = handleSingleShipmentDeliveryFee($weight,"Box");
                $deliveryFee = $df;
            }

            $tax = 0.13 * $deliveryFee;
            $cbp_delivery_fee = $deliveryFee;
            $truck_fee = 0;
            $total = ($rate) + $markup + $cbp_delivery_fee + $tax;
            $rates = rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $tax, $total);
        }
    }

    // US-INTERNATIONAL
    else if($shipFromCountry == "US" && $recipientCountry != "US"){

        foreach ($rates as $key=>$value) {
            // $postageMarkupPercentage = handleMarkup($value['carrier']);
            $rate = getConversionRate($value['total'],$value['currency']);


            $weight = @$data['parcel_dimensions_model']['weight'];

            if($data['unit_type'] == "metric"){
                $weight = unitConvesion($weight, "G", "LBS");
            }
            
            // if($postageMarkupPercentage){
            //     $markup = $rate * ($postageMarkupPercentage / 100);
            // }else{
            $markup = 0;
            // }

            $df = handleSingleShipmentDeliveryFee($weight,"Box");
            $deliveryFee = $df;

            $tax = 0.13 * $deliveryFee;
            $cbp_delivery_fee = $deliveryFee;

            $truck_fee = 0;
            $total = $rate + $deliveryFee + $tax;
            $rates = rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $tax, $total);
        }
    }
    // dd($rates)
    return $rates;

}



function calculateTruckFee($length, $width, $height, $weight, $unit_type, $postal_code){

    $truck_fee = TruckFee::where("postal_code",$postal_code)->get()->first();

    if($truck_fee){
      $truckFee = $truck_fee->amount;
    }else{
      $truckFee = 0;
    }
    

    if(strtolower($unit_type) == "imperial"){
        $dimsWeight = ($length * $width * $height) / 139;

        
    }else{
        $dimsWeight = ($length * $width * $height) / 5000;
    }
    
    if($weight > $dimsWeight){

        if(strtolower($unit_type) == "metric"){
            $weight = unitConvesion($weight,"G","LBS");
        }
        $truck_fee = $truckFee * $weight;
    }else{
        $truck_fee = $truckFee * $dimsWeight;
    }


    return $truck_fee;
}

function getTaxPercentage($postal_code, $province){
    $r = Taxes::where("postal_code","LIKE","%".$postal_code[0]."%")
                        ->orWhere("province","=",$province)
                        ->orWhere("description","LIKE","%".$province."%")
                        ->get()->first();
    if($r){
        return $r->tax_percent / 100;
    }else{
        return 0;
    }
}


function handleSingleShipmentDeliveryFee($weight, $type){
        if($type == "Letter"){
            $df = DB::table('delivery_fees')
                ->where('max_weight', '<', 30)
                ->where("is_active",1)
                ->get()->first();

            $price = $df->letter_mail_price;
        }else{
            $df = DB::table('delivery_fees')
                ->where('min_weight', '<=', $weight)
                ->where('max_weight', '>=', $weight)
                ->where("is_active",1)
                ->get()->first();
            $price = $df->price;
            
        }
        return $price;
}

function rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $tax, $total ){
    $rates[$key]['truck_fee'] = number_format($truck_fee,2);
    $rates[$key]['tax'] = number_format($tax,2);
    $rates[$key]['markup'] = number_format($markup,2);
    $rates[$key]['cbp_delivery_fee'] = number_format($cbp_delivery_fee,2);
    $rates[$key]['total'] = number_format($total,2);

    return $rates;
}


function ratesCompare($rate1 = [], $rate2 =[] , $rate3 = []){
    // dd($rate1);
    // $a = $rate1['rates'];

    $a = $rate1;
    $b = $rate2;
    $c = $rate3;
    // dd($b);

    // //RATE1
    // foreach ($a as $aKey => $aValue) {
    //     // dd($a);
    //    foreach ($a as $bKey => $bValue) {
    //        if(@$aValue['est_delivery_time'] == @$bValue['est_delivery_time']){
    //             if(@$aValue['rate'] < @$bValue['rate']){
    //                 unset($a[$bKey]);
    //             }
    //        }
    //    }
    // }


    // // RATE2
    // foreach ($b as $aKey => $aValue) {
    //    foreach ($b as $bKey => $bValue) {
    //        if(@$aValue['est_delivery_time'] == @$bValue['est_delivery_time']){
    //             if(@$aValue['rate'] < @$bValue['rate']){
    //                 unset($b[$bKey]);
    //             }
    //        }
    //    }
    // }

    // // dd($b);


    // foreach ($a as $aKey => $aValue) {
    //    foreach ($b as $bKey => $bValue) {
    //        if(@$aValue['est_delivery_time'] == @$bValue['est_delivery_time']){
    //             if(@$aValue['rate'] < @$bValue['rate']){
    //                 unset($b[$bKey]);
    //             }
    //        }
    //    }
    // }

    $rates =  array_merge($a, $b, $c);
    // dd($rates);

    return sortRates( $rates);

    
}


function handleLabelCreation($data, $shipmentId, $imageData=null){
    // dd($imageData);
    
    $shipping_label = new ShippingLabel; 

    if($data['shipment_type'] == "PD"){


        $decoded = base64_decode($imageData);

        $f = finfo_open();

        $mime_type = finfo_buffer($f, $decoded, FILEINFO_MIME_TYPE);

        // if($mime_type == "image/png"){
        //     $file = $shipCode.'.png';
        // }else{
        //     $file = $shipCode.'.pdf';
        // }

        $fileName = $shipmentId.".pdf";
        Storage::disk('local')->put('public/labels/'.$fileName, $decoded);

        // file_put_contents("label.pdf", $decoded);
    }else{
        //internal label
        $shipping_label->generate($shipmentId);
    }

}


function renameCBP($rates){
    foreach ($rates as $key => $value) {
        $rates[$key]['desc'] = "CBP ".$value['desc'];
    }

    return $rates;
}


function sortRates($rates){
    $price = array();
    foreach ($rates as $key => $row)
    {
        $price[$key] = @$row['rate'];
    // dd($price);
    }
    array_multisort($price, SORT_ASC, $rates);
    return $rates;

}


function insertCarrier($rates, $carrier){
    // $rates = $data['data']['rates'];
    foreach ($rates as $key => $value) {
        $rates[$key]['carrier']= $carrier;
    }

    // $data['data']['rates'] = $rates;



    return $rates;
}


function getCouponCode(Request $r){
    $date_today = Date("Y-m-d");
    // dd($date_today);
    $res = Coupon::where("coupon", $r->code)
                ->where("startDate","<=",$date_today)
                ->where("endDate",">=",$date_today)
                ->get()->first();

    if($res){
        return ['status'=>true,'coupon'=>$res];
    }else{
        return ['status'=>false];

    }

}

function check_response($r){
    if($r['meta']['code'] == 200){
            if($r['data']['errors']){
               $res = [ 'status'=>false, 'errors' => $r['data']['errors'] ];
                
            }else{

                $res = [ 'status'=>true, 'rates' => $r['data']['rates'] ];
            }
    }else{
        $res = [ 'status'=>false, 'errors' => $r['meta']['error_message'] ];
    }

    return $res;
}


function filter_rate_detail($r, $type){
    $arr = [];
    foreach ($r as $key => $value) {
        if($value['type'] == $type){
            $arr = $value;
        }
    }

    return $arr;
}


?>