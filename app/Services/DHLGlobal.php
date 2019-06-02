<?php
namespace cbp\Services;

use cbp\SenderMailingAddress;
use cbp\Sender;
use Auth;
use cbp\Helpers\Helpers; 
use DB;
use Config;
use Response;
use SimpleXMLElement;

class DHLGLobal
{


    function __construct()
    {
        //$this->middleware('auth');
        // $this->url = '/package';
        // $this->eloquentModel = new Shipment;
        $this->host = env('DHL_HOST', "https://api.dhlglobalmail.com/v2/");
        $this->username = env('DHL_USERNAME');
        $this->password = env('DHL_PASSWORD');
        $this->clientid = env('DHL_CLIENTID');
        $this->pickupNumber = env('DHL_PICKUPNO');
        $this->distributionCenter = env('DHL_DISTRIBUTION');

        //rates
        // $this->rate_host = "https://api-sandbox.dhlecommerce.com";//env('DHL_RATE_HOST');
        $this->rate_host = env('DHL_RATE_HOST');
        $this->rate_clientid = env('DHL_RATE_CLIENT_ID');
        $this->rate_secret = env('DHL_RATE_CLIENT_SECRET');
    }


    public function generateToken(){

        // https://api.dhlglobalmail.com/v2/locations/5300000/closeout/id?access_token=:accesstoken&client_id=:clientid
        // dd($this->username);

        $access = [
            'username' => $this->username,
            'password' => $this->password
        ];


        // dd(http_build_query($access));
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, "https://api.dhlglobalmail.com/v2/auth/access_token?username=".$this->username."&password=".$this->password);
        curl_setopt($ch, CURLOPT_URL, $this->host."auth/access_token?".http_build_query($access));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($ch, CURLOPT_POST, 1);

        // curl_setopt($ch, CURLOPT_POSTFIELDS, "RQXML=" . $xml);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($server_output);
        // dd($res);
        return $res->data->access_token;

    }


    public function generateRateToken(){

        // https://api.dhlglobalmail.com/v2/locations/5300000/closeout/id?access_token=:accesstoken&client_id=:clientid
        // dd($this->username);

        $access = [
            'username' => $this->rate_clientid,
            'password' => $this->rate_secret
        ];

        // dd($this->rate_host."/efulfillment/v1/auth/accesstoken");
        // dd(http_build_query($access));
        $payload = json_encode($access);



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->rate_host."/efulfillment/v1/auth/accesstoken");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->rate_clientid . ":" . $this->rate_secret);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);


        $res = json_decode($output);

        return $res->access_token;

    }


    private function execRequest($data=array(), $path){
        $token = $this->generateToken();
        
        $ch = curl_init();

         $access = [
            'access_token' => $token,
            'client_id' => $this->clientid
        ];

        $payload = json_encode($data);

        // echo ($payload);
        // exit;


        curl_setopt($ch, CURLOPT_URL, $this->host.$path."?".http_build_query($access));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json;charset=UTF-8'));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);

        return $server_output;
        // echo ($server_output);
        // die();

    }

    private function execRateRequest($data=array(), $path){
        $token = $this->generateRateToken();

        // $token = "4wI9pFu6DDfPzgs9AXKdxEFYUeMAiuzpiUz8TUujpoGkhyJfdeZhYB";

        
        $ch = curl_init();

        $payload = json_encode($data);

        // dd($this->rate_host.$path);

        // dd($payload);
        // exit;
        // curl_setopt($ch, CURLOPT_URL, "https://private-anon-c1f0afcb93-dhlecglobalshippingproductfinderapi.apiary-mock.com/info/v1/products?lang=");
        curl_setopt($ch, CURLOPT_URL, "https://api.dhlecommerce.com/info/v1/products?lang=");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type:application/json;charset=UTF-8",
          "Accept: application/json",
          "Authorization: Bearer $token"
        ));

        curl_setopt($ch, CURLOPT_POSTFIELDS,$payload);
        $server_output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($server_output);


        return $server_output;
        // echo ($server_output);
        // die();

    }


    public function getRates($r){
        $d = @$r['parcel_dimensions_model'];
        $recipient = @$r['recipient_model'];
        $ship_from_address = $r['ship_from_address_model'];
        $insurance = @$r['insurance_model'];

        $signature_require = @$r['postage_option_model']['req_signature'];
        $unit_type = $r['unit_type'];


        if($unit_type == "imperial"){
            $unit_size = "IN";
            $unit_weight = "LB";
        }else{
            $unit_size = "CM";
            $unit_weight = "KG";
        }

        $packageDetails = array (
                    'weight' => (float)@$d['weight'],
                    'length' => (float)@$d['length'],
                    'width' => (float)@$d['width'],
                    'height' => (float)@$d['height'],
                    'weightUom' => $unit_weight,
                    'dimensionUom' => $unit_size,
                    "trackingOption"=> "FULL"

                );
            


        if(@$r['_signature'] == "false" && @$r['_insurance']== "false"){
            //display original rate without  signature and insurance
        }
        else if(@$r['_signature'] == "true" || @$r['_insurance'] == "true"){
            if($r['_signature'] == "true"){
                if($signature_require){
                    $packageDetails['deliveryConfirmation'] = "SCAN";
                }else{
                    $dimensions['deliveryConfirmation'] = "NONE";

                }
            }

            if($r['_insurance'] == "true"){

                if($insurance){
                    // $dimensions['insured_currency'] = $insurance['currency'];
                    if($insurance['insured_value'] > 0){
                        $packageDetails['insuredValue'] = (float)$insurance['insured_value'];
                    }
                }
            }

        }else{

            if($insurance){
                if($insurance['insured_value'] > 0){
                    $packageDetails['insuredValue'] = (float)$insurance['insured_value'];
                }
            }

            if($signature_require){
                $packageDetails['deliveryConfirmation'] = "SCAN";
            }else{
                $dimensions['deliveryConfirmation'] = "NONE";

            }
        }

        $data = [

            "pickupAddress"=> [
                'address1' => @$ship_from_address['address_1'],
                // 'address2' => @$ship_from_address['address_2'],
                'city' => @$ship_from_address['city'],
                'state' => @$ship_from_address['province'],
                'postalCode' => @$ship_from_address['postal'],
                'country' => @$ship_from_address['country'],
            ],
            "consigneeAddress"=> [
                'address1' => @$recipient['address_1'],
                // 'address2' => @$recipient['address_2'],
                'city' => @$recipient['city'],
                'state' => @$recipient['province'],
                'postalCode' => @$recipient['postal'],
                'country' => @$recipient['country'],
            ],
            "packageDetails"=> $packageDetails,
            "rate"=> [
            "calculateRate"=> true
            ],
            "pickupAccount"=> $this->pickupNumber,
            "currency"=> "USD"

        ];


        $response = $this->execRateRequest($data, "/info/v1/products");
        return $this->parseRatesAccordingToRocketship($response);
        // dd($response);
        

        // try {
        //     $response = $this->execRateRequest($data, "/info/v1/products");
        //     $res = json_decode($response);
        //     return $res;
        // } catch (Exception $e) {
        //     throw new \Exception("DHL API Request Error: " . $e); 
        // }

    }

    public function parseRatesAccordingToRocketship($rates){

        $rates = json_decode($rates,true);
        // dd($rates);

        $rates_hold = [];
        foreach ($rates['products'] as $key => $value) {
            $rates_hold[] = [
                "desc" => $value['product']['productName'],
                "rate" => $value['product']['rate']['rate'],
                "currency" => $value['product']['rate']['currency'],
                "service_code" => $value['product']['productId'],
                "est_delivery_time" => "",
                "package_type" => "",
                "rate_detail" => $value['product']['rate']['rateComponents'],
            ];
        }

        $_rates = [
            "meta" => [
                "code" => 200,
                "error_message" => ""
            ],
            "data" => [
                "errors" => null,
                "rates" => $rates_hold
            ]
        ];

        return $_rates;
        
    }


    private function handleWeightAndDimensions($d){

        $dimensions = [];
        $weight = [];
        

        if($d['unit_type'] == "imperial"){
            $dimensions = array (
                "units"=> "IN",
                'length' => @(float)$d['length'],
                'width' => @(float)$d['width'],
                'height' => @(float)$d['height'],
            );

            $weight = [
                "units"=> "LB",
                "value"=> @(float)$d['weight']
            ];

        }else{

            $dimensions = array (
                "units"=> "IN",
                'length' => unitConvesion($d['length'], "CM", "IN"),
                'width' => unitConvesion($d['width'], "CM", "IN"),
                'height' => unitConvesion($d['height'], "CM", "IN"),
            );

            $weight = [
                "units"=> "LB",
                "value"=> unitConvesion($d['weight'], "G", "LBS")
            ];
        }
        

        return ['dimensions'=>$dimensions, 'weight'=>$weight];

    }


    public function generateShippingLabel($r, $ids){
        // dd($ids);
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


        $carrierCode = $postage['carrier'];
        $companyName = "";
        $shipFromAddress2 = "";



        $companyName = @$ship_from_address['company'];
        $shipFromAddress2 = "CBP Fulfillment ".$ids['recipient_id']." P".$ids['shipment_id'];
    

        $totalValue = 0;
        $customsDetails = [];
        foreach ($items as $key => $value) {

            $customsDetails[]= [
                "itemDescription"=> $value['description'],
                "countryOfOrigin"=> $value['country'],
                // "hsCode"=> "888888888888",
                "packagedQuantity"=> $value['quantity'],
                "itemValue"=> $value['value'],
                "skuNumber"=> $value['description']
            ];

            $totalValue += $value['value'];
        }

        $packageDesc = implode(",", $customsDetails[0]);

        $data = [
            "shipments" => [
                    [
                    "pickup"=> (integer)$this->pickupNumber,
                    "distributionCenter"=> $this->distributionCenter,
                    "packages"=> [

                        [

                            "consigneeAddress"=> [
                                "address1"=> $recipient['address_1'],
                                "city"=> $recipient['city'],
                                "country"=> $recipient['country'],
                                // "email"=> ,
                                "name"=> $sender['first_name']." ".$sender['last_name'],
                                "phone"=> @$recipient['contact_no'],
                                "postalCode"=> $recipient['postal'],
                                "state"=> $recipient['province']
                            ],

                            "packageDetails" => [
                                // "billingRef1"=> "test ref 1",
                                // "billingRef2"=> "test ref 2",
                                // "codAmount"=> 0,
                                "currency"=> "USD",
                                "declaredValue"=> $totalValue,
                                // "dgCategory"=> "02",
                                "dimensionUom"=> $wd['dimensions']['units'],
                                // "dutyCharges"=> 10.00,
                                "dutiesPaid"=> "N",
                                'height' => $wd['dimensions']['height'],
                                'length' => $wd['dimensions']['length'],
                                'width' => $wd['dimensions']['width'],
                                // "freightCharges"=> 10.00,            
                                // "insuredValue"=> 1,
                                // "mailtype"=> "7",
                                "orderedProduct"=> $postage['service_code'],
                                "packageDesc"=> $packageDesc,
                                "packageId"=> $ids['shipment_id'].$ids['recipient_id'].generateCode(5),
                                "packageRefName"=> $ids['shipment_id']."-".$ids['recipient_id'],
                                // "taxCharges"=> 10.00,            
                                "weight"=> $wd['weight']['value'],
                                "weightUom"=> $wd['weight']['units'],
                            ],

                            "returnAddress"=> [
                                "address1"=> $ship_from_address['address_1'],
                                "address2"=> $shipFromAddress2,
                                "city"=> $ship_from_address['city'],
                                "companyName"=> $companyName,
                                "country"=> $ship_from_address['country'],
                                "name"=> $sender['first_name']." ".$sender['last_name'],
                                "postalCode"=> $ship_from_address['postal'],
                                "state"=> $ship_from_address['province']
                            ],  

                            "customsDetails"=> $customsDetails  
                        ]      

                    ]
                ]
            ]
        ];

        // dd($data);


        try {
            $response = $this->execRequest($data, "label/multi/image.json");
            $res = json_decode($response, true);
            // dd($res);
            return $res;
        } catch (Exception $e) {
            throw new \Exception("DHL API Request Error: " . $e); 
        }


    }


    public function closeOutShipments(){



        $s = \App\Shipment::where('status_id',4)
                        ->whereIn('postage_option',['SESTL','SESUL'])
                        ->whereNull("closeout_id")
                        ->whereNotNull("package_id")
                        ->get();

        $packageIDs = [];
        foreach ($s as $key => $value) {
            // array_push($packageIDs, $value->package_id);
            $packageIDs[]["packageId"] = $value->package_id;
        }

        // dd($packageIDs);

        $data = [
            "closeoutRequests" => [
                    [
                    "packages"=> $packageIDs
                ]
            ]
        ];

        // echo json_encode($data);
        // exit;


        try {
           $response = $this->execRequest($data, "locations/".$this->pickupNumber."/closeout/multi.json");
            $res = json_decode($response);

            return $res;
        } catch (Exception $e) {
            throw new \Exception("DHL API Request Error: " . $e); 
        }


    }

    

}
