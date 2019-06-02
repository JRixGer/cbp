<?php

namespace cbp\Http\Controllers;

use Illuminate\Http\Request;
use \cbp\Sender;
use \cbp\User;
use \cbp\SenderPhysicalAddress;
use \cbp\SenderBusiness;
use \cbp\SenderMailingAddress;
use \cbp\SenderPowerOfAtty;
use \cbp\CBPAddress;
use \cbp\Http\Requests\ProfileRegistrationRequest;
use \cbp\Http\Requests\BusinessRegistrationRequest;
use \cbp\Http\Requests\SenderMailingAddressRequest;
use Auth;
use Validator;
use DB;
use PDF;
use cbp\Http\Controllers\Mail;
use Session;

use Illuminate\Support\Facades\Hash;

class SenderController extends Controller
{
    

    public function __construct(){
    	$this->middleware(['auth','verified']);
    }

    public function getUserInfo(){

        $data = User::leftJoin("senders as s","s.id","=","users.sender_id")
                        ->where("users.id",Auth::User()->id)
                        ->select("users.email",DB::raw("CONCAT(s.first_name,' ',s.last_name) as name"))
                        ->get()->first();
        return Response()->json($data);
    }
    
    public function getAccountInfo(){
        $data = Sender::with(['sender_physical_address'])->where("id",Auth::User()->sender_id)
                        ->get()->first();
        return Response()->json($data);
    }

    public function getBusinessInfo(){
        $data = SenderBusiness::where("sender_id",Auth::User()->sender_id)
                        ->get()->first();
        return Response()->json($data);
    }

    public function getMailingInfo(){
        $data = SenderMailingAddress::where("sender_id",Auth::User()->sender_id)
                        ->get()->first();
        return Response()->json($data);
    }

    public function getUserAccountInfo(){

        $userInfo = Sender::with(['sender_physical_address','sender_mailing_address','sender_business_address','sender_power_of_atty'])->where("id",Auth::User()->sender_id)
                        ->get()->first();
        
        $usAddress = CBPAddress::where("country","US")
                        ->get()->first();
        return Response()->json(['userInfo' => $userInfo, 'usAddress' => $usAddress, 'loggedId' => Auth::User()->id, 'loggedEmail' => Auth::User()->email]);
    }
 

    public function updateUserAccountInfo(ProfileRegistrationRequest $r){

        $data = $r->toArray();
        $s = new Sender;
        $s_data = [
            'first_name'=>$data['first_name'],
            'last_name'=>$data['last_name'],
            'email'=>$data['email'],
            'contact_no'=>$data['contact_no'],
        ];
        $res = $s->updateOrCreate(['id'=>$data['sender_id']], $s_data);
        $sender_id = $res->id;

        #set physical address
        $spa = new SenderPhysicalAddress;
        $spa_data = [
            'sender_id' => $sender_id,
            'address_1' => $data['address_1'],
            'address_2' => @$data['address_2'],
            'city' => $data['city'],
            'province' => $data['province'],
            'postal' => $data['postal'],
            'country' => $data['country'],
        ];
        $res = $spa->updateOrCreate(['id'=>$data['address_id']], $spa_data);

        if($res){
           return Response()->json(['status' => true]);
        }else{
           return Response()->json(['status' => false]);
        }

    }

    public function updateUserAccountPOA(Request $r){

        $data = $r->toArray();
        
        //dd($data);

        $poa = new SenderPowerOfAtty;
        if($data['poa']['has_canadian_in'] == 0)
        {
            $poa_ina_data = [
            'import_number' => $data['import_number']['import_number'],
            'id' => $data['poa']['sender_id'],
            'sender_id' => $data['poa']['sender_id'],
            'business_name' => $data['poa']['business_name'],
            'address_1' => $data['poa']['address_1'],
            'city' => $data['poa']['city'],
            'country' => $data['poa']['country'],
            'province_state' => $data['poa']['province_state'],
            'full_name_of_corp_client' => $data['poa']['full_name_of_corp_client'],
            'name_of_municipality' => $data['poa']['name_of_municipality'],
            'name_of_province_state' => $data['poa']['name_of_province_state'],
            'name_of_country' => $data['poa']['name_of_country'],
            'name_of_signing_authority_1' => $data['poa']['name_of_signing_authority_1'],
            'office_held_by_signing_authority_1' => $data['poa']['office_held_by_signing_authority_1'],
            'signature_1' => $data['poa']['signature_1'],
            'name_of_signing_authority_2' => $data['poa']['name_of_signing_authority_2'],
            'office_held_by_signing_authority_2' => $data['poa']['office_held_by_signing_authority_2'],
            'signature_2' => $data['poa']['signature_2'],

            'business_number' => $data['ina']['business_number'],
            'language' => $data['ina']['language'],
            'operating_trade' => $data['ina']['operating_trade'],
            'import_export_program_account_name' => $data['ina']['import_export_program_account_name'],
            'physical_business_location' => $data['ina']['physical_business_location'],
            'physical_city' => $data['ina']['physical_city'],
            'physical_province_state' => $data['ina']['physical_province_state'],
            'physical_postal_zip_code' => $data['ina']['physical_postal_zip_code'],
            'physical_country' => $data['ina']['physical_country'],
            'contact_person_title' => $data['ina']['contact_person_title'],
            'contact_person_first_name' => $data['ina']['contact_person_first_name'],
            'contact_person_last_name' => $data['ina']['contact_person_last_name'],
            'contact_person_work_tel_no' => $data['ina']['contact_person_work_tel_no'],
            'contact_person_ext' => $data['ina']['contact_person_ext'],
            'contact_person_work_fax_no' => $data['ina']['contact_person_work_fax_no'],
            'contact_person_mobile_no' => $data['ina']['contact_person_mobile_no'],
            'transport' => $data['ina']['transport'],
            'type_of_goods' => $data['ina']['type_of_goods'],
            'estimated_annual_value' => $data['ina']['estimated_annual_value'],
            'major_bus_description' => $data['ina']['major_bus_description'],
            'major_bus_product_services_1' => $data['ina']['major_bus_product_services_1'],
            'revenue_from_product_services_1' => $data['ina']['revenue_from_product_services_1'],
            'major_bus_product_services_2' => $data['ina']['major_bus_product_services_2'],
            'revenue_from_product_services_2' => $data['ina']['revenue_from_product_services_2'],
            'major_bus_product_services_3' => $data['ina']['major_bus_product_services_3'],
            'revenue_from_product_services_3' => $data['ina']['revenue_from_product_services_3'],
            'partner_type' => $data['ina']['partner_type'],
            'sign_title' => $data['ina']['sign_title'],
            'sign_first_name' => $data['ina']['sign_first_name'],
            'sign_last_name' => $data['ina']['sign_last_name'],
            'sign_tel_no' => $data['ina']['sign_tel_no'],
            'sign_signature' => $data['ina']['sign_signature']
            ];
        }
        else
        {
            $poa_ina_data = [
            'import_number' => $data['import_number']['import_number'],
            'id' => $data['poa']['sender_id'],
            'sender_id' => $data['poa']['sender_id'],
            'business_name' => $data['poa']['business_name'],
            'address_1' => $data['poa']['address_1'],
            'city' => $data['poa']['city'],
            'country' => $data['poa']['country'],
            'province_state' => $data['poa']['province_state'],
            'full_name_of_corp_client' => $data['poa']['full_name_of_corp_client'],
            'name_of_municipality' => $data['poa']['name_of_municipality'],
            'name_of_province_state' => $data['poa']['name_of_province_state'],
            'name_of_country' => $data['poa']['name_of_country'],
            'name_of_signing_authority_1' => $data['poa']['name_of_signing_authority_1'],
            'office_held_by_signing_authority_1' => $data['poa']['office_held_by_signing_authority_1'],
            'signature_1' => $data['poa']['signature_1'],
            'name_of_signing_authority_2' => $data['poa']['name_of_signing_authority_2'],
            'office_held_by_signing_authority_2' => $data['poa']['office_held_by_signing_authority_2'],
            'signature_2' => $data['poa']['signature_2']
            ];            
        }
       
        $res = $poa->updateOrCreate(['id'=>$data['poa']['id']], $poa_ina_data);

        if($res){
           return Response()->json(['status' => true]);
        }else{
           return Response()->json(['status' => false]);
        }

    }

    public function docrequest(Request $r){

        $userInfo = Sender::with(['sender_physical_address','sender_mailing_address','sender_business_address','sender_power_of_atty'])->where("id",Auth::User()->sender_id)
                        ->get()->first();

        return Response()->json(['status' => true, 'data' => $userInfo]);         
    }
    
    public function poa_download(Request $r)
    {

        $data = $r->toArray();
        
        if(!$data){
            $data = [];
        }
        $pdf = PDF::loadView('documents.user_poa', $data);
        $r->session()->forget("biz");
        return $pdf->download('poa.pdf');
    }

    public function ina_download(Request $r)
    {
        
        $data = $r->toArray();
        if(!$data){
            $data = [];
        }
        $pdf = PDF::loadView('documents.user_ina', $data);
        $r->session()->forget("biz");
        return $pdf->download('ina.pdf');
    }


    public function pwResetEmailConfirmation(Request $r)
    {
        $data = $r->toArray();

        if(!$data){
            $data = [];
        }
        $e = $data['userInfo']['email'];
        //$e = "jrixgeromO@gmail.com";
        \Mail::send('documents.pw_reset', $data, function($message) use($e)
        {

            $message->from('dthai@crossborderpickups.ca', 'CBP');
            $message->to($e)->subject('Password Reset Confirmation');
        });
    }

    public function emUpdateEmailConfirmation(Request $r)
    {
        $data = $r->toArray();

        if(!$data){
            $data = [];
        }
        
        $e = $data['userInfo']['loggedEmail'];
        //$e = "jrixgeromO@gmail.com";
        \Mail::send('documents.email_change', $data, function($message) use($e)
        {

            $message->from('dthai@crossborderpickups.ca', 'CBP');
            $message->to($e)->subject('Login Email Update Confirmation');
        });
    }

    public function poaEmail(Request $r)
    {
        $data = $r->toArray();

        if(!$data){
            $data = [];
        }
        $pdf = PDF::loadView('documents.user_poa', $data);
        \Mail::send('documents.user_poa', $data, function($message) use($pdf)
        {
            $message->from('dthai@crossborderpickups.ca', 'CBP');
            $message->to('dthai@crossborderpickups.ca')->subject('Power of Atty');
            $message->cc(Auth::User()->email);
            $message->attachData($pdf->output(), "poa.pdf");
        });
    }

    public function inaEmail(Request $r)
    {
        
        $data = $r->toArray();
        if(!$data){
            $data = [];
        }

        $pdf = PDF::loadView('documents.user_ina', $data);

        \Mail::send('documents.user_ina', $data, function($message) use($pdf)
        {
            $message->from('dthai@crossborderpickups.ca', 'CBP');
            $message->to('dthai@crossborderpickups.ca')->subject('Import Number Application');
            $message->cc(Auth::User()->email);
            $message->attachData($pdf->output(), "ina.pdf");
        });
    }

    
    // protected function resetPassword($user, $password)
    // {
    //     $user->password = Hash::make($password);

    //     $user->setRememberToken(Str::random(60));

    //     $user->save();

    //     event(new PasswordReset($user));

    //     $this->guard()->login($user);
    // }


    public function profileRegistration(ProfileRegistrationRequest $r){

    	// dd(Auth::User()->email);
    	$data = $r->toArray();
        $data['email'] = Auth::User()->email;
        // dd($data);
        $s = new Sender;
        $s_data = [
            'first_name'=>$data['first_name'],
            'last_name'=>$data['last_name'],
            'email'=>$data['email'],
            'has_business'=>  (@$data['has_business']) ? 1 : 0 ,
            'referral'=> @$data['referral'],
            'marketing_emails'=> (@$data['marketing_emails']) ? 1 : 0,
            'import_number'=>@$data['import_number']
        ];
        $res = $s->updateOrCreate(['email'=>$data['email']], $s_data);
        $sender_id = $res->id;

        // dd($sender_id);
        #mailing address
        if(@$data['mailing_address']){
            $mailing_address_id = $this->mailingAddress($data['mailing_address'], $sender_id);
        }

        #business regisrtaion
        if(@$data['business_registration']){
            $this->businessRegistration($data['business_registration'], $sender_id, $mailing_address_id);
        }

        #set physical address
        $spa = new SenderPhysicalAddress;
        $spa_data = [
            'sender_id' => $sender_id,
            'address_1' => $data['address_1'],
            'address_2' => @$data['address_2'],
            'city' => $data['city'],
            'province' => $data['province'],
            'postal' => $data['postal'],
            'country' => $data['country'],
        ];
        $res = $spa->updateOrCreate($spa_data);
        
        #set sender_id in users table
        $u = User::find(Auth::User()->id);
    	$u->sender_id = $sender_id;
    	$res = $u->save();
        if($res){
           return Response()->json(['status' => true, 'id' => $u->id]);
        }else{
    	   return Response()->json(['status' => false]);
        }
    }



    public function updateProfileRegistration($r){

        // dd(Auth::User()->email);
        $data = $r->toArray();
        $data['email'] = Auth::User()->email;
        // dd($data);

        if(@$data['import_number_model']['with_import_number'] =="yes"){
            $import_number = @$data['import_number_model']['import_number'];
        }else{
            $import_number = "";
        }
    
        $sender_id = $this->senderRegistration($data['account_info_model'], $import_number);
    

        #mailing address
        $mailing_address_id = null;
        if(@$data['business_address_model']["mailing_address_chk"]  == true && @$data['mailing_address_model']){
            $mailing_address_id = $this->mailingAddress($data['mailing_address_model'], $sender_id);
        }

        #business regisrtaion
        if(@$data['business_address_model']){

            $this->businessRegistration($data['business_address_model'], $sender_id, $mailing_address_id);
        }

        #set physical address
        $res = $this->SenderPhysicalAddress($data['account_info_model'], $sender_id);

        if($res){
            return Response()->json(['status'=>true]);
        }else{
            
            return Response()->json(['status'=>false]);
        }

    }


    public function SenderPhysicalAddress($data, $sender_id){
        $spa = new SenderPhysicalAddress;
        $spa_data = [
            'sender_id' => $sender_id,
            'address_1' => $data['address_1'],
            'address_2' => @$data['address_2'],
            'city' => $data['city'],
            'province' => $data['province'],
            'postal' => $data['postal'],
            'country' => $data['country'],
        ];
        $res = $spa->updateOrCreate($spa_data);
        return $res;
    }


    public function senderRegistration($data, $import_number){
        $s = new Sender;
        $s_data = [
            'first_name'=>$data['first_name'],
            'last_name'=>$data['last_name'],
            'email'=>$data['email'],
            'has_business'=>  (@$data['has_business']) ? 1 : 0 ,
            'referral'=> @$data['referral'],
            'marketing_emails'=> (@$data['marketing_emails']) ? 1 : 0,
            'import_number'=> $import_number
        ];
        $res = $s->updateOrCreate(['email'=>$data['email']], $s_data);
        return  $res->id;
    }


    public function businessRegistration($data, $sender_id, $mailing_address_id){
        $sb = new SenderBusiness;
        // dd($data);
        // if(@$data['import_number']){
        //     $sb_data = [
        //         'sender_id' => $sender_id,
        //         'import_number' => $data['import_number'],
        //         'business_name' => "",
        //         'business_number' => "",
        //         'business_location' => "",
        //         'address_1' => "",
        //         'address_2' => "",
        //         'city' => "",
        //         'province' => "",
        //         'postal' => "",
        //         'country' => "",
        //     ];
        // }else{

            $sb_data = [
                'sender_id' => $sender_id,
                'business_name' => $data['business_name'],
                'business_number' => @$data['business_number'],
                'business_location' => @$data['business_location'],
                'address_1' => $data['address_1'],
                'address_2' => @$data['address_2'],
                'city' => $data['city'],
                'province' => $data['province'],
                'postal' => $data['postal'],
                'country' => $data['country'],
            ];

            // if($mailing_address_id){
                $sb_data['sender_mailing_address_id'] = $mailing_address_id;
            // }
        // }
        $res = $sb->updateOrCreate(['sender_id'=>$sender_id],$sb_data);

        if($res){
           return Response()->json(['status' => true]);
        }else{
           return Response()->json(['status' => false]);
        }
    }


    public function mailingAddress($data, $sender_id){
        $sma = new SenderMailingAddress;

        $sma_data = [
            'sender_id' => $sender_id,
            'address_1' => $data['address_1'],
            'address_2' => @$data['address_2'],
            'city' => $data['city'],
            'province' => $data['province'],
            'postal' => $data['postal'],
            'country' => $data['country'],
        ];
        $res = $sma->updateOrCreate(['sender_id'=>$sender_id],$sma_data);

        return $res->id;
    }

    public function updateProfile(Request $r){
        $errors = [];

        $data = $r->all();
        $data['account_info_model'] = (@$data['account_info_model']) ? @$data['account_info_model'] :[];
        $data['business_address_model'] = (@$data['business_address_model']) ? @$data['business_address_model'] :[];
        $data['mailing_address_model'] = (@$data['mailing_address_model']) ? @$data['mailing_address_model'] :[];

        // print_r($r->all());
        $validateAccountInfo = Validator::make(@$data['account_info_model'],[
            'first_name' => 'required',
            'last_name' => "required",
            'address_1' => "required",
            'city' => "required",
            'province' => "required",
            'country' => "required",
            'postal' => "required",
        ]);

        if($validateAccountInfo->fails()){
            $errors['account_info'] =$validateAccountInfo->messages();
        }



        if(@$data['import_number_model']['with_import_number']== "no"){
            $validateBusinessAddress = Validator::make(@$data['business_address_model'],[
                'business_name' => 'required',
                'business_number' => "required",
                'business_location' => "required",
                'address_1' => "required",
                'city' => "required",
                'province' => "required",
                'country' => "required",
                'postal' => "required",
            ]);

            if($validateBusinessAddress->fails()){
                $errors['business_address'] =$validateBusinessAddress->messages();
            }
        }else{

            if(@$data['import_number_model']){

                $validateImportNumber = Validator::make($data['import_number_model'],[
                    'import_number' => 'required',
                ]);
                
                if($validateImportNumber->fails()){
                    $errors['import_number'] =$validateImportNumber->messages();
                }
            }
            
        }




        if(@$data['business_address_model']['mailing_address_chk']){

            $validateBusinessAddress = Validator::make($data['mailing_address_model'],[

                'address_1' => "required",
                'city' => "required",
                'province' => "required",
                'country' => "required",
                'postal' => "required",
            ]);

            if($validateBusinessAddress->fails()){
                $errors['mailing_address'] =$validateBusinessAddress->messages();
            }
        }
    


       

        if($errors){

            return Response($errors, 422);
        }else{
            $this->updateProfileRegistration($r);
        }


    }


    public function validateBusinessRegistration(BusinessRegistrationRequest $r){
        //;validate 
        return Response()->json(['status' => true]);

    }


    public function validateMailingAddressRegistration(SenderMailingAddressRequest $r){
        //;validate 
        return Response()->json(['status' => true]);

    }


    public function getShipFromAddresses(){
       

        $sma_CA = CBPAddress::where("country","CA")->where("province","ON")->get()->first();

        $sma_US = CBPAddress::where("country","US")
                                    ->get()->first();
        return Response()->json(['ca_address'=>$sma_CA, 'us_address'=>$sma_US ]);

    }



}
