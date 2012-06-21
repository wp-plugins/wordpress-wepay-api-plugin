<?php

require WPWEPAY_PATH.'wepaysdk.php';
require WPWEPAY_API_PATH.'wepayapi.php';
require 'wp-config.php';

if (!empty($_POST['checkout_id'])) {
$thecheckoutid = $_POST['checkout_id'];
}else{
$thecheckoutid = $_GET['checkout_id'];
}

$client_id =  get_option('wepay_clientID');
$client_secret =  get_option('wepay_clientsecret');
$access_token =  get_option('wepay_accesstoken');
$account_id =  get_option('wepay_accountID');


Wepay::useProduction($client_id, $client_secret);
$wepay = new WePay($access_token);

try {
$checkout = $wepay->request('checkout', array(
'checkout_id' => $thecheckoutid, ));
} catch (WePayException $e) { // if the API call returns an error, get the error message for display later
$error = $e->getMessage();
}

////////////// EDIT BELOW HERE //////////////
 
////do something based on object states
////see different states here https://www.wepay.com/developer/reference/object_states


	if ($checkout->state == "captured") {
	 ///do something here
    } elseif ($checkout->state == "authorized") {
        ///do something here
    } elseif ($checkout->state == "reserved") {
        ///do something here
    } elseif ($checkout->state == "settled") {
        ///do something here
    } elseif ($checkout->state == "cancelled") {
        ///do something here
    } elseif ($checkout->state == "refunded") {
        ///do something here
    } elseif ($checkout->state == "charged back") {
        ///do something here
    } elseif ($checkout->state == "failed") {
        ///do something here
    } elseif ($checkout->state == "expired") {
        ///do something here
	}
?>