<?php

namespace cbp\Http\Controllers;
use cbp\Http\Requests;
use Illuminate\Http\Request;
use Validator;
use URL;
use Session;
use Redirect;
use Input;
use cbp\User;
use cbp\Sender;
use Stripe\Error\Card;
use Illuminate\Support\Facades\DB;
use Cartalyst\Stripe\Stripe;
use Auth;

class AddMoneyController extends Controller
{
	public function __construct(){
    	$this->middleware(['auth','verified']);
    }
    
	public function payWithStripe()
	 {
		 return view('paywithstripe');
	 }
	
	public function getWalletDetail($type)
	{
		
		 //echo $type;

		 $userID = Auth::User()->sender_id;

		 if($type == 'All')
			 $transaction_history = DB::table('transaction_history')
 			 	->select('id',
	                'user_id',
	                'amount',
	                'status',
	                'type',
	                'created_at',
	                'updated_at',
	                'amount',
	                'pay_with',
	                'fee',
	                DB::raw("SUM(IF(type='Credit',amount,0)) as credit_added"),
	                DB::raw("SUM(IF(type='Debit',amount,0)) as credit_used")
	            )    
				->where('user_id', $userID)->groupBy('id')->orderBy('id')->get();
		 else
		 	 $transaction_history = DB::table('transaction_history')
 			 	->select('id',
	                'user_id',
	                'amount',
	                'status',
	                'type',
	                'created_at',
	                'updated_at',
	                'amount',
	                'pay_with',
	                'fee'	                
	            )   
		 	 ->where('user_id', $userID)->where('type', $type)->orderBy('id')->get();

		 $wallet = DB::table('wallet')->where('user_id', $userID)->first();
		 if($wallet){
			return Response()->json(['cash' => $wallet->cash, 'transaction_history' => $transaction_history]);
         }else{
			return Response()->json(['status' => false]);
         }

	}

	public function payWithWallet(Request $request)
	{

		 $input = $request->all();
		 //echo'<pre>';print_r($input);die;
		 $userID = Auth::User()->sender_id;
		 $wallet = DB::table('wallet')->where('user_id', $userID)->first();

		 $amount = $request->get('amount');	 
		 $batchIds = $request->get('batchIds');	  
		 $fromWhere = $request->get('fromWhere');	  

		 if ($wallet && $wallet->cash > 0) {
		    $result = DB::table('wallet')
				 ->where('user_id', $userID)
				 ->decrement('cash' , $amount, ['description' => 'test', 'history' => 'test']);
			     DB::table('transaction_history')->insert( ['user_id' => $userID, 'amount' => $amount, 'status' => 'Success', 'type' => 'Debit', 'pay_with' => 'wallet']
			);
 
			if($result){
				updatePaidShipments($batchIds, $fromWhere); 	
				return Response()->json(['status' => true, 'walletSuccess' => 'Payment Successfully Done']);
			}else{
				return Response()->json(['status' => false, 'walletError' => 'Something went wrong please try again']);
			}
		

		 } else {
			  return Response()->json(['status' => false, 'walletError' => 'You have insufficient balance']);
		 }
	}

	public function postPaymentWithStripe(Request $request)
	{


		$userID = Auth::User()->sender_id;
		$userQry = Sender::where('id' , '=', $userID)->first();
		$userInfo = $userQry->toArray();

		if($request->get('withSavedCard') == 'true')
		{
			$validator = Validator::make($request->all(), [
				 'amount' => 'required',
			]);
		}	
		else if($request->get('withSavedCard') == 'false')
		{
			$validator = Validator::make($request->all(), [
				 'card_no' => 'required',
				 'ccExpiryMonth' => 'required',
				 'ccExpiryYear' => 'required',
				 'cvvNumber' => 'required',
				 'amount' => 'required',
			]);
		}	
		else
			return Response()->json(['status' => false, 'stripeError' => 'Money not add in wallet']);

	
		$input = $request->all();
		if ($validator->passes()) { 
			 $input = array_except($input,array('_token'));
			 $stripe = Stripe::make('sk_test_PVXtzkhKGE6eV0iuxTqgh4iZ');
		try {

			$amount = ($request->get('amount')+.3)/0.971;
			$fee = (($request->get('amount')+.3)/0.971)-$request->get('amount');

			if($request->get('withSavedCard') == 'true')
			{
				$charge = $stripe->charges()->create([
				    'customer' => $userInfo['stripe_cust_id'],
				    'currency' => 'USD',
				    'amount'   => $amount,
				]);

			}	
			else if($request->get('withSavedCard') == 'false')
			{
				$token = $stripe->tokens()->create([
				 	'card' => [
				 	'number' => $request->get('card_no'),
				 	'exp_month' => $request->get('ccExpiryMonth'),
				 	'exp_year' => $request->get('ccExpiryYear'),
				 	'cvc' => $request->get('cvvNumber'),
				],
				]);
	
				if (!isset($token['id'])) {
					return Response()->json(['status' => false, 'stripeError' => 'Money not add in wallet']);
				}
				else
				{

					$charge = $stripe->charges()->create([
						 'card' => $token['id'],
						 'currency' => 'CAD',
						 'amount' => $amount,
						 'description' => 'Added in wallet',
					]);
				}


			}	


			if($charge['status'] == 'succeeded') {
				$userID = Auth::User()->sender_id;
				$wallet = DB::table('wallet')->where('user_id', $userID)->exists();
				if ($wallet) {
					  DB::table('wallet')
						->where('user_id', $userID)
						->increment('cash' , $request->get('amount'), ['description' => 'test', 'history' => 'test']);
					  
					  DB::table('transaction_history')->insert(
						['user_id' => $userID, 'amount' => $request->get('amount'), 'fee' => $fee, 'status' => 'Success', 'type' => 'Credit', 'pay_with' => 'credit_card']
					 );  
				} else {
					  DB::table('wallet')->insert(
						['user_id' => $userID, 'cash' => $request->get('amount'), 'description' => 'test', 'history' => 'test']
					 );
					  DB::table('transaction_history')->insert(
						['user_id' => $userID, 'amount' => $request->get('amount'), 'fee' => $fee, 'status' => 'Success', 'type' => 'Credit', 'pay_with' => 'credit_card']
					 );
				}
				return Response()->json(['status' => true, 'stripeSuccess' => 'Money Added Successfully in Wallet']);
			} else {
				return Response()->json(['status' => false, 'stripeError' => 'Money not add in wallet']);
			}

		 } catch (Exception $e) {
			 return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
		 } catch(\Cartalyst\Stripe\Exception\CardErrorException $e) {
			 return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
		 } catch(\Cartalyst\Stripe\Exception\MissingParameterException $e) {
			 return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
		 }
		 }else{		 
			return Response()->json(['status' => false, 'stripeError' => 'Missing fields']);		 
		 }
	 }

	 public function ShipmentPaymentWithStripe(Request $request)
	 {

	    $userID = Auth::User()->sender_id;
		$userQry = Sender::where('id' , '=', $userID)->first();
		$userInfo = $userQry->toArray();

		if($request->get('withSavedCard') == 'true')
		{
			$validator = Validator::make($request->all(), [
				 'amount' => 'required',
			]);
		}	
		else if($request->get('withSavedCard') == 'false')
		{

			 $validator = Validator::make($request->all(), [
				 'card_no' => 'required',
				 'ccExpiryMonth' => 'required',
				 'ccExpiryYear' => 'required',
				 'cvvNumber' => 'required',
				 'amount' => 'required',
			 ]);

		}	
		else
			return Response()->json(['status' => false, 'stripeError' => 'Credit card payment error']);

		$input = $request->all();
		if ($validator->passes()) { 
			$input = array_except($input,array('_token'));
			$stripe = Stripe::make('sk_test_PVXtzkhKGE6eV0iuxTqgh4iZ');
			try {

		   	    // 	$token = $stripe->tokens()->create([
				// 		 'card' => [
				// 		 'number' => $request->get('card_no'),
				// 		 'exp_month' => $request->get('ccExpiryMonth'),
				// 		 'exp_year' => $request->get('ccExpiryYear'),
				// 		 'cvc' => $request->get('cvvNumber'),
				// 	 ],
				// 	 ]);

				// if (!isset($token['id'])) {
				// }
				
				// $amount = ($request->get('amount')+.3)/0.971;
				// $fee = (($request->get('amount')+.3)/0.971)-$request->get('amount');
				
				// $charge = $stripe->charges()->create([
				// 	 'card' => $token['id'],
				// 	 'currency' => 'CAD',
				// 	 'amount' => $amount,
				// 	 'description' => 'payment on shipment',
				// ]);


				$amount = ($request->get('amount')+.3)/0.971;
				$fee = (($request->get('amount')+.3)/0.971)-$request->get('amount');

				if($request->get('withSavedCard') == 'true')
				{
					$charge = $stripe->charges()->create([
					    'customer' => $userInfo['stripe_cust_id'],
					    'currency' => 'USD',
					    'amount'   => $amount,
					]);

				}	
				else if($request->get('withSavedCard') == 'false')
				{
					$token = $stripe->tokens()->create([
					 	'card' => [
					 	'number' => $request->get('card_no'),
					 	'exp_month' => $request->get('ccExpiryMonth'),
					 	'exp_year' => $request->get('ccExpiryYear'),
					 	'cvc' => $request->get('cvvNumber'),
					],
					]);
		
					if (!isset($token['id'])) {
						return Response()->json(['status' => false, 'stripeError' => 'Credit card payment error']);
					}
					else
					{

						$charge = $stripe->charges()->create([
							 'card' => $token['id'],
							 'currency' => 'CAD',
							 'amount' => $amount,
							 'description' => 'Payment on shipment',
						]);
					}


				}	

				if($charge['status'] == 'succeeded') {

					 updatePaidShipments($request->get('batchIds'), $request->get('fromWhere')); 
 					 DB::table('transaction_history')->insert(['user_id' => $userID, 'amount' => $request->get('amount'), 'fee' => $fee, 'status' => 'Success', 'type' => 'Debit', 'pay_with' => 'credit_card']);  

					 return Response()->json(['status' => true]);//exit();
				} else {
					 return Response()->json(['status' => false, 'stripeError' => 'Money not add in wallet']);
				}

			} catch (Exception $e) {
				 return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
			} catch(\Cartalyst\Stripe\Exception\CardErrorException $e) {
				 return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
			} catch(\Cartalyst\Stripe\Exception\MissingParameterException $e) {
				 return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
			}
		}else{		 
			return Response()->json(['status' => false, 'stripeError' => 'Missing fields']);		 
		}
	}
}
