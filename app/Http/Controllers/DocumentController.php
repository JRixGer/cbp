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
use \cbp\ShipmentStatusHistory;
use \cbp\CsvImportTemp;
use \cbp\PostageRate;
use \cbp\CBPAddress;
use \cbp\Carrier;

use \cbp\Http\Requests\RecipientRegistrationRequest;
use \cbp\Http\Requests\ParcelDimensionsRequest;

use \cbp\Services\Rocketship; 
use \cbp\Services\ShipStation; 
use \cbp\Services\ShippingLabel; 
use \cbp\Services\DHLGLobal; 

class DocumentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */


    public function docrequest(Request $r){
        $data = $r->toArray();
        $r->session()->put('biz', $data);

    }
    
    public function poa(Request $r)
    {
        $data = $r->session()->get('biz');
        if(!$data){
            $data = [];
        }
        // dd($data);
        $pdf = PDF::loadView('documents.poa', $data);
        $r->session()->forget("biz");
        // return $pdf->stream();
        return $pdf->download('general_POA.pdf');
    }


    public function import_number(Request $r)
    {
        $data = $r->session()->get('biz');
        if(!$data){
            $data = [];
        }
        $pdf = PDF::loadView('documents.import_number', $data);
        $r->session()->forget("biz");
        // return $pdf->stream();
        return $pdf->download('import_number_application.pdf');
    }


    public function upload(Request $r){
        // dd($r->toArray());
        // dd($_FILES["file"]["type"]);
        // dd($r->file);
        $file_type = "";
        try {
            if(in_array($_FILES["file"]["type"], ['image/jpeg', "image/png"])){
                $filename = date( 'YmdHis', time() ).'.png';
                $imgPath = Image::make($r->file); 
                $imgPath->stream();
                Storage::disk('local')->put("public/attachments/".$filename,  $imgPath);
                $file_type = "image";
                $image_path = Storage::url('public/attachments/'.$filename);
            }else{
                $file = $r->file;
                $extension = $file->getClientOriginalExtension();

                $filename = date( 'YmdHis', time() ).'.'.$extension;
                Storage::disk('local')->put("public/attachments/".$filename,  File::get($file));
                $file_type = "document";
                $image_path = Storage::url('public/attachments/'.$filename);
            }

            $m = new Attachment;
            $d['user_id'] = Auth::User()->id;
            $d['category'] = $r->upload_category;
            $d['path'] = $image_path;
            $d['file_type'] = $file_type;
            $m->updateOrCreate(['user_id'=>$d['user_id'],'category'=>$d['category']], $d);
     
            
            return Response()->json(['status'=>true,"message"=>"Upload Successful!"]);

        } catch (\Exception $e) {
            return Response()->json(['status'=>false,"message"=>"File Error"]);
        }
    }


    public function importCSV(Request $r){

        $sender_id = Auth::User()->sender_id;
        $import_batch = generateCode($sender_id, 10);

        $senderInfo= getSenderInfo($sender_id);

        $caCountry = array("CA", "CAN", "CANADA");
        $usCountry = array("US", "USA", "AMERICA");

        $csv = $r->toArray();

        //dd($csv);

        $res = storeCSVTemp($csv, $sender_id, $import_batch);

        $status = true;
        $csv_qry = DB::table('csv_import_temp')->select('*')->where('sender_id','=',$sender_id)->where('import_batch','=',$import_batch)->orderby('firstName','lastName','phone','businessName','email','addressLine1','addressLine2','city','provState','postalZipCode','intlProvState','country')->get();
        $reci_temp = "";
        
        
        foreach ($csv_qry as $csvItem) {
            
            if (in_array(strtoupper($csvItem->country), $usCountry))
                $country = "US";
            else if (in_array(strtoupper($csvItem->country), $caCountry))
                $country = "CA";
            else
                $country = $csvItem->country;

            $shipArr = array();
            $shipInfo = array();

            $shipInfo['ship_from_address_model'] = getCBPAddress($senderInfo['country'], $csvItem->shipment_type); 
            $shipInfo['unit_type'] = "imperial";    

            DB::beginTransaction();
            $shipment_code = generateCode($sender_id, 7);
            $tracking_no = generateCode($sender_id, 15);
            $order_id = generateCode($sender_id, 7);

            $recipient = new Recipient;
            $recipient_data = [
                'first_name' => $csvItem->firstName,
                'last_name' => $csvItem->lastName,
                'contact_no' => $csvItem->phone,
                'company' => $csvItem->company,    
                'email' => $csvItem->email     
            ];
            $search_data = [
                'email' => $csvItem->email,   
                'first_name' => $csvItem->firstName,  
                'last_name' => $csvItem->lastName  
            ];
            $recip_id = $recipient->updateOrCreate($search_data, $recipient_data)->id;

            $recipient_address = new RecipientAddress;
            $recipient_address_data = [
                'recipient_id' => $recip_id,
                'address_1' => $csvItem->addressLine1,
                'address_2' => $csvItem->addressLine2,
                'city' => $csvItem->city,
                'province' => $csvItem->provState,
                'postal' => $csvItem->postalZipCode,
                'intl_prov_state' => $csvItem->intlProvState,
                'country' => $country
            ];
            $search_data = [
                'recipient_id' => $recip_id 
            ];
            $recip_add_id = $recipient_address->updateOrCreate($search_data, $recipient_address_data)->id;
            if(!$recip_add_id)
                $status = false;

            
            $shipInfo['recipient_model'] = getToInfo($csvItem);       
            $shipInfo['parcel_dimensions_model'] = getDimInfo($csvItem);
            $shipInfo['shipment_type'] = $csvItem->shipment_type;
            $shipInfo['parcel_letter_status'] = strtolower($csvItem->letterMail);

            if(!$recip_id)
                $status = false;

            $shipment = new Shipment;

            /*
              $periods = array(
              'decade' => 315569260,
              'year' => 31556926,
              'month' => 2629744,
              'week' => 604800,
              'day' => 86400,
              'hour' => 3600,
              'minute' => 60,
              'second' => 1
              );
              to get seconds: (UNIX_TIMESTAMP(mydate)*1000)
            */

            $carrier_id = "";
            if($csvItem->shipment_type == "PD"){

                $prefix = 'PDL';
                if($shipInfo['ship_from_address_model']['country'] == "CA")
                    $prefix = 'PGO';

                // $parcelType["parcel_type"] = "Box"; 
                // $parcelType["usps_box_status"] = "no"; 
                // $shipInfo["parcel_types"] = $parcelType;
                // $shipInfo["signature_require_model"] = 0;
                // $shipInfo["shipment_status"] = 1;
                // $shipInfo["recipient_model"]["company"] = "ABC" ;

                $shipDetails = handlePostageDeliveryRates($shipInfo);
                //dd($shipInfo);
                //echo '>> '.sizeof($shipDetails['response']);

                if($shipDetails['status'] == false || sizeof($shipDetails['response']) == 0)
                {
                    $shipment->import_status = 'No rates available. Please check the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS" and declare item details';
                    $shipment->note = 'Imported with error';                }
                else
                {
                    $shipment->import_status = 'Imported successfully';
                    $shipArr = $shipDetails['response'];

                    //echo '>>> '.$shipArr[0]['negotiated_rate'];

                    $carrier_id = @$this->getCarrierId(@$shipArr[0]['carrier'])->id;
                    $carrier = (@$shipArr[0]['carrier'])? @$shipArr[0]['carrier']:"";
                    $carrier_desc = (@$shipArr[0]['postage_type'])? @$shipArr[0]['postage_type']:"";

                    $currency = (@$shipArr[0]['currency'])? @$shipArr[0]['currency']:"CAD";
                    $service_code = (@$shipArr[0]['service_code'])? @$shipArr[0]['service_code']:"";
                    $est_delivery_time = (@$shipArr[0]['est_delivery_time'])? @$shipArr[0]['est_delivery_time']:"";
                    $package_type = (@$shipArr[0]['package_type'])? @$shipArr[0]['package_type']:"";
                    $rate_detail = (@$shipArr[0]['rate_detail'])? @$shipArr[0]['rate_detail']:"";
                    $postage_type = (@$shipArr[0]['postage_type'])? @$shipArr[0]['postage_type']:"";
                    $postage_fee = (@$shipArr[0]['negotiated_rate'])? @$shipArr[0]['negotiated_rate']:@$shipArr[0]['rate'];
                    $total_fee = (@$shipArr[0]['total'])? @$shipArr[0]['total']:0;
                    $est_delivery_no_ofdays = (@$shipArr[0]['estimated_delivery'])? @$shipArr[0]['estimated_delivery']:"";
                    $truck_fee = (@$shipArr[0]['truck_fee'])? @$shipArr[0]['truck_fee']:0;
                    $markup = (@$shipArr[0]['markup'])? @$shipArr[0]['markup']:0;
                    $cbp_delivery_fee = (@$shipArr[0]['cbp_delivery_fee'])? @$shipArr[0]['cbp_delivery_fee']:0;

                    
                    //$shipInfo['postage_option_model']['details'] = getPostageInfo($shipArr);

                    $postageDetails['desc'] = $carrier_desc;
                    $postageDetails['currency'] = $currency;
                    $postageDetails['value'] = $total_fee;
                    $postageDetails['est_delivery_time'] = $est_delivery_time;
                    $postageDetails['package_type'] = $package_type;
                    $postageDetails['service_code'] = $service_code;
                    $postageDetails['postage_type'] = $postage_type;

                    $postage['details'] = $postageDetails;
                    if($postage_fee != 0)
                        $postage_rate_id = $this->addPostageRate($postage['details']);  
                    else
                    {
                        $postage_rate_id = 0;
                        $carrier = "";
                        $desc = "";
                        $est_delivery_no_ofdays = "";
                    }
                    $shipment->note = '';

                }

                $shipment->postage = empty($isPostage)? 0:$isPostage;
                $shipment->postage_fee = empty($postage_fee)? 0:$postage_fee;
                $shipment->truck_fee = empty($truck_fee)? 0:$truck_fee;
                $shipment->total_fee = empty($total_fee)? 0:$total_fee;

                $shipment->carrier_id = empty($carrier_id)? "":$carrier_id;
                $shipment->carrier = empty($carrier)? "":$carrier;
                $shipment->carrier_desc = empty($carrier_desc)? "":$carrier_desc;

                $shipment->mark_up = empty($markup)? 0:$markup;
                $shipment->postage_rate_id = empty($postage_rate_id)? 0:$postage_rate_id;
                $shipment->duration = empty($est_delivery_no_ofdays)? "":$est_delivery_no_ofdays;


            }else{
                $prefix = 'DEL';
                $shipDetails = handleDeliveryOnlyRates($shipInfo);

                $rate = 0;
                if($shipDetails['status'] == false)
                {
                    $shipment->import_status = 'Note: '.$shipDetails['message'];
                    $shipment->note = 'Imported with error';
                    $shipArr = NULL;
                }
                else
                {

                    $shipArr = (@$shipDetails) ? $shipDetails : NULL;
                    $shipment->import_status = 'Imported successfully';

                    $carrier_id = @$this->getCarrierId(@$shipArr[0]['carrier'])->id;
                    $carrier_desc = "CBP Delivery";

                    $rate = $shipArr['rate'];
                    $est_delivery_no_ofdays = "";
                



                }    

                $shipment->carrier_id = $carrier_id;
                $shipment->carrier = $carrier_desc;
                $shipment->carrier_desc = $carrier_desc;

                $shipment->mark_up = 0;
                $shipment->currency = "CAD";
                $shipment->delivery_fee = empty($rate)? 0:$rate;
                $shipment->total_fee = empty($rate)? 0:$rate;                
                $shipment->postage_rate_id = 0;
                $shipment->note = '';
            }

            $shipment->shipment_code = $shipment_code;
            $shipment->order_id = $order_id;
            $shipment->tracking_no = $tracking_no;
            $shipment->recipient_id = $recip_id;
            $shipment->sender_id = $sender_id;
            $shipment->shipment_type = $csvItem->shipment_type; 
            $shipment->length = $csvItem->length;
            $shipment->width = $csvItem->width;
            $shipment->height = $csvItem->height;
            $shipment->weight = $csvItem->weight;    
            $shipment->size_unit = $csvItem->unit_size;    
            $shipment->weight_unit = $csvItem->unit_weight; 
            $shipment->require_signature = isset($csvItem->isSignatureReq)? 1:0;
            $shipment->letter_mail = $csvItem->letterMail;
            $shipment->import_batch = $import_batch;

            $cbp_add = getCBPAddress($senderInfo['country'], $csvItem->shipment_type, $carrier_id); 

            $zoneSkipPostal=['T','R','V'];
            $zoneSkipProvince=['ab','alberta','mb','manitoba','bc','british columbia'];

            $postal = $shipInfo['recipient_model']['postal'];
            $province = $shipInfo['recipient_model']['province'];

            if($shipInfo['recipient_model']['country'] == "CA" && $shipInfo['ship_from_address_model']['country'] == "CA"){

                if(in_array($postal[0], $zoneSkipPostal) && in_array(strtolower($province), $zoneSkipProvince)){

                    if(in_array(strtolower($province), ['ab','alberta','mb','manitoba'])){
                        $sma_CA = CBPAddress::where("country","CA")->where("province","AB")->get()->first();
                    }else{
                        $sma_CA = CBPAddress::where("country","CA")->where("province","BC")->get()->first();
                    }

                }else{
                    $sma_CA = CBPAddress::where("country","CA")->where("province","ON")->get()->first();

                }

                $shipment->cbp_address_id = $sma_CA->id;
            }else{

                $shipment->cbp_address_id = $cbp_add['cbp_address_id'];
                
            }

            //$shipment->cbp_address_id = $cbp_add['cbp_address_id'];

            // $statusCode = getStatusCode('Unpaid');
            // $shipment->shipment_status_id = $statusCode;
            $shipment->recipient_address_id = $recip_add_id;

            $ship = $shipment->save();
            $ship_id = $shipment->id;
            $carrierInfo[$shipment->id] = $shipArr;

            generateSequence($prefix, $ship_id);

            $idsArr = [
                'shipment_id'=>$ship_id, 
                'recipient_id'=>$recip_id, 
                'recipient_name'=>$csvItem->firstName." ".$csvItem->lastName,
            ];

            if(!$ship)
                $status = false;

            // need to loop here to save each item
            for ($j = 1; $j <= 20; $j++)
            {
                if(!empty($csvItem->{'item'.$j})  || !empty($csvItem->{'originCountry'.$j}) || ($csvItem->{'qty'.$j} > 0) || ($csvItem->{'itemValue'.$j} > 0))
                {
                    $shipment_item = new ShipmentItem;
                    $shipment_item->shipment_id = $ship_id;

                    $shipment_item->description = $csvItem->{'item'.$j};
                    $shipment_item->qty = $csvItem->{'qty'.$j};
                    $shipment_item->value = $csvItem->{'itemValue'.$j};
                    $shipment_item->origin_country = $csvItem->{'originCountry'.$j};
                    
                    $ship_item = $shipment_item->save();
                    if(!$ship_item)
                        $status = false;                       

                    $shipInfo['item_information_model']['description'] = $csvItem->{'item'.$j};
                    $shipInfo['item_information_model']['country'] = $csvItem->{'originCountry'.$j};
                    $shipInfo['item_information_model']['quantity'] = $csvItem->{'qty'.$j};
                    $shipInfo['item_information_model']['value'] = $csvItem->{'itemValue'.$j};

                }
            }
            // 

            //dd($shipInfo);
   
            // if($csvItem->shipment_type == "PD" && $ship)
            // {
            //     $ss = new ShipStation;
            //     $ss_shipment = $ss->createShipment($shipInfo, $idsArr);
            //     handleLabelCreation($shipInfo, $ss_shipment['shipmentId'], $ss_shipment['labelData']);
            // }


            //$reci_temp = $recip_id; 
            //updateShipment($ship_id, $statusCode); 

            if($status)
                DB::commit();
            else            
                DB::rollback();

            $carrier_id = "";
            $desc =  "";
            $currency = "CAD";
            $service_code =  "";
            $est_delivery_time =  "";
            $package_type =  "";
            $rate_detail =  "";
            $postage_type =  "";
            $postage_fee =  0;
            $total_fee =  0;
            $est_delivery_no_ofdays =  "";
            $truck_fee =  0;
            $markup =  0;
            $cbp_delivery_fee = 0;
        }

        DB::table('csv_import_temp')->where('sender_id','=',$sender_id)->where('import_batch','=',$import_batch)->delete();
        return Response()->json(['status' => $status, 'orderSummary' => orderSummary($sender_id, $import_batch), 'import_batch' => $import_batch, 'orderItems' => orderItems($sender_id, $import_batch), 'carrierInfo' => $carrierInfo]);    
    }

    private function getCarrierId($carrier){
        return Carrier::where("carrier_id",$carrier)->get()->first();
    }

    public function updateShipment(Request $r){

        $caCountry = array("CA", "CAN", "CANADA");
        $usCountry = array("US", "USA", "AMERICA");
        
        $sender_id = Auth::User()->sender_id;
        $senderInfo= getSenderInfo($sender_id);

        $shipInfo['ship_from_address_model'] = getCBPAddress($senderInfo['country'], $r->shipment_type);   

        if (in_array(strtoupper($r->country), $usCountry))
            $country = "US";
        else if (in_array(strtoupper($r->country), $caCountry))
            $country = "CA";
        else
            $country = $r->country;


        $to['first_name'] = $r->FirstName;
        $to['last_name'] = $r->LastName;
        $to['contact_no'] = $r->Phone;
        $to['address_1'] = $r->AddressLine1;
        $to['address_2'] = $r->AddressLine2;
        $to['city'] = $r->City;
        $to['postal'] = $r->PostalZipCode;
        $to['province'] = $r->ProvState;
        $to['intl_prov_state'] = $r->IntlProvState;
        $to['country'] = $Country;
        $shipInfo['recipient_model'] = $to;       

        $dimension['length'] = $r->Length;
        $dimension['width'] = $r->Width;
        $dimension['height'] = $r->Height;
        $dimension['weight'] = $r->Weight;
        $shipInfo['parcel_dimensions_model'] = $dimension;

        $shipInfo['shipment_type'] = $r->shipment_type;
        $shipInfo['parcel_letter_status'] = strtolower($r->LetterMail);
        $shipInfo['unit_type'] = "imperial";    

        $status = true;
        $shipment = Shipment::find($r->shipment_id);
        $carrier_id = "";
        $m = "";
        if($r->shipment_type == "PD"){
            $shipDetails = handlePostageDeliveryRates($shipInfo);
            
            $withError = false;
            $m = 'No rates available. Please check the "SHIP FROM ADDRESS" or adjust "PARCEL DIMENSIONS" and declare item details';
            if($shipDetails['status'] == false)
            {
                $shipment->import_status = $m;
                $shipment->note = 'Update with error';
                $withError = true;
            }
            else if(sizeof($shipDetails['response']) == 0)
            {
                $shipment->import_status = $m;
                $shipment->note = 'Update with error';
                $withError = true;
            }
            else
            {
                $withError = false;
                $shipment->import_status = '';
                $shipArr = $shipDetails['response'];
                $shipArr['id'] = $r->shipment_id;

                $carrier_id = @$this->getCarrierId(@$shipArr[0]['carrier'])->id;
                $carrier = (@$shipArr[0]['carrier'])? @$shipArr[0]['carrier']:"";
                $carrier_desc = (@$shipArr[0]['postage_type'])? @$shipArr[0]['postage_type']:"";

                $currency = (@$shipArr[0]['currency'])? @$shipArr[0]['currency']:"CAD";
                $service_code = (@$shipArr[0]['service_code'])? @$shipArr[0]['service_code']:"";
                $est_delivery_time = (@$shipArr[0]['est_delivery_time'])? @$shipArr[0]['est_delivery_time']:"";
                $package_type = (@$shipArr[0]['package_type'])? @$shipArr[0]['package_type']:"";
                $rate_detail = (@$shipArr[0]['rate_detail'])? @$shipArr[0]['rate_detail']:"";
                $postage_type = (@$shipArr[0]['postage_type'])? @$shipArr[0]['postage_type']:"";
                $postage_fee = (@$shipArr[0]['negotiated_rate'])? @$shipArr[0]['negotiated_rate']:@$shipArr[0]['rate'];
                $total_fee = (@$shipArr[0]['total'])? $shipArr[0]['total']:0;
                $est_delivery_no_ofdays = (@$shipArr[0]['estimated_delivery'])?@ $shipArr[0]['estimated_delivery']:"";
                $truck_fee = (@$shipArr[0]['truck_fee'])? @$shipArr[0]['truck_fee']:"0.00";
                $markup = (@$shipArr[0]['markup'])? @$shipArr[0]['markup']:"0.00";
                $cbp_delivery_fee = (@$shipArr[0]['cbp_delivery_fee'])? @$shipArr[0]['cbp_delivery_fee']:"";
                                                                                                                                                                                                                    
                //$shipInfo['postage_option_model']['details'] = getPostageInfo($shipArr);

                $postageDetails['desc'] = $carrier_desc;
                $postageDetails['currency'] = $currency;
                $postageDetails['value'] = $postage_fee;
                $postageDetails['est_delivery_time'] = $est_delivery_time;
                $postageDetails['package_type'] = $package_type;
                $postageDetails['service_code'] = $service_code;
                $postageDetails['postage_type'] = $postage_type;

                $postage['details'] = $postageDetails;
                if($postage_fee != 0)
                    $postage_rate_id = $this->addPostageRate($postage['details']);  
                else
                {
                    $postage_rate_id = 0;
                    $carrier = "";
                    $desc = "";
                    $est_delivery_no_ofdays = "";
                }
                $shipment->note = '';

                $shipment->postage = empty($isPostage)? 0:$isPostage;
                $shipment->postage_fee = empty($postage_fee)? 0:$postage_fee;
                $shipment->truck_fee = empty($truck_fee)? 0:$truck_fee;
                $shipment->total_fee = empty($total_fee)? 0:$total_fee;
                $shipment->carrier_id = empty($carrier_id)? "":$carrier_id;
                $shipment->carrier = empty($carrier)? "":$carrier;
                $shipment->carrier_desc = empty($carrier_desc)? "":$carrier_desc;
                $shipment->mark_up = empty($markup)? 0:$markup;
                $shipment->postage_rate_id = empty($postage_rate_id)? 0:$postage_rate_id;
                $shipment->duration = empty($est_delivery_no_ofdays)? "":$est_delivery_no_ofdays;        

            }

    
        
        }else{
            $shipDetails = handleDeliveryOnlyRates($shipInfo);
            $rate = 0;
            $m = 'Error, please check your entries';
            $withError = false;
            if($shipDetails['status'] == false)
            {
                $withError = true;
                $shipment->import_status =  $m;
                $shipment->note = 'Update with error';
            }
            else
            {
                $withError = false;
                $shipArr = (@$shipDetails) ? $shipDetails : NULL;
                $shipment->import_status = '';
        
                $carrier_id = $this->getCarrierId("CBP Delivery");
                $carrier_desc = "CBP Delivery";

                $rate = $shipArr['rate'];
                $est_delivery_no_ofdays = "";
     
                $shipment->carrier_id = $carrier_id;
                $shipment->carrier = $carrier_desc;
                $shipment->carrier_desc = $carrier_desc;
                $shipment->mark_up = 0;
                $shipment->currency = "CAD";
                $shipment->delivery_fee = empty($rate)? 0:$rate;
                $shipment->total_fee = empty($rate)? 0:$rate;  
                $shipment->postage_rate_id = 0;
                $shipment->note = '';

              }    
        
        }

       
        if($withError)
        {
            return Response()->json(['status' => false, 'message' => $m]); 
        }
        else
        {

            $shipment->length = $r->Length;
            $shipment->width = $r->Width;
            $shipment->height = $r->Height;
            $shipment->weight = $r->Weight;
            $shipment->size_unit = $r->SizeUnit; 
            $shipment->weight_unit = $r->WeightUnit; 
            $cbp_add = getCBPAddress($senderInfo['country'], $r->shipment_type, $carrier_id); 

            $zoneSkipPostal=['T','R','V'];
            $zoneSkipProvince=['ab','alberta','mb','manitoba','bc','british columbia'];

            $postal = $shipInfo['recipient_model']['postal'];
            $province = $shipInfo['recipient_model']['province'];

            if($shipInfo['recipient_model']['country'] == "CA" && $shipInfo['ship_from_address_model']['country'] == "CA"){

                if(in_array($postal[0], $zoneSkipPostal) && in_array(strtolower($province), $zoneSkipProvince)){

                    if(in_array(strtolower($province), ['ab','alberta','mb','manitoba'])){
                        $sma_CA = CBPAddress::where("country","CA")->where("province","AB")->get()->first();
                    }else{
                        $sma_CA = CBPAddress::where("country","CA")->where("province","BC")->get()->first();
                    }

                }else{
                    $sma_CA = CBPAddress::where("country","CA")->where("province","ON")->get()->first();
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
                }

                $shipment->cbp_address_id = $sma_CA->id;
            }else{

                $shipment->cbp_address_id = $cbp_add['cbp_address_id'];
                
            }

            //$shipment->cbp_address_id = $cbp_add['cbp_address_id'];

            $res = $shipment->save();        

            $recipient = Recipient::find($r->recipient_id);
            $recipient->first_name = $r->FirstName;
            $recipient->last_name = $r->LastName;
            $recipient->contact_no = $r->Phone;
            $recipient->company = $r->BusinessName;
            $recipient->email = $r->Email;
            $res = $recipient->save();  

            $recipient_address = RecipientAddress::find($r->recipient_addresses_id);
            $recipient_address->address_1 = $r->AddressLine1;
            $recipient_address->address_2 = $r->AddressLine2;
            $recipient_address->city = $r->City;
            $recipient_address->province = $r->ProvState;
            $recipient_address->postal = $r->PostalZipCode;
            $recipient_address->intl_prov_state = $r->IntlProvState;
            $recipient_address->country = $country;
            $res = $recipient_address->save();  

            //if($res)
                return Response()->json(['status' => true, 'message' => 'Updated successfully', 'orderSummary' => orderSummary($sender_id, $r->import_batch), 'import_batch' => $r->import_batch, 'orderItems' => orderItems($sender_id, $r->import_batch), 'singleCarrierInfo' => $shipArr]);    
                //return Response()->json(['status' => true, 'message' => 'Updated successfully', 'carrierUpdate' => $shipArr]); 
            //else
            //    return Response()->json(['status' => false, 'message' => 'Error during database update']); 
        }

    }



    public function retrieveSummary(Request $r){
        $sender_id = Auth::User()->sender_id;
        return Response()->json(['orderSummary' => orderSummary($sender_id, $r->input('0'))]);               
    }    


    private function addPostageRate($data){

        $postageRate = new PostageRate;

        $postageRate->description = @$data['desc'];
        $postageRate->currency = @$data['currency'];
        $postageRate->value = @$data['value'];
        $postageRate->est_delivery_time = @Date("Y-m-d", strtotime($data['est_delivery_time']));
        $postageRate->package_type = @$data['package_type'];
        $postageRate->service_code = @$data['service_code'];
        $postageRate->postage_type = @$data['postage_type'];
        $postageRate->other_cost = 0;
        
        $res = $postageRate->save();
        if($res){
            return $postageRate->id;
        }
    }

    public function updateCarrier(Request $r){  // review and complete this part, tomorrow

        $shipArr = $r->toArray(); 

        $carrier_id = (@$shipArr['carrierSelected']['carrier_id'])? @$shipArr['carrierSelected']['carrier_id']:"";
        $carrier = (@$shipArr['carrierSelected']['carrier'])? @$shipArr['carrierSelected']['carrier']:"";
        $carrier_desc = (@$shipArr['carrierSelected']['postage_type'])? @$shipArr['carrierSelected']['postage_type']:"";

        $currency = (@$shipArr['carrierSelected']['currency'])? @$shipArr['carrierSelected']['currency']:"CAD";
        $service_code = (@$shipArr['carrierSelected']['service_code'])? @$shipArr['carrierSelected']['service_code']:"";
        $est_delivery_time = (@$shipArr['carrierSelected']['est_delivery_time'])? @$shipArr['carrierSelected']['est_delivery_time']:"";
        $package_type = (@$shipArr['carrierSelected']['package_type'])? @$shipArr['carrierSelected']['package_type']:"";
        $rate_detail = (@$shipArr['carrierSelected']['rate_detail'])? @$shipArr['carrierSelected']['rate_detail']:"";
        $postage_type = (@$shipArr['carrierSelected']['postage_type'])? @$shipArr['carrierSelected']['postage_type']:"";
        $postage_fee = (@$shipArr['carrierSelected']['negotiated_rate'])? @$shipArr['carrierSelected']['negotiated_rate']:@$shipArr['carrierSelected']['rate'];
        $total_fee = (@$shipArr['carrierSelected']['total'])? @$shipArr['carrierSelected']['total']:0;
        $est_delivery_no_ofdays = (@$shipArr['carrierSelected']['estimated_delivery'])? @$shipArr['carrierSelected']['estimated_delivery']:"";
        $truck_fee = (@$shipArr['carrierSelected']['truck_fee'])? @$shipArr['carrierSelected']['truck_fee']:"0.00";
        $markup = (@$shipArr['carrierSelected']['markup'])? @$shipArr['carrierSelected']['markup']:"0.00";
        $cbp_delivery_fee = (@$shipArr['carrierSelected']['cbp_delivery_fee'])? @$shipArr['carrierSelected']['cbp_delivery_fee']:"";

        $postageDetails['desc'] = $carrier_desc;
        $postageDetails['currency'] = $currency;
        $postageDetails['value'] = $postage_fee;
        $postageDetails['est_delivery_time'] = $est_delivery_time;
        $postageDetails['package_type'] = $package_type;
        $postageDetails['service_code'] = $service_code;
        $postageDetails['postage_type'] = $postage_type;

        $postage['details'] = $postageDetails;
        $postage_rate_id = $this->addPostageRate($postage['details']);  

        $shipment = Shipment::find($shipArr['orderSummary']['shipment_id']);
        $shipment->postage = empty($isPostage)? 0:$isPostage;
        $shipment->postage_fee = empty($postage_fee)? 0:$postage_fee;
        $shipment->truck_fee = empty($truck_fee)? 0:$truck_fee;
        $shipment->total_fee = empty($total_fee)? 0:$total_fee;
        $shipment->carrier_id = empty($carrier_id)? "":$carrier_id;
        $shipment->carrier = empty($carrier)? "":$carrier;
        $shipment->carrier_desc = empty($carrier_desc)? "":$carrier_desc;
        $shipment->mark_up = empty($markup)? 0:$markup;
        $shipment->postage_rate_id = empty($postage_rate_id)? 0:$postage_rate_id;
        $shipment->duration = empty($est_delivery_no_ofdays)? "":$est_delivery_no_ofdays;   
        $shipment->insurance_cover_amount = $shipArr['orderSummary']['InsuranceCoverAmount']; 
        $shipment->postage_rate_id = $postage_rate_id;

        $res = $shipment->save();        
        
        if($res)
            return Response()->json(['status' => true, 'message' => 'Updated successfully']); 
        else
            return Response()->json(['status' => false, 'message' => 'Error during database update']); 
    }



    public function processShipment(Request $r){


        $d = $r->toArray();
        $status = false;
        $sender_id = Auth::User()->sender_id;
        if($d['updateType'] == 'save' || $d['updateType'] == 'payment')
            processFee($d);

        for($i=0; $i<sizeof($d['shipment']); $i++)    
            $shipIds[$d['shipment'][$i]['shipment_id']] = $d['shipment'][$i]['shipment_id'];
        //DB::beginTransaction();
        if($d['updateType'] == 'save')
        {

            $statusCode = getStatusCode('Unpaid');
            foreach ($shipIds as $shipId) {
                $status = updateShipment($shipId, $statusCode); 
                if(!$status)    
                    break;
            }

        }
        else if($d['updateType'] == 'cancel')
        {
            foreach ($shipIds as $shipId) {
                $shipment = Shipment::find($shipId);
                if($shipment)
                    $shipment->delete();   
            }
        }
        else if($d['updateType'] == 'payment')
        {
        
            $statusCode = getStatusCode('Unpaid');
            foreach ($shipIds as $shipId) {
                $status = updateShipment($shipId, $statusCode); 
                // if(!$status)    
                //     break;
            }
            
            $shipInfo = getShipInfo($d['shipment'][0]['import_batch'], $sender_id, 'bcode');

            $ss = new ShipStation;
            $finalShipIds = array();

            for($i=0; $i < sizeof($shipInfo); $i++)
            {
                $s = $shipInfo[$i];
                $j = $shipInfo[$i]['idsArr'];

                $sid = $s['cbp_ship_id'];
                $sld = null;
                $finalShipIds[] = $s['cbp_ship_id'];

                // $statusCode = getStatusCode('Ready');
                // $status = updateShipment($sid, $statusCode); 
                                    
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
            return Response()->json(['status'=>true, "shipment_ids"=>$finalShipIds]);

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


    
    public function checkCoupon(Request $r){ 

        $query = DB::table('coupons')
            ->select('*');

        $searchValue = $r->input('search');
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where(DB::raw("BINARY coupon"), '=', $searchValue);
            });
        }
        
        $coupon = $query->count();
        if($coupon > 0 && !empty($searchValue))
        {
            $coupon = $query->get();
            return ['notFound' => false, 'amount' => $coupon[0]->amount, 'type' => $coupon[0]->type, 'coupon' => $coupon[0]->coupon, 'draw' => $r->input('draw')];
        }
        else if(empty($searchValue))
            return ['notFound' => false, 'amount' => '', 'type' => '',  'coupon' => '', 'draw' => $r->input('draw')];
        else 
            return ['notFound' => true, 'amount' => '', 'type' => '',  'coupon' => '', 'draw' => $r->input('draw')];

    }

}

