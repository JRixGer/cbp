<?php

namespace cbp\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use DB;
use File;
use Image;
use Storage;
use Auth;
use \cbp\Attachment;
use \cbp\Recipient;
use \cbp\RecipientAddress;
use \cbp\Shipment;
use \cbp\ShipmentItem;
use \cbp\ShipmentStatus;
use \cbp\CsvImportTemp;

use \cbp\Http\Requests\RecipientRegistrationRequest;
use \cbp\Http\Requests\ParcelDimensionsRequest;

use \cbp\Services\Rocketship; 
use \cbp\Services\ShipStation; 
use \cbp\Services\ShippingLabel; 
use \cbp\Services\DHLGLobal; 


class ReportController extends Controller
{
          
    public function allShipments(Request $r){

        $sender_id = Auth::User()->sender_id;

        $main_qry = DB::table('shipments')
            ->select('shipments.id as shipment_id',
                'shipments.internal_ship_id as internal_ship_id',
                'shipments.internal_order_id as internal_order_id',
                'shipments.sender_id as sender_id',
                'shipments.shipment_code as shipment_code',
                'shipments.shipment_type as shipment_type',
                'shipments.order_id as order_id',
                'shipments.tracking_no as Tracking',
                'shipments.recipient_id as recipient_id',
                'shipments.grouped_order_id as grouped_order_id',

                'shipments.length as Length',
                'shipments.width as Width',
                'shipments.height as Height',
                'shipments.weight as Weight',

                'shipments.size_unit as SizeUnit',
                'shipments.weight_unit as WeightUnit',

                'shipments.require_signature as isSignatureReq',
                'shipments.letter_mail as LetterMail',
                'shipments.import_batch as import_batch',
                'shipments.created_at as Created',
                
                'shipments.carrier_id as CarrierId',
                'shipments.carrier as Carrier',
                'shipments.carrier_desc as CarrierDesc',
                'shipments.duration as Duration',
                'shipments.currency as Currency',
                'shipments.insurance_cover as InsuranceCover',
                'shipments.insurance_cover_amount as InsuranceCoverAmount',                
                'shipments.note as Note',
                'shipments.import_status as import_status',
            
                'shipments.coupon_code as CouponCode',
                'shipments.coupon_type as CouponType',
                'shipments.coupon_amount as CouponAmount',
         
                'shipments.mark_up as MarkUp',
                'shipments.postage_fee as PostageFee',
                'shipments.delivery_fee as DeliveryFee',
                DB::raw("IF(shipments.shipment_type='DO',shipments.delivery_fee,shipments.total_fee) as TotalFees"),
                
                DB::raw("IF((shipment_status.name='Unpaid' OR shipment_status.name='Incomplete'),0,shipments.amount_paid) as AmountPaid"),

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
         
                DB::raw("CONCAT(recipients.first_name, ' ', recipients.last_name) AS RecipientName"),
             
                'recipient_addresses.address_1 as AddressLine1',
                'recipient_addresses.address_2 as AddressLine2',
                'recipient_addresses.city as City',
                'recipient_addresses.province as ProvState',
                'recipient_addresses.postal as PostalZipCode',
                'recipient_addresses.intl_prov_state as IntlProvState',
                'recipient_addresses.country as Country',
                'recipient_addresses.id as recipient_addresses_id',
                                                                                                                 
                'shipments.shipment_status_id as ship_status_id',
                'shipment_status.name as name',
                'shipment_status.description as status_description',
                DB::raw("(SUM(shipment_items.qty)) as Items"),
                DB::raw("(SUM(shipment_items.value * shipment_items.qty)  ) as ItemsValue")
               )
            ->leftjoin('cbp_addresses', 'cbp_addresses.id', '=', 'shipments.cbp_address_id')
            ->leftjoin('recipient_addresses', 'recipient_addresses.id', '=', 'shipments.recipient_address_id')
            ->leftjoin('recipients', 'recipients.id', '=', 'recipient_addresses.recipient_id')
            ->leftjoin('shipment_items', 'shipment_items.shipment_id', '=', 'shipments.id')
            ->leftjoin('shipment_status', 'shipment_status.id', '=', 'shipments.shipment_status_id')
            ->where('shipments.sender_id', '=', $sender_id)
            ->where('shipment_status.name', '<>', 'Void')
            ->where('recipient_addresses.is_active', '<>', 0)
            ->groupby('shipments.id');

        if ( $r->input('client') ) {
            return $main_qry->orderby('shipments.recipient_id','shipments.shipment_code')
                            ->distinct()
                            ->get();
        }
            
        $columns = [
                'shipments.id',
                'shipments.internal_ship_id',
                'shipments.internal_order_id',
                'shipments.created_at',
                'recipients.first_name',
                'recipient_addresses.country',
                'shipments.shipment_type',
                'shipments.carrier',
                'shipment_status.name',
                'shipment_items.value',
                'shipments.tracking_no'
            ];
          

        $length = $r->input('length');
        $column = $r->input('column'); //Index
        $dir = $r->input('dir');
        $searchValue = $r->input('search');
        
        // $searchStartDt = empty($r->input('dtStart'))? false:$r->input('dtStart').' 00:00:01';
        // $searchEndDt = empty($r->input('dtEnd'))? false:$r->input('dtEnd').' 23:59:59';
        
        $searchStartDt = empty($r->input('dtStart'))? false:$r->input('dtStart');
        $searchEndDt = empty($r->input('dtEnd'))? false:$r->input('dtEnd');

        $query = $main_qry->orderBy($columns[$column], $dir);



        if ($searchValue && $searchStartDt && $searchEndDt) {
            $query->where(function($query) use ($searchValue, $searchStartDt, $searchEndDt) {

               $query->where(DB::raw("(STR_TO_DATE(shipments.created_at,'%Y-%m-%d'))"), '>=', $searchStartDt)
                        ->where(DB::raw("(STR_TO_DATE(shipments.created_at,'%Y-%m-%d'))"), '<=', $searchEndDt)
                        ->where(function($query) use ($searchValue) {
                            $query->where('recipients.first_name', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipients.last_name', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.shipment_code', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.id', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.carrier', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.internal_ship_id', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.internal_order_id', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.shipment_type', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipment_status.name', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.tracking_no', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.country', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.carrier_id', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.grouped_order_id', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.length', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.width', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.height', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.weight', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.size_unit', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.weight_unit', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.require_signature', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.letter_mail', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.import_batch', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.duration', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.currency', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.insurance_cover_amount', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.note', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.import_status', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.coupon_code', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.coupon_amount', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.mark_up', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.postage_fee', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.delivery_fee', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipients.contact_no', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipients.email', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipients.company', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.address_1', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.address_2', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.city', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.province', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.postal', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.intl_prov_state', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.country', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipment_status.description', 'like', '%' . $searchValue . '%');
                            });
                });
        }
        else if ($searchValue && ($searchStartDt==false || $searchEndDt==false)) {
            $query->where(function($query) use ($searchValue) {
                $query->where('recipients.first_name', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipients.last_name', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.shipment_code', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.id', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.carrier', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.internal_ship_id', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.internal_order_id', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.shipment_type', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipment_status.name', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.tracking_no', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.country', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.carrier_id', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.grouped_order_id', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.length', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.width', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.height', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.weight', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.size_unit', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.weight_unit', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.require_signature', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.letter_mail', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.import_batch', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.duration', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.currency', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.insurance_cover_amount', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.note', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.import_status', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.coupon_code', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.coupon_amount', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.mark_up', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.postage_fee', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipments.delivery_fee', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipients.contact_no', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipients.email', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipients.company', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.address_1', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.address_2', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.city', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.province', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.postal', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.intl_prov_state', 'like', '%' . $searchValue . '%')
                                ->orWhere('recipient_addresses.country', 'like', '%' . $searchValue . '%')
                                ->orWhere('shipment_status.description', 'like', '%' . $searchValue . '%');

            });
        }
        else if (!$searchValue && ($searchStartDt==true && $searchEndDt==true)) {
            $query->where(function($query) use ($searchStartDt, $searchEndDt) {
                $query->where(DB::raw("(STR_TO_DATE(shipments.created_at,'%Y-%m-%d'))"), '>=', $searchStartDt)
                      ->where(DB::raw("(STR_TO_DATE(shipments.created_at,'%Y-%m-%d'))"), '<=', $searchEndDt);
            });
        }

          

        // if ($searchValue && $searchStartDt && $searchEndDt) {
        //     $query->where(function($query) use ($searchValue, $searchStartDt, $searchEndDt) {
                
        //         $query->where('recipients.first_name', 'like', '%' . $searchValue . '%')
        //               ->orWhere('recipients.last_name', 'like', '%' . $searchValue . '%')
        //               ->orWhere('shipment.shipment_code', 'like', '%' . $searchValue . '%')
        //               ->orWhere('shipment.id', 'like', '%' . $searchValue . '%')
        //               ->orWhere('shipment.carrier', 'like', '%' . $searchValue . '%')
        //               ->whereBetween('shipments.created_at', [$searchStartDt, $searchEndDt]);
            
        //     });
        // }
        // else if ($searchValue && ($searchStartDt==false || $searchEndDt==false)) {
        //     $query->where(function($query) use ($searchValue) {
                
        //         $query->where('recipients.first_name', 'like', '%' . $searchValue . '%')
        //               ->orWhere('recipients.last_name', 'like', '%' . $searchValue . '%')
        //               ->orWhere('shipment.shipment_code', 'like', '%' . $searchValue . '%')
        //               ->orWhere('shipment.carrier', 'like', '%' . $searchValue . '%')
        //               ->orWhere('shipment.id', 'like', '%' . $searchValue . '%');
        //     });
        // }
        // else if (!$searchValue && ($searchStartDt==true && $searchEndDt==true)) {
        //     $query->where(function($query) use ($searchStartDt, $searchEndDt) {
        //         $query->whereBetween('shipments.created_at', [$searchStartDt, $searchEndDt]);
        //     });
        // }


        $shipment = $query->distinct()->paginate($length);
        //dd($shipment);
        return ['data' => $shipment, 'draw' => $r->input('draw')];

    }


    public function groupedOrders(Request $r){

        $sender_id = Auth::User()->sender_id;

        $main_qry = DB::table('grouped_orders')
            ->select('grouped_orders.id',
                'shipments.shipment_date',
                'shipments.grouped_order_id',                
                'grouped_orders.created_at',
                DB::raw("(GROUP_CONCAT(CONCAT(shipments.internal_ship_id,'-', shipments.internal_order_id) SEPARATOR ', ')) as groupedIds")
               )
            ->leftjoin('shipments', 'grouped_orders.id', '=', 'shipments.grouped_order_id')
            ->where('shipments.grouped_order_id', '<>', NULL)
            ->groupby('grouped_orders.id');

        if ( $r->input('client') ) {
            return $main_qry->orderby('grouped_orders.id')
                            ->distinct()
                            ->get();
        }
            
        $columns = [
                'grouped_orders.id',
                'grouped_orders.created_at',
            ];
          

        $length = $r->input('length');
        $column = $r->input('column'); //Index
        $dir = $r->input('dir');
        $searchValue = $r->input('search');
        
        $query = $main_qry->orderBy($columns[$column], $dir);
        
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('shipments.internal_ship_id', 'like', '%' . $searchValue . '%')
                      ->orWhere('shipments.internal_order_id', 'like', '%' . $searchValue . '%');
            });
        }
        
        $groupedOrders = $query->distinct()->paginate($length);
        //dd($groupedOrders);
        return ['data' => $groupedOrders, 'draw' => $r->input('draw')];

    }

    public function groupedOrder(Request $r){

        $d = $r->toArray();
        $m  = DB::table('shipments')
                ->select('shipments.id as shipment_id',
                'shipments.internal_ship_id as internal_ship_id',
                'shipments.internal_order_id as internal_order_id',
                'shipments.grouped_order_id as grouped_order_id',
                'shipments.created_at as Created',
                'shipments.shipment_date as ShipmentDate',
                'recipients.first_name as FirstName',
                'recipients.last_name as LastName',
                'recipients.contact_no as Phone',
                'recipients.email as Email',
                'recipients.company as BusinessName',
                DB::raw("CONCAT(recipients.first_name, ' ', recipients.last_name) AS RecipientName"),
                'recipient_addresses.address_1 as AddressLine1',
                'recipient_addresses.address_2 as AddressLine2',
                'recipient_addresses.city as City',
                'recipient_addresses.province as ProvState',
                'recipient_addresses.postal as PostalZipCode',
                'recipient_addresses.intl_prov_state as IntlProvState',
                'recipient_addresses.country as Country',
                'recipient_addresses.id as recipient_addresses_id')
            ->leftjoin('recipient_addresses', 'recipient_addresses.id', '=', 'shipments.recipient_address_id')
            ->leftjoin('recipients', 'recipients.id', '=', 'recipient_addresses.recipient_id')
            ->where('shipments.grouped_order_id','=',$d['id'])
            ->get();

        return Response()->json($m);
    }

    public function removeOrder(Request $r){

        $d = $r->toArray();

        $order_qry = DB::table('shipments')->where('id', '=', $d[0])->first();
        if ($order_qry) 
        {
            $nextOrderId = str_replace('OB', '', $order_qry->internal_order_id);
            $nextOrderId = 'OB'.str_replace('G', '', $nextOrderId);
        }

        $status = DB::table('shipments')
            ->where('id','=', $d[0])
            ->update(['internal_order_id' => $nextOrderId, 'grouped_order_id' => NULL]);
       
    }  
    
    public function reverseOrder(Request $r){

        $d = $r->toArray();
        $order_qry = DB::table('shipments')->where('grouped_order_id', '=', $d[0])->get();
        for($i=0; $i < sizeof($order_qry); $i++)
        {

            $nextOrderId = str_replace('OB', '', $order_qry[$i]->internal_order_id);
            $nextOrderId = 'OB'.str_replace('G', '', $nextOrderId);
    
            $status = DB::table('shipments')
                ->where('id','=', $order_qry[$i]->id)
                ->update(['internal_order_id' => $nextOrderId, 'grouped_order_id' => NULL]);


        }
    }  

    public function processShipment(Request $r){


        $d = $r->toArray();
        $status = true;
        $sender_id = Auth::User()->sender_id;
        if(!empty($d['isMultiple']))
        {
            if($d['isMultiple'] == 'isMultiple')
                for($i=0; $i<sizeof($d['shipmentInfo']); $i++)    
                    $shipIds[$d['shipmentInfo'][$i][0]] = $d['shipmentInfo'][$i][0];
            else
                $shipIds[$d['shipmentInfo']] = $d['shipmentInfo'];              
        }    
        else
            $shipIds = NULL;
         
        //DB::beginTransaction();
        if($d['updateType'] == 'void')
        {

            $statusCode = getStatusCode('Void');
            foreach ($shipIds as $shipId) {
                $status = updateShipment($shipId, $statusCode); 
                if(!$status)    
                    break;

                // $shipment = Shipment::find($shipId);
                // if($shipment)
                //     $shipment->delete();  
            }

        }
        else if($d['updateType'] == 'archive' && !empty($shipIds))
        {

            foreach ($shipIds as $shipId) 
                $ids[] = $shipId; 
            
            $res = processArchive($ids);
            if($res)
                return Response()->json(['status' => true, 'message' => 'Archived successfully']);                 
            else
                return Response()->json(['status' => false, 'message' => 'Archived error!']);                 

        }
        else if($d['updateType'] == 'payment' && !empty($shipIds))
        {
            $sender_id = Auth::User()->sender_id;
            $statusCode = getStatusCode('Unpaid');
            $ids = array();
            foreach ($shipIds as $shipId) {
                $status = updateShipment($shipId, $statusCode); 
                $ids[] = $shipId; 
                // if(!$status)    
                //     break;
            }
            
            $shipInfo = getShipInfo($ids, $sender_id, 'ids');
            $ss = new ShipStation;
            $finalShipIds = array();
			$total_fee = 0;//kulddep
            for($i=0; $i < sizeof($shipInfo); $i++)
            {
                $s = $shipInfo[$i];
                $j = $shipInfo[$i]['idsArr'];
				
                $sid = $s['cbp_ship_id'];
                $sld = null;
                $finalShipIds[] = $s['cbp_ship_id'];
                $total_fee = $total_fee + $s['total_fee'];                    
                if($s['shipment_type'] == "PD")
                { 

                    $ss_shipment = $ss->createShipment($s, $j);

                    if(!empty($d['shipmentDate']))
                        $shipment_date = date('Y/m/d', strtotime($d['shipmentDate'])); 
                    else
                        $shipment_date = NULL; 

                    if(in_array($s['postage_option_model']['details']['carrier'], ["UPS","USPS"])){
                        if(@$ss_shipment['shipmentId']){

                            $sid = $ss_shipment['shipmentId'];
                            $sld = $ss_shipment['labelData'];
                            handleLabelCreation($s, $sid, $sld);
                            udpateShipmentDetails($s, $s['cbp_ship_id'], $ss_shipment, $shipment_date); // to do in helper
                            handleInternalLabelCreation($s['cbp_ship_id']);
                        }
                    
                    }
                    else if($s['postage_option_model']['details']['carrier'] == "DHL")
                    {

                        $DHL = new DHLGLobal;
                        $order = $DHL->generateShippingLabel($s, $j);
                    }
                    else
                    {

                        $order = $ss->createOrder($s, $j);
                        if(@$order['orderId']){
                            $ss_shipment = $ss->createLabelForOrder($s, $order['orderId']);
                            $sid = $ss_shipment['shipmentId'];
                            $sld = $ss_shipment['labelData'];
                            handleLabelCreation($s, $sid, $sld);
                            udpateShipmentDetails($s, $s['cbp_ship_id'], $ss_shipment, $shipment_date); // to do in helper
                            handleInternalLabelCreation($s['cbp_ship_id']);
                        }
                    }
                }
                else
                {
                    handleLabelCreation($s, $sid, $sld);
                    udpateShipmentDetails($s, $sid, NULL); // to do in helper
                }
                //handleReports($s, $s['cbp_ship_id'], $sld, 'bol');


            }
            return Response()->json(['status'=>true, "shipment_ids"=>$finalShipIds, "TotalAmount"=>$total_fee]);

        }    
        else if($d['updateType'] == 'download')
        {
            foreach ($shipIds as $shipId) 
                $ids[] = $shipId; 

            $downloadData = getDownloadData($sender_id, $ids);
            $senderInfo = getSenderInfo($sender_id); 
            return Response()->json(['status'=>true, "download_data"=>$downloadData, "sender_info"=>$senderInfo]);
        }
        else if($d['updateType'] == 'order')
        {


            if(!empty($d['shipmentDate']))
                $shipment_date = date('Y/m/d', strtotime($d['shipmentDate'])); 
            else
                $shipment_date = NULL;             

            $prefix = 'OBG';
            $nextOrderId = generateAndGetSequence($prefix); 
            $nextGroupedOrderId = generateAndGetGroupedId(); 
            foreach ($shipIds as $shipId) {

                $order_qry = DB::table('shipments')->where('id', '=', $shipId)->first();
                if ($order_qry) 
                {
                    $nextOrderId = str_replace('OB', '', $order_qry->internal_order_id);
                    $nextOrderId = $prefix.str_replace('G', '', $nextOrderId);
                }

                $status = DB::table('shipments')
                    ->where('id','=', $shipId)
                    ->update(['shipment_date' => $shipment_date, 'internal_order_id' => $nextOrderId, 'grouped_order_id' => $nextGroupedOrderId]);
            }
            //return Response()->json(['status'=>true]);
        }

        if($status)
        {
            //DB::commit();
            return Response()->json(['status' => true, 'message' => 'Updated successfully']); 
        }
        else
        {
            //DB::rollback();
            return Response()->json(['status' => false, 'message' => 'Error during database update']); 

        }
    }    

    public function allItems(Request $r){

        $d = $r->toArray();

    }    

    public function downloadBill($shipCode){
        
        if(file_exists(Storage::path('public/packing_slip/'.$shipCode.'.pdf'))){
            return Storage::download('public/packing_slip/'.$shipCode.'.pdf');
        }  
        // else
        //     return Response()->json(['status' => false, 'message' => 'Packing slip not yet generated']); 
    }


    public function printBill($shipCode){

        // if(file_exists(Storage::path('public/packing_slip/'.$shipCode.'.pdf'))){
        //     return Storage::url('public/packing_slip/'.$shipCode.'.pdf');
        // }  
        
        // printBill:function(id){
        //     window.open("/storage/packing_slip/ps-"+id+".pdf").print();
        // },

        $pdf = new \PDFMerger;

        if(file_exists(Storage::path('public/packing_slip/'.$shipCode.'.pdf'))){
            $pdf->addPDF(Storage::path('public/packing_slip/'.$shipCode.'.pdf'), 'all');
        }  

        // if(file_exists(Storage::path('public/labels/'.$shipment_code.'.pdf')) && $shipment->carrier != "CANADA POST"){
        //     $pdf->addPDF(Storage::path('public/labels/'.$shipment_code.'.pdf'), 'all');
        // }  

        // if(file_exists(Storage::path('public/labels/in_'.$shipment_code.'.pdf'))){
        //     $pdf->addPDF(Storage::path('public/labels/in_'.$shipment_code.'.pdf'), 'all');
        // }      


        $pdf->merge('browser', $shipCode.'.pdf');

   }


}
