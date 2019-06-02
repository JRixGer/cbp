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

class CreditCardController extends Controller
{
	public function __construct(){
    	$this->middleware(['auth','verified']);
    }
    


	public function getSavedCard()
	{

		$userID = Auth::User()->sender_id;
		$userQry = Sender::where('id' , '=', $userID)->first();
		$userInfo = $userQry->toArray();

		if(!empty($userInfo['stripe_cust_id']))
		{
	        $savedCard = DB::table('credit_cards')
	            ->select('*')
	            ->where('customer', '=' ,$userInfo['stripe_cust_id'])
	            ->limit(1)->get();

	        if($savedCard)    
	        	return ['status' => true, 'savedCard' => $savedCard];
			else        
	        	return ['status' => false];
    	}
    	else
    		return ['status' => false];

	}

	public function delCreditCard(Request $request)
	{

		$d = $request->toArray();
		$input = $request->all();
		$input = array_except($input,array('_token'));
		$stripe = Stripe::make('sk_test_PVXtzkhKGE6eV0iuxTqgh4iZ');
		try {

			$card = $stripe->cards()->delete($d['data']['customer'], $d['data']['card_id']);
			//$card = $stripe->cards()->delete('cus_4EBumIjyaKooft', 'card_4DmaB3muM8SNdZ');

			if ($card['deleted']) {

			    $res = DB::table('credit_cards')
			        ->where('customer', $d['data']['customer'])
			        ->where('card_id', $d['data']['card_id'])
			        ->delete();
			} 
			else 
			{
				return Response()->json(['status' => false, 'stripeError' => 'Error during card deletion in Stripe']);
			}
		}
		catch (Exception $e) {
			return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
		} catch(\Cartalyst\Stripe\Exception\CardErrorException $e) {
			return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
		} catch(\Cartalyst\Stripe\Exception\MissingParameterException $e) {
			return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
		}

	}

	public function getCreditCard(Request $r)
	{

        $main_qry = DB::table('credit_cards')
            ->select('*');
                

        if ( $r->input('client') ) 
        {
            return $main_qry->orderby('created_at')
                            ->get();
        }

        $columns = [
                'id',
                'customer',
                'card_id',
                'object',
                'address_city',
                'address_country',
                'address_line1',
                'address_state',
                'address_zip',
                'brand',
                'exp_month',
                'exp_year',
                'last4',
                'created_at'
            ];

        $length = $r->input('length');
        $column = $r->input('column'); //Index
        $dir = $r->input('dir');
        $searchValue = $r->input('search');
        
        $query = $main_qry->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                 $query->where('last4', 'like', '%' . $searchValue . '%')
                      ->orWhere('brand', 'like', '%' . $searchValue . '%');
            });
        }

        $creditCards= $query->paginate($length);
        //dd($coupons);
        return ['status' => true,'data' => $creditCards, 'draw' => $r->input('draw')];

	}

	public function addCreditCard(Request $request)
	{


		$userID = Auth::User()->sender_id;
		$userQry = Sender::where('id' , '=', $userID)->first();
		$userInfo = $userQry->toArray();

		$validator = Validator::make($request->all(), [
			 'card_no' => 'required',
			 'ccExpiryMonth' => 'required',
			 'ccExpiryYear' => 'required',
			 'cvvNumber' => 'required',
		]);
		$input = $request->all();
		if ($validator->passes()) 
		{ 
			$input = array_except($input,array('_token'));
			$stripe = Stripe::make('sk_test_PVXtzkhKGE6eV0iuxTqgh4iZ');
			try {

				if(!empty($userInfo['stripe_cust_id']))
				{
					$customer = $stripe->customers()->find($userInfo['stripe_cust_id']);
					if(!$customer)				
					{
						$customer = $stripe->customers()->create([
						    'email' => $userInfo['email'],
						]);

						$senderUpdate = Sender::find($userID);
					  	$senderUpdate->stripe_cust_id = $customer['id'];
					  	$res = $senderUpdate->save();
					}
				}
				else
				{
					$customer = $stripe->customers()->create([
					    'email' => $userInfo['email'],
					]);

					$senderUpdate = Sender::find($userID);
				  	$senderUpdate->stripe_cust_id = $customer['id'];
				  	$res = $senderUpdate->save();

				}

				//$customer = $stripe->customers()->find('cus_EaTqiaiecrtXDO');
				//echo $customer['email'];

				$token = $stripe->tokens()->create([
				 	'card' => [
				 	'number' => $request->get('card_no'),
				 	'exp_month' => $request->get('ccExpiryMonth'),
				 	'exp_year' => $request->get('ccExpiryYear'),
				 	'cvc' => $request->get('cvvNumber'),
					],
		
				]);


				if (isset($token['id'])) {
					$card = $stripe->cards()->create($userInfo['stripe_cust_id'], $token['id']);

				 	if($card) 
				 	{
						DB::table('credit_cards')->insert(['customer' => $card['customer'], 
								'card_id' => $card['id'], 
								'object' => $card['object'], 
								'address_city' => $card['address_city'], 
								'address_country' => $card['address_country'], 
								'address_line1' => $card['address_line1'], 
								'address_state' => $card['address_state'], 
								'address_zip' => $card['address_zip'], 
								'brand' => $card['brand'], 
								'exp_month' => $card['exp_month'], 
								'exp_year' => $card['exp_year'], 
								'funding' => $card['funding'], 
								'fingerprint' => $card['fingerprint'], 
								'last4' => $card['last4']]
							 );
						return Response()->json(['status' => true, 'stripeSuccess' => 'Card added successfully in Stripe']);
					} 
					else 
					{
						return Response()->json(['status' => false, 'stripeError' => 'Error during card creation in Stripe']);
					}
				}
			 } catch (Exception $e) {
				 return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
			 } catch(\Cartalyst\Stripe\Exception\CardErrorException $e) {
				 return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
			 } catch(\Cartalyst\Stripe\Exception\MissingParameterException $e) {
				 return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
			 }
		}
		else
		{		 
			return Response()->json(['status' => false, 'stripeError' => 'Missing fields']);		 
		}
	}


	public function purchaseCredit(Request $request)
	{


		$userID = Auth::User()->sender_id;
		$userQry = Sender::where('id' , '=', $userID)->first();
		$userInfo = $userQry->toArray();

		$validator = Validator::make($request->all(), [
			 'amount' => 'required',
		]);
		$input = $request->all();


		if ($validator->passes()) 
		{ 
			$input = array_except($input,array('_token'));
			$stripe = Stripe::make('sk_test_PVXtzkhKGE6eV0iuxTqgh4iZ');
			try {

				//echo $userInfo['stripe_cust_id'];
				
				$customer = $stripe->customers()->find($userInfo['stripe_cust_id']);
				if($customer)
				{

					$amount = ($request->get('amount')+.3)/0.971;
					$fee = (($request->get('amount')+.3)/0.971)-$request->get('amount');				
					$charge = $stripe->charges()->create([
					    'customer' => $userInfo['stripe_cust_id'],
					    'currency' => 'USD',
					    'amount'   => $amount,
					]);

					if (isset($charge['id'])) 
					{
						$wallet = DB::table('wallet')->where('user_id', $userID)->exists();
						if ($wallet) 
						{
							DB::table('wallet')
								->where('user_id', $userID)
								->increment('cash' , $request->get('amount'), ['description' => 'test', 'history' => 'test']);
							  
							DB::table('transaction_history')->insert(
								['user_id' => $userID, 'amount' => $request->get('amount'), 'fee' => $fee, 'status' => 'Success', 'type' => 'Credit', 'pay_with' => 'credit_card']
							 );  
						} 
						else 
						{
							DB::table('wallet')->insert(
								['user_id' => $userID, 'cash' => $request->get('amount'), 'description' => 'test', 'history' => 'test']
							 );
							DB::table('transaction_history')->insert(
								['user_id' => $userID, 'amount' => $request->get('amount'), 'fee' => $fee, 'status' => 'Success', 'type' => 'Credit', 'pay_with' => 'credit_card']
							 );
						}

						return Response()->json(['status' => true, 'stripeSuccess' => 'Successfully purchased credit']);
					} 
					else 
					{
						return Response()->json(['status' => false, 'stripeError' => 'Error during credit purchase']);
					}
				}
				else
					return Response()->json(['status' => false, 'stripeError' => 'Error during retrieving customer info']);


			} catch (Exception $e) {
				return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
			} catch(\Cartalyst\Stripe\Exception\CardErrorException $e) {
				return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
			} catch(\Cartalyst\Stripe\Exception\MissingParameterException $e) {
				return Response()->json(['status' => false, 'stripeError' => $e->getMessage()]);
			}
		}
		else
		{		 
			return Response()->json(['status' => false, 'stripeError' => 'Missing fields']);		 
		}
	}
}
