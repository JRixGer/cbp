<?php 
namespace cbp\Services;

use cbp\SenderMailingAddress;
use cbp\Sender;
use cbp\CBPAddress;
use Auth;

class Rocketship{

	public function __construct(){
		$this->apiKey = env('ROCKETSHIP_KEY');
        $this->test = env('ROCKETSHIP_TEST');
	    $this->UPSCA_username = env('UPSCA_USERNAME');
        $this->UPSCA_password = env('UPSCA_PASSWORD');
        $this->UPSCA_key = env('UPSCA_KEY');
	    $this->UPSCA_account_no = env('UPSCA_ACCOUNTNO');

        $this->UPSUS_username = env('UPSUS_USERNAME');
        $this->UPSUS_password = env('UPSUS_PASSWORD');
        $this->UPSUS_key = env('UPSUS_KEY');
        $this->UPSUS_account_no = env('UPSUS_ACCOUNTNO');

        $this->FEDEX_meter_no = env('FEDEX_METERNO');
        $this->FEDEX_password = env('FEDEX_PASSWORD');
        $this->FEDEX_key = env('FEDEX_KEY');
        $this->FEDEX_account_no = env('FEDEX_ACCOUNTNO');

	    $this->CANADAPOST_username = env('CANADAPOST_USERNAME');
        $this->CANADAPOST_password = env('CANADAPOST_PASSWORD');
        $this->CANADAPOST_key = env('CANADAPOST_KEY');
	    $this->CANADAPOST_account_no = env('CANADAPOST_ACCOUNTNO');


        $this->DHL_username = env('DHL_USERNAME');
        $this->DHL_password = env('DHL_PASSWORD');

        $this->STAMPS_username = env('STAMPS_USERNAME');
        $this->STAMPS_password = env('STAMPS_PASSWORD');
	     
	    $this->options = array(
	            'http_endpoint' => 'https://api.rocketship.it/v1/',
	            // 'http_endpoint' => env('ROCKETSHIP_HOST', 'http://localhost:8080/v1/'),
	        );
	}


	public function request($params)
    {
        if ($this->apiKey != '') {
            return $this->httpRequest($params);
        }
    }


    public function httpRequest($data)
    {
        $dataString = json_encode($data);
        // dd($this->options);
        $ch = curl_init($this->options['http_endpoint']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'x-api-key: '. $this->apiKey,
            'Content-Type: application/json',
        ));

        $result = curl_exec($ch);
        // dd($result);
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


    private function unitConvesion($value, $unitFrom, $unitTo){

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

    private function CanadaPost_data($r, $zoneskip=false){


        $zoneSkipPostal=['T','R','V'];
        $zoneSkipProvince=['ab','alberta','mb','manitoba','bc','british columbia'];
        

        $postal = $r['recipient_model']['postal'];
        $province = $r['recipient_model']['province'];

        if(in_array($postal[0], $zoneSkipPostal) && in_array(strtolower($province), $zoneSkipProvince) && $zoneskip){

            if(in_array(strtolower($province), ['ab','alberta','mb','manitoba'])){
                $sma_CA = CBPAddress::where("country","CA")->where("province","AB")->get()->first();
            }else{
                $sma_CA = CBPAddress::where("country","CA")->where("province","BC")->get()->first();
            }


        }else{

            if($zoneskip){
                //return nothing if zoneskip is set but does not met the criteria
                return [];
            }


            $sma_CA = CBPAddress::where("country","CA")->where("province","ON")->get()->first();

        }

        $r['ship_from_address_model'] = $sma_CA->toArray();


        $signature_require = @$r['postage_option_model']['req_signature'];

        $d = @$r['parcel_dimensions_model'];
        $recipient = @$r['recipient_model'];
        $ship_from_address = @$r['ship_from_address_model'];
        $insurance = @$r['insurance_model'];
        // $insurance = @$r['postage_option_model']['insured_value'];
        $sender = Sender::find(Auth::User()->sender_id);
        $unit_type = $r['unit_type'];


        //DEFAULT UNITS
        $unit_size = "CM";
        $unit_weight = "KG";

        if($r['shipment_type'] == "PD"){

            if($unit_type == "imperial"){
                $dimensions = array (
                        'weight' => $this->unitConvesion($d['weight'], "LBS", "KG"),
                        'length' => $this->unitConvesion($d['length'], "IN", "CM"),
                        'width' => $this->unitConvesion($d['width'], "IN", "CM"),
                        'height' => $this->unitConvesion($d['height'], "IN", "CM"),
                    );

            }else{

                $dimensions = array (
                    'weight' => $this->unitConvesion($d['weight'], "G", "KG"),
                    'length' => @(float)$d['length'],
                    'width' => @(float)$d['width'],
                    'height' => @(float)$d['height'],
                );

            }

        }else{

            if($unit_type == "imperial"){
                $dimensions = array (
                        'weight' => $this->unitConvesion($d['weight'], "LBS", "KG")

                    );
            }else{
                $dimensions = array (
                    'weight' => $this->unitConvesion($d['weight'], "G", "KG"),

                    );

            }
        }

        
        if(@$r['_signature'] == "false" && @$r['_insurance']== "false"){
            //display original rate without  signature and insurance
        }
        else if(@$r['_signature'] == "true" || @$r['_insurance'] == "true"){
            if($r['_signature'] == "true"){
                if($signature_require){
                    $dimensions['signature_type'] = "DIRECT";
                }else{
                    $dimensions['signature_type'] = "NO_SIGNATURE_REQUIRED";

                }
            }

            if($r['_insurance'] == "true"){
                if($insurance){
                    $dimensions['insured_currency'] = $insurance['currency'];
                    $dimensions['insured_value'] = (float)$insurance['insured_value'];
                }
            }

        }else{

            if($insurance){
                $dimensions['insured_currency'] = $insurance['currency'];
                $dimensions['insured_value'] = (float)$insurance['insured_value'];
            }

            if($signature_require){
                $dimensions['signature_type'] = "DIRECT";
            }else{
                $dimensions['signature_type'] = "NO_SIGNATURE_REQUIRED";

            }
        }

        // dd($dimensions);

        $ship_from_address['postal'] = str_replace(" ", "", $ship_from_address['postal']);
        $recipient['postal'] = str_replace(" ", "", $recipient['postal']);

        $data = array (
            'carrier' => 'canada',
            'action' => 'GetAllRates',
            'params' => array (
            'username' => $this->CANADAPOST_username,
            'password' => $this->CANADAPOST_password,
            'key' => $this->CANADAPOST_key,
            'account_number' => $this->CANADAPOST_account_no,
            'contract_id'=>'0042932528',
            'packages' => array (
                  0 => $dimensions,
              ),
            'weight_unit' => $unit_weight,
            'length_unit' => $unit_size,
            // "packaging_type"=> $package['package_type'],
            'shipper' => $sender->first_name." ".$sender->last_name,
            // 'ship_addr1' => $ship_from_address['address1'],
            // 'ship_addr2' => $ship_from_address->addre_ss2,
            // 'ship_phone' => $profile->phone_no,
            'ship_state' => $ship_from_address['province'],
            // 'ship_city' => $ship_from_address['city'],
            'ship_code' => $ship_from_address['postal'],
            'ship_country' => $ship_from_address['country'],
            'to_state' => $recipient['province'],
            // 'ship_city' => 'BELLEVILLE',
            // 'ship_code' => 'K8N5W6',
            // 'residential' => true,
            'to_country' => $recipient['country'],
            'to_code' => $recipient['postal'],
            'negotiated_rates' => true,
            'residential'=>true,
            "customer_classification"=> "00"

        )
       );

        // dd($data);

        return $data;
    }



    public function getCanadaPostRates($r,$zoneskip=false){

        // $ship_from_address = SenderMailingAddress::where("country","CA")->get()->first();
        // dd($r);
        // $_data = $r;
        

        $data = $this->CanadaPost_data($r, $zoneskip);
        // dd($data);
        if($data){
            $response = $this->request($data);
            return $response;
            
        }else{
            return [];
        }
        


    }


    private function UPSCA_data($r){
        // $ship_from_address = SenderMailingAddress::where("country","CA")->get()->first();
        $sender = Sender::find(Auth::User()->sender_id);

        // $_data = $r;
        // $signature_require = @$r['signature_require_model'];
        $signature_require = @$r['postage_option_model']['req_signature'];
        $d = @$r['parcel_dimensions_model'];
        $recipient = @$r['recipient_model'];
        $ship_from_address = @$r['ship_from_address_model'];
        $insurance = @$r['insurance_model'];
        

        $unit_type = $r['unit_type'];


        //default size and weight unit
        $unit_size = "IN";
        $unit_weight = "LBS";

        if($r['shipment_type'] == "PD"){
            if($unit_type == "imperial"){
                $dimensions = array (
                    'weight' => @(float)$d['weight'],
                    'length' => @(float)$d['length'],
                    'width' => @(float)$d['width'],
                    'height' => @(float)$d['height'],
                );

            }else{

            $dimensions = array (
                    'weight' => $this->unitConvesion($d['weight'], "G", "LBS"),
                    'length' => $this->unitConvesion($d['length'], "CM", "IN"),
                    'width' => $this->unitConvesion($d['width'], "CM", "IN"),
                    'height' => $this->unitConvesion($d['height'], "CM", "IN"),
                );
            }

        }else{

            if($unit_type == "imperial"){
                $dimensions = array (
                    'weight' => @(float)$d['weight'],

                    );
            }else{
                $dimensions = array (
                        'weight' => $this->unitConvesion($d['weight'], "G", "LBS")

                    );

            }
        }

        // if($r['shipment_type'] == "PD"){
        //     $dimensions = array (
        //             'weight' => @(float)$d['weight'],
        //             'length' => @(float)$d['length'],
        //             'width' => @(float)$d['width'],
        //             'height' => @(float)$d['height'],
        //             // 'insured_currency' => 'USD',
        //             // 'insured_value' => 700,
        //         );

        // }else{
        //     $dimensions = array (
        //             'weight' => @(float)$d['weight']
        //         );
        // }


        if(@$r['_signature'] == "false" && @$r['_insurance']== "false"){
            //display original rate without  signature and insurance
        }
        else if(@$r['_signature'] == "true" || @$r['_insurance'] == "true"){
            if($r['_signature'] == "true"){
                if($signature_require){
                    $dimensions['signature_type'] = "DIRECT";
                }else{
                    // $dimensions['signature_type'] = "NO_SIGNATURE_REQUIRED";

                }
            }

            if($r['_insurance'] == "true"){

                if($insurance){
                    // $dimensions['insured_currency'] = $insurance['currency'];
                    $dimensions['insured_value'] = (float)$insurance['insured_value'];
                }
            }

        }else{

            if($insurance){
                $dimensions['insured_value'] = (float)$insurance['insured_value'];
            }

            if($signature_require){
                $dimensions['signature_type'] = "DIRECT";
            }else{
                // $dimensions['signature_type'] = "NO_SIGNATURE_REQUIRED";

            }
        }



        // if($insurance){
        //     $dimensions['insured_currency'] = $insurance['currency'];
        //     $dimensions['insured_value'] = (float)$insurance['insured_value'];
        // }

        // if($signature_require){
        //     $dimensions['signature_type'] = "DIRECT";
        // }else{
        //     $dimensions['signature_type'] = "NO_SIGNATURE_REQUIRED";
        // }
        // dd($dimensions);

        $data = array (
            'carrier' => 'UPS',
            'action' => 'GetAllRates',
            'params' => array (
            'key' => $this->UPSCA_key,
            'username' => $this->UPSCA_username,
            'password' => $this->UPSCA_password,
            'account_number' => $this->UPSCA_account_no,
            'packages' => array (
                  0 => $dimensions,
              ),
            "pickup_type"=> "01",
            "packaging_type"=> "02",
            'shipper' => $sender->first_name." ".$sender->last_name,
            'ship_addr1' => $ship_from_address['address_1'],
            'ship_addr2' => $ship_from_address['address_2'],
            'ship_city' => $ship_from_address['city'],
            'ship_state' => $ship_from_address['province'],
            'ship_code' => $ship_from_address['postal'],
            'ship_country' => $ship_from_address['country'],
            'weight_unit' => $unit_weight,
            'length_unit' => $unit_size,
            // 'ship_phone' => $profile->phone_no,
            'to_code' => $recipient['postal'],
            'to_country' => $recipient['country'],
            'to_state' => $recipient['province'],
            'residential' => true,
            'negotiated_rates' => true,
            // 'signature_type' => 'ADULT',
            'customer_classification' => '01',
            // "debug"=>true

        )
       );

        return $data;
    }


    public function getUPSCARates($r){

        $data = $this->UPSCA_data($r);
        // dd($data);
        $response = $this->request($data);
        // dd($response);
        return $response;

    }


    private function UPSUS_data($r){
        // $_data = $r;
        $d = @$r['parcel_dimensions_model'];
        $recipient = @$r['recipient_model'];
        $ship_from_address = $r['ship_from_address_model'];
        $sender = Sender::find(Auth::User()->sender_id);
        $insurance = @$r['insurance_model'];
        $item_information = @$r['item_information_model'];
        // $signature_require = @$r['signature_require_model'];
        $signature_require = @$r['postage_option_model']['req_signature'];

        $unit_type = $r['unit_type'];


        //default size and weight unit
        $unit_size = "IN";
        $unit_weight = "LBS";


        $customs = [];
        if($item_information){

            foreach ($item_information as $key => $value) {
                $customs[] = $value;
            }

        }

        // dd($r);

        if($r['shipment_type'] == "PD" && @$r['parcel_types']['usps_box_status'] == "no"){
            

                if($unit_type == "imperial"){
                    $dimensions = array (
                        'weight' => @(float)$d['weight'],
                        'length' => @(float)$d['length'],
                        'width' => @(float)$d['width'],
                        'height' => @(float)$d['height'],
                    );

                }else{

                $dimensions = array (
                        'weight' => $this->unitConvesion($d['weight'], "G", "LBS"),
                        'length' => $this->unitConvesion($d['length'], "CM", "IN"),
                        'width' => $this->unitConvesion($d['width'], "CM", "IN"),
                        'height' => $this->unitConvesion($d['height'], "CM", "IN"),
                    );
                }


        }else{
            if($unit_type == "imperial"){
                $dimensions = array (
                    'weight' => @(float)$d['weight'],

                    );
            }else{
                $dimensions = array (
                        'weight' => $this->unitConvesion($d['weight'], "G", "LBS")

                    );

            }
        }



        if(@$r['_signature'] == "false" && @$r['_insurance']== "false"){
            //display original rate without  signature and insurance
        }
        else if(@$r['_signature'] == "true" || @$r['_insurance'] == "true"){
            if($r['_signature'] == "true"){
                if($signature_require){
                    $dimensions['signature_type'] = "DIRECT";
                }
            }

            if($r['_insurance'] == "true"){
                if($insurance){
                    $dimensions['insured_currency'] = $insurance['currency'];
                    $dimensions['insured_value'] = (float)$insurance['insured_value'];
                }
            }

        }else{

            if($insurance){
                $dimensions['insured_currency'] = $insurance['currency'];
                $dimensions['insured_value'] = (float)$insurance['insured_value'];
            }

            if($signature_require){
                $dimensions['signature_type'] = "DIRECT";
            }
        }

        // if($insurance){
        //     $dimensions['insured_currency'] = $insurance['currency'];
        //     $dimensions['insured_value'] = (float)$insurance['insured_value'];
        // }

        // if($signature_require){
        //     $dimensions['signature_type'] = "DIRECT";
        // }


        $data = array (
            'carrier' => 'UPS',
            'action' => 'GetAllRates',
            'params' => array (
            'key' => $this->UPSUS_key,
            'username' => $this->UPSUS_username,
            'password' => $this->UPSUS_password,
            'account_number' => $this->UPSUS_account_no,
            'packages' => array (
                  0 => $dimensions,
              ),
            'customs'=>$customs,
            // "packaging_type"=> "02",
            "pickup_type"=> "01",
            "packaging_type"=> "02",
            'shipper' => $sender->first_name." ".$sender->last_name,
            'ship_addr1' => $ship_from_address['address_1'],
            'ship_addr2' => $ship_from_address['address_2'],
            'ship_city' => $ship_from_address['city'],
            'ship_state' => $ship_from_address['province'],
            'ship_code' => $ship_from_address['postal'],
            'ship_country' => $ship_from_address['country'],
            'weight_unit' => $unit_weight,
            'length_unit' => $unit_size,
            // 'ship_phone' => $profile->phone_no,
            'to_code' => $recipient['postal'],
            'to_country' => $recipient['country'],
            'to_state' => $recipient['province'],
            'negotiated_rates' => true,
            'residential' => true,
            'customer_classification' => '00',

        )
       );

        return $data;
    }


    public function getUPSUSRates($r){

        
        $data = $this->UPSUS_data($r);
        // dd($data);


        $response = $this->request($data);

        return $response;

    }


    private function STAMPS_data($r){
        $d = @$r['parcel_dimensions_model'];
        $p = @$r['parcel_types'];
        $recipient = @$r['recipient_model'];
        $ship_from_address = $r['ship_from_address_model'];
        $sender = Sender::find(Auth::User()->sender_id);
        // $signature_require = @$r['signature_require_model'];
        $signature_require = @$r['postage_option_model']['req_signature'];
        $insurance = @$r['insurance_model'];

        $unit_type = $r['unit_type'];



        $unit_size = "IN";
        $unit_weight = "LBS";


        // dd($ship_from_address);

        if($r['shipment_type'] == "PD" && @$r['parcel_types']['usps_box_status'] == "no"){

            if($unit_type == "imperial"){
                $dimensions = array (
                    'weight' => @(float)$d['weight'],
                    'length' => @(float)$d['length'],
                    'width' => @(float)$d['width'],
                    'height' => @(float)$d['height'],
                    );
            }else{

                $dimensions = array (
                    
                    'weight' => $this->unitConvesion($d['weight'], "G", "LBS"),
                    'length' => $this->unitConvesion($d['length'], "CM", "IN"),
                    'width' => $this->unitConvesion($d['width'], "CM", "IN"),
                    'height' => $this->unitConvesion($d['height'], "CM", "IN")
                    );

            }

        }else{

            if($unit_type == "imperial"){

                $dimensions = array (
                        'weight' => @(float)$d['weight']
                    );
            }else{
                $dimensions = array (
                        'weight' => $this->unitConvesion($d['weight'], "G", "LBS")
                    );
            }
        }



        if(@$r['_signature'] == "false" && @$r['_insurance']== "false"){
            //display original rate without  signature and insurance
        }
        else if(@$r['_signature'] == "true" || @$r['_insurance'] == "true"){
            if($r['_signature'] == "true"){
                if($signature_require){
                    $dimensions['signature_type'] = "DIRECT";
                }else{
                    $dimensions['signature_type'] = "NO_SIGNATURE_REQUIRED";

                }
            }

            if($r['_insurance'] == "true"){
                if($insurance){
                    // $dimensions['insured_currency'] = $insurance['currency'];
                    $dimensions['insured_value'] = (float)$insurance['insured_value'];
                }
            }

        }else{

            if($insurance){
                $dimensions['insured_value'] = (float)$insurance['insured_value'];
            }

            if($signature_require){
                $dimensions['signature_type'] = "DIRECT";
            }else{
                $dimensions['signature_type'] = "NO_SIGNATURE_REQUIRED";

            }
        }


        // if($insurance){
        //     $dimensions['insured_currency'] = $insurance['currency'];
        //     $dimensions['insured_value'] = (float)$insurance['insured_value'];
        // }


        if($signature_require){
            // $dimensions['signature_type'] = "DIRECT";
            // $dimensions['packaging_type'] = "Package";
        }


        $data = array (
            'carrier' => 'stamps',
            'action' => 'GetAllRates',
            'params' => array (
                'key' => $this->UPSUS_key,
                'username' => $this->STAMPS_username,
                'password' => $this->STAMPS_password,
                // 'account_number' => $this->UPSUS_account_no,
                'packages' => array (
                      0 => $dimensions,
                  ),
                // "packaging_type"=> "02",
                'shipper' => $sender->first_name." ".$sender->last_name,
                'ship_addr1' => $ship_from_address['address_1'],
                'ship_addr2' => $ship_from_address['address_2'],
                'ship_city' => $ship_from_address['city'],
                'ship_state' => $ship_from_address['province'],
                'ship_code' => $ship_from_address['postal'],
                'ship_country' => $ship_from_address['country'],
                'weight_unit' => $unit_weight,
                'length_unit' => $unit_size,
                // 'ship_phone' => $profile->phone_no,
                'to_code' => $recipient['postal'],
                'to_country' => $recipient['country'],
                'test'=>$this->test,
                'residential' => true,

                "addons"=> [
                    "SC-A-HP", // hidden postage
                    "SC-A-INS", // insurance
                ],
            )
       );

        if($signature_require){
            $data['params']['signature_required'] = true;
        }

        return $data;
    }


    public function getSTAMPSRates($r){

        
        // $_data = $r;
        // dd($r);
        
        // dd($data);
        // dd($data);
        $data = $this->STAMPS_data($r);
        $response = $this->request($data);
        // dd($response);

        return $response;

    }


    public function getUSPSBoxOptions($r){

       
        $_data = $r;
        $p = @$r['parcel_types'];
        $d = @$r['usps_options_model'];
        $recipient = $r['recipient_model'];
        $ship_from_address = $r['ship_from_address_model'];
        
        $unit_type = $r['unit_type'];


        if($unit_type == "imperial"){
            $unit_size = "IN";
            $unit_weight = "LBS";
            $dimensions = array (
                    'weight' => @(float)$d['weight'],
            );
        }else{
            $unit_size = "CM";
            $unit_weight = "KG";
            $dimensions = array (
                    'weight' => $this->unitConvesion($d['weight'], "G", "KG"),
            );
        }

        // dd($ship_from_address);


        $data = array (
            'carrier' => 'stamps',
            'action' => 'GetAllRates',
            'params' => array (
            'key' => $this->UPSUS_key,
            'username' => $this->STAMPS_username,
            'password' => $this->STAMPS_password,
            // 'account_number' => $this->UPSUS_account_no,
            'packages' => array (
                  0 => $dimensions,
              ),
            // "packaging_type"=> "02",
            'shipper' => "CBP",
            'ship_addr1' => $ship_from_address['address_1'],
            'ship_addr2' => $ship_from_address['address_2'],
            'ship_city' => $ship_from_address['city'],
            'ship_state' => $ship_from_address['province'],
            'ship_code' => $ship_from_address['postal'],
            'ship_country' => $ship_from_address['country'],
            'weight_unit' => $unit_weight,
            'length_unit' => $unit_size,
            // 'ship_phone' => $profile->phone_no,
            'to_code' => $recipient['postal'],
            'to_country' => $recipient['country'],
            'test'=>$this->test
        )
       );
        // dd($data);
        // dd($data);


        $response = $this->request($data);
        return $response;

    }


    private function FedEx_data($r){
        // $signature_require = @$r['signature_require_model'];
        $signature_require = @$r['postage_option_model']['req_signature'];
        $d = $r['parcel_dimensions_model'];
        $recipient = $r['recipient_model'];
        $ship_from_address = $r['ship_from_address_model'];
        $sender = Sender::find(Auth::User()->sender_id);
        $insurance = @$r['insurance_model'];

        $unit_type = $r['unit_type'];


        //default size and weight unit
        $unit_size = "IN";
        $unit_weight = "LBS";


        if($r['shipment_type'] == "PD"){
            if($unit_type == "imperial"){
                $dimensions = array (
                    'weight' => @(float)$d['weight'],
                    'length' => @(float)$d['length'],
                    'width' => @(float)$d['width'],
                    'height' => @(float)$d['height'],
                );

            }else{

            $dimensions = array (
                    'weight' => $this->unitConvesion($d['weight'], "G", "LBS"),
                    'length' => $this->unitConvesion($d['length'], "CM", "IN"),
                    'width' => $this->unitConvesion($d['width'], "CM", "IN"),
                    'height' => $this->unitConvesion($d['height'], "CM", "IN"),
                );
            // print_r($dimensions);exit;
            }

        }else{

            if($unit_type == "imperial"){
                $dimensions = array (
                    'weight' => @(float)$d['weight'],

                    );
            }else{
                $dimensions = array (
                        'weight' => $this->unitConvesion($d['weight'], "G", "LBS")

                    );

            }
        }

        // if($r['shipment_type'] == "PD"){
        //     $dimensions = array (
        //             'weight' => @(float)$d['weight'],
        //             'length' => @(float)$d['length'],
        //             'width' => @(float)$d['width'],
        //             'height' => @(float)$d['height'],
        //         );


        // }else{
        //     $dimensions = array (
        //             'weight' => @(float)$d['weight']
        //         );
        // }


        if(@$r['_signature'] == "false" && @$r['_insurance']== "false"){
            //display original rate without  signature and insurance
        }
        else if(@$r['_signature'] == "true" || @$r['_insurance'] == "true"){
            if($r['_signature'] == "true"){
                if($signature_require){
                    $dimensions['signature_type'] = "DIRECT";
                }else{
                    $dimensions['signature_type'] = "NO_SIGNATURE_REQUIRED";

                }
            }

            if($r['_insurance'] == "true"){

                if($insurance){
                    $dimensions['insured_currency'] = $insurance['currency'];
                    $dimensions['insured_value'] = (float)$insurance['insured_value'];

                }
            }

        }else{

            if($insurance){
                $dimensions['insured_currency'] = $insurance['currency'];
                $dimensions['insured_value'] = (float)$insurance['insured_value'];

            }

            if($signature_require){
                $dimensions['signature_type'] = "DIRECT";
            }else{
                $dimensions['signature_type'] = "NO_SIGNATURE_REQUIRED";

            }
        }

        // if($insurance){
        //     $dimensions['insured_currency'] = $insurance['currency'];
        //     $dimensions['insured_value'] = (float)$insurance['insured_value'];
        // }

        // if($signature_require){
        //     $dimensions['signature_type'] = "DIRECT";
        // }else{
        //     $dimensions['signature_type'] = "NO_SIGNATURE_REQUIRED";
        // }

        $data = array (
            'carrier' => 'FedEx',
            'action' => 'GetAllRates',
            'params' => array (
            'key' => $this->FEDEX_key,
            'meter_number' => $this->FEDEX_meter_no,
            'password' => $this->FEDEX_password,
            'account_number' => $this->FEDEX_account_no,
            'packages' => array (
                  0 => $dimensions
              ),
            'packaging_type' => 'YOUR_PACKAGING',
            // 'dropoff_type' => 'REGULAR_PICKUP',
            "customer_classification"=>"00",
            'shipper' => $sender->first_name." ".$sender->last_name,
            'ship_addr1' => $ship_from_address['address_1'],
            'ship_addr2' => $ship_from_address['address_2'],
            'ship_city' => $ship_from_address['city'],
            'ship_state' => $ship_from_address['province'],
            'ship_code' => $ship_from_address['postal'],
            'ship_country' => $ship_from_address['country'],
            'weight_unit' => $unit_weight,
            'length_unit' => $unit_size,
            // 'ship_phone' => $profile->phone_no,
            'to_code' => $recipient['postal'],
            'to_country' => $recipient['country'],
            'to_state' => $recipient['province'],
             'negotiated_rates' => true,
             'test' =>  $this->test,

        )
       );


        return $data;
    }


    public function getFedExRates($r){

        // $ship_from_address = SenderMailingAddress::where("country","US")->get()->first();
        
        // $_data = $r;
        
        // dd($data);
        // dd($data);

        $data = $this->FedEx_data($r);
        $response = $this->request($data);

        return $response;

    }


    private function DHL_data($r){
        $d = @$r['parcel_dimensions_model'];
        $recipient = @$r['recipient_model'];
        $ship_from_address = $r['ship_from_address_model'];
        $sender = Sender::find(Auth::User()->sender_id);
        $insurance = @$r['insurance_model'];
        $item_information = @$r['item_information_model'];
        // $signature_require = @$r['signature_require_model'];
        $signature_require = @$r['postage_option_model']['req_signature'];
        $unit_type = $r['unit_type'];


        if($unit_type == "imperial"){
            $unit_size = "IN";
            $unit_weight = "LB";
        }else{
            $unit_size = "CM";
            $unit_weight = "KG";
        }

        // dd($this->DHL_password);
        // dd($ship_from_address);
        // dd($recipient);
        // http_response_code(500);
        // dd($ship_from_address->city);
        // dd($this->DHL_password);
        $dimensions = array (
                    'weight' => (float)@$d['weight'],
                    'length' => (float)@$d['length'],
                    'width' => (float)@$d['width'],
                    'height' => (float)@$d['height'],
                );

        if($insurance){
            $dimensions['insured_currency'] = $insurance['currency'];
            $dimensions['insured_value'] = (float)$insurance['insured_value'];
        }

        if($signature_require){
            $dimensions['signature_type'] = "DIRECT";
        }


        $customs_value = 0;
        if($item_information){

            foreach ($item_information as $key => $value) {
                $customs_value += (float)$value['value'];
            }

        }


        $data = array (
            'carrier' => 'DHL',
            'action' => 'GetAllRates',
            'params' => array (

            'username' => $this->DHL_username,
            'password' => $this->DHL_password,
            'customs_value' => (float)$customs_value,
            'currency' => 'USD',
            'packages' => array (
                  0 => $dimensions,
              ),
            // "packaging_type"=> "02",
            'shipper' => $sender->first_name." ".$sender->last_name,
            'ship_addr1' => $ship_from_address['address_1'],
            'ship_addr2' => $ship_from_address['address_2'],
            'ship_city' => $ship_from_address['city'],
            'ship_state' => $ship_from_address['province'],
            'ship_code' => $ship_from_address['postal'],
            'ship_country' => $ship_from_address['country'],
            'weight_unit' => $unit_weight,
            'length_unit' => $unit_size,
            'to_code' => $recipient['postal'],
            'to_country' => $recipient['country'],
            'test' => $this->test,

        )
       );



        return $data;
    }

    public function getDHLRates($r){

        $data = $this->DHL_data($r);

        $response = $this->request($data);
        dd($response);
        return $response;

    }



    public function validateAddress($r){
        $data = array(
            'carrier' => 'canada',
            'action' => 'AddressValidate',
            'params' => array(
                // 'key' => $this->UPSUS_key,
            //    'username' => "ashfaq.owais",
            // 'password' => "Toront12",
                'to_name' => 'John Doe',
                'to_addr1' => '2920 Zoo Drive',
                'to_state' => 'CA',
                'to_city' => 'San Diego',
                'to_code' => '92112',
                'to_country' => 'US',
                'test' => $this->test,
            )
        );


        $response = $this->request($data);
        dd($response);
        return $response;
    }


}

?>