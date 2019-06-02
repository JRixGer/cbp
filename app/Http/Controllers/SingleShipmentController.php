<?php

namespace cbp\Http\Controllers;

use Illuminate\Http\Request;
use \cbp\Sender;
use \cbp\User;
use \cbp\SenderPhysicalAddress;
use \cbp\SenderBusiness;
use \cbp\SenderMailingAddress;
use \cbp\Shipment;
use \cbp\ShipmentItem;
use \cbp\Recipient;
use \cbp\RecipientAddress;
use \cbp\ParcelRate;
use \cbp\Carrier;
use \cbp\PostageRate;
use \cbp\CBPAddress;
use \cbp\ConversionRate;
use \cbp\Coupon;
use \cbp\DeliveryFee;
use \cbp\Taxes;
use \cbp\TruckFee;
use \cbp\UspsService;

use \cbp\Http\Requests\RecipientRegistrationRequest;
use \cbp\Http\Requests\ParcelDimensionsRequest;

use \cbp\Services\Rocketship; 
use \cbp\Services\ShipStation; 
use \cbp\Services\ShippingLabel; 
use \cbp\Services\DHLGlobal; 

use \cbp\Helpers\Helpers; 
use DB;
use Storage;
use Auth;
use Session;

class SingleShipmentController extends Controller
{
    

    public function __construct(){
    	$this->middleware(['auth','verified']);
        $this->shipping_label = new ShippingLabel; 
        $this->show_all_rates = env("SHOW_ALL_RATES");
    }


    public function index(){
        
    }


    public function validateRecipient(RecipientRegistrationRequest $r){
        //just validate fields and nothing follows

        return Response()->json(['status'=>true]);
    }

    public function validateParcelDimensions(ParcelDimensionsRequest $r){
        //just validate fields and nothing follows
        return Response()->json(['status'=>true]);
    }


    public function voidLabel(Request $r){

        $this->shipping_label->generate(46);
        dd("sdf");
        // $h = new Helpers;

        $ss =  new ShipStation;
        // $r->get("shippmentID");

        $res = $ss->voidLabel($r->get("shipmentID"));
        dd($res);
    }


    public function getShipment($shipID){
        $m  = Shipment::find($shipID);
        return Response()->json($m);
    }


    public function getShipments(Request $r){

        $d = $r->toArray();
        $ids = array();
        foreach ($d as $id) 
            $ids[] = $id; 

        $m  = DB::table('shipments')
          ->select('*')
          ->whereIn('id', $ids)
          ->get();

        // foreach ($m as $data) 
        //     $status = updateShipmentAmount($data->id, $data->total_fee); //kuldeep 


        return Response()->json($m);
    }

    public function downloadLabel($shipment_code){
        // return Storage::download('public/labels/'.$shipCode.'.pdf');

        $pdf = new \PDFMerger;


        if(file_exists(Storage::path('public/labels/'.$shipment_code.'.pdf'))){
            $pdf->addPDF(Storage::path('public/labels/'.$shipment_code.'.pdf'), 'all');
        }  

        if(file_exists(Storage::path('public/labels/in_'.$shipment_code.'.pdf'))){
            $pdf->addPDF(Storage::path('public/labels/in_'.$shipment_code.'.pdf'), 'all');
        }      


        $pdf->merge('download', $shipment_code.'.pdf');
    }

    public function printLabel($shipment_code){

        $pdf = new \PDFMerger;

        $shipment = Shipment::where("shipment_code",$shipment_code)->get()->first();

        if(file_exists(Storage::path('public/labels/'.$shipment_code.'.pdf')) && $shipment->carrier != "CANADA POST"){
            $pdf->addPDF(Storage::path('public/labels/'.$shipment_code.'.pdf'), 'all');
        }  

        if(file_exists(Storage::path('public/labels/in_'.$shipment_code.'.pdf'))){
            $pdf->addPDF(Storage::path('public/labels/in_'.$shipment_code.'.pdf'), 'all');
        }      


        $pdf->merge('browser', $shipment_code.'.pdf');
    }



    public function createShipment(Request $r){

        $d = $r->toArray();

        if($d['shipment_type'] =="PD"){
            return $this->createShipmentPD($d, true);
        }else{

            return $this->createShipmentDO($d, true);
        }
       
    }


    public function saveShipment(Request $r){

        $d = $r->toArray();

        if($d['shipment_type'] =="PD"){
            return $this->createShipmentPD($d);
        }else{

            return $this->createShipmentDO($d);
        }
       
    }


    private function createShipmentPD($d, $createLabel=false){
        $ss =  new ShipStation;
        $shipment = [];

        $postage = @$d['postage_option_model'];

        $recipient_id = $this->addRecipient($d['recipient_model']);

        $recipient_address_id = $this->addRecipientAddress($d['recipient_model'], $recipient_id);

        // $carrier_id = Carrier::where("name",$postage['details']['carrier'])->first()->id;
        $postage_rate_id = $this->addPostageRate($postage['details']);




        $carrier = (@$d['postage_option_model']['details']['carrier']) ? @$d['postage_option_model']['details']['carrier'] : null; 
        $carrier_desc = (@$d['postage_option_model']['details']['postage_type']) ? @$d['postage_option_model']['details']['postage_type'] : null; 
        $cbp_address_id = (@$d['ship_from_address_model']['id']) ? @$d['ship_from_address_model']['id'] : 0; 


        $arr = [
            "recipient_id" => $recipient_id,
            "recipient_address_id" => $recipient_address_id,
            "carrier_id" =>$this->getCarrierId($carrier),
            "carrier" =>$carrier,
            "carrier_desc" =>$carrier_desc,
            "postage_rate_id" =>$postage_rate_id,
            "markup" =>@$d['postage_option_model']['details']['markup'],
            'cbp_address_id'=>$cbp_address_id,
            'shipment_type'=>@$d['shipment_type'],
            // "insurance_cover" => @$d['postage_option_model']['insured_value'],
            "insurance_cover" => @$d['insurance_model']['insured_value'],
            // "insurance_cover_amount" => @$d['insurance_model']['premium_fee'],
            "coupon_code"=>@$d['postage_option_model']['coupon_model']['coupon'],
            "coupon_type"=>@$d['postage_option_model']['coupon_model']['type'],
            "coupon_amount"=>@$d['postage_option_model']['coupon_model']['amount']
        ];

        // dd($arr);
        // dd($recipient_address_id);

        $shipment_id = $this->addShipmentDetails($d, $arr);

        $prefix = 'OB';   // added rico
        generateSequence($prefix, $shipment_id);    // added rico

        if($shipment_id){

            $recipient = Recipient::find($recipient_id);
            $idsArr = [
                    'shipment_id'=>$shipment_id, 
                    'recipient_id'=>$recipient_id, 
                    'recipient_name'=>$recipient->first_name." ".$recipient->last_name,
                ];

            if($createLabel){

                $postage = @$d['postage_option_model']['details'];
                $carrierCode = $postage['carrier'];


                // $DHL = new DHLGLobal;
                //     $order = $DHL->generateShippingLabel($d, $idsArr);

                if(in_array($carrierCode, ["UPS","USPS"])){

                    $shipment = $ss->createShipment($d, $idsArr);
                    // dd($shipment);


                    if(@$shipment['shipmentId']){
                        $this->handleLabelCreation($d, $shipment['shipmentId'], $shipment['labelData']);
                        $this->udpateShipmentDetails($d, $shipment_id, $shipment);
                        handleInternalLabelCreation($shipment_id);
                    }
                }else if($carrierCode == "DHL"){

                    $DHL = new DHLGLobal;
                    $order = $DHL->generateShippingLabel($d, $idsArr);

                    // dd($order);
                    $packageID = $order['data']['shipments'][0]['packages'][0]['responseDetails']['labelDetails'][0]['packageId'];
                    $trackingNumber = $order['data']['shipments'][0]['packages'][0]['responseDetails']['trackingNumber'];
                    $_label = $order['data']['shipments'][0]['packages'][0]['responseDetails']['labelDetails'][0]['labelData'];

                    // dd($_label);
                    handleInternalLabelCreationDHL($_label, $packageID);

                    $shipment['shipmentId'] = $packageID; 
                    $shipment['trackingNumber'] = $trackingNumber;
                    $this->udpateShipmentDetails($d, $shipment_id, $shipment);
                    handleInternalLabelCreation($shipment_id);
                    // dd("sdf");

                }else{

                    $order = $ss->createOrder($d, $idsArr);
                    if($order['orderId']){

                        if($carrierCode == "CANADA POST"){
                            $_label = null;
                            // print_r($shipment_id);exit;
                            handleInternalLabelCreation($shipment_id);
                            $this->udpateShipmentDetails($d, $shipment_id, $shipment);
                        }else{
                            $shipment = $ss->createLabelForOrder($d, $order['orderId']);
                            $_label = $shipment['labelData'];
                            $this->handleLabelCreation($d, $shipment['shipmentId'], $_label);
                            $this->udpateShipmentDetails($d, $shipment_id, $shipment);
                            handleInternalLabelCreation($shipment_id);
                            // print_r($shipment);
                            // print_r("successs");exit;

                        }
                        
                    }
                }
            }

            if(@$d['item_information_model']){
                $res = $this->addItemDetails(@$d['item_information_model'], $shipment_id);
                handleReports($d, $shipment_id, null, 'bol');  // added rico
            }

            return Response()->json(['status'=>true, "shipment_id"=>$shipment_id]);
            
        }else{
            return Response()->json(['status'=>false]);

        }
    }


    private function createShipmentDO($d, $createLabel=false){
        $ss =  new ShipStation;
        $shipment = [];


        $recipient_id = $this->addRecipient($d['recipient_model']);

        $recipient_address_id = $this->addRecipientAddress($d['recipient_model'], $recipient_id);




        $carrier_id = $this->getCarrierId("CBP Delivery");
        $carrier_desc = "CBP Delivery";
        $cbp_address_id = (@$d['ship_from_address_model']['id']) ? @$d['ship_from_address_model']['id'] : 0; 


        $arr = [
            "carrier" =>$carrier_desc,
            "carrier_id" =>$carrier_id,
            "carrier_desc" =>$carrier_desc,
            "recipient_id" => $recipient_id,
            "recipient_address_id" => $recipient_address_id,
            'cbp_address_id'=>$cbp_address_id,
            "coupon_code"=>@$d['delivery_fee_model']['coupon_model']['coupon'],
            "coupon_type"=>@$d['delivery_fee_model']['coupon_model']['type'],
            "coupon_amount"=>@$d['delivery_fee_model']['coupon_model']['amount']
        ];

        // dd($arr);
        // dd($recipient_address_id);

        $shipment_id = $this->addShipmentDetails($d, $arr );
        
        $prefix = 'OB';
        generateSequence($prefix, $shipment_id); 
 
        if($shipment_id){
            $recipient = Recipient::find($recipient_id);

            $idsArr = [
                    'shipment_id'=>$shipment_id, 
                    'recipient_id'=>$recipient_id, 
                    'recipient_name'=>$recipient->first_name." ".$recipient->last_name,
                ];

            if($createLabel){
                $this->udpateShipmentDetails($d, $shipment_id, $shipment);
                $this->handleLabelCreation($d, $shipment_id, null);
            }
            

            if(@$d['item_information_model']){
                $res = $this->addItemDetails(@$d['item_information_model'], $shipment_id);
            }
            handleReports($d, $shipment_id, null, 'bol');  // added rico


            return Response()->json(['status'=>true, "shipment_id"=>$shipment_id]);
            
        }else{
            return Response()->json(['status'=>false]);

        }
    }



    private function addShipmentDetails($data, $arr){

        if(@$data['id']){
            $m = Shipment::find(@$data['id']);
        }else{
            $m = new Shipment;
        }

        if($data['shipment_type'] == "PD"){
            $prefix = 'PDL';
            
            if($data['ship_from_address_model']['country'] == "CA")
                $prefix = 'PGO';

            $delivery_fee = (@$data['postage_option_model']['details']['cbp_delivery_fee']) ? @$data['postage_option_model']['details']['cbp_delivery_fee'] : 0; 
            $truck_fee = (@$data['postage_option_model']['details']['truck_fee']) ? @$data['postage_option_model']['details']['truck_fee'] : 0; 

            $_postage = @$data['postage_option_model']['details'];

            $postage_fee = (@$_postage['negotiated_rate']) ? @$_postage['negotiated_rate'] : @$_postage['rate']; 

            $total_fee = (@$data['postage_option_model']['details']['total']) ? @$data['postage_option_model']['details']['total'] : 0; 
            
        }else{
            $prefix = 'DEL';
            $delivery_fee = @$data['delivery_fee_model']['rate'];
            $postage_fee = 0;
            $total_fee = @$data['delivery_fee_model']['total'];
            $truck_fee =0;
        }


        $unit_type = $data['unit_type'];


        if($unit_type == "imperial"){
            $unit_size = "IN";
            $unit_weight = "LBS";
        }else{
            $unit_size = "CM";
            $unit_weight = "G";
        }


        $zoneSkipPostal=['T','R','V'];
        $zoneSkipProvince=['ab','alberta','mb','manitoba','bc','british columbia'];

        $postal = $data['recipient_model']['postal'];
        $province = $data['recipient_model']['province'];

        if($data['recipient_model']['country'] == "CA" && $data['ship_from_address_model']['country'] == "CA"){

            if(in_array($postal[0], $zoneSkipPostal) && in_array(strtolower($province), $zoneSkipProvince)){

                if(in_array(strtolower($province), ['ab','alberta','mb','manitoba'])){
                    $sma_CA = CBPAddress::where("country","CA")->where("province","AB")->get()->first();
                }else{
                    $sma_CA = CBPAddress::where("country","CA")->where("province","BC")->get()->first();
                }

            }else{
                $sma_CA = CBPAddress::where("country","CA")->where("province","ON")->get()->first();

            }

            $m->cbp_address_id = $sma_CA->id;
        }else{

            $m->cbp_address_id = @$arr['cbp_address_id'];
            
        }

        // $m->shipment_code = @$manifest['shipmentId'];
        $m->sender_id = Auth::User()->sender_id;

        $m->shipment_type = @$data['shipment_type'];

        if($arr['carrier'] == "CANADA POST"){
            $m->shipment_code = generateCode("CP",7);
        }
        $m->recipient_id = @$arr['recipient_id'];
        $m->recipient_address_id = @$arr['recipient_address_id'];
        $m->carrier_id = @$this->getCarrierId(@$arr['carrier'])->id;
        $m->carrier = @$arr['carrier'];
        $m->carrier_desc = @$arr['carrier_desc'];
        $m->postage_rate_id = @$arr['postage_rate_id'];
        // $m->shipment_date = $;

        $m->insurance_cover = @$arr['insurance_cover'];
        $m->insurance_cover_amount = @$arr['insurance_cover_amount'];
        $m->length = @$data['parcel_dimensions_model']['length'];
        $m->width = @$data['parcel_dimensions_model']['width'];
        $m->height = @$data['parcel_dimensions_model']['height'];
        $m->size_unit = $unit_size;
        $m->weight = @$data['parcel_dimensions_model']['weight'];
        $m->letter_option = @$data['parcel_dimensions_model']['usps_options'];
        $m->weight_unit = $unit_weight;
        // $m->require_signature = (@$data['signature_require_model']) ? @$data['signature_require_model'] : 0;
        if(@$data['parcel_types']['parcel_type'] == "Letter"){
            $m->require_signature = null;
        }else{
            $m->require_signature = (@$data['postage_option_model']['req_signature']) ? @$data['postage_option_model']['req_signature'] : 0;
        }
        $m->delivery_fee = $delivery_fee;
        $m->truck_fee = $truck_fee;
        $m->postage_fee = $postage_fee;
        $m->mark_up = @$arr['markup'];
        $m->coupon_code = @$arr['coupon_code'];
        $m->coupon_type = @$arr['coupon_type'];
        $m->coupon_amount = @$arr['coupon_amount'];
        $m->note = '';
        // $m->amount_paid = $;
        $m->total_fee = $total_fee;
        $m->shipment_status_id = $data['shipment_status'];
        $res = $m->save();
       
        if($res){
            $this->logShipmentStatusHistory($m->id, 3); //set to ready
            generateSequence($prefix, $m->id);
            return $m->id;
        }

    }

    private function getCarrierId($carrier){
        return Carrier::where("carrier_id",$carrier)->get()->first();
    }

    private function logShipmentStatusHistory($shipment_id, $status_id){
        $m = new \cbp\ShipmentStatusHistory;

        $m->shipment_id = $shipment_id;
        $m->shipment_status_id = $status_id;
        $m->save();
    }


    private function udpateShipmentDetails($data=[], $id, $manifest){
        $m = Shipment::find($id);

        if($data['shipment_type'] == "PD"){

            if($data['postage_option_model']['details']['carrier'] == "CANADA POST"){
                $m->tracking_no = generateCode($id,15);
            }else{
                $m->shipment_code = @$manifest['shipmentId'];
                $m->tracking_no = @$manifest['trackingNumber'];
            }

            $m->order_id = @$manifest['orderId'];
            $m->cbme = "CBMESHIP".@$id;
            $m->amount_paid = $data['postage_option_model']['total'];

            
        }
        else{
            
            $m->order_id = @$manifest['orderId'];
            $m->shipment_code = generateCode($id,7);
            $m->tracking_no = generateCode($id,15);
            $m->cbme = "CBMECBD".$id;
            $m->amount_paid = $data['delivery_fee_model']['total'];
			
        }
        $m->save();
    }

    private function addItemDetails($items, $shipment_id){

        $arr = [];
        foreach ($items as $key => $data) {
            # code...
            if(@$data['id']){
                $m = ShipmentItem::find(@$data['id']);
            }else{
                $m = new ShipmentItem;
            }
            $m->shipment_id = @$shipment_id;
            $m->pallet_id = @$data['pallet_id'];
            $m->bag_id = @$data['bag_id'];
            $m->description = @$data['description'];
            $m->value = @$data['value'];
            $m->qty = @$data['quantity'];
            $m->origin_country = @$data['country'];
            $m->note = @$data['note'];
            $res = $m->save();
            if($res){
                array_push($arr, $m->id);
            }
        }

        return $arr;

    }

    private function addPostageRate($data){
        $m = new PostageRate;
        $m->description = @$data['desc'];
        $m->currency = @$data['currency'];
        $m->value = @$data['total'];
        $m->est_delivery_time = @Date("Y-m-d", strtotime($data['est_delivery_time']));
        $m->package_type = @$data['package_type'];
        $m->service_code = @$data['service_code'];
        $m->other_cost = 0;
        $res = $m->save();
        if($res){
            return $m->id;
        }
    }

    private function addRecipient($data){
        $m = new Recipient;
        // dd($data);
        $s_data = [
            'first_name'=>"CBPTEST-".@$data['first_name'],
            'last_name'=>@$data['last_name'],
            'email'=>@$data['email'],
            'company'=>@$data['company'],
            'contact_no'=>@$data['contact_no']
        ];
        $res = $m->updateOrCreate(['email'=>@$data['email'],'first_name'=>@$data['first_name'], 'last_name'=>@$data['last_name']], $s_data);
        return $res->id;
    }

    private function addRecipientAddress($data, $recipient_id){
        $m = new RecipientAddress;
        // dd($recipient_id);
        $s_data = [
            'recipient_id'=>$recipient_id,
            'address_1'=>@$data['address_1'],
            'address_2'=>@$data['address_2'],
            'city'=>@$data['city'],
            'province'=>@$data['province'],
            'postal'=>@$data['postal'],
            'country'=>@$data['country']
        ];

        // dd($s_data);
        $res = $m->updateOrCreate(['recipient_id'=>$recipient_id,
            'address_1'=>$data['address_1'], 
            'postal'=>$data['postal'], 'country'=>$data['country'] ], $s_data);
        return $res->id;
    }



    public function getRates(Request $r){
    	$d = $r->toArray();

        if($d['shipment_type'] == "PD"){
            return $this->handlePostageDeliveryRates($d);
            // return '{"status":true,"response":[{"desc":"DHL GM PACKET PLUS","rate":10.68,"currency":"USD","service_code":"29","est_delivery_time":"","package_type":"","rate_detail":[{"rateComponentId":"ZBPT","partId":"29","description":"Base Rate","amount":10.44},{"rateComponentId":"ZFL1","partId":"29","description":"Fuel Surcharge","amount":0.24}],"carrier":"DHL","insurance_fee":"0.00","signature_fee":"0.00","postage_type":"CBP Saver","postage_type_code":"cbp_saver","total":"14.20","estimated_delivery":"2-6 Days","truck_fee":"0.00","tax":"0.00","markup":"0.00","cbp_delivery_fee":"0.00"},{"desc":"DHL GM Parcel Priority","rate":10.97,"currency":"USD","service_code":"54","est_delivery_time":"","package_type":"","rate_detail":[{"rateComponentId":"ZBPT","partId":"54","description":"Base Rate","amount":10.73},{"rateComponentId":"ZFL1","partId":"54","description":"Fuel Surcharge","amount":0.24}],"carrier":"DHL","insurance_fee":"0.00","signature_fee":"0.00","postage_type":"CBP Saver","postage_type_code":"cbp_saver","total":"14.59","estimated_delivery":"2-6 Days","truck_fee":"0.00","tax":"0.00","markup":"0.00","cbp_delivery_fee":"0.00"}]}';
        }else{
            return $this->handleDeliveryOnlyRates($d);

        }
        
    }



    public function getRatesByCarrier(Request $r){
        $d = $r->toArray();

        // return '{"status"=>true,"response"=>{"desc"=>"DHL GM PACKET PLUS","rate"=>10.68,"currency"=>"USD","service_code"=>"29","est_delivery_time"=>"","package_type"=>"","rate_detail"=>[{"rateComponentId"=>"ZBPT","partId"=>"29","description"=>"Base Rate","amount"=>10.44},{"rateComponentId"=>"ZFL1","partId"=>"29","description"=>"Fuel Surcharge","amount"=>0.24}],"carrier"=>"DHL","insurance_fee"=>"0.00","signature_fee"=>"0.00","postage_type"=>"CBP Saver","postage_type_code"=>"cbp_saver","total"=>"14.20","estimated_delivery"=>"2-6 Days","truck_fee"=>"0.00","tax"=>"0.00","markup"=>"0.00","cbp_delivery_fee"=>"0.00"}}';


        

        $_rate = [];
        $from_country = @$d['ship_from_address_model']['country'];
        $to_country = $d['recipient_model']['country'];
        $carrier = $d['postage_option_model']['details']['carrier'];
        $service_code = @$d['postage_option_model']['details']['service_code'];
        $package_type = @$d['postage_option_model']['details']['package_type'];
            
            if($from_country == "CA" && $to_country == "CA"){

                if(@$carrier== "CANADA POST"){
                    $_rate = $this->CACA_CANADA_POST($d);

                }else if(@$carrier== "UPS"){
                    $_rate = $this->CACA_UPS($d);

                }else if(@$carrier== "FEDEX"){
                    $_rate = $this->CACA_FEDEX($d);
                }


            }
            else if($to_country == "US"){
                
                $_rate = $this->compare_CA_US_rates($d,"byCarrier");

   
                if($d['postage_option_model']['details']['address_flow'] == "US_US"){
                    $d['ship_from_address_model'] = CBPAddress::where("country","US")->get()->first()->toArray();
                }else{
                    $d['ship_from_address_model'] = CBPAddress::where("country","CA")->where("province","ON")->get()->first()->toArray();
                }

                // dd($_rate);
            }
            else if($from_country == "CA" && $to_country == "US"){
                $_rate = $this->CAUS_UPS($d);
            }
            elseif($from_country == "CA" &&  !in_array($to_country, ["CA","US"])){
                if($carrier== "UPS"){
                    $_rate = $this->CAINT_UPS($d);
                }else if($carrier== "DHL"){
                    $_rate = $this->CAINT_DHL($d);

                }

            }
            else if($from_country == "US" && $to_country == "US"){ 
                if($carrier== "UPS"){
                    $_rate = $this->USUS_UPS($d);
                }else if($carrier == "USPS"){
                    $_rate[] = $this->USUS_USPS($d);
                }

            }else if ($from_country == "US" && $to_country != "US"){
                $_rate = $this->USINT_USPS($d);
            }

        // dd($_rate);

        $res = $this->postageRateTriggers($d, $_rate);
        $res = $this->filterRocketshipRatesByServiceCode($res, $service_code, $carrier, $package_type);


        return Response()->json(['status'=>true,"response"=>$res]);
       
    }


    private function CACA_CANADA_POST($d){
        $_rate = [];
        $postage_type_code = @$d['postage_option_model']['details']['postage_type_code'];
        
        $rs = new Rocketship;
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

            'rate4_normal' => $rate4_normal,
            'rate4_signature' => $rate4_signature,
            'rate4_insurance' => $rate4_insurance,
        ];


        if($postage_type_code == "cbp_saver_zone_skip"){
            //zoneskipped
            if(@$d['postage_option_model']['details']['zone_skipped']){

                $_rate = $this->CBP_Saver_CACA_ZoneSkipped($sign_insurance, $rate4);
            }else{
                $_rate = $this->CBP_Saver_CACA($sign_insurance, $rate1, false, false);
            }
        }else if($postage_type_code == "cbp_expedited"){
            $_rate = $this->CBP_Expedited_CACA($sign_insurance, $rate1, false);

        }else if($postage_type_code == "cbp_express"){
            $_rate = $this->CBP_Express_CACA($sign_insurance, $rate1, false);
        }


        return $_rate;
    }

    private function CACA_UPS($d){
        $_rate = [];
        $postage_type_code = @$d['postage_option_model']['details']['postage_type_code'];

        $rs = new Rocketship;
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
            'rate2_normal' => $rate2_normal,
            'rate2_signature' => $rate2_signature,
            'rate2_insurance' => $rate2_insurance,
        ];


        if($postage_type_code == "cbp_saver"){
            //zoneskipped
            if(@$d['postage_option_model']['details']['zone_skipped']){
                $_rate = $this->CBP_Saver_CACA_ZoneSkipped($sign_insurance, $rate);
            }else{
                $_rate = $this->CBP_Saver_CACA($sign_insurance, false, $rate2, false);
            }
        }else if($postage_type_code == "cbp_expedited"){
            $_rate = $this->CBP_Expedited_CACA($sign_insurance, false, $rate2);

        }else if($postage_type_code == "cbp_express"){
            $_rate = $this->CBP_Express_CACA($sign_insurance, false, $rate2);
        }


        return $_rate;
    }

    private function CACA_FEDEX($d){
        $_rate = [];
        $postage_type_code = @$d['postage_option_model']['details']['postage_type_code'];

        $rs = new Rocketship;
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

        $sign_insurance = [
            'rate3_normal' => $rate3_normal,
            'rate3_signature' => $rate3_signature,
            'rate3_insurance' => $rate3_insurance,
        ];


        if($postage_type_code == "cbp_saver"){
            $_rate = $this->CBP_Saver_CACA($sign_insurance, false, false, $rate3);
        }


        return $_rate;
    }

    private function CAUS_UPS($d){
        $_rate = [];
        $postage_type_code = @$d['postage_option_model']['details']['postage_type_code'];

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


        if($postage_type_code == "cbp_saver"){

             $_rate = $this->CBP_Saver_CAUS($sign_insurance, $rate1);
        
        }else if($postage_type_code == "cbp_expedited"){
            
            $_rate = $this->CBP_Expedited_CAUS($sign_insurance, $rate1);

        }else if($postage_type_code == "cbp_express"){
            $_rate = $this->CBP_Express_CAUS($sign_insurance, $rate1);
        }


        return $_rate;
    }

    private function CAINT_UPS($d){
        $_rate = [];
        $postage_type_code = @$d['postage_option_model']['details']['postage_type_code'];

        $rs = new Rocketship;
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
            'rate2_normal' => $rate2_normal,
            'rate2_signature' => $rate2_signature,
            'rate2_insurance' => $rate2_insurance,
        ];

        if($postage_type_code == "cbp_expedited"){
            
            $_rate = $this->CBP_Expedited_CAInt($sign_insurance, $rate2);

        }else if($postage_type_code == "cbp_express"){
            $_rate = $this->CBP_Express_CAInt($sign_insurance, $rate2);
        }


        return $_rate;
    }

    private function CAINT_DHL($d){
        $_rate = [];
        $postage_type_code = @$d['postage_option_model']['details']['postage_type_code'];

        $dhl = new DHLGLobal; 
        $rate1 = $dhl->getRates($d);

        $d['_signature'] = "false";
        $d['_insurance'] = "false";
        $rate1_normal = $dhl->getRates($d);


        $d['_signature'] = "true";
        $d['_insurance'] = "false";
        $rate1_signature = $dhl->getRates($d);
        // dd($rate1_signature);

        $d['_signature'] = "false";
        $d['_insurance'] = "true";
        $rate1_insurance = $dhl->getRates($d);

        $sign_insurance = [
                    'rate1_normal' => $rate1_normal,
                    'rate1_signature' => $rate1_signature,
                    'rate1_insurance' => $rate1_insurance,
                ];

        if($postage_type_code == "cbp_saver"){
            
            $_rate = $this->CBP_Saver_CAInt($sign_insurance, $rate1);

        }


        return $_rate;
    }


    private function USUS_UPS($d){
        $_rate = [];
        $postage_type_code = @$d['postage_option_model']['details']['postage_type_code'];

        $rs = new Rocketship;
        $rate1 = $rs->getUPSUSRates($d);
        $d['_signature'] = "false";
        $d['_insurance'] = "false";
        $rate1_normal = $rs->getUPSUSRates($d);
        // dd($rate1_normal);

        $d['_signature'] = "true";
        $d['_insurance'] = "false";
        $rate1_signature = $rs->getUPSUSRates($d);
        // dd($rate1_signature);

        $d['_signature'] = "false";
        $d['_insurance'] = "true";
        $rate1_insurance = $rs->getUPSUSRates($d);


        if($postage_type_code == "cbp_saver"){
             $_rates = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"03","UPS"); //UPS Ground
            if($_rates) { 
                $_rates['insurance_fee'] = splitInsurance($rate1_normal, $rate1_insurance, "03","");
                $_rates['signature_fee'] = splitSignature($rate1_normal, $rate1_signature, "03","");


                $_rates['postage_type'] = "CBP Saver";   
                $_rates['postage_type_code'] = "cbp_saver";   
                $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
                $_rates['estimated_delivery'] = "2-6 Days";
                $_rates['address_flow'] = "US_US";

                $_rate[] = $_rates;

            }
        
        }

        // dd($rate1);

        return $_rate;
    }

    private function USUS_USPS($data){
        $rates = [];
        $postage_type_code = @$data['postage_option_model']['details']['postage_type_code'];
        $service_code = @$data['postage_option_model']['details']['service_code'];
        $package_type = @$data['postage_option_model']['details']['package_type'];

        $rs = new Rocketship;
        $rate2 = $rs->getSTAMPSRates($data);
        // print_r($rate2);
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


        if($postage_type_code == "cbp_saver"){
            if(@$rate2['data']['rates'] && $weight <=70){

                if($data['parcel_types']['parcel_type'] == "Box" && $data['parcel_types']['usps_box_status'] == "no"){

                    $dimsWeight = $length + ($width * 2) + ($height * 2);

                    // print_r($dimsWeight);
                    // exit;
                    //USPS FIRST CLASS
                    $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-FC", "Large Envelope or Flat","USPS"); //USPS First class
                   
                    if($d && $service_code == "US-FC" && $package_type == "Large Envelope or Flat") { 
                        $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance, "US-FC","Large Envelope or Flat");
                        $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature, "US-FC","Large Envelope or Flat");
                        $rates = $d; 
                    }

                    $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-FC", "Package","USPS"); //USPS First class
                    if($d && $service_code == "US-FC" && $package_type == "Package") { 
                        $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-FC", "Package");
                        $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-FC", "Package");
                        $rates = $d; 
                    }

                    $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-FC", "Thick Envelope","USPS"); //USPS First class
                    if($d  && $service_code == "US-FC" && $package_type == "Thick Envelope") { 
                        $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-FC", "Thick Envelope");
                        $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-FC", "Thick Envelope");
                        $rates = $d; 
                    }



                    //USPS PRIORITY

                    if($dimsWeight <= 84){

                        // $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-PM", "Large Envelope or Flat","USPS"); //USPS PRIORITY PACKAGE
                        // if($d && $service_code == "US-PM" && $package_type == "Large Envelope or Flat") { 
                        //     $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-PM", "Large Envelope or Flat");
                        //     $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-PM", "Large Envelope or Flat");
                        //     $rates = $d; 
                        // }

                        $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-PM", "Package","USPS"); //USPS PRIORITY PACKAGE
                        if($d && $service_code == "US-PM" && $package_type == "Package") { 
                            $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-PM", "Package");
                            $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-PM", "Package");
                            $rates = $d; 
                        }

                        // $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-PM", "Thick Envelope","USPS"); //USPS PRIORITY PACKAGE
                        // if($d && $service_code == "US-PM" && $package_type == "Thick Envelope") { 
                        //     $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-PM", "Thick Envelope");
                        //     $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-PM", "Thick Envelope");
                        //     $rates = $d; 
                        // }

                    }else if($dimsWeight > 84 && $dimsWeight <= 108){

                        $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-PM", "Large Package","USPS"); //USPS PRIORITY PACKAGE
                        if($d  && $service_code == "US-PM" && $package_type == "Large Package") { 
                            $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-PM", "Large Package");
                            $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-PM", "Large Package");
                            $rates = $d; 
                        }
                    }


                    if($dimsWeight <= 108){
                        //USPS EPRESS MAIL
                        // $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-XM", "Large Envelope or Flat","USPS");
                        // if($d && $service_code == "US-XM" && $package_type == "Large Envelope or Flat") { 
                        //     $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-XM", "Large Envelope or Flat");
                        //     $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-XM", "Large Envelope or Flat");
                        //     $rates = $d; 
                        // }

                        $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-XM", "Package","USPS"); 
                        if($d && $service_code == "US-XM" && $package_type == "Package") { 
                            $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-XM", "Package");
                            $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-XM", "Package");
                            $rates = $d; 
                        }

                        // $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-XM", "Thick Envelope","USPS");
                        // if($d && $service_code == "US-XM" && $package_type == "Thick Envelope") { 
                        //     $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-XM", "Thick Envelope");
                        //     $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-XM", "Thick Envelope");
                        //     $rates = $d; 
                        // }
                    }


                }else if($data['parcel_types']['parcel_type'] == "Box" && $data['parcel_types']['usps_box_status'] == "yes"){
                    $rates = []; // this will not include rates from USPS
                    $options = explode("|", $data['usps_options_model']['usps_options']);
                    $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],$options[0], $options[1],"USPS"); //USPS PRIORITY PACKAGE
                    // print_r($options);
                    // print_r($rate2);
                    if($d && $service_code == $options[0] && $package_type == $options[1]) { 
                        $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,$options[0], $options[1]);
                        $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,$options[0], $options[1]);
                        $rates = $d; 
                    }

                }else if($data['parcel_types']['parcel_type'] == "Letter" ){

                    $options = explode("|", $data['usps_options_model']['usps_options']);
                    $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],$options[0], $options[1],"USPS"); //USPS PRIORITY PACKAGE
                    if($d && $service_code == $options[0] && $package_type == $options[1]) { 
                        $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,$options[0], $options[1]);
                        $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,$options[0], $options[1]);
                        $rates = $d; 
                    }

                }else{ $rates = []; }

            }

            if($rates) { 
                $rates['postage_type'] = "CBP Saver";   
                $rates['postage_type_code'] = "cbp_saver";   
                $rates['total'] = (@$rates['negotiated_rate']) ? @$rates['negotiated_rate'] : $rates['rate'];
                $rates['estimated_delivery'] = "2-6 Days";
                $rates['address_flow'] = "US_US";


            }
        
        }


        return $rates;
    }

    private function USINT_USPS($data){
        $_rate = [];
        $postage_type_code = @$data['postage_option_model']['details']['postage_type_code'];
        $service_code = @$data['postage_option_model']['details']['service_code'];
        $package_type = @$data['postage_option_model']['details']['package_type'];
        
        $rs = new Rocketship;
        $rate1 = $rs->getSTAMPSRates($data);
        // $data['_signature'] = "false";
        // $data['_insurance'] = "false";
        // $rate1_normal = $rs->getSTAMPSRates($data);
        // dd($rate1_normal);
        // print_r($rate1_normal);


        // $data['_signature'] = "true";
        // $data['_insurance'] = "false";
        // $rate1_signature = $rs->getSTAMPSRates($data);
        // // print_r($rate1_signature);

        // $data['_signature'] = "false";
        // $data['_insurance'] = "true";
        // $rate1_insurance = $rs->getSTAMPSRates($data);

        // dd($package_type);
        if($postage_type_code == "cbp_saver"){
            
            $d = $this->filterUSPSByServiceCodeAndPackageType($rate1['data']['rates'],"US-PMI","Package","USPS"); //USPS Priority Mail International

            if($d && $service_code == "US-PMI" && $package_type == "Package") { 
                $d['insurance_fee'] = filter_rate_detail($d['rate_detail'], "SC-A-INS")['amount']; //splitInsurance($rate1_normal, $rate1_insurance, "US-PMI","Package");
                $d['signature_fee'] = filter_rate_detail($d['rate_detail'], "SC-A-HP")['amount'];//splitSignature($rate1_normal, $rate1_signature, "US-PMI","Package");
                $_rates = $d; 
            }

            $d = $this->filterUSPSByServiceCodeAndPackageType($rate1['data']['rates'],"US-FCI","Package","USPS"); //USPS First Class Mail International
            if($d && $service_code == "US-FCI" && $package_type == "Package") { 
                $d['insurance_fee'] = filter_rate_detail($d['rate_detail'], "SC-A-INS")['amount']; //splitInsurance($rate1_normal, $rate1_insurance, "US-FCI","Package");
                $d['signature_fee'] = filter_rate_detail($d['rate_detail'], "SC-A-HP")['amount']; //splitSignature($rate1_normal, $rate1_signature, "US-FCI","Package");
                $_rates = $d; 
            }

            // dd($_rates);

            if($_rates) { 
                // $_rates['insurance_fee'] = splitInsurance($rate1_normal, $rate1_insurance, "03","");
                // $_rates['signature_fee'] = splitSignature($rate1_normal, $rate1_signature, "03","");


                $_rates['postage_type'] = "CBP Saver";   
                $_rates['postage_type_code'] = "cbp_saver";   
                $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
                $_rates['estimated_delivery'] = "2-6 Days";
                $_rate[] = $_rates;

            }
        
        }

        // dd($_rates);
        return $_rate;
    }

    private function handleDeliveryOnlyRates($d){
        // dd($d);
        // $letterRate = 0.25;
        // dd($rate);
        if(@$d['parcel_letter_status'] == "yes"){
            $rate = DeliveryFee::where("max_weight","<",30)->first()->letter_mail_price;
            return Response()->json(['status'=>true, "rate"=>$rate]);
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
                return Response()->json(['status'=>true, "rate"=>$_rate]);
                
            }else{
                return Response()->json(['status'=>false, "message"=>"Rate Error"]);

            }
                
        }
    }





    private function handlePostageDeliveryRates($d){
        $from_country = @$d['ship_from_address_model']['country'];
        $to_country = $d['recipient_model']['country'];

      
        if($from_country == "CA" && $to_country == "CA"){

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

                $CBP_Saver_CACA = $this->CBP_Saver_CACA($sign_insurance, $rate1, $rate2, $rate3);
                $CBP_Saver_CACA_ZoneSkipped = $this->CBP_Saver_CACA_ZoneSkipped($sign_insurance, $rate4);
                $CBP_Expedited_CACA = $this->CBP_Expedited_CACA($sign_insurance, $rate1, $rate2);
                $CBP_Express_CACA = $this->CBP_Express_CACA($sign_insurance, $rate1, $rate2);


                // if($this->show_all_rates){

                if($CBP_Saver_CACA) $rate1 = $CBP_Saver_CACA;
                  if($CBP_Expedited_CACA) $rate2 = $CBP_Expedited_CACA;
                  if($CBP_Express_CACA) $rate3 = $CBP_Express_CACA;
                  if($CBP_Saver_CACA_ZoneSkipped) $rate4 = $CBP_Saver_CACA_ZoneSkipped;

                  $rates = array_merge($rate1, $rate2, $rate3, $rate4);

                // }else{

                //   if($CBP_Saver_CACA) $rates[] = $CBP_Saver_CACA;
                //   if($CBP_Saver_CACA_ZoneSkipped) $rates[] = $CBP_Saver_CACA_ZoneSkipped;
                //   if($CBP_Expedited_CACA) $rates[] = $CBP_Expedited_CACA;
                //   if($CBP_Express_CACA) $rates[] = $CBP_Express_CACA;

                // }

                // dd($rates);

                // $_rate1 = $this->renameCBP($rate1['data']['rates']);
                // $a = $this->insertCarrier($_rate1,"CANADA POST");
                // $b = $this->insertCarrier($rate2['data']['rates'],"UPS");
                // $c = $this->insertCarrier($rate3['data']['rates'],"FedEx");

                // dd($rate1);
                // $data = $rate1;
                // $data['data']['rates'] = $this->ratesCompare($a, $b, $c);
                // dd($data['data']['rates']);
                $res =  $rates;


            } catch (\Exception $e) {
                 return Response()->json(['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS"']);
                
            }

            // dd($res);

        }
        else if($to_country == "US"){
            //NEW LOGIC WHICH WILL COMPARE RATES FROM CA AND US SHIPFROM AND DISPLAY LOWEST RATES

            $rates = $this->compare_CA_US_rates($d);
 
        }
        else if($from_country == "CA" && $to_country == "US"){


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

                $CBP_Saver_CAUS = $this->CBP_Saver_CAUS($sign_insurance, $rate1);
                $CBP_Expedited_CAUS = $this->CBP_Expedited_CAUS($sign_insurance, $rate1);
                $CBP_Express_CAUS = $this->CBP_Express_CAUS($sign_insurance, $rate1);


                if($this->show_all_rates){

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
                 return Response()->json(['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS"']);
                
            }
        }elseif($from_country == "CA" &&  !in_array($to_country, ["CA","US"])){
            try {

                $rs = new Rocketship;
                // $rate1 = $rs->getDHLRates($d);
                // dd($rate1);
                $dhl = new DHLGLobal; 
                $rate1 = $dhl->getRates($d);

                $d['_signature'] = "false";
                $d['_insurance'] = "false";
                $rate1_normal = $dhl->getRates($d);


                $d['_signature'] = "true";
                $d['_insurance'] = "false";
                $rate1_signature = $dhl->getRates($d);
                // dd($rate1_signature);

                $d['_signature'] = "false";
                $d['_insurance'] = "true";
                $rate1_insurance = $dhl->getRates($d);
            
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
                    'rate1_normal' => $rate1_normal,
                    'rate1_signature' => $rate1_signature,
                    'rate1_insurance' => $rate1_insurance,

                    'rate2_normal' => $rate2_normal,
                    'rate2_signature' => $rate2_signature,
                    'rate2_insurance' => $rate2_insurance,
                ];
                $rates = [];

                $CBP_Saver_CAInt = $this->CBP_Saver_CAInt($sign_insurance, $rate1);

                $CBP_Expedited_CAInt = $this->CBP_Expedited_CAInt($sign_insurance, $rate2);
                $CBP_Express_CAInt = $this->CBP_Express_CAInt($sign_insurance, $rate2);

                
                if($this->show_all_rates){
                    $rate1 = [];
                    $rate2 = [];
                    $rate3 = [];


                  if($CBP_Saver_CAInt) $rate1 = $CBP_Saver_CAInt;
                  if($CBP_Expedited_CAInt) $rate2 = $CBP_Expedited_CAInt;
                  if($CBP_Express_CAInt) $rate3 = $CBP_Express_CAInt;
                  
                  $rates = array_merge($rate1, $rate2, $rate3);
                  // $rates = array_merge( $rate2, $rate3);

                }else{

                    if($CBP_Saver_CAInt) $rates[] = $CBP_Saver_CAInt;
                    if($CBP_Expedited_CAInt) $rates[] = $CBP_Expedited_CAInt;
                    if($CBP_Express_CAInt) $rates[] = $CBP_Express_CAInt;

                }

                $res =  $rates;

            

                // $rate1 = $rs->getUPSCARates($d);
                // $rate2 = $rs->getDHLRates($d);
                // // dd($rate2);

                // $a = $this->insertCarrier($rate1['data']['rates'],"UPS");
                // $b = $this->insertCarrier($rate2['data']['rates'],"DHL");
                // // dd($b);

                // // dd($rate1);
                // $data = $rate1;
                // $data['data']['rates'] = $this->ratesCompare($a, $b);

                // $data['data']['rates'] = $this->sortRates( $data['data']['rates']);
                // $data['data']['rates'] = $this->insertCarrier( $data['data']['rates'], "UPS");
                // $res = $data;
            } catch (\Exception $e) {
                 return Response()->json(['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS" and declare item details']);
            }

        }elseif($from_country == "US" && $to_country == "US"){
            try { 
                $rates = [];

                    if($this->show_all_rates){
                        if($this->CBP_Saver_USUS($d)) $rates = $this->CBP_Saver_USUS($d);

                    }else{
                        if($this->CBP_Saver_USUS($d)) $rates[] = $this->CBP_Saver_USUS($d);
                    }

                $res =  $rates;
                
                // // $rs->getSTAMPSRates($d);
                // $data = $rs->getSTAMPSRates($d);
                // $data['data']['rates'] = $this->sortRates( $data['data']['rates']);
                // $data['data']['rates'] = $this->insertCarrier( $data['data']['rates'], "USPS");
                // // dd($d['parcel_types']['parcel_type']);
                // $data['data']['rates'] = $this->filterUSPSRates($data['data']['rates'],  $d['usps_options_model']['upsps_option'], $d['parcel_types']['parcel_type']);
                // // dd($data['data']['rates']);
                // $res = $data;
            } catch (\Exception $e) {
                return Response()->json(['status'=>false,"message"=>'No rates available. Please review your shipment details"']);
            }
            
        }elseif($from_country == "US" && $to_country != "US"){

                
            try {

                $rates = [];
                if($this->show_all_rates){
                    if($this->CBP_Saver_USInt($d)) $rates = $this->CBP_Saver_USInt($d);
                }else{
                    if($this->CBP_Saver_USInt($d)) $rates[] = $this->CBP_Saver_USInt($d);

                }
                $res =  $rates;

                // $data = $rs->getUPSUSRates($d);
                // // dd($data);
                // $data['data']['rates'] = $this->sortRates( $data['data']['rates']);
                // $data['data']['rates'] = $this->insertCarrier( $data['data']['rates'], "UPS");
                // $res = $data;

            } catch (\Exception $e) {
                return Response()->json(['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS"']);
                
            }

        }


 
        $res = $this->postageRateTriggers($d, $rates);

        $_rates = $this->group_postage_tier($res);
        $_rates_cbp_saver = $this->sortShipstationRates($_rates['cbp_saver']);

        $_rates_cbp_expedited =  $this->sortShipstationRates($_rates['cbp_expedited']);
        $_rates_cbp_express = $this->sortShipstationRates($_rates['cbp_express']);

        if($this->show_all_rates){
            $res = array_merge($_rates_cbp_saver, $_rates_cbp_expedited, $_rates_cbp_express);
        }else{
            $res = array_merge([$_rates_cbp_saver[0]], [$_rates_cbp_expedited[0]], [$_rates_cbp_express[0]]);
        }

        $res = $this->sortShipstationRates($res);
        // $this->sortShipstationRates($_rates['cbp_saver']);
        

        // $rs->getUFedExRates($d);
        // $ss->test($d);
        // dd($res);

        return Response()->json(['status'=>true,"response"=>$res]);
    }


    public function ca_us_rates($d){


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

            $CBP_Saver_CAUS = $this->CBP_Saver_CAUS($sign_insurance, $rate1);
            $CBP_Expedited_CAUS = $this->CBP_Expedited_CAUS($sign_insurance, $rate1);
            $CBP_Express_CAUS = $this->CBP_Express_CAUS($sign_insurance, $rate1);


            if($this->show_all_rates){

              if($CBP_Saver_CAUS) $rate1 = $CBP_Saver_CAUS;
              if($CBP_Expedited_CAUS) $rate2 = $CBP_Expedited_CAUS;
              if($CBP_Express_CAUS) $rate3 = $CBP_Express_CAUS;

              $rates = array_merge($rate1, $rate2, $rate3);
            
            }else{

                if($CBP_Saver_CAUS) $rates[] = $CBP_Saver_CAUS;
                if($CBP_Expedited_CAUS) $rates[] = $CBP_Expedited_CAUS;
                if($CBP_Express_CAUS) $rates[] = $CBP_Express_CAUS;

            }

            return $rates;

        } catch (\Exception $e) {
             return [];
            
        }
    }


    public function us_us_rates($d){
        try { 
            $rates = [];

                if($this->show_all_rates){
                    if($this->CBP_Saver_USUS($d)) $rates = $this->CBP_Saver_USUS($d);

                }else{
                    if($this->CBP_Saver_USUS($d)) $rates[] = $this->CBP_Saver_USUS($d);
                }

            return $rates;
            
        } catch (\Exception $e) {
            return [];
        }
    }


    public function compare_CA_US_rates($d, $byCarrier=null){

        
        if(@$byCarrier){
            // return [];
            if($d['postage_option_model']['details']['address_flow'] == "US_US"){
                $d['ship_from_address_model'] = CBPAddress::where("country","US")->get()->first()->toArray();

                if($d['postage_option_model']['details']['carrier'] == "UPS"){

                    return $this->USUS_UPS($d);

                }else{
                    $rate[] = $this->USUS_USPS($d);
                    return $rate;
                    
                }

            }else{
                $d['ship_from_address_model'] = CBPAddress::where("country","CA")->where("province","ON")->get()->first()->toArray();
                return $this->CAUS_UPS($d);

            }
        }


        
        
        $parcel_types = @$d['parcel_types'];
        if( $parcel_types['parcel_type'] == "Box"){
            if($parcel_types['usps_box_status'] == "yes"){
                $d['ship_from_address_model'] = CBPAddress::where("country","US")->get()->first()->toArray();
                $us_rates = $this->us_us_rates($d);
                return $this->sortShipstationRates($us_rates);
                
            }else{

                $d['ship_from_address_model'] = CBPAddress::where("country","CA")->where("province","ON")->get()->first()->toArray();
                $ca_rates = $this->ca_us_rates($d);
                $CA_postage_rate = $this->sortShipstationRates($ca_rates);
                // dd($CA_postage_rate);

                // $CA_postage_rate = [["desc"=>"UPS Standard","rate"=>47.79,"negotiated_rate"=>14.94,"currency"=>"CAD","service_code"=>"11","est_delivery_time"=>"2019-03-28T23=>30=>00-00=>00","delivery_days"=>3,"package_type"=>"","rate_detail"=>[["amount"=>47.79,"currency"=>"CAD","type"=>"TransportationCharges"],["amount"=>0,"currency"=>"","type"=>"BaseServiceCharge"]],"billing_weight"=>13,"carrier"=>"UPS","insurance_fee"=>"0.00","signature_fee"=>"0.00","postage_type"=>"CBP Saver","postage_type_code"=>"cbp_saver","total"=>14.94,"estimated_delivery"=>"2-6 Days","address_flow"=>"CA_US"],["desc"=>"UPS Worldwide Saver","rate"=>244.9,"negotiated_rate"=>52.76,"currency"=>"CAD","service_code"=>"65","est_delivery_time"=>"2019-03-26T23=>30=>00-00=>00","delivery_days"=>1,"package_type"=>"","rate_detail"=>[["amount"=>244.9,"currency"=>"CAD","type"=>"TransportationCharges"],["amount"=>0,"currency"=>"","type"=>"BaseServiceCharge"]],"billing_weight"=>13,"carrier"=>"UPS","insurance_fee"=>"0.00","signature_fee"=>"0.00","postage_type"=>"CBP Express","postage_type_code"=>"cbp_express","total"=>52.76,"estimated_delivery"=>"1-3 Days","address_flow"=>"CA_US"],["desc"=>"UPS Worldwide Express","rate"=>257.65,"negotiated_rate"=>55.43,"currency"=>"CAD","service_code"=>"07","est_delivery_time"=>"2019-03-26T10=>30=>00-00=>00","delivery_days"=>1,"package_type"=>"","rate_detail"=>[["amount"=>257.65,"currency"=>"CAD","type"=>"TransportationCharges"],["amount"=>0,"currency"=>"","type"=>"BaseServiceCharge"]],"billing_weight"=>13,"carrier"=>"UPS","insurance_fee"=>"0.00","signature_fee"=>"0.00","postage_type"=>"CBP Expedited","postage_type_code"=>"cbp_expedited","total"=>55.43,"estimated_delivery"=>"2-4 Days","address_flow"=>"CA_US"],["desc"=>"UPS Worldwide Express","rate"=>257.65,"negotiated_rate"=>55.43,"currency"=>"CAD","service_code"=>"07","est_delivery_time"=>"2019-03-26T10=>30=>00-00=>00","delivery_days"=>1,"package_type"=>"","rate_detail"=>[["amount"=>257.65,"currency"=>"CAD","type"=>"TransportationCharges"],["amount"=>0,"currency"=>"","type"=>"BaseServiceCharge"]],"billing_weight"=>13,"carrier"=>"UPS","insurance_fee"=>"0.00","signature_fee"=>"0.00","postage_type"=>"CBP Express","postage_type_code"=>"cbp_express","total"=>55.43,"estimated_delivery"=>"1-3 Days","address_flow"=>"CA_US"]];





                $d['ship_from_address_model'] = CBPAddress::where("country","US")->get()->first()->toArray();
                $us_rates = $this->us_us_rates($d);
                $US_postage_rate = $this->sortShipstationRates($us_rates);
                
                // $US_postage_rate = [["desc"=>"USPS Priority Mail","rate"=>7.61,"currency"=>"USD","service_code"=>"US-PM","est_delivery_time"=>"2019-03-27","delivery_days"=>2,"package_type"=>"Large Envelope or Flat","rate_detail"=>[["amount"=>0,"currency"=>"USD","type"=>"SC-A-INS"],["amount"=>0,"currency"=>"USD","type"=>"SC-A-HP"]],"carrier"=>"USPS","insurance_fee"=>"0.00","signature_fee"=>"0.00","postage_type"=>"CBP Saver","postage_type_code"=>"cbp_saver","total"=>7.61,"estimated_delivery"=>"2-6 Days","address_flow"=>"US_US"],["desc"=>"USPS Priority Mail","rate"=>7.61,"currency"=>"USD","service_code"=>"US-PM","est_delivery_time"=>"2019-03-27","delivery_days"=>2,"package_type"=>"Package","rate_detail"=>[["amount"=>0,"currency"=>"USD","type"=>"SC-A-INS"],["amount"=>0,"currency"=>"USD","type"=>"SC-A-HP"]],"carrier"=>"USPS","insurance_fee"=>"0.00","signature_fee"=>"0.00","postage_type"=>"CBP Saver","postage_type_code"=>"cbp_saver","total"=>7.61,"estimated_delivery"=>"2-6 Days","address_flow"=>"US_US"],["desc"=>"USPS Priority Mail","rate"=>7.61,"currency"=>"USD","service_code"=>"US-PM","est_delivery_time"=>"2019-03-27","delivery_days"=>2,"package_type"=>"Thick Envelope","rate_detail"=>[["amount"=>0,"currency"=>"USD","type"=>"SC-A-INS"],["amount"=>0,"currency"=>"USD","type"=>"SC-A-HP"]],"carrier"=>"USPS","insurance_fee"=>"0.00","signature_fee"=>"0.00","postage_type"=>"CBP Saver","postage_type_code"=>"cbp_saver","total"=>7.61,"estimated_delivery"=>"2-6 Days","address_flow"=>"US_US"],["desc"=>"UPS Ground","rate"=>17.7,"currency"=>"USD","service_code"=>"03","est_delivery_time"=>"2019-03-27T23=>00=>00-00=>00","delivery_days"=>2,"package_type"=>"","rate_detail"=>[["amount"=>17.7,"currency"=>"USD","type"=>"TransportationCharges"],["amount"=>0,"currency"=>"USD","type"=>"376"],["amount"=>1.2,"currency"=>"USD","type"=>"375"],["amount"=>12.55,"currency"=>"USD","type"=>"BaseServiceCharge"]],"billing_weight"=>13,"carrier"=>"UPS","insurance_fee"=>"0.00","signature_fee"=>"0.00","postage_type"=>"CBP Saver","postage_type_code"=>"cbp_saver","total"=>17.7,"estimated_delivery"=>"2-6 Days","address_flow"=>"US_US"],["desc"=>"USPS Express Mail","rate"=>25.42,"currency"=>"USD","service_code"=>"US-XM","est_delivery_time"=>"","package_type"=>"Package","rate_detail"=>[["amount"=>0,"currency"=>"USD","type"=>"SC-A-INS"],["amount"=>0,"currency"=>"USD","type"=>"SC-A-HP"]],"carrier"=>"USPS","insurance_fee"=>"0.00","signature_fee"=>"0.00","postage_type"=>"CBP Saver","postage_type_code"=>"cbp_saver","total"=>25.42,"estimated_delivery"=>"2-6 Days","address_flow"=>"US_US"],["desc"=>"USPS Express Mail","rate"=>25.42,"currency"=>"USD","service_code"=>"US-XM","est_delivery_time"=>"","package_type"=>"Thick Envelope","rate_detail"=>[["amount"=>0,"currency"=>"USD","type"=>"SC-A-INS"],["amount"=>0,"currency"=>"USD","type"=>"SC-A-HP"]],"carrier"=>"USPS","insurance_fee"=>"0.00","signature_fee"=>"0.00","postage_type"=>"CBP Saver","postage_type_code"=>"cbp_saver","total"=>25.42,"estimated_delivery"=>"2-6 Days","address_flow"=>"US_US"]];

                


                $rates = $this->compare_postage_tier($CA_postage_rate, $US_postage_rate);


                // $rates = array_merge($ca_rates, $us_rates);

                // if((float)$CA_lowest_postage_rate < (float)$US_lowest_postage_rate){
                //     $rates =  $CA_postage_rate;
                // }else{
                //     $rates =  $US_postage_rate;
                // }
                
                return $rates;
            }

        }else{
            
            $d['ship_from_address_model'] = CBPAddress::where("country","US")->get()->first()->toArray();
            $us_rates = $this->us_us_rates($d);
            return $this->sortShipstationRates($us_rates);
        }




    }


    public function compare_postage_tier($rate1, $rate2){
        // $_rate1 = $this->sortShipstationRates($rate1);
        // $_rate2 = $this->sortShipstationRates($rate2);

        $cbp_saver = [];
        $cbp_expedited = [];
        $cbp_express = [];

        $_rate1 = $this->group_postage_tier($rate1);
        $_rate2 = $this->group_postage_tier($rate2);

        $cbp_saver = $this->handle_postage_tier($_rate1, $_rate2, "cbp_saver");
        // dd($cbp_saver); 
        $cbp_expedited = $this->handle_postage_tier($_rate1, $_rate2, "cbp_expedited");
        $cbp_express = $this->handle_postage_tier($_rate1, $_rate2, "cbp_express");

        return array_merge($cbp_saver, $cbp_expedited, $cbp_express );   

    }



    public function handle_postage_tier($_rate1, $_rate2, $posatage_type){
        if(count($_rate1[$posatage_type]) > count($_rate2[$posatage_type])){
            return $this->arrange_by_lowest($_rate1[$posatage_type], $_rate2[$posatage_type]);
        }else{
            return $this->arrange_by_lowest($_rate2[$posatage_type], $_rate1[$posatage_type]);
        }
    }

    public function arrange_by_lowest($rate1, $rate2){
        if(count($rate1) > 0 && count($rate2) == 0) return $rate1;
        if(count($rate2) > 0 && count($rate1) == 0) return $rate2;
        if(count($rate2) == 0 && count($rate1) == 0) return [];

        foreach ($rate1 as $key => $value) {
                if(count($rate2) > 0){
                    foreach ($rate2 as $k => $v) {
                       if($v['total'] < $value['total']){
                            $rate1[$key] = $v;
                            unset($rate2[$k]);
                       }
                    }
                }
            }

            return $rate1;
    }

    public function group_postage_tier($rates){

        $cbp_saver = [];
        $cbp_expedited = [];
        $cbp_express = [];

        foreach ($rates as $key => $value) {
            if($value['postage_type_code'] == "cbp_saver"){
                $cbp_saver[] = $value;
            }

            else if($value['postage_type_code'] == "cbp_expedited"){
                $cbp_expedited[] = $value;
            }

            else if($value['postage_type_code'] == "cbp_express"){
                $cbp_express[] = $value;
            }
        }

        $tier["cbp_saver"] = $cbp_saver;
        $tier["cbp_expedited"] = $cbp_expedited;
        $tier["cbp_express"] = $cbp_express;


        return $tier;

    }




    // public function getRates(Request $r){
    //     $d = $r->toArray();
    //     $rs =  new Rocketship;
    //     $ss =  new ShipStation;

    //     // dd($ss->createShipment($d));

    //     $from_country = $d['ship_from_address_model']['country'];
    //     $to_country = $d['recipient_model']['country'];


    //     $rates = [];
    //     if($from_country == "CA" && $to_country == "CA"){
                
    //         try {
    //             if($this->CBP_Saver_CACA($d))  $rates[] = $this->CBP_Saver_CACA($d);
    //             if($this->CBP_Expedited_CACA($d))  $rates[] = $this->CBP_Expedited_CACA($d);
    //             if($this->CBP_Express_CACA($d))  $rates[] = $this->CBP_Express_CACA($d);

    //         } catch (\Exception $e) {
    //              return Response()->json(['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS"']);
                
    //         }

    //         // dd($res);

    //     }else if($from_country == "CA" && $to_country == "US"){
    //         try {

    //             if($this->CBP_Saver_CAUS($d)) $rates[] = $this->CBP_Saver_CAUS($d);
    //             if($this->CBP_Expedited_CAUS($d)) $rates[] = $this->CBP_Expedited_CAUS($d);
    //             if($this->CBP_Express_CAUS($d)) $rates[] = $this->CBP_Express_CAUS($d);


    //         } catch (\Exception $e) {
    //              return Response()->json(['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS"']);
                
    //         }

    //     }elseif($from_country == "CA" && $to_country != "US"){
                
    //         try {
    //             if($this->CBP_Saver_CAInt($d)) $rates[] = $this->CBP_Saver_CAInt($d);
    //             if($this->CBP_Expedited_CAInt($d)) $rates[] = $this->CBP_Expedited_CAInt($d);
    //             if($this->CBP_Express_CAInt($d)) $rates[] = $this->CBP_Express_CAInt($d);

    //         } catch (\Exception $e) {
    //              return Response()->json(['status'=>false,"message"=>'No rates available. Please check if you have set and selected the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS" and declare item details']);
    //         }

    //     }elseif($from_country == "US" && $to_country == "US"){

    //             if($this->CBP_Saver_USUS($d)) $rates[] = $this->CBP_Saver_USUS($d);

    //         try {
    //         } catch (\Exception $e) {
    //             return Response()->json(['status'=>false,"message"=>'No rates available. Please review your shipment details"']);
    //         }
            
    //     }elseif($from_country == "US" && $to_country != "US"){

    //             if($this->CBP_Saver_USInt($d)) $rates[] = $this->CBP_Saver_USInt($d);

    //         try {
    //         } catch (\Exception $e) {
    //             return Response()->json(['status'=>false,"message"=>'No rates available. Please review your shipment details"']);
    //         }
            
    //     }

    //     // $res['data']['rates'] = $this->postageRateTriggers($d, $res['data']['rates']);
    //     // 

    //     // $rs->getUFedExRates($d);
    //     // $ss->test($d);
    //     // dd($res);

    //     return Response()->json(['status'=>true,"response"=>$rates]);
    // }


    private function sortShipstationRates($rates){

        // dd($rates);
        //SORT ACCORDING TO COST
        for ($i=0; $i < count($rates) ; $i++) { 
            for ($z=0; $z < count($rates) ; $z++) { 
                if($z < count($rates) - 1){

                    if(@$rates[$z]['total'] > @$rates[$z + 1]['total']){
                        $hold =  $rates[$z];
                        $rates[$z] = $rates[$z + 1];
                        $rates[$z + 1] = $hold;
                    }
                }
            }

        }

        // dd($rates);


        if($this->show_all_rates){
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

    private function filterRocketshipRatesByServiceCode($rates, $serviceCode, $carrier=null, $packageType=null){
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

    private function filterUSPSByServiceCodeAndPackageType($rates, $serviceCode, $packageType, $carrier=null){
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


    private function expedited_zone_skip($d){
        $rs = new Rocketship;
        $rate1 = $rs->getCanadaPostRates($d);
    }


    private function CBP_Saver_CACA($sign_insurance, $rate1, $rate2, $rate3){

        // $rs = new Rocketship;
        // $rate1 = $rs->getCanadaPostRates($data);
        // $rate2 = $rs->getUPSCARates($data);
        // $rate3 = $rs->getFedExRates($data);
        $rates = [];


        if(@$rate1['data']['rates']){   
            $d = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"DOM.EP", "CANADA POST"); //Expedited Parcel
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
            $d = $this->filterRocketshipRatesByServiceCode($rate2['data']['rates'],"11","UPS"); //UPS Standard
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate2_normal'], $sign_insurance['rate2_insurance'], "11","");
                $d['signature_fee'] = splitSignature($sign_insurance['rate2_normal'], $sign_insurance['rate2_signature'], "11","");
                $rates[] = $d; 
            }

        }
        if(@$rate3['data']['rates']){
            $d = $this->filterRocketshipRatesByServiceCode($rate3['data']['rates'],"FEDEX_GROUND","FEDEX"); //edEx Ground®
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate3_normal'], $sign_insurance['rate3_insurance'], "FEDEX_GROUND","YOUR_PACKAGING");
                $d['signature_fee'] = splitSignature($sign_insurance['rate3_normal'], $sign_insurance['rate3_signature'], "FEDEX_GROUND","YOUR_PACKAGING");

                $rates[] = $d; 
            }
        }

       // $_rates = $this->sortShipstationRates($rates);

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

       // $_rates = $this->sortShipstationRates($rates);


       //display all rates
        $_rates = $rates;

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
            $_rates[$key]['estimated_delivery'] = "2-6 Days";

       }

       // if($this->show_all_rates){
       // }else{

       //     //hold display lowest single rate
       //     if($_rates){
       //          $_rates['postage_type'] = "CBP Saver";   
       //          $_rates['postage_type_code'] = "cbp_saver";   
       //          $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
       //          $_rates['estimated_delivery'] = "2-6 Days";
       //     }
       // }


       
       return $_rates;


    }


    private function CBP_Saver_CACA_ZoneSkipped($sign_insurance, $rate){

        // $rs = new Rocketship;
        // $rate1 = $rs->getCanadaPostRates($data);
        // $rate2 = $rs->getUPSCARates($data);
        // $rate3 = $rs->getFedExRates($data);
        $rates = [];

        if(@$rate['data']['rates']){
            $d = $this->filterRocketshipRatesByServiceCode($rate['data']['rates'],"DOM.EP", "CANADA POST"); //Expedited Parcel
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate4_normal'], $sign_insurance['rate4_insurance'], "DOM.EP","");
                $d['signature_fee'] = splitSignature($sign_insurance['rate4_normal'], $sign_insurance['rate4_signature'], "DOM.EP","");
                if($d['insurance_fee'] && $d['signature_fee']){
                    $d['rate'] = number_format($d['rate'] + $d['signature_fee'], 2 ); 
                }
                $rates[] = $d; 
            }
        }

       

       // $_rates = $this->sortShipstationRates($rates);

        $_rates = $rates;
       //display all rates

       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Saver + Zone Skip";   
            $_rates[$key]['postage_type_code'] = "cbp_saver_zone_skip";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "2-6 Days";
            $_rates[$key]['zone_skipped'] = true;

       }

       // if($this->show_all_rates){
       // }else{

       //     //hold display lowest single rate
       //     if($_rates){
       //          $_rates['postage_type'] = "CBP Saver + Zone Skip";   
       //          $_rates['postage_type_code'] = "cbp_saver_zone_skip";   
       //          $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
       //          $_rates['estimated_delivery'] = "2-6 Days";
       //          $_rates[$key]['zone_skipped'] = true;
       //     }
       // }


       
       return $_rates;


    }

    private function CBP_Saver_CAUS($sign_insurance, $rate1){
        // dd($rate1);
        // $rs = new Rocketship;
        // $rate1 = $rs->getUPSCARates($data);
        // $rate2 = $rs->getDHLRates($data);
        // dd($rate2);
        $rates = [];
        if(@$rate1['data']['rates']){
            $d = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"11","UPS"); //UPS Standard
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "11","");
                $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "11","");
                $rates[] = $d; 
            }

        }

        // if(@$rate2['data']['rates']){
        //     $d = $this->filterRocketshipRatesByServiceCode($rate2['data']['rates'],"H","DHL"); //ECONOMY SELECT
        //     if($d) { $rates[] = $d; }

        // }


       // $_rates = $this->sortShipstationRates($rates);
       // dd($_rates);
        // $ss =  new ShipStation;
        // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_standard_international");
        // $DHL_rate = $ss->getShipStationsRatesUS($data,  "dhl_global_mail", "globalmail_packet_standard");

        // $rates = [];
        // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;
        // if(@$DHL_rate[0]['shipmentCost']) $rates[] = $DHL_rate;

        // $_rates = $this->sortShipstationRates($rates);
        $_rates = $rates;
       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Saver";   
            $_rates[$key]['postage_type_code'] = "cbp_saver";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "2-6 Days";
            $_rates[$key]['address_flow'] = "CA_US";
       }
       // if($this->show_all_rates){
       // }else{

       //     //hold display lowest single rate
       //     if($_rates){
       //          $_rates['postage_type'] = "CBP Saver";   
       //          $_rates['postage_type_code'] = "cbp_saver";   
       //          $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
       //          $_rates['estimated_delivery'] = "2-6 Days";
       //          $_rates['address_flow'] = "CA_US";
       //     }
       // }

        return $_rates;
    
    }

    private function CBP_Saver_USUS($data){

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
            $d = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"03","UPS"); //UPS GRound
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
                $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-FC", "Large Envelope or Flat","USPS"); //USPS First class
               
                if($d) { 
                    $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance, "US-FC","Large Envelope or Flat");
                    $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature, "US-FC","Large Envelope or Flat");
                    $rates[] = $d; 
                }

                $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-FC", "Package","USPS"); //USPS First class
                if($d) { 
                    $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-FC", "Package");
                    $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-FC", "Package");
                    $rates[] = $d; 
                }

                $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-FC", "Thick Envelope","USPS"); //USPS First class
                if($d) { 
                    $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-FC", "Thick Envelope");
                    $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-FC", "Thick Envelope");
                    $rates[] = $d; 
                }



                //USPS PRIORITY

                if($dimsWeight <= 84){

                    

                    // $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-PM", "Large Envelope or Flat","USPS"); //USPS PRIORITY PACKAGE
                    // if($d) { 
                    //     $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-PM", "Large Envelope or Flat");
                    //     $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-PM", "Large Envelope or Flat");
                    //     $rates[] = $d; 
                    // }

                    $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-PM", "Package","USPS"); //USPS PRIORITY PACKAGE
                    if($d) { 
                        $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-PM", "Package");
                        $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-PM", "Package");
                        $rates[] = $d; 
                    }

                    // $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-PM", "Thick Envelope","USPS"); //USPS PRIORITY PACKAGE
                    // if($d) { 
                    //     $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-PM", "Thick Envelope");
                    //     $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-PM", "Thick Envelope");
                    //     $rates[] = $d; 
                    // }

                }else if($dimsWeight > 84 && $dimsWeight <= 108){

                     $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-PM", "Large Package","USPS"); //USPS PRIORITY PACKAGE
                    if($d) { 
                        $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-PM", "Large Package");
                        $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-PM", "Large Package");
                        $rates[] = $d; 
                    }
                }


                if($dimsWeight <= 108){
                    //USPS EPRESS MAIL

                    #TEMPORARILY REMOVED
                    // $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-XM", "Large Envelope or Flat","USPS");
                    // if($d) { 
                    //     $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-XM", "Large Envelope or Flat");
                    //     $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-XM", "Large Envelope or Flat");
                    //     $rates[] = $d; 
                    // }

                    $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-XM", "Package","USPS"); 
                    if($d) { 
                        $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-XM", "Package");
                        $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-XM", "Package");
                        $rates[] = $d; 
                    }

                    // $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],"US-XM", "Thick Envelope","USPS");
                    // if($d) { 
                    //     $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,"US-XM", "Thick Envelope");
                    //     $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,"US-XM", "Thick Envelope");
                    //     $rates[] = $d; 
                    // }
                }


            }else if($data['parcel_types']['parcel_type'] == "Box" && $data['parcel_types']['usps_box_status'] == "yes"){
                $rates = []; // this will not include rates from USPS
                $options = explode("|", $data['usps_options_model']['usps_options']);
                $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],$options[0], $options[1],"USPS"); //USPS PRIORITY PACKAGE
                if($d) { 
                    $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,$options[0], $options[1]);
                    $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,$options[0], $options[1]);
                    $rates[] = $d; 
                }

            }else if($data['parcel_types']['parcel_type'] == "Letter" ){

                $options = explode("|", $data['usps_options_model']['usps_options']);
                $d = $this->filterUSPSByServiceCodeAndPackageType($rate2['data']['rates'],$options[0], $options[1],"USPS"); //USPS PRIORITY PACKAGE
                if($d) { 
                    $d['insurance_fee'] = splitInsurance($rate2_normal, $rate2_insurance,$options[0], $options[1]);
                    $d['signature_fee'] = splitSignature($rate2_normal, $rate2_signature,$options[0], $options[1]);
                    $rates[] = $d; 
                }

            }else{ $d = []; }

        
            

        }


       // $_rates = $this->sortShipstationRates($rates);
        $_rates = $rates;

       foreach ($_rates as $key => $value) {


            $_rates[$key]['postage_type'] = "CBP Saver";   
            $_rates[$key]['postage_type_code'] = "cbp_saver";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "2-6 Days";
            $_rates[$key]['address_flow'] = "US_US";

       }

       // if($this->show_all_rates){
       // }else{

       //      // if($_rates["carrier"] == "USPS"){
       //      //     if(@$value['rate_detail']){
       //      //         $_rates[$key]['signature_fee'] = $value['rate_detail'][0]['amount'];   
       //      //     }
       //      // }

       //     //hold display lowest single rate
       //     if($_rates){
       //          $_rates['postage_type'] = "CBP Saver";   
       //          $_rates['postage_type_code'] = "cbp_saver";   
       //          $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
       //          $_rates['estimated_delivery'] = "2-6 Days";
       //          $_rates['address_flow'] = "US_US";
                
       //     }
       // }

        return $_rates;

        // $ss =  new ShipStation;
        // $USPS_rate = $ss->getShipStationsRatesUS($data,  "stamps_com", "usps_priority_mail");
        // $UPS_rate = $ss->getShipStationsRatesUS($data,  "ups", "ups_standard");

        // $rates = [];
        // if(@$USPS_rate[0]['shipmentCost']) $rates[] = $USPS_rate;
        // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;

        // $_rates = $this->sortShipstationRates($rates);

        // if($_rates){
        //     $_rates['postageType'] = "CBP Saver";
        //     $_rates['postageCurrency'] = "USD";
        //     $_rates['total'] = $_rates['shipmentCost'];
        //     $_rates['estimated_delivery'] = "2 days or more";

        // }

        // return $_rates;
    
    }

    private function CBP_Saver_CAInt( $sign_insurance, $rate1){
        // $rs = new Rocketship;
        // $rate1 = $rs->getDHLRates($data);
        // dd($rate1);
       
        $rates = [];
        if(@$rate1['data']['rates']){
            $d = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"54", "DHL"); //DHL GM Parcel Priority
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "54","");
                $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "54","");
                $rates[] = $d; 
            }

            $d = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"29", "DHL"); //DHL GM PACKET PLUS"
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "29","");
                $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "29","");
                $rates[] = $d; 
            }
        }


       // $_rates = $this->sortShipstationRates($rates);

        $_rates = $rates;

       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Saver";   
            $_rates[$key]['postage_type_code'] = "cbp_saver";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "2-6 Days";
       }
       // if($this->show_all_rates){
       // }else{

       //     //hold display lowest single rate
       //     if($_rates){
       //          $_rates['postage_type'] = "CBP Saver";   
       //          $_rates['postage_type_code'] = "cbp_saver";   
       //          $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
       //          $_rates['estimated_delivery'] = "2-6 Days";
       //     }
       // }

        return $_rates;


        // $ss =  new ShipStation;
        // $DHL_rate = $ss->getShipStationsRatesUS($data,  "dhl_global_mail", "globalmail_packet_standard");

        // $rates = [];
        // if(@$DHL_rate[0]['shipmentCost']) $rates[] = $DHL_rate;

        // $_rates = $this->sortShipstationRates($rates);

        // if($_rates){
        //     $_rates['postageType'] = "CBP Saver";
        //     $_rates['postageCurrency'] = "CAD";
        //     $_rates['total'] = $_rates['shipmentCost'];
        //     $_rates['estimated_delivery'] = "2 days or more";

        // }

        // return $_rates;
    
    }

    private function CBP_Saver_USInt($data){

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
            $d = $this->filterUSPSByServiceCodeAndPackageType($rate1['data']['rates'],"US-PMI","Package","USPS"); //USPS Priority Mail International
            if($d) { 
                $d['insurance_fee'] = splitInsurance($rate1_normal, $rate1_insurance, "US-PMI","Package");
                $d['signature_fee'] = splitSignature($rate1_normal, $rate1_signature, "US-PMI","Package");
                $rates[] = $d; 
            }


            $d = $this->filterUSPSByServiceCodeAndPackageType($rate1['data']['rates'],"US-EMI","Package","USPS"); //USPS Priority Mail International
            if($d) { 
                $d['insurance_fee'] = splitInsurance($rate1_normal, $rate1_insurance, "US-EMI","Package");
                $d['signature_fee'] = splitSignature($rate1_normal, $rate1_signature, "US-EMI","Package");
                $rates[] = $d; 
            }


            $d = $this->filterUSPSByServiceCodeAndPackageType($rate1['data']['rates'],"US-FCI","Package","USPS"); //USPS First Class Mail International
            if($d) { 
                $d['insurance_fee'] = splitInsurance($rate1_normal, $rate1_insurance, "US-FCI","Package");
                $d['signature_fee'] = splitSignature($rate1_normal, $rate1_signature, "US-FCI","Package");
                $rates[] = $d; 
            }
        }


       // $_rates = $this->sortShipstationRates($rates);

        $_rates = $rates;

       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Saver";   
            $_rates[$key]['postage_type_code'] = "cbp_saver";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "2-6 Days";
       }
       // if($this->show_all_rates){
       // }else{

       //     //hold display lowest single rate
       //     if($_rates){
       //          $_rates['postage_type'] = "CBP Saver";   
       //          $_rates['postage_type_code'] = "cbp_saver";   
       //          $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
       //          $_rates['estimated_delivery'] = "2-6 Days";
       //     }
       // }

        return $_rates;

        // $ss =  new ShipStation;
        // $USPS_rate = $ss->getShipStationsRatesUS($data,  "stamps_com", "usps_priority_mail_international");

        // $rates = [];
        // if(@$USPS_rate[0]['shipmentCost']) $rates[] = $USPS_rate;

        // $_rates = $this->sortShipstationRates($rates);

        // if($_rates){
        //     $_rates['postageType'] = "CBP Saver";
        //     $_rates['postageCurrency'] = "USD";
        //     $_rates['total'] = $_rates['shipmentCost'];
        //     $_rates['estimated_delivery'] = "2 days or more";
        // }

        // return $_rates;
    
    }




    private function CBP_Expedited_CACA($sign_insurance, $rate1, $rate2){
        // $rs = new Rocketship;
        // $rate1 = $rs->getCanadaPostRates($data);
        // $rate2 = $rs->getUPSCARates($data);

        $rates = [];
        if(@$rate1['data']['rates']){
            $d = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"DOM.XP","CANADA POST"); //Canada Post Xpresspost
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "DOM.XP","");
                $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "DOM.XP","");
                $rates[] = $d; 
            }
        }

        if(@$rate2['data']['rates']){
            $d = $this->filterRocketshipRatesByServiceCode($rate2['data']['rates'],"13",'UPS'); //UPS Express Saver / UPS Next Day Air Saver
            // dd($d);
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate2_normal'], $sign_insurance['rate2_insurance'], "13","");
                $d['signature_fee'] = splitSignature($sign_insurance['rate2_normal'], $sign_insurance['rate2_signature'], "13","");
                $rates[] = $d; 
            }

        }


       // $_rates = $this->sortShipstationRates($rates);

        $_rates = $rates;


       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Expedited";   
            $_rates[$key]['postage_type_code'] = "cbp_expedited";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "2-4 Days";
       }

       // if($this->show_all_rates){
       // }else{

       //     //hold display lowest single rate
       //     if($_rates){
       //           $_rates['postage_type'] = "CBP Expedited";   
       //          $_rates['postage_type_code'] = "cbp_expedited";   
       //          $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
       //          $_rates['estimated_delivery'] = "2-4 Days";
       //     }
       // }
       
       return $_rates;

        // $ss =  new ShipStation;
        // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_next_day_air_saver");
        // $CANADA_rate = $ss->getShipStationsRatesCA($data,  "canada_post", "xpresspost");

        // $rates = [];
        // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;
        // if(@$CANADA_rate[0]['shipmentCost']) $rates[] = $CANADA_rate;

        // $_rates = $this->sortShipstationRates($rates);

        // if($_rates){
        //     $_rates['postageType'] = "CBP Expedited";
        //     $_rates['postageCurrency'] = "CAD";
        //     $_rates['total'] = $_rates['shipmentCost'];
        //     $_rates['estimated_delivery'] = "1 to 2 days";
        // }

        // return $_rates;

    }

    private function CBP_Expedited_CAUS($sign_insurance, $rate1){

        // $rs = new Rocketship;
        // $rate1 = $rs->getUPSCARates($data);
        // dd($rate1);
        $rates = [];

        if(@$rate1['data']['rates']){
            $d = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"08",'UPS'); //UPS expedited
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "08","");
                $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "08","");
                $rates[] = $d; 
            }

        }


       // $_rates = $this->sortShipstationRates($rates);
        $_rates = $rates;

       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Expedited";   
            $_rates[$key]['postage_type_code'] = "cbp_expedited";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "2-4 Days";
            $_rates[$key]['address_flow'] = "CA_US";
       }

       // if($this->show_all_rates){
       // }else{

       //     //hold display lowest single rate
       //     if($_rates){
       //           $_rates['postage_type'] = "CBP Expedited";   
       //          $_rates['postage_type_code'] = "cbp_expedited";   
       //          $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
       //          $_rates['estimated_delivery'] = "2-4 Days";
       //          $_rates['address_flow'] = "CA_US";
       //     }
       // }

       return $_rates;

        // $ss =  new ShipStation;
        // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_next_day_air_international");
        // $rates = [];
        // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;

        // $_rates = $this->sortShipstationRates($rates);

        // if($_rates){
        //     $_rates['postageType'] = "CBP Expedited";
        //     $_rates['postageCurrency'] = "CAD";
        //     $_rates['total'] = $_rates['shipmentCost'];
        //     $_rates['estimated_delivery'] = "1 to 2 days";
        // }

        // return $_rates;

    }

    private function CBP_Expedited_CAInt($sign_insurance, $rate1){
        // $rs = new Rocketship;
        // $rate1 = $rs->getUPSCARates($data);

        $rates = [];
        if(@$rate1['data']['rates']){
             $d = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"08",'UPS'); //UPS Worldwide Expedited
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate2_normal'], $sign_insurance['rate2_insurance'], "08","");
                $d['signature_fee'] = splitSignature($sign_insurance['rate2_normal'], $sign_insurance['rate2_signature'], "08","");
                $rates[] = $d; 
                // dd($sign_insurance['rate2_normal']);
            }

        }


       // $_rates = $this->sortShipstationRates($rates);
        $_rates = $rates;

       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Expedited";   
            $_rates[$key]['postage_type_code'] = "cbp_expedited";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "2-4 Days";
       }
       // if($this->show_all_rates){
       // }else{

       //     //hold display lowest single rate
       //     if($_rates){
       //           $_rates['postage_type'] = "CBP Expedited";   
       //          $_rates['postage_type_code'] = "cbp_expedited";   
       //          $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
       //          $_rates['estimated_delivery'] = "2-4 Days";
       //     }
       // }
       
       return $_rates;

        // $ss =  new ShipStation;
        // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_worldwide_expedited");

        // $rates = [];
        // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;

        // $_rates = $this->sortShipstationRates($rates);

        // if($_rates){
        //     $_rates['postageType'] = "CBP Expedited";
        //     $_rates['postageCurrency'] = "CAD";
        //     $_rates['total'] = $_rates['shipmentCost'];
        //     $_rates['estimated_delivery'] = "1 to 2 days";
        // }

        // return $_rates;

    }


    private function CBP_Express_CACA($sign_insurance, $rate1, $rate2){
        // $rs = new Rocketship;
        // $rate1 = $rs->getCanadaPostRates($data);
        // $rate2 = $rs->getUPSCARates($data);

        // dd($rate2);

        $rates = [];
        if(@$rate1['data']['rates']){
            $d = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"DOM.PC","CANADA POST"); //Canada Post priority
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "DOM.PC","");
                $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "DOM.PC","");
                $rates[] = $d; 
            }
        }

        if(@$rate2['data']['rates']){
            $d = $this->filterRocketshipRatesByServiceCode($rate2['data']['rates'],"13","UPS"); //UPS Express Saver / UPS Next Day Air Saver
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate2_normal'], $sign_insurance['rate2_insurance'], "13","");
                $d['signature_fee'] = splitSignature($sign_insurance['rate2_normal'], $sign_insurance['rate2_signature'], "13","");
                $rates[] = $d; 
            }

        }


       // $_rates = $this->sortShipstationRates($rates);
        $_rates = $rates;

       foreach ($_rates as $key => $value) {
           # code...
            $_rates[$key]['postage_type'] = "CBP Express";   
            $_rates[$key]['postage_type_code'] = "cbp_express";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "1-3 Days";
       }

       // if($this->show_all_rates){
       // }else{

       //     //hold display lowest single rate
       //     if($_rates){
       //           $_rates['postage_type'] = "CBP Express";   
       //          $_rates['postage_type_code'] = "cbp_express";   
       //          $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
       //          $_rates['estimated_delivery'] = "1-3 Days";
       //     }
       // }
       
       return $_rates;

        // $ss =  new ShipStation;
        // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_next_day_air_saver");
        // $CANADA_rate = $ss->getShipStationsRatesCA($data,  "canada_post", "priority");

        // $rates = [];
        // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;
        // if(@$CANADA_rate[0]['shipmentCost']) $rates[] = $CANADA_rate;

        // $_rates = $this->sortShipstationRates($rates);

        // if($_rates){
        //     $_rates['postageType'] = "CBP Express";
        //     $_rates['postageCurrency'] = "CAD";
        //     $_rates['total'] = $_rates['shipmentCost'];
        //     $_rates['estimated_delivery'] = "less than a day";
        // }

        // return $_rates;

    }

    private function CBP_Express_CAUS($sign_insurance, $rate1){
        // $rs = new Rocketship;
        // $rate1 = $rs->getUPSCARates($data);

        // dd($rate1);

        $rates = [];


        if(@$rate1['data']['rates']){
            $d = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"65","UPS"); //UPS Worldwide Saver
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "65","");
                $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "65","");
                $rates[] = $d; 
            }

            // $d = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"07","UPS"); //UPS Worldwide Express
            // if($d) { 
            //     $d['insurance_fee'] = splitInsurance($sign_insurance['rate1_normal'], $sign_insurance['rate1_insurance'], "07","");
            //     $d['signature_fee'] = splitSignature($sign_insurance['rate1_normal'], $sign_insurance['rate1_signature'], "07","");
            //     $rates[] = $d; 
            // }
            // dd($d);

        }


       // $_rates = $this->sortShipstationRates($rates);

        $_rates = $rates;

       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Express";   
            $_rates[$key]['postage_type_code'] = "cbp_express";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "1-3 Days";
            $_rates[$key]['address_flow'] = "CA_US";

       }

       // if($this->show_all_rates){
       // }else{

       //     //hold display lowest single rate
       //     if($_rates){
       //           $_rates['postage_type'] = "CBP Express";   
       //          $_rates['postage_type_code'] = "cbp_express";   
       //          $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
       //          $_rates['estimated_delivery'] = "1-3 Days";
       //          $_rates['address_flow'] = "CA_US";

       //     }
       // }
       
       return $_rates;

        // $ss =  new ShipStation;
        // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_next_day_air_international");

        // $rates = [];
        // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;

        // $_rates = $this->sortShipstationRates($rates);

        // if($_rates){
        //     $_rates['postageType'] = "CBP Express";
        //     $_rates['postageCurrency'] = "CAD";
        //     $_rates['total'] = $_rates['shipmentCost'];
        //     $_rates['estimated_delivery'] = "less than a day";
        // }

        // return $_rates;

    }

    private function CBP_Express_CAInt($sign_insurance, $rate1){
        // $rs = new Rocketship;
        // $rate1 = $rs->getUPSCARates($data);

        // dd($rate1);

        $rates = [];


        if(@$rate1['data']['rates']){
            $d = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"65","UPS"); //UPS Express Saver / UPS Next Day Air Saver
            if($d) { 
                $d['insurance_fee'] = splitInsurance($sign_insurance['rate2_normal'], $sign_insurance['rate2_insurance'], "65","");
                $d['signature_fee'] = splitSignature($sign_insurance['rate2_normal'], $sign_insurance['rate2_signature'], "65","");
                $rates[] = $d; 
            }

            // $d = $this->filterRocketshipRatesByServiceCode($rate1['data']['rates'],"54","UPS"); // UPS Worldwide Express Plus
            // if($d) { $rates[] = $d; }


        }


       // $_rates = $this->sortShipstationRates($rates);
        $_rates = $rates;


       foreach ($_rates as $key => $value) {
           # code...

            $_rates[$key]['postage_type'] = "CBP Express";   
            $_rates[$key]['postage_type_code'] = "cbp_express";   
            $_rates[$key]['total'] = (@$_rates[$key]['negotiated_rate']) ? @$_rates[$key]['negotiated_rate'] : $_rates[$key]['rate'];
            $_rates[$key]['estimated_delivery'] = "1-3 Days";
       }
       // if($this->show_all_rates){
       // }else{

       //     //hold display lowest single rate
       //     if($_rates){
       //           $_rates['postage_type'] = "CBP Express";   
       //          $_rates['postage_type_code'] = "cbp_express";   
       //          $_rates['total'] = (@$_rates['negotiated_rate']) ? @$_rates['negotiated_rate'] : $_rates['rate'];
       //          $_rates['estimated_delivery'] = "1-3 Days";
       //     }
       // }
       
       return $_rates;

        // $ss =  new ShipStation;
        // $UPS_rate = $ss->getShipStationsRatesCA($data,  "ups", "ups_worldwide_saver");

        // $rates = [];
        // if(@$UPS_rate[0]['shipmentCost']) $rates[] = $UPS_rate;

        // $_rates = $this->sortShipstationRates($rates);

        // if($_rates){
        //     $_rates['postageType'] = "CBP Express";
        //     $_rates['postageCurrency'] = "CAD";
        //     $_rates['total'] = $_rates['shipmentCost'];
        //     $_rates['estimated_delivery'] = "less than a day";
        // }

        // return $_rates;

    }


    private function filterUSPSRates($rates, $selectedOption, $parcel_type){
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


    public function uspsOptions(Request $r){
        $d = $r->toArray();


        $rates = $this->uspsServices();

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

        return Response()->json(['status'=>true,  "options"=>$arr ]);
        
    }

    private function uspsServices(){
        return UspsService::get()->toArray();
    }

    public function getConversionRate($rate, $currency){
        $data = ConversionRate::where("currency",$currency)->get()->first();

        if($data){
            return $rate * $data->rate;
        }else{
            return $rate;
        }
    }

    private function handleMarkup($carrier){
        $data = Carrier::where("carrier_id",$carrier)->get()->first();
        if($data){
            return $data->mark_up;
        }else{
            return 0;
        }
    }




    private function postageRateTriggers($data, $rates){

        //TEMPORARY VALUE. TO BE CONFIGURED IN STAFF PORTAL
        $postageMarkupPercentage = 0;//1;
        $truckFee =  0;//0.1;  //$/lb
        $tax = 0;

        // --END--
        // dd($deliveryFee);



        $recipientCountry = $data['recipient_model']['country'];
        $shipFromCountry = @$data['ship_from_address_model']['country'];
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
        $taxPercentage = $this->getTaxPercentage($recipientPostal, $recipientProvince);

        //CA-CA
        if($shipFromCountry == "CA" && $recipientCountry == "CA"){
            // echo "<pre>";
            // print_r($rates);
            // exit;
            foreach ($rates as $key=>$value) {
                $postageMarkupPercentage = $this->handleMarkup($value['carrier']);

                $rate = $this->getConversionRate($value['total'],$value['currency']);

                if($postageMarkupPercentage){
                    $markup = $rate * ($postageMarkupPercentage / 100);
                }else{
                    $markup = 0;
                }
               
                if($value['carrier'] == "CANADA POST"){ 

                    


                    //ADD TRUCK FEES FOR THE ZONE SKIP POSTAL
                    if(@$value['zone_skipped']){
                        // dd($value);
                        $truck_fee = $this->calculateTruckFee($length, $width, $height, $weight, $unit_type, $recipientPostal[0]);

                        $truck_fee_tax = $truck_fee * $taxPercentage;

                        $rate_tax = $rate * $taxPercentage;
                        $markup_tax = $markup * $taxPercentage;
                        $rate_before_tax = $rate - $rate_tax;
                        $total_tax = $rate_tax + $markup_tax + $truck_fee_tax;



                        $cbp_delivery_fee = 0;
                        // $total = $rate + $markup + $truck_fee;
                        $total = $rate_before_tax + $markup + $total_tax + $truck_fee;

                        $rates = $this->rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $total_tax, $total);
                    }else{


                        $cbp_delivery_fee = 0;

                        $rate_tax = $rate * $taxPercentage;
                        $markup_tax = $markup * $taxPercentage;
                        $rate_before_tax = $rate - $rate_tax;
                        $total_tax =$rate_tax + $markup_tax;

                        $truck_fee = 0;
                        $total = $rate_before_tax + $markup + $total_tax;
                        $rates = $this->rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $total_tax, $total);


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
                    $rates = $this->rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $total_tax, $total);

                }else if($value['carrier'] == "FEDEX"){

                    // $rate_tax = $value['taxes'][0]['amount'];
                    $rate_tax = $rate * $taxPercentage;
                    $markup_tax = $markup * $taxPercentage;
                    $rate_before_tax = $rate - $rate_tax;
                    $total_tax =$rate_tax + $markup_tax;

                    $cbp_delivery_fee = 0;
                    $truck_fee = 0;
                    $total = $rate_before_tax + $markup + $total_tax;
                    $rates = $this->rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $total_tax, $total);

                }
            }
  

        }

        else if($recipientCountry == "US"){

            foreach ($rates as $key => $value) {

                
                if($value['address_flow'] == "CA_US"){
                    $postageMarkupPercentage = $this->handleMarkup(@$value['carrier']);

                    $rate = $this->getConversionRate($value['total'],$value['currency']);
                    
                    if($postageMarkupPercentage){
                        $markup = $rate * ($postageMarkupPercentage / 100);
                    }else{
                        $markup = 0;
                    }

                    $cbp_delivery_fee = 0;
                    $tax = 0;

                    if(@$data['parcel_types']['parcel_type'] == "Letter"){
                        $weight = @$data['usps_options_model']['weight'];
                        if($data['unit_type'] == "metric"){
                            $weight = unitConvesion($weight, "G", "LBS");
                        }

                        // $cbp_delivery_fee = $this->handleSingleShipmentDeliveryFee($weight,"Letter");

                    }else{
                        if(@$data['parcel_types']['usps_box_status'] == "yes"){
                            $weight = @$data['usps_options_model']['weight'];
                        }else{
                            $weight = @$data['parcel_dimensions_model']['weight'];

                        }

                        if($data['unit_type'] == "metric"){
                            $weight = unitConvesion($value, "G", "LBS");
                        }
                    }

                    $truck_fee = 0;
                    $total = $rate + $markup + $cbp_delivery_fee + $tax;
                    $rates = $this->rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $tax, $total);

                }
                else{

                    $rate = $this->getConversionRate($value['total'],$value['currency']);
                    $markup = 0;

                    $deliveryFee = 0;
                    if(@$data['parcel_types']['parcel_type'] == "Letter"){
                        $weight = @$data['usps_options_model']['weight'];
                        if($data['unit_type'] == "metric"){
                            $weight = unitConvesion($weight, "G", "LBS");
                        }
                        $deliveryFee = $this->handleSingleShipmentDeliveryFee($weight,"Letter");

                    }else{
                        if(@$data['parcel_types']['usps_box_status'] == "yes"){
                            $weight = @$data['usps_options_model']['weight'];
                        }else{
                            $weight = @$data['parcel_dimensions_model']['weight'];

                        }

                        if($data['unit_type'] == "metric"){
                            $weight = unitConvesion($weight, "G", "LBS");
                        }


                        $df = $this->handleSingleShipmentDeliveryFee($weight,"Box");
                        $deliveryFee = $df;
                    }

                    $tax = 0.13 * $deliveryFee;
                    $cbp_delivery_fee = $deliveryFee;
                    $truck_fee = 0;
                    $total = ($rate) + $markup + $cbp_delivery_fee + $tax;
                    $rates = $this->rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $tax, $total);

                }
            }
        }
        //CA - INTERNATIONAL
        else if ($shipFromCountry == "CA" && $recipientCountry != "CA"){

            // echo "<pre>";
            // print_r($rates);
            // exit;
            foreach ($rates as $key=>$value) {
                $postageMarkupPercentage = $this->handleMarkup(@$value['carrier']);

                $rate = $this->getConversionRate($value['total'],$value['currency']);
                
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

                        // $cbp_delivery_fee = $this->handleSingleShipmentDeliveryFee($weight,"Letter");

                    }else{
                        if(@$data['parcel_types']['usps_box_status'] == "yes"){
                            $weight = @$data['usps_options_model']['weight'];
                        }else{
                            $weight = @$data['parcel_dimensions_model']['weight'];

                        }

                        if($data['unit_type'] == "metric"){
                            $weight = unitConvesion($value, "G", "LBS");
                        }

                        // $df = $this->handleSingleShipmentDeliveryFee($weight,"Box");
                        // $cbp_delivery_fee = $df;
                    }

                    // $tax = 0.13 * $cbp_delivery_fee;

                }

                $truck_fee = 0;
                $total = $rate + $markup + $cbp_delivery_fee + $tax;
                $rates = $this->rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $tax, $total);
            }
        }
        
        // US-US
        else if($shipFromCountry == "US" && $recipientCountry == "US"){

            // dd($rates);
            foreach ($rates as $key=>$value) {

                // $postageMarkupPercentage = $this->handleMarkup($value['carrier']);
                $rate = $this->getConversionRate($value['total'],$value['currency']);
                $markup = 0;

                $deliveryFee = 0;
                if(@$data['parcel_types']['parcel_type'] == "Letter"){
                    $weight = @$data['usps_options_model']['weight'];
                    if($data['unit_type'] == "metric"){
                        $weight = unitConvesion($weight, "G", "LBS");
                    }
                    $deliveryFee = $this->handleSingleShipmentDeliveryFee($weight,"Letter");

                }else{
                    if(@$data['parcel_types']['usps_box_status'] == "yes"){
                        $weight = @$data['usps_options_model']['weight'];
                    }else{
                        $weight = @$data['parcel_dimensions_model']['weight'];

                    }

                    if($data['unit_type'] == "metric"){
                        $weight = unitConvesion($weight, "G", "LBS");
                    }


                    $df = $this->handleSingleShipmentDeliveryFee($weight,"Box");
                    $deliveryFee = $df;
                }

                $tax = 0.13 * $deliveryFee;
                $cbp_delivery_fee = $deliveryFee;
                $truck_fee = 0;
                $total = ($rate) + $markup + $cbp_delivery_fee + $tax;
                $rates = $this->rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $tax, $total);
            }
        }

        // US-INTERNATIONAL
        else if($shipFromCountry == "US" && $recipientCountry != "US"){

            foreach ($rates as $key=>$value) {
                // $postageMarkupPercentage = $this->handleMarkup($value['carrier']);
                $rate = $this->getConversionRate($value['total'],$value['currency']);


                $weight = @$data['parcel_dimensions_model']['weight'];

                if($data['unit_type'] == "metric"){
                    $weight = unitConvesion($weight, "G", "LBS");
                }
                
                // if($postageMarkupPercentage){
                //     $markup = $rate * ($postageMarkupPercentage / 100);
                // }else{
                $markup = 0;
                // }

                $df = $this->handleSingleShipmentDeliveryFee($weight,"Box");
                $deliveryFee = $df;

                $tax = 0.13 * $deliveryFee;
                $cbp_delivery_fee = $deliveryFee;

                $truck_fee = 0;
                $total = $rate + $deliveryFee + $tax;
                $rates = $this->rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $tax, $total);
            }
        }
        // dd($rates)
        return $rates;

    }



    private function calculateTruckFee($length, $width, $height, $weight, $unit_type, $postal_code){

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

    private function getTaxPercentage($postal_code, $province){
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


    private function handleSingleShipmentDeliveryFee($weight, $type){
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

    private function rateHandler($rates, $key, $truck_fee, $markup, $cbp_delivery_fee, $tax, $total ){
        $rates[$key]['truck_fee'] = number_format($truck_fee,2);
        $rates[$key]['tax'] = number_format($tax,2);
        $rates[$key]['markup'] = number_format($markup,2);
        $rates[$key]['cbp_delivery_fee'] = number_format($cbp_delivery_fee,2);
        $rates[$key]['total'] = number_format($total,2);

        return $rates;
    }


    private function ratesCompare($rate1 = [], $rate2 =[] , $rate3 = []){
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

        return $this->sortRates( $rates);

        
    }


    public function editShipment($sid){
        $ship = Shipment::with(['recipient','recipient_address','shipfrom','shipmentitems','postage_rate'])->where("id",$sid)->get()->first();
        return Response()->json($ship);
        // dd($ship->toArray());

    }

    public function copyShipment(Request $r){
        

        $data = Shipment::find($r->id);

        $m = new Shipment;

        // $m->shipment_code = $data->shipment_code;
        $m->sender_id = $data->sender_id;
        // $m->tracking_no = $data->tracking_no;
        // $m->order_id = $data->order_id;
        $m->recipient_id = $data->recipient_id;
        $m->carrier = $data->carrier;
        $m->postage_rate_id = $data->postage_rate_id;
        $m->shipment_date = $data->shipment_date;
        $m->length = $data->length;
        $m->width = $data->width;
        $m->height = $data->height;
        $m->size_unit = $data->size_unit;
        $m->weight = $data->weight;
        $m->weight_unit = $data->weight_unit;
        $m->require_signature = $data->require_signature;
        $m->delivery_fee = $data->delivery_fee;
        $m->amount_paid = $data->amount_paid;
        $m->shipment_status_id = $data->shipment_status_id;
        $m->received = $data->received;
        $m->duration = $data->duration;
        $m->delivered = $data->delivered;
        $m->note = $data->note;
        $m->letter_mail = $data->letter_mail;
        $m->import_batch = $data->import_batch;
        $m->import_status = $data->import_status;
        $m->carrier_desc = $data->carrier_desc;
        $m->currency = $data->currency;
        $m->shipment_type = $data->shipment_type;
        $m->cbp_address_id = $data->cbp_address_id;
        $m->insurance_cover = $data->insurance_cover;
        $m->insurance_cover_amount = $data->insurance_cover_amount;
        $m->letter_option = $data->letter_option;
        $m->postage_fee = $data->postage_fee;
        $m->total_fee = $data->total_fee;
        $m->postage = $data->postage;
        $m->truck_fee = $data->truck_fee;
        $m->recipient_address_id = $data->recipient_address_id;
        $m->bag_id = $data->bag_id;
        $m->pallet_id = $data->pallet_id;
        // $m->cbme = $data->cbme;
        $m->carrier_id = $data->carrier_id;
        $m->mark_up = $data->mark_up;
        $m->coupon_code = $data->coupon_code;
        $m->coupon_type = $data->coupon_type;
        $m->coupon_amount = $data->coupon_amount;
        $m->picked_up = $data->picked_up;
        $m->in_transit = $data->in_transit;
        // $m->internal_ship_id = $data->internal_ship_id;
        // $m->internal_order_id = $data->internal_order_id;
        // $m->grouped_order_id = $data->grouped_order_id;

        if($m->save()){

            $si = ShipmentItem::where("shipment_id",$r->id)->get();

            $_params = [];
            foreach ($si as $key => $value) {

                $_params[]=[
                    "pallet_id" => $value->pallet_id,
                    "bag_id" => $value->bag_id,
                    "description" => $value->description,
                    "value" => $value->value,
                    "qty" => $value->qty,
                    "origin_country" => $value->origin_country,
                    "note" => $value->note
                ];

            }


            $this->addItemDetails($_params, $m->id);

            return Response()->json(['status'=>true]);
        }

    }


    public function getRecipientByShipId($sid){
        $ship = Shipment::find($sid);
        $r = Recipient::with(["recipient_address"])->where("id",$ship->recipient_id)->get()->first();
        return Response()->json($r);
    }

    public function getShipFromByShipId($sid){
        $ship = Shipment::find($sid);
        $r = CBPAddress::where("id",$ship->cbp_address_id)->get()->first();
        return Response()->json($r);
    }


    private function handleLabelCreation($data, $shipmentId, $imageData=null){
        // dd($imageData);


        if($data['shipment_type'] == "PD"){


            $decoded = base64_decode($imageData);

            $f = finfo_open();

            $mime_type = finfo_buffer($f, $decoded, FILEINFO_MIME_TYPE);

            if($mime_type == "image/png"){
                // $file = $shipCode.'.png';
                $fileName = $shipmentId.".png";
            }else{;
                $fileName = $shipmentId.".pdf";
            }

            Storage::disk('local')->put('public/labels/'.$fileName, $decoded);
            dd($mime_type);
            // file_put_contents("label.pdf", $decoded);
        }else{
            //internal label
            $this->shipping_label->generate($shipmentId);
        }

    }


    private function renameCBP($rates){
        foreach ($rates as $key => $value) {
            $rates[$key]['desc'] = "CBP ".$value['desc'];
        }

        return $rates;
    }


    private function sortRates($rates){
        $price = array();
        foreach ($rates as $key => $row)
        {
            $price[$key] = @$row['rate'];
        // dd($price);
        }
        array_multisort($price, SORT_ASC, $rates);
        return $rates;

    }


    private function insertCarrier($rates, $carrier){
        // $rates = $data['data']['rates'];
        foreach ($rates as $key => $value) {
            $rates[$key]['carrier']= $carrier;
        }

        // $data['data']['rates'] = $rates;



        return $rates;
    }


    public function getCouponCode(Request $r){
        $date_today = Date("Y-m-d");
        // dd($date_today);
        $res = Coupon::where("coupon", $r->code)
                    ->where("startDate","<=",$date_today)
                    ->where("endDate",">=",$date_today)
                    ->get()->first();

        if($res){
            return Response()->json(['status'=>true,'coupon'=>$res]);
        }else{
            return Response()->json(['status'=>false]);

        }

    }

    private function check_response($r){
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

}
    