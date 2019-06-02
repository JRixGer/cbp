<?php

namespace cbp\Http\Controllers;

use Illuminate\Http\Request;
use \cbp\Sender;
use \cbp\User;
use \cbp\SenderPhysicalAddress;
use \cbp\SenderMailingAddress;

use \cbp\Http\Requests\SenderMailingAddressRequest;
use Auth;

class ShipFromAddressController extends Controller
{
    

    public function __construct(){
    	$this->middleware(['auth','verified']);
    }


    public function register(SenderMailingAddressRequest $r){

        $data = $r->toArray();
        $sma = new SenderMailingAddress;
        $sender_id = Auth::User()->sender_id;
        $sma_data = [
            'sender_id' => $sender_id,
            'address_1' => $data['address_1'],
            'address_2' => @$data['address_2'],
            'city' => $data['city'],
            'province' => $data['province'],
            'postal' => $data['postal'],
            'country' => $data['country'],
        ];
        $res = $sma->updateOrCreate(['sender_id'=>$sender_id, 
                                    'country'=>$data['country']
                            ],$sma_data);

        if($res){
           return Response()->json(['status' => true]);
        }else{
           return Response()->json(['status' => false]);
        }

    }


    public function getShipFromAddresses(){
        $sma_CA = SenderMailingAddress::where("sender_id",Auth::User()->sender_id)
                                    ->where("country","CA")
                                    ->get()->first();

        $sma_US = SenderMailingAddress::where("sender_id",Auth::User()->sender_id)
                                    ->where("country","US")
                                    ->get()->first();
        return Response()->json(['ca_address'=>$sma_CA, 'us_address'=>$sma_US ]);

    }



}
