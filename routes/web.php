<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);

Route::get('/', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/staff_portal','UserManagementController@index');

Route::group(['prefix'=>'senders'], function(){
	Route::get('/','SenderController@index');
	Route::post('/profileRegistration','SenderController@profileRegistration');
	Route::post('/businessRegistration','SenderController@businessRegistration');
	Route::post('/validateBusinessRegistration','SenderController@validateBusinessRegistration');
	Route::post('/validateMailingAddressRegistration','SenderController@validateMailingAddressRegistration');
	Route::get('/getShipFromAddresses','SenderController@getShipFromAddresses');
	Route::post('/updateUserAccountInfo','SenderController@updateUserAccountInfo');	
	Route::post('/updateUserAccountPOA','SenderController@updateUserAccountPOA');	
	Route::post('/request','SenderController@docrequest');
	Route::post('/poa', 'SenderController@poaEmail');
	Route::post('/ina', 'SenderController@inaEmail');	
	Route::post('/pw_reset_email', 'SenderController@pwResetEmailConfirmation');	
	Route::post('/em_change_email', 'SenderController@emUpdateEmailConfirmation');	
});



Route::group(['prefix'=>'profile'], function(){
	Route::get('/user/info','SenderController@getUserInfo');
	Route::get('/user/account_info','SenderController@getUserAccountInfo');
	Route::post('/update','SenderController@updateProfile');
	Route::get('/accountinfo','SenderController@getAccountInfo');
	Route::get('/businessinfo','SenderController@getBusinessInfo');
	Route::get('/mailinginfo','SenderController@getMailingInfo');
	Route::post('/password_reset', 'ProfileController@update');
	Route::post('/change_email', 'ProfileController@updateEmail');
	
});



Route::group(['prefix'=>'address'], function(){
	Route::post('/register','ShipFromAddressController@register');
});



Route::group(['prefix'=>'recipients'], function(){
	// Route::get('/','RecipientController@index');
	Route::post('/register','RecipientController@register');
	// Route::post('/validate','RecipientController@justValidate');
	// Route::post('/businessRegistration','SenderController@businessRegistration');
	// Route::post('/validateBusinessRegistration','SenderController@validateBusinessRegistration');
	// Route::post('/validateMailingAddressRegistration','SenderController@validateMailingAddressRegistration');

});

 

Route::group(['prefix'=>'shipment'], function(){
	Route::get('/single','SingleShipmentController@index');
	Route::post('/rates','SingleShipmentController@getRates');
	Route::post('/ratesByCarrier','SingleShipmentController@getRatesByCarrier');
	Route::post('/validate/recipient','SingleShipmentController@validateRecipient');
	Route::post('/validate/dimensions','SingleShipmentController@validateParcelDimensions');
	Route::post('/createShipment','SingleShipmentController@createShipment');
	Route::post('/saveShipment','SingleShipmentController@saveShipment');
	Route::get('/voidLabel','SingleShipmentController@voidLabel');
	Route::post('/uspsoptions','SingleShipmentController@uspsOptions');
	Route::get('/getShipment/{id}','SingleShipmentController@getShipment');
	Route::post('/getShipments','SingleShipmentController@getShipments');
	Route::get('/downloadLabel/{id}','SingleShipmentController@downloadLabel');
	Route::get('/getCouponCode','SingleShipmentController@getCouponCode');
	Route::get('/printLabel/{id}','SingleShipmentController@printLabel');
	Route::get('/edit/{id}','SingleShipmentController@editShipment');
	Route::post('/copy','SingleShipmentController@copyShipment');
});



Route::group(['prefix'=>'document'], function(){
	Route::post('/request','DocumentController@docrequest');
	Route::get('/poa', 'DocumentController@poa');
	Route::get('/import_number', 'DocumentController@import_number');
	Route::post('/upload', 'DocumentController@upload');
	Route::post('/import_csv', 'DocumentController@importCSV');
	Route::post('/update_carrier', 'DocumentController@updateCarrier');
	Route::post('/process_shipment', 'DocumentController@processShipment');
	Route::post('/update_address_dimension', 'DocumentController@updateAddressAndDimension');
	Route::post('/update_shipment', 'DocumentController@updateShipment');
	Route::get('/check_coupon', 'DocumentController@checkCoupon');
	Route::get('/retrieve_summary', 'DocumentController@retrieveSummary');
});


Route::group(['prefix'=>'report'], function(){
	Route::get('/all_shipments','ReportController@allShipments');
	Route::get('/grouped_orders','ReportController@groupedOrders');
	Route::post('/grouped_order','ReportController@groupedOrder');
	Route::post('/remove_order','ReportController@removeOrder');
	Route::post('/reverse_order','ReportController@reverseOrder');
	Route::post('/process_shipment','ReportController@processShipment');
	Route::get('/all_items','ReportController@allItems');
	Route::get('/downloadBill/{id}','ReportController@downloadBill');
	Route::get('/printBill/{id}','ReportController@printBill');

});

Route::group(['prefix'=>'site'], function(){
	Route::get('/notifications/','MessageController@getMessages');
	Route::post('/mark_read','MessageController@markRead');
});

/////////////////////

Route::get('/no_access', 'NoAccessController@index');
// Route::group(['middleware' => 'admin'], function()
// {
// 	Route::get('/staff_portal','UserManagementController@index');
// });

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function()
{
	Route::get('/user','UserManagementController@allUsers');
});

Route::get('/logout', 'LogoutController@logout');

Route::get('/paypal', function(){
	return view('paywithpaypal');
});

Route::group(['prefix'=>'addmoney'], function(){
	Route::post('/stripe','AddMoneyController@postPaymentWithStripe');
	Route::post('/paywithstripe','AddMoneyController@ShipmentPaymentWithStripe');
	Route::post('/paywithwallet','AddMoneyController@payWithWallet');
	Route::get('/walletdetail/{id}','AddMoneyController@getWalletDetail');
	Route::get('/stripe', 'AddMoneyController@payWithStripe');	
});

Route::group(['prefix'=>'payment'], function(){
	Route::get('/get_creditcards', 'CreditCardController@getCreditCard');	
	Route::get('/get_saved_card', 'CreditCardController@getSavedCard');	
	Route::post('/add_card','CreditCardController@addCreditCard');
	Route::post('/purchase_credit','CreditCardController@purchaseCredit');
	Route::post('/del_creditcard','CreditCardController@delCreditCard');
});
