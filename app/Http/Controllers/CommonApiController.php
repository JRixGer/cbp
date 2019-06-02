<?php

namespace cbp\Http\Controllers;

use Illuminate\Http\Request;
use \cbp\Country;
use \cbp\User;
use Auth;
class CommonApiController extends Controller
{
    

    public function getCountries(){
    	return Response()->json(Country::orderBy("name")->get());
    }


	public function getUserProfile(){

	print_r(Auth::User());
	exit;
    	// $data = User::with(['sender'])->where("id",Auth::User()->sender_id)->get();
    	// return Response()->json($data);
    }
}
