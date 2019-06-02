<?php 
namespace cbp\Services;

use cbp\SenderMailingAddress;
use cbp\Sender;
use Auth;
use \cbp\Helpers\Helpers; 

class ShipStation{

    public function __construct(){

        $this->SHIPSTATION_CA_KEY = env('SHIPSTATION_CA_KEY');
        $this->SHIPSTATION_CA_SECRET = env('SHIPSTATION_CA_SECRET');

        $this->SHIPSTATION_US_KEY = env('SHIPSTATION_US_KEY');
        $this->SHIPSTATION_US_SECRET = env('SHIPSTATION_US_SECRET');


         
        $this->options = array(
                'http_endpoint' => 'https://ssapi.shipstation.com/',
                // 'http_endpoint' => env('ROCKETSHIP_HOST', 'http://localhost:8080/v1/'),
            );
    }


    public function request($params)
    {
        if ($this->apiKey != '') {
            return $this->httpRequest($params);
        }
    }


    public function httpRequest($ch)
    {
       

        

        $result = curl_exec($ch);
        $resp = json_decode($result, true);
        if (!$resp) {
            return array(
                'meta' => array(
                    'code' => 500,
                    'error_message' => 'Unable to parse JSON, got: '. $result,
                ),
            );
        }

        $this->response = $resp;


        return $this->response;
    }



    public function getShipStationsRatesCA($r,  $carrierCode, $serviceCode){
        // $dataString = json_encode($r);

        $d = $r['parcel_dimensions_model'];
        $signature_require = @$r['signature_require_model'];
        $recipient = $r['recipient_model'];
        $ship_from_address = $r['ship_from_address_model'];
        $ship_from_address['postal'] = str_replace(" ", "", $ship_from_address['postal']);
        $recipient['postal'] = str_replace(" ", "", $recipient['postal']);

        $dimensions = array (
                    "units"=> "inches",
                    // 'weight' => @(float)$d['weight'],
                    'length' => @(float)$d['length'],
                    'width' => @(float)$d['width'],
                    'height' => @(float)$d['height'],
                );
        $confirmation = "none";
        if($signature_require){
            
            if(strtolower($carrierCode) == "fedex"){
                $confirmation = "direct_signature";

            }
        }


        

        // $username= $this->SHIPSTATION_KEY;
        // $password= $this->SHIPSTATION_SECRET;
        $userpass = "$this->SHIPSTATION_CA_KEY:$this->SHIPSTATION_CA_SECRET";
        // $mime = base64_encode($userpass);
        // dd($r);

        $data = [
                  "carrierCode"=>$carrierCode,
                  "serviceCode"=> $serviceCode,
                  "packageCode"=> null,
                  "fromPostalCode"=> $ship_from_address['postal'],
                  "toState"=> $recipient['province'],
                  "toCountry"=> $recipient['country'],
                  "toPostalCode"=> $recipient['postal'],
                  "toCity"=> $recipient['city'],
                  "weight"=> [
                    "value"=> (float)$d['weight'],
                    "units"=> "pounds"
                  ],
                  "dimensions"=> $dimensions,
                  "confirmation"=> $confirmation,
                  "residential"=> false
                ];

        $ch = curl_init($this->options['http_endpoint']."/shipments/getrates");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_USERPWD, "$this->SHIPSTATION_CA_KEY:$this->SHIPSTATION_CA_SECRET");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
             'Content-Type: application/json',
            // "Authorization: Basic ". $mime
        ));
        

        $res = $this->httpRequest($ch);
        if($res){
            $res[0]['carrierCode'] = $carrierCode;
        }
        // dd($res);
        return $res;
        
    }

    public function getShipStationsRatesUS($r,  $carrierCode, $serviceCode){
        // $dataString = json_encode($r);

        $d = $r['parcel_dimensions_model'];
        $signature_require = @$r['signature_require_model'];
        $recipient = $r['recipient_model'];
        $ship_from_address = $r['ship_from_address_model'];
        $ship_from_address['postal'] = str_replace(" ", "", $ship_from_address['postal']);
        $recipient['postal'] = str_replace(" ", "", $recipient['postal']);

        $dimensions = array (
                    "units"=> "inches",
                    // 'weight' => @(float)$d['weight'],
                    'length' => @(float)$d['length'],
                    'width' => @(float)$d['width'],
                    'height' => @(float)$d['height'],
                );
        $confirmation = "direct_signature";
        if($signature_require){
            
            if(strtolower($carrierCode) == "fedex"){
                $confirmation = "direct_signature";

            }
        }

        // $username= $this->SHIPSTATION_KEY;
        // $password= $this->SHIPSTATION_SECRET;
        $userpass = "$this->SHIPSTATION_US_KEY:$this->SHIPSTATION_US_SECRET";
        // $mime = base64_encode($userpass);
        // dd($r);

        $data = [
                  "carrierCode"=>$carrierCode,
                  "serviceCode"=> $serviceCode,
                  "packageCode"=> null,
                  "fromPostalCode"=> $ship_from_address['postal'],
                  "toState"=> $recipient['province'],
                  "toCountry"=> $recipient['country'],
                  "toPostalCode"=> $recipient['postal'],
                  "toCity"=> $recipient['city'],
                  "weight"=> [
                    "value"=> (float)$d['weight'],
                    "units"=> "pounds"
                  ],
                  "dimensions"=> $dimensions,
                  "confirmation"=> $confirmation,
                  "residential"=> false
                ];
        // dd($data);

        $ch = curl_init($this->options['http_endpoint']."/shipments/getrates");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_USERPWD, "$this->SHIPSTATION_US_KEY:$this->SHIPSTATION_US_SECRET");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
             'Content-Type: application/json',
            // "Authorization: Basic ". $mime
        ));
        

        $res = $this->httpRequest($ch);
        // dd($res);
        return $res;
        
    }

    public function getShipStationsShipments(){
        // $dataString = json_encode($r);

        $userpass = "$this->SHIPSTATION_US_KEY:$this->SHIPSTATION_US_SECRET";
        // $mime = base64_encode($userpass);
        // dd($r);


        $ch = curl_init($this->options['http_endpoint']."/shipments");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        // curl_setopt($ch, CURLOPT_POST, TRUE);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_USERPWD, "$this->SHIPSTATION_US_KEY:$this->SHIPSTATION_US_SECRET");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
             'Content-Type: application/json',
            // "Authorization: Basic ". $mime
        ));
        

        $res = $this->httpRequest($ch);
        dd($res);
        return $res;
        
    }


    private function getServiceAndCarrierCode($r){
        $country_origin = $r['ship_from_address_model']['country'];
        $country_destination = $r['recipient_model']['country'];
        $service_code = $r['postage_option_model']['details']['service_code'];

        $arr = [];
        if( $country_origin == "CA" && $country_destination == "CA"){    

            if($service_code =="DOM.EP") // Expedited Parcel
            {
                $arr['service_code'] = "expedited_parcel";
                $arr['carrier_code'] = "canada_post";

            }
            else if($service_code =="DOM.XP") // Xpresspost
            {
                $arr['service_code'] = "xpresspost";
                $arr['carrier_code'] = "canada_post";

            }
            else if($service_code =="DOM.PC") // Canada Post priority
            {
                $arr['service_code'] = "priority";
                $arr['carrier_code'] = "canada_post";

            }
            else if($service_code =="11") // UPS Standard
            {
                $arr['service_code'] = "ups_standard";
                $arr['carrier_code'] = "ups";
            }

            else if($service_code =="13") // UPS Express Saver / UPS Next Day Air Saver
            {
                $arr['service_code'] = "ups_next_day_air_saver";
                $arr['carrier_code'] = "ups";
            }
            else if($service_code =="FEDEX_GROUND") // UPS Standard
            {
                $arr['service_code'] = "fedex_ground";
                $arr['carrier_code'] = "fedex";
            }
        }
        else if($country_origin == "CA" && $country_destination == "US"){

            if($service_code =="11") // UPS Standard
            {
                $arr['service_code'] = "ups_standard";
                $arr['carrier_code'] = "ups";
            }
            else if($service_code =="07") // UPS Express Saver / UPS Next Day Air Saver
            {
                $arr['service_code'] = "ups_next_day_air_saver";
                $arr['carrier_code'] = "ups";
            }

            else if($service_code =="65") // UPS Worldwide Saver
            {
                $arr['service_code'] = "ups_worldwide_saver";
                $arr['carrier_code'] = "ups";
            }

            else if($service_code =="07") //UPS Worldwide Express
            {
                $arr['service_code'] = "ups_worldwide_express_plus";
                $arr['carrier_code'] = "ups";
            }

            else if($service_code =="H") // economy select
            {
                $arr['service_code'] = "dhl_canada_economy_select";
                $arr['carrier_code'] = "dhl_express_canada";
            }
        }
        else if($country_origin == "CA" &&  !in_array($country_destination, ['US','CA'])){
            if($service_code =="P") // economy select
            {
                $arr['service_code'] = "dhl_canada_express_worldwide";
                $arr['carrier_code'] = "dhl_express_canada";

            }else if($service_code =="08") // economy select
            {
                $arr['service_code'] = "ups_worldwide_expedited";
                $arr['carrier_code'] = "ups";
            }
            else if($service_code =="65") // UPS Worldwide Saver
            {
                $arr['service_code'] = "ups_worldwide_saver";
                $arr['carrier_code'] = "ups";
            }
        }
        else if($country_origin == "US" &&  $country_destination == "US"){
            if($service_code =="03") // UPS GROUND
            {
                $arr['service_code'] = "ups_ground";
                $arr['carrier_code'] = "ups";
            }

            if($service_code =="US-PM") // USPS PRIORITY PACKAGE
            {
                $arr['service_code'] = "usps_priority_mail";
                $arr['carrier_code'] = "stamps_com";
            }

            if($service_code =="US-FC") // USPS PRIORITY PACKAGE
            {
                $arr['service_code'] = "usps_first_class_mail";
                $arr['carrier_code'] = "stamps_com";
            }
        }
        else if($country_origin == "US" &&  !in_array($country_destination, ['US','CA'])){
            if($service_code =="US-PMI") // USPS Priority Mail International
            {
                $arr['service_code'] = "usps_priority_mail_international";
                $arr['carrier_code'] = "stamps_com";
            }

            if($service_code =="US-FCI") 
            {
                $arr['service_code'] = "usps_first_class_mail_international";
                $arr['carrier_code'] = "stamps_com";
            }

            if($service_code =="US-EMI") 
            {
                $arr['service_code'] = "usps_priority_mail_express_international";
                $arr['carrier_code'] = "stamps_com";
            }
        }

        return $arr;
    }


    private function handleWeightAndDimensions($d){

        $dimensions = [];
        $weight = [];
        

        if($d['unit_type'] == "imperial"){
            $dimensions = array (
                "units"=> "inches",
                'length' => @(float)$d['length'],
                'width' => @(float)$d['width'],
                'height' => @(float)$d['height'],
            );

            $weight = [
                "units"=> "pounds",
                "value"=> @(float)$d['weight']
            ];

        }else{

            $dimensions = array (
                "units"=> "inches",
                'length' => unitConvesion($d['length'], "CM", "IN"),
                'width' => unitConvesion($d['width'], "CM", "IN"),
                'height' => unitConvesion($d['height'], "CM", "IN"),
            );

            $weight = [
                "units"=> "pounds",
                "value"=> unitConvesion($d['weight'], "G", "LBS")
            ];
        }
        

        return ['dimensions'=>$dimensions, 'weight'=>$weight];

    }


    public function createShipment($r,$ids){

        // $dataString = json_encode($r);
        // dd($r);
        $d = $r['parcel_dimensions_model'];
        $unit_type = $r['unit_type'];
        $recipient = $r['recipient_model'];
        $ship_from_address = $r['ship_from_address_model'];
        $ship_from_address['postal'] = str_replace(" ", "", $ship_from_address['postal']);
        // $ship_from_address['postal'] = $ship_from_address['postal'];
        $recipient['postal'] = str_replace(" ", "", $recipient['postal']);

        $postage = @$r['postage_option_model']['details'];
        $items = @$r['item_information_model'];

        $sender = Sender::where("id",Auth::User()->sender_id)->first()->toArray();
        // dd($sender);

        $scc = $this->getServiceAndCarrierCode($r);

        if($postage['carrier'] == "FEDEX"){
            $confirmation = "direct_signature";
        }else{
            $confirmation="delivery";
        }


            $_params = [
                "source_country"=>$recipient['country'],
                "destination_country"=>$ship_from_address['country'],
                "unit_type"=>$unit_type,
                "length"=>@$d['length'],
                "width"=>@$d['width'],
                "height"=>@$d['height'],
                "weight"=>@$d['weight']
            ];


        $wd = $this->handleWeightAndDimensions($_params);

        // print_r($wd);
        // exit;
        // dd($r);
        // $username= $this->SHIPSTATION_KEY;
        // $password= $this->SHIPSTATION_SECRET;
        // $mime = base64_encode($userpass);
        // dd($r);
        $carrierCode = $postage['carrier'];
        $companyName = "";
        $shipFromAddress2 = "";


        if($carrierCode == "UPS"){
            $companyName = "CBP Fulfillment ".$ids['recipient_id']." P".$ids['shipment_id'];
            $shipFromAddress2 = @$ship_from_address['address_2'];
        }else if($carrierCode == "USPS"){
            $companyName = @$ship_from_address['company'];
            $shipFromAddress2 = "CBP Fulfillment ".$ids['recipient_id']." P".$ids['shipment_id'];
        }
        
        // dd($companyName);

        $data = [
                  "carrierCode"=> $scc['carrier_code'],
                  "serviceCode"=> $scc['service_code'],
                  "packageCode"=> "package",
                  "confirmation"=> $confirmation,
                  "shipDate"=> date("Y-m-d"),
                  "weight"=> $wd['weight'],
                  "dimensions"=> $wd['dimensions'],
                  "shipFrom"=> [
                    "name"=> $sender['first_name']." ".$sender['last_name'],
                    "company"=> $companyName,
                    // "company"=> @$ship_from_address['company'],
                    "street1"=> $ship_from_address['address_1'],
                    "street2"=> $shipFromAddress2,
                    "street3"=> null,
                    "city"=> $ship_from_address['city'],
                    "state"=> $ship_from_address['province'],
                    "postalCode"=> $ship_from_address['postal'],
                    "country"=> $ship_from_address['country'],
                    "phone"=> @$sender['contact_no'],
                    "residential"=> false
                  ],
                  "shipTo"=> [
                    "name"=> $recipient['first_name']." ".$recipient['last_name'],
                    "company"=> @$recipient['company'],
                    "street1"=> $recipient['address_1'],
                    "street2"=> @$recipient['address_2'],
                    "street3"=> null,
                    "city"=> $recipient['city'],
                    "state"=> $recipient['province'],
                    "postalCode"=> $recipient['postal'],
                    "country"=> $recipient['country'],
                    "phone"=> @$recipient['contact_no'],
                    "residential"=> false
                ],
                  "insuranceOptions"=> null,
                  "internationalOptions"=>null,
                  "internationalOptions"=>null,
                  // "internationalOptions"=> [
                  //       "contents"=>"merchandise",
                  //       'customsItems'=>[
                  //           [
                  //               "description"=>"sdf",
                  //               "quantity"=>1,
                  //               "value"=>10,
                  //           ]
                  //       ],
                  //       "nonDelivery"=>"return_to_sender"
                  // ],

                  "advancedOptions"=> null,
                  "testLabel"=> false
                ];


            if($recipient['country'] == "CA" && $ship_from_address['country'] == "CA"){
                //no item details

            }else{

                if($items){
                    $data['internationalOptions']['contents'] = "merchandise";
                    $data['internationalOptions']['nonDelivery'] = "return_to_sender";
                    $data['internationalOptions']['customsItems'] = $items;
                }
            }

            if($ship_from_address['country'] == "CA" ){
                $userpass = "$this->SHIPSTATION_CA_KEY:$this->SHIPSTATION_CA_SECRET";
            }else{
                $userpass = "$this->SHIPSTATION_US_KEY:$this->SHIPSTATION_US_SECRET";
            }



        $ch = curl_init($this->options['http_endpoint']."shipments/createlabel");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_USERPWD, $userpass);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
             'Content-Type: application/json',
            // "Authorization: Basic ". $mime
        ));

        

        $res = $this->httpRequest($ch);
        // dd($res);

        return $res;
    }


    public function createOrder($r, $ids){
        // echo "<pre>";
        // print_r($r);
        // exit;

        // $res = $this->listWarehouses($r);
        // print_r($res);
        // exit;
        $d = $r['parcel_dimensions_model'];
        $unit_type = $r['unit_type'];
        $recipient = $r['recipient_model'];
        $ship_from_address = $r['ship_from_address_model'];
        $ship_from_address['postal'] = str_replace(" ", "", $ship_from_address['postal']);
        // $ship_from_address['postal'] = $ship_from_address['postal'];
        $insurance = @$r['insurance_model'];
        $recipient['postal'] = str_replace(" ", "", $recipient['postal']);


        $postage = @$r['postage_option_model']['details'];
        $carrierCode = $postage['carrier'];

        $sender = Sender::where("id",Auth::User()->sender_id)->first()->toArray();
        // dd($sender);

        $scc = $this->getServiceAndCarrierCode($r);


        if($recipient['country'] == "CA" && $ship_from_address['country'] == "CA"){
            $items = [];
        }else{
            // $items = @$r['item_information_model'];
            $items = $this->orderItem($r['item_information_model']);
        //     echo "<pre>";
        // print_r($items);
        // exit;
        }


        if($ship_from_address['country'] == "US"){
            $warehouseID = 68128;
        }else{
            $warehouseID = 82842;
        }

        $customField1 = "";
        $customField2 = "";


        if($carrierCode == "CANADA POST"){
            $customField1 = $ids['shipment_id'];
            $customField2 = $ids['recipient_id']."-".$ids['recipient_name'];
        }else if($carrierCode == "FEDEX"){
            $customField1 = $ids['recipient_id']."-".$ids['recipient_name'];
        }

        if($postage['carrier'] == "FEDEX"){
            $confirmation = "direct_signature";
        }else{
            $confirmation="delivery";
        }


            $_params = [
                "source_country"=>$recipient['country'],
                "destination_country"=>$ship_from_address['country'],
                "unit_type"=>$unit_type,
                "length"=>@$d['length'],
                "width"=>@$d['width'],
                "height"=>@$d['height'],
                "weight"=>@$d['weight']
            ];


        $wd = $this->handleWeightAndDimensions($_params);


        $data = [
            "orderNumber"=> generateCode("OrdrNo",12),
            "orderKey"=> null,
            "orderDate"=> Date("Y-m-d"),
            "paymentDate"=> Date("Y-m-d"),
            "shipByDate"=> null,
            "orderStatus"=> "awaiting_shipment",
            "customerId"=> $ids['recipient_id'],
            "customerUsername"=> null,
            "customerEmail"=> null,
            "billTo"=> [
                "name"=> $recipient['first_name']." ".$recipient['last_name'],
                "company"=> @$recipient['company'],
                "street1"=> $recipient['address_1'],
                "street2"=> @$recipient['address_2'],
                "street3"=> null,
                "city"=> $recipient['city'],
                "state"=> $recipient['province'],
                "postalCode"=> $recipient['postal'],
                "country"=> $recipient['country'],
                "phone"=> @$recipient['contact_no'],
                "residential"=> false
            ],
            "shipTo"=> [
                "name"=> $recipient['first_name']." ".$recipient['last_name'],
                "company"=> @$recipient['company'],
                "street1"=> $recipient['address_1'],
                "street2"=> @$recipient['address_2'],
                "street3"=> null,
                "city"=> $recipient['city'],
                "state"=> $recipient['province'],
                "postalCode"=> $recipient['postal'],
                "country"=> $recipient['country'],
                "phone"=> @$recipient['contact_no'],
                "residential"=> false
            ],
            "items"=> $items,

            "amountPaid"=> @$postage['total'],
            "taxAmount"=> @$postage['tax'],
            "shippingAmount"=> (@$postage['truck_fee']) ? @$postage['truck_fee'] : @$postage['cbp_delivery_fee'],
            "customerNotes"=> null,
            "internalNotes"=> null,
            "gift"=> false,
            "giftMessage"=> null,
            "paymentMethod"=> null,
            "requestedShippingService"=> @$postage['desc'],
            "carrierCode"=> strtolower(@$scc['carrier_code']),
            "serviceCode"=> strtolower(@$scc['service_code']),
            "packageCode"=> "package",

            "confirmation"=> $confirmation,
            "shipDate"=> Date("Y-m-d"),
            "weight"=> $wd['weight'],
            "dimensions"=> $wd['dimensions'],
            "insuranceOptions"=> null,
            "internationalOptions"=> null,
            "advancedOptions"=> [
                "warehouseId"=> $warehouseID,
                "nonMachinable"=> false,
                "saturdayDelivery"=> false,
                "containsAlcohol"=> false,
                "mergedOrSplit"=> false,
                "mergedIds"=> [],
                "parentId"=> null,
                "storeId"=> null,
                "customField1"=> $customField1,
                "customField2"=> $customField2,
                "customField3"=> "",
                "source"=> "Webstore",
                "billToParty"=> null,
                "billToAccount"=> null,
                "billToPostalCode"=> null,
                "billToCountryCode"=> null
            ],
            "tagIds"=> []
        ];

        if($insurance){
            $data['insuranceOptions'] = [
                "provider"=> "carrier",
                "insureShipment"=> true,
                "insuredValue"=> $insurance['insured_value']
            ];
        }

        if($recipient['country'] == "CA" && $ship_from_address['country'] == "CA"){
            //no item details

        }else{

            if($items){
                $data['internationalOptions']['contents'] = "merchandise";
                $data['internationalOptions']['nonDelivery'] = "return_to_sender";
                $data['internationalOptions']['customsItems'] = $items;
            }
        }


        if($ship_from_address['country'] == "CA" ){
            $userpass = "$this->SHIPSTATION_CA_KEY:$this->SHIPSTATION_CA_SECRET";
        }else{
            $userpass = "$this->SHIPSTATION_US_KEY:$this->SHIPSTATION_US_SECRET";
        }


        $ch = curl_init($this->options['http_endpoint']."orders/createorder");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_USERPWD, $userpass);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
             'Content-Type: application/json',
            // "Authorization: Basic ". $mime
        ));

        

        $res = $this->httpRequest($ch);
        //  echo "<pre>";
        // print_r($res);
        // exit;

       // $x =  $this->listServices($r);

        return $res;

    }


    public function createLabelForOrder($r, $order_id){

        $d = $r['parcel_dimensions_model'];
        $unit_type = $r['unit_type'];
        $recipient = $r['recipient_model'];
        $ship_from_address = $r['ship_from_address_model'];
        $ship_from_address['postal'] = str_replace(" ", "", $ship_from_address['postal']);
        // $ship_from_address['postal'] = $ship_from_address['postal'];
        $insurance = @$r['insurance_model'];
        $recipient['postal'] = str_replace(" ", "", $recipient['postal']);


        $postage = @$r['postage_option_model']['details'];
        $items = @$r['item_information_model'];
        $carrierCode = $postage['carrier'];


        if($postage['carrier'] == "FEDEX"){
            $confirmation = "direct_signature";
        }else{
            $confirmation="delivery";
        }


            $_params = [
                "source_country"=>$recipient['country'],
                "destination_country"=>$ship_from_address['country'],
                "unit_type"=>$unit_type,
                "length"=>@$d['length'],
                "width"=>@$d['width'],
                "height"=>@$d['height'],
                "weight"=>@$d['weight']
            ];


        $wd = $this->handleWeightAndDimensions($_params);



       


        if($ship_from_address['country'] == "CA" ){
            $userpass = "$this->SHIPSTATION_CA_KEY:$this->SHIPSTATION_CA_SECRET";
        }else{
            $userpass = "$this->SHIPSTATION_US_KEY:$this->SHIPSTATION_US_SECRET";
        }



        $data = [
            "orderId"=> $order_id,
            "carrierCode"=> strtolower(@$postage['carrier']),
            "serviceCode"=> strtolower(@$postage['service_code']),
            "packageCode"=> "package",
            "confirmation"=> $confirmation,
            "shipDate"=> Date("Y-m-d"),
            "weight"=> @$wd['weight'],
            "dimensions"=> @$wd['dimensions'],
            "insuranceOptions"=> null,
            "internationalOptions"=> null,
            "advancedOptions"=> null,
            "testLabel"=> false
        ];


         if($insurance){
            $data['insuranceOptions'] = [
                "provider"=> "carrier",
                "insureShipment"=> true,
                "insuredValue"=> $insurance['insured_value']
            ];
        }


         if($recipient['country'] == "CA" && $ship_from_address['country'] == "CA"){
            //no item details

        }else{

            if($items){
                $data['internationalOptions']['contents'] = "merchandise";
                $data['internationalOptions']['nonDelivery'] = "return_to_sender";
                $data['internationalOptions']['customsItems'] = $items;
            }
        }


        $ch = curl_init($this->options['http_endpoint']."orders/createlabelfororder");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_USERPWD, $userpass);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
             'Content-Type: application/json',
            // "Authorization: Basic ". $mime
        ));

        

        $res = $this->httpRequest($ch);
        // dd($res);

        // echo "<pre>";
        // print_r($res);
        // exit;
        return $res;
    }


    public function orderItem($items){
        $arr = [];
        foreach ($items as $key => $value) {
            $arr[] = [
                'name'=>$value['description'],
                'sku'=>$value['description'],
                'quantity'=>$value['quantity'],
                'unitPrice'=>$value['value'],
                'warehouseLocation' => $value['country']

            ];
        }

        return $arr;
    }

    public function listWarehouses($r){
        $ship_from_address = $r['ship_from_address_model'];

        if($ship_from_address['country'] == "CA" ){
            $userpass = "$this->SHIPSTATION_CA_KEY:$this->SHIPSTATION_CA_SECRET";
        }else{
            $userpass = "$this->SHIPSTATION_US_KEY:$this->SHIPSTATION_US_SECRET";
        }


        $ch = curl_init($this->options['http_endpoint']."warehouses");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        // curl_setopt($ch, CURLOPT_POST, TRUE);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_USERPWD, $userpass);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
             'Content-Type: application/json',
            // "Authorization: Basic ". $mime
        ));


        $res = $this->httpRequest($ch);

        return $res;
    }


    public function listServices($r){
        $ship_from_address = $r['ship_from_address_model'];

        if($ship_from_address['country'] == "CA" ){
            $userpass = "$this->SHIPSTATION_CA_KEY:$this->SHIPSTATION_CA_SECRET";
        }else{
            $userpass = "$this->SHIPSTATION_US_KEY:$this->SHIPSTATION_US_SECRET";
        }


        $ch = curl_init($this->options['http_endpoint']."carriers/listservices?carrierCode=ups");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        // curl_setopt($ch, CURLOPT_POST, TRUE);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_USERPWD, $userpass);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
             'Content-Type: application/json',
            // "Authorization: Basic ". $mime
        ));


        $res = $this->httpRequest($ch);

        return $res;
    }


    public function voidLabel($shipmentID){
        
        // $username= $this->SHIPSTATION_KEY;
        // $password= $this->SHIPSTATION_SECRET;
        // $userpass = "$this->SHIPSTATION_KEY:$this->SHIPSTATION_SECRET";
        // $mime = base64_encode($userpass);
        // dd($mime);
        // dd($shipmentID);
        $userpass = "$this->SHIPSTATION_CA_KEY:$this->SHIPSTATION_CA_SECRET";

        $datastring = json_encode(["shipmentID"=>$shipmentID]);

        $ch = curl_init($this->options['http_endpoint']."/shipments/voidlabel");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datastring);

        curl_setopt($ch, CURLOPT_USERPWD, $userpass);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
             'Content-Type: application/json',
            // "Authorization: Basic ". $mime
        ));
        

        $res = $this->httpRequest($ch);
        // dd($res);
        return $res;
        
    }

    public function test($r){
        $dataString = json_encode($r);
        // $username= $this->SHIPSTATION_KEY;
        // $password= $this->SHIPSTATION_SECRET;
        // $userpass = "$this->SHIPSTATION_KEY:$this->SHIPSTATION_SECRET";
        // $mime = base64_encode($userpass);
        // dd($mime);

        $ch = curl_init($this->options['http_endpoint']."/shipments/getrates");
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_USERPWD, "$this->SHIPSTATION_US_KEY:$this->SHIPSTATION_US_SECRET");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            // 'Content-Type: application/json',
            // "Authorization: Basic ". $mime
        ));
        

        $res = $this->httpRequest($ch);
        // dd($res);
        return $res;
        
    }




}

?>