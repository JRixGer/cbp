<?php

namespace cbp\Http\Controllers;

use Illuminate\Http\Request;
use \cbp\Recipient;
use \cbp\User;
use \cbp\Http\Requests\RecipientRegistrationRequest;
use Auth;

class RecipientController extends Controller
{
    

    public function __construct(){
    	$this->middleware(['auth','verified']);
    }


    public function register(RecipientRegistrationRequest $r){

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
            'marketing_emails'=> (@$data['marketing_emails']) ? 1 : 0
        ];
        $res = $s->updateOrCreate(['email'=>$data['email']], $s_data);
        $sender_id = $res->id;

        // dd($sender_id);
        #mailing address
        if(@$data['mailing_address']){
            $this->mailingAddress($data['mailing_address'], $sender_id);
        }

        #business regisrtaion
        if(@$data['business_registration']){
            $this->businessRegistration($data['business_registration'], $sender_id);
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
        $res = $spa->firstOrCreate($spa_data);
        
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

    public function justValidate(RecipientRegistrationRequest $r){
        // runs validation request
        // nothing follows
    }


}
