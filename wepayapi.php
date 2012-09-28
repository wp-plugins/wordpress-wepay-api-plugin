<?php
/*
Plugin Name: WePay API Wordpress Plugin
Plugin URI: http://www.alanpinnt.com/wordpress-wepay-plugin/
Description: With this plugin you can make direct API requests using short codes. With this method your not limited to just buttons or invoices. This is an addon to the main WePay Plugin, which you need to run this one. This plugin is not for novice users, although you do not have to write any PHP code you do have to understand the calls being made and how to properly make them.
Version: 1.2
Author: Alan pinnt
Author URI: http://www.alanpinnt.com/
License: GPL3
    Copyright 2012 Alan Pinnt www.alanpinnt.com
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('WEPAY_API_PLUGIN_NAME', 'Wepay Wordpress Plugin');
define('WEPAY_API_PLUGIN_URI', 'http://www.alanpinnt.com/wordpress-wepay-api-plugin/');
define('WEPAY_API_VERSION', '1.2');
define('WEPAY_API_AUTHOR', 'Alan Pinnt');
define('WEPAY_ADDON_API_AUTHOR_URI', 'http://www.alanpinnt.com/');

define( 'WPWEPAY_API_URL', plugin_dir_url(__FILE__) );
define( 'WPWEPAY_API_PATH', plugin_dir_path(__FILE__) );
define( 'WPWEPAY_API_BASENAME', plugin_basename( __FILE__ ) );




add_action('admin_menu', 'wepay_api_page');

function wepay_api_page() {
   add_menu_page("Wepay API", "Wepay API", 0, "wepay-api", "wepay_api_help",'','9999999');
}

function wepay_api_help() {

print '<div class="wrap"><h2>How to use the Wepay API Plugin</h2>
<p>This plugin uses shortcodes, the short code calls are almost identical to the main WePay plugin but the difference is the name of the calls. This plugin is not for the novice user. You have to understand the API calls being made if you want them to work.</a>
<br /><br />
Short codes are really simple to use, just a little bit of writing and then past into the page or post you want it to be on.
<br />
<h3>Here is a sample short code:</h3>
For a link:<br />
<code>[wepay-api-link text="Buy Now" amount="1.00" sdesc="testing" css="buttoncss" feepayer="Payee"]</code>
<br /><br />
For an iframe:<br />
<code>[wepay-api-iframe text="Buy Now" amount="1.00" sdesc="testing" css="buttoncss" feepayer="Payee"]</code>
<br /><br />
-With the short code we call on "wepay-api-link" as the short code itself. That will create a checkout link. Or you can call [wepay-api-iframe] which will give you an iframe checkout.<br />
"Text" is the value or what you want the button to say.<br />
"amount" is amount you want to charge.<br />
"sdesc" a short description of what you want to sell or the payee is donating for.<br />
"css" is the css property you want to call on, otherwise if you do not it will just look like a link.<br />
"feepayer" specifies who is going to pay for the fees for this transaction, you (Payee) or the purchaser (Payer). You must use "Payee" or "Payer", anything else and it will not work.<br />
"thankq" this will set the page you user will go to after payment is made. If it is not set it will use the default thank you page you set in your WePay account settings.<br />
<br />
<h3>Other API Calls</h3>
When making /app calls<br />
<code>[wepay-api-app]</code><br /><br />
When making /user calls<br />
<code>[wepay-api-user]</code><br /><br />
When making /account calls<br />
<code>[wepay-api-account]</code><br /><br />
When making /checkout calls<br />
<code>[wepay-api-checkout]</code><br /><br />
When making /preapproval calls<br />
<code>[wepay-api-preapproval]</code><br /><br />
When making /withdrawal calls<br />
<code>[wepay-api-withdrawal]</code><br /><br />
Each call has specific requirements and default values. The link posted in the FAQ is the source for help making certain calls.
<h3>FAQ</h3>
For more information on how to make calls thru this plugin, see the listing of API calls here <a href="http://www.alanpinnt.com/wordpress-wepay-api-plugin/" target="_blank">http://www.alanpinnt.com/wordpress-wepay-api-plugin/</a>
<br />
<br />
We<a href="https://www.wepay.com/developer/reference" target="_blank">https://www.wepay.com/developer/reference</a>
<h3>Questions</h3>
Contact me from my blog <a href="http://www.alanpinnt.com/contact-me/" target="_blank">www.alanpinnt.com</a>
</p>
</div>';	
}



///Shortcode
function wepay_api_link_shortcode($thelink){
	
	extract( shortcode_atts( array('type' => 'GOODS', 'refid' => '', 'amount' => '1.00', 'feepayer' => 'Payee', 'sdesc' => 'Short Description', 'email_mess' => 'Thank you for your payment.', 'tax' => '0', 'css' => 'button', 'thankq' => '1', 'ipn' => 'N'), $thelink ) );

	$b_text = "{$text}";
	$b_type = "{$type}";
	$b_refid = "{$refid}";
	$b_amount = "{$amount}";
	$b_feepayer = "{$feepayer}";
	$b_sdesc = "{$sdesc}";
	$b_emailpayer = "{$email_mess}";
	$b_tax = "{$tax}";
	$b_css = "{$css}";
    $b_thankq  = "{$thankq}";
    $b_ipn  = "{$ipn}";
	
if (empty($b_refid)) {$b_refid = '1';} 
 
require WPWEPAY_PATH.'wepaysdk.php';

$client_id =  get_option('wepay_clientID');
$client_secret =  get_option('wepay_clientsecret');
$access_token =  get_option('wepay_accesstoken');
$account_id =  get_option('wepay_accountID');

if ($b_thankq=='1') {$thetqpage =get_option('wepay_thankqpage');}  else {$thetqpage = $b_thankq;}

if (get_option('wepay_mode') == 's') {$whattouse = useStaging;} elseif (get_option('wepay_mode') == 'p') {$whattouse = useProduction;}
 
Wepay::$whattouse($client_id, $client_secret);
$wepay = new WePay($access_token);
 

if ($b_ipn=='Y') {
      
try {
$checkout = $wepay->request('/checkout/create', array(
'account_id' => $account_id,
'amount' => $b_amount,
'short_description' => $b_sdesc,
'type' => $b_type, 
'reference_id' => $b_refid,
'fee_payer' => $b_feepayer,
'callback_uri' => WPWEPAY_API_URL."wepayipn.php", 
'payer_email_message' => $b_emailpayer,
'charge_tax' => $b_tax,
'redirect_uri' => $thetqpage,));
} catch (WePayException $e) {$error = $e->getMessage();}

} else {
    
try {
$checkout = $wepay->request('/checkout/create', array(
'account_id' => $account_id,
'amount' => $b_amount,
'short_description' => $b_sdesc,
'type' => $b_type, 
'reference_id' => $b_refid,
'fee_payer' => $b_feepayer,
'payer_email_message' => $b_emailpayer,
'charge_tax' => $b_tax,
'redirect_uri' => $thetqpage,));
} catch (WePayException $e) {$error = $e->getMessage();}
}

////build the button in here
return $checkout->checkout_uri;
}
add_shortcode( 'wepay-api-link', 'wepay_api_link_shortcode');




function wepay_api_iframe_shortcode($theiframe){
	
	extract( shortcode_atts( array('type' => 'GOODS', 'refid' => '', 'amount' => '1.00', 'feepayer' => 'Payee', 'sdesc' => 'Short Description', 'email_mess' => 'Thank you for your payment.', 'tax' => '0', 'css' => 'button', 'thankq' => '1', 'ipn' => 'N'), $theiframe ) );

	$b_text = "{$text}";
	$b_type = "{$type}";
	$b_refid = "{$refid}";
	$b_amount = "{$amount}";
	$b_feepayer = "{$feepayer}";
	$b_sdesc = "{$sdesc}";
	$b_emailpayer = "{$email_mess}";
	$b_tax = "{$tax}";
	$b_css = "{$css}";
    $b_thankq  = "{$thankq}";
    $b_ipn  = "{$ipn}";
	
if (empty($b_refid)) {$b_refid = '1';} 
 
require WPWEPAY_PATH.'wepaysdk.php';

$client_id =  get_option('wepay_clientID');
$client_secret =  get_option('wepay_clientsecret');
$access_token =  get_option('wepay_accesstoken');
$account_id =  get_option('wepay_accountID');
 
if ($b_thankq=='1') {$thetqpage =get_option('wepay_thankqpage');}  else {$thetqpage = $b_thankq;}
 
if (get_option('wepay_mode') == 's') {$whattouse = useStaging;} elseif (get_option('wepay_mode') == 'p') {$whattouse = useProduction;}
 
Wepay::$whattouse($client_id, $client_secret);
$wepay = new WePay($access_token);
 
if ($b_ipn=='Y') {
      
try {
$checkout = $wepay->request('/checkout/create', array(
'account_id' => $account_id,
'amount' => $b_amount,
'short_description' => $b_sdesc,
'type' => $b_type,
'mode' => 'iframe',
'reference_id' => $b_refid,
'fee_payer' => $b_feepayer,
'callback_uri' => WPWEPAY_API_URL."wepayipn.php", 
'payer_email_message' => $b_emailpayer,
'charge_tax' => $b_tax,
'redirect_uri' => $thetqpage,));
} catch (WePayException $e) {$error = $e->getMessage();}

} else {
    
try {
$checkout = $wepay->request('/checkout/create', array(
'account_id' => $account_id,
'amount' => $b_amount,
'short_description' => $b_sdesc,
'type' => $b_type,
'mode' => 'iframe',
'reference_id' => $b_refid,
'fee_payer' => $b_feepayer,
'payer_email_message' => $b_emailpayer,
'charge_tax' => $b_tax,
'redirect_uri' => $thetqpage,));
} catch (WePayException $e) {$error = $e->getMessage();}
}

print '<div id="checkout_div"></div>
<script type="text/javascript" src="https://www.wepay.com/js/iframe.wepay.js">
</script>

<script type="text/javascript">
WePay.iframe_checkout("checkout_div", "'.$checkout->checkout_uri.'");
</script>';
}
add_shortcode( 'wepay-api-iframe', 'wepay_api_iframe_shortcode' );




function wepay_api_app_sc($data) {
//// https://www.wepay.com/developer/reference/app	
	
    extract( shortcode_atts( array('do' => '/app', 'response' => 'gaq_domains'), $data ) );

	$wapi_do = "{$do}";
	$wapi_response = "{$response}";
	
require WPWEPAY_PATH.'wepaysdk.php';

$client_id =  get_option('wepay_clientID');
$client_secret =  get_option('wepay_clientsecret');
$access_token =  get_option('wepay_accesstoken');
$account_id =  get_option('wepay_accountID');
 
if (get_option('wepay_mode') == 's') {$whattouse = useStaging;} elseif (get_option('wepay_mode') == 'p') {$whattouse = useProduction;}
Wepay::$whattouse($client_id, $client_secret); $wepay = new WePay($access_token);

if ($wapi_do == '/app') { 
try {
$call = $wepay->request($wapi_do, array(
'client_id' => $client_id,
'client_secret' => $client_secret,));
} catch (WePayException $e) {$error = $e->getMessage();}

} elseif ($wapi_do=='/app/modify') {
try {
$call = $wepay->request($wapi_do, array(
'client_id' => $client_id,
'client_secret' => $client_secret,));
} catch (WePayException $e) {$error = $e->getMessage();}   
}

print $call-> $wapi_response;
}
add_shortcode( 'wepay-api-app', 'wepay_api_app_sc' );



function wepay_api_user_sc($data) {
//// https://www.wepay.com/developer/reference/user
	
    extract( shortcode_atts( array('do' => '/user', 'response' => '','email' => '', 
    'scope' => '/account,/account/find,/account/create,/account/modify,/account/delete,/account/balance,/checkout,/checkout/find,/checkout/create,/checkout/cancel,/checkout/capture,/checkout/refund,/user,/preapproval,/preapproval/find,/preapproval/create,/preapproval/cancel,/disbursement,/disbursement/find,/disbursement/create,/transfer,/transfer,/transfer/find,/transfer/refund',
    'fname' => '','lname' => '','ipad' => '','device' => '','uri' => ''), $data ));

	$wapi_do = "{$do}";
	$wapi_response = "{$response}";
    $wapi_email = "{$email}";
    $wapi_scope = "{$scope}";
    $wapi_fname = "{$fname}";
    $wapi_lname = "{$lname}";
    $wapi_ipad = "{$ipad}";
    $wapi_device = "{$device}";
    $wapi_uri = "{$uri}";
	
require WPWEPAY_PATH.'wepaysdk.php';

$client_id =  get_option('wepay_clientID');
$client_secret =  get_option('wepay_clientsecret');
$access_token =  get_option('wepay_accesstoken');
$account_id =  get_option('wepay_accountID');
 
if (get_option('wepay_mode') == 's') {$whattouse = useStaging;} elseif (get_option('wepay_mode') == 'p') {$whattouse = useProduction;}
Wepay::$whattouse($client_id, $client_secret); $wepay = new WePay($access_token);

///// https://www.wepay.com/developer/reference/user#lookup
if ($wapi_do == '/user') { 
try {
$call = $wepay->request($wapi_do);
} catch (WePayException $e) {$error = $e->getMessage();}

///// https://www.wepay.com/developer/reference/user#register
} elseif ($wapi_do=='/user/register') {
try {
$call = $wepay->request($wapi_do, array(
'client_id' => $client_id,
'client_secret' => $client_secret,
'email' => $wapi_email,
'scope' => $wapi_scope,
'first_name' => $wapi_fname,
'last_name' => $wapi_lname,
'orginal_ip' => $wapi_ipad,
'original_device' => $wapi_device,
'redirect_uri' => $wapi_uri,
));
} catch (WePayException $e) {$error = $e->getMessage();}

///// https://www.wepay.com/developer/reference/user#resend_confirmation
} elseif ($wapi_do=='/user/resend_confirmation') {
try {
$call = $wepay->request($wapi_do);
} catch (WePayException $e) {$error = $e->getMessage();}  
}

print $call-> $wapi_response;
}
add_shortcode( 'wepay-api-user', 'wepay_api_user_sc' );



function wepay_api_account_sc($data) {
//// https://www.wepay.com/developer/reference/account
	
    extract( shortcode_atts( array('do' => '/account', 'response' => '','account_id' => '','name' => '','description' => '','reference_id' => '','account_uri' => '','payment_limit' => '','image_uri' => '','taxes' => ''), $data ));

	$wapi_do = "{$do}";
	$wapi_response = "{$response}";
    $wapi_accountid = "{$account_id}";
    $wapi_name = "{$name}";
    $wapi_referenceid = "{$reference_id}";
    $wapi_accounturi = "{$account_uri}";
    $wapi_imageuri = "{$image_uri}";
    $wapi_taxes = "{$taxes}";
	
require WPWEPAY_PATH.'wepaysdk.php';

$client_id =  get_option('wepay_clientID');
$client_secret =  get_option('wepay_clientsecret');
$access_token =  get_option('wepay_accesstoken');
$account_id =  get_option('wepay_accountID');
 
if (get_option('wepay_mode') == 's') {$whattouse = useStaging;} elseif (get_option('wepay_mode') == 'p') {$whattouse = useProduction;}
Wepay::$whattouse($client_id, $client_secret); $wepay = new WePay($access_token);

///// https://www.wepay.com/developer/reference/account#lookup
if ($wapi_do == '/account') { 
try {
$call = $wepay->request($wapi_do, array(
'account_id' => $wapi_accountid,
));
} catch (WePayException $e) {$error = $e->getMessage();}

///// https://www.wepay.com/developer/reference/account#find
} elseif ($wapi_do=='/account/find') {
try {
$call = $wepay->request($wapi_do, array(
'name' => $wapi_name,
'reference_id' => $wapi_referenceid,
));
} catch (WePayException $e) {$error = $e->getMessage();}

///// https://www.wepay.com/developer/reference/account#create
} elseif ($wapi_do=='/account/create') {
try {
$call = $wepay->request($wapi_do, array(
'name' => $wapi_name,
'description' => $wapi_description,
'reference_id' => $wapi_referenceid,
'image_uri' => $wapi_imageuri,
));
} catch (WePayException $e) {$error = $e->getMessage();}  

///// https://www.wepay.com/developer/reference/account#modify
} elseif ($wapi_do=='/account/modify') {
try {
$call = $wepay->request($wapi_do, array(
'account_id' => $wapi_accountid,
'name' => $wapi_name,
'description' => $wapi_description,
'reference_id' => $wapi_referenceid,
'image_uri' => $wapi_imageuri,
));
} catch (WePayException $e) {$error = $e->getMessage();}  

///// https://www.wepay.com/developer/reference/account#delete
} elseif ($wapi_do=='/account/delete') {
try {
$call = $wepay->request($wapi_do, array(
'account_id' => $wapi_accountid,
));
} catch (WePayException $e) {$error = $e->getMessage();}  

///// https://www.wepay.com/developer/reference/account#balance
} elseif ($wapi_do=='/account/balance') {
try {
$call = $wepay->request($wapi_do, array(
'account_id' => $wapi_accountid,
));
} catch (WePayException $e) {$error = $e->getMessage();}  

///// https://www.wepay.com/developer/reference/account#set_tax
} elseif ($wapi_do=='/account/set_tax') {
try {
$call = $wepay->request($wapi_do, array(
'account_id' => $wapi_accountid,
'taxes' => $wapi_taxes,
));
} catch (WePayException $e) {$error = $e->getMessage();}

///// https://www.wepay.com/developer/reference/account#get_tax
} elseif ($wapi_do=='/account/get_tax') {
try {
$call = $wepay->request($wapi_do, array(
'account_id' => $wapi_accountid,
));
} catch (WePayException $e) {$error = $e->getMessage();}
}

print $call-> $wapi_response;
}
add_shortcode( 'wepay-api-account', 'wepay_api_account_sc' );



function wepay_api_checkout_sc($data) {
//// https://www.wepay.com/developer/reference/checkout
	
    extract( shortcode_atts( array('do' => '/checkout', 'response' => '','checkoutid' => '','accountid' => '','start' => '0',
    'limit' => '50','referenceid' => '','state' => '','longdescription' => '','type' => 'GOODS','amount' => '0.01',
    'appfee' => '0.00','feepayer' => 'Payee','redirect' => 'http://www.google.com','callback' => WPWEPAY_API_PATH."wepayipn.php",'shipping' => '0','shippingfee' => '0',
    'chargetax' => '0','mode' => 'regular','cancel' => 'Customer cancelled, default answer.'), $data ));

	$wapi_do = "{$do}";
	$wapi_response = "{$response}";
    $wapi_checkoutid = "{$checkoutid}";
    $wapi_referenceid = "{$referenceid}";
    $wapi_accountid = "{$accountid}";
    $wapi_start = "{$start}";
    $wapi_limit = "{$limit}";
    $wapi_state = "{$state}";
    $wapi_longdesc = "{$longdescription}";
    $wapi_type = "{$type}";
    $wapi_amount = "{$amount}";
    $wapi_appfee = "{$appfee}";
    $wapi_feepayer = "{$feepayer}";
    $wapi_redirect = "{$redirect}";
    $wapi_callback = "{$callback}";
    $wapi_shipping = "{$shipping}";
    $wapi_shippingfee = "{$shippingfee}";
    $wapi_chargetax = "{$chargetax}";
    $wapi_mode = "{$mode}";
    $wapi_cancel = "{$cancel}";
	
require WPWEPAY_PATH.'wepaysdk.php';

$client_id =  get_option('wepay_clientID');
$client_secret =  get_option('wepay_clientsecret');
$access_token =  get_option('wepay_accesstoken');
$account_id =  get_option('wepay_accountID');
 
if (get_option('wepay_mode') == 's') {$whattouse = useStaging;} elseif (get_option('wepay_mode') == 'p') {$whattouse = useProduction;}
Wepay::$whattouse($client_id, $client_secret); $wepay = new WePay($access_token);

///// https://www.wepay.com/developer/reference/checkout#lookup
if ($wapi_do == '/checkout') { 
try {
$call = $wepay->request($wapi_do, array(
'checkout_id' => $wapi_checkoutid,
));
} catch (WePayException $e) {$error = $e->getMessage();}

///// https://www.wepay.com/developer/reference/checkout#find
} elseif ($wapi_do=='/checkout/find') {
try {
$call = $wepay->request($wapi_do, array(
'account_id' => $wapi_name,
'start' => $wapi_start,
'limit' => $wapi_limit,
'reference_id' => $wapi_referenceid,
'state' => $wapi_state,
));
} catch (WePayException $e) {$error = $e->getMessage();}

///// https://www.wepay.com/developer/reference/checkout#create
} elseif ($wapi_do=='/checkout/create') {
try {
$call = $wepay->request($wapi_do, array(
'account_id' => $wapi_accountid,
'short_description' => $wapi_description,
'long_description' => $wapi_longdesc,
'type' => $wapi_type,
'reference_id' => $wapi_referenceid,
'amount' => $wapi_amount,
'app_fee' => $wapi_appfee,
'fee_payer' => $wapi_feepayer,
'redirect_uri' => $wapi_redirect,
'require_shipping' => $wapi_shipping,
'shipping_fee' => $wapi_shippingfee,
'charge_tax' => $wapi_chargetax,
'mode' => $wapi_mode,
));
} catch (WePayException $e) {$error = $e->getMessage();}  

///// https://www.wepay.com/developer/reference/checkout#cancel
} elseif ($wapi_do=='/checkout/cancel') {
try {
$call = $wepay->request($wapi_do, array(
'checkout_id' => $wapi_checkoutid,
'cancel_reason' => $wapi_cancel,
));
} catch (WePayException $e) {$error = $e->getMessage();}  

///// https://www.wepay.com/developer/reference/checkout#refund
} elseif ($wapi_do=='/checkout/refund') {
try {
$call = $wepay->request($wapi_do, array(
'checkout_id' => $wapi_checkoutid,
'refund_reason' => $wapi_cancel,
'amount' => $wapi_amount,
));
} catch (WePayException $e) {$error = $e->getMessage();}  

///// https://www.wepay.com/developer/reference/checkout#capture
} elseif ($wapi_do=='/checkout/capture') {
try {
$call = $wepay->request($wapi_do, array(
'checkout_id' => $wapi_checkoutid,
));
} catch (WePayException $e) {$error = $e->getMessage();}
}

print $call-> $wapi_response;
}
add_shortcode( 'wepay-api-checkout', 'wepay_api_checkout_sc' );





function wepay_api_preapproval_sc($data) {
//// https://www.wepay.com/developer/reference/preapproval
	
    extract( shortcode_atts( array('do' => '/preapproval', 'response' => '','accountid' => '',
    'referenceid' => '','preapprovalid' => '','state' => '',
    'shortdescription' => '',
    'longdescription' => '','type' => 'GOODS','amount' => '0.01',
    'appfee' => '0.00','feepayer' => 'Payee','redirect' => 'http://www.google.com','callback' => WPWEPAY_API_PATH."wepayipn.php",
    'shipping' => '0','shippingfee' => '0',
    'chargetax' => '0','mode' => 'regular','period' => 'monthly','frequency' => '1',
    'starttime' => '','endtime' => '','autorecur' => 'false'
    ), $data ));

	$wapi_do = "{$do}";
	$wapi_response = "{$response}";
    $wapi_accountid = "{$accountid}";
    $wapi_referenceid = "{$referenceid}";
    $wapi_preapprovalid = "{$preapprovalid}";
    $wapi_state = "{$state}";
    $wapi_description = "{$longdescription}";
    $wapi_longdesc = "{$longdescription}";
    $wapi_type = "{$type}";
    $wapi_amount = "{$amount}";
    $wapi_appfee = "{$appfee}";
    $wapi_feepayer = "{$feepayer}";
    $wapi_redirect = "{$redirect}";
    $wapi_callback = "{$callback}";
    $wapi_shipping = "{$shipping}";
    $wapi_shippingfee = "{$shippingfee}";
    $wapi_chargetax = "{$chargetax}";
    $wapi_mode = "{$mode}";
    $wapi_cancel = "{$cancel}";
    $wapi_period = "{$period}";
    $wapi_freq = "{$frequency}";
    $wapi_start = "{$starttime}";
    $wapi_end = "{$endtime}";
    $wapi_autorecur = "{$autorecur}";
    
require WPWEPAY_PATH.'wepaysdk.php';

$client_id =  get_option('wepay_clientID');
$client_secret =  get_option('wepay_clientsecret');
$access_token =  get_option('wepay_accesstoken');
$account_id =  get_option('wepay_accountID');
 
if (get_option('wepay_mode') == 's') {$whattouse = useStaging;} elseif (get_option('wepay_mode') == 'p') {$whattouse = useProduction;}
Wepay::$whattouse($client_id, $client_secret); $wepay = new WePay($access_token);

///// https://www.wepay.com/developer/reference/preapproval#lookup
if ($wapi_do == '/preapproval') { 
try {
$call = $wepay->request($wapi_do, array(
'preapproval_id' => $wapi_preapprovalid,
));
} catch (WePayException $e) {$error = $e->getMessage();}

///// https://www.wepay.com/developer/reference/preapproval#find
} elseif ($wapi_do=='/preapproval/find') {
try {
$call = $wepay->request($wapi_do, array(
'state' => $wapi_state,
'reference_id' => $wapi_referenceid,
));
} catch (WePayException $e) {$error = $e->getMessage();}

///// https://www.wepay.com/developer/reference/preapproval#create
} elseif ($wapi_do=='/preapproval/create') {
try {
$call = $wepay->request($wapi_do, array(
'account_id' => $wapi_accountid,
'short_description' => $wapi_description,
'long_description' => $wapi_longdesc,
'type' => $wapi_type,
'reference_id' => $wapi_referenceid,
'amount' => $wapi_amount,
'app_fee' => $wapi_appfee,
'fee_payer' => $wapi_feepayer,
'redirect_uri' => $wapi_redirect,
'require_shipping' => $wapi_shipping,
'shipping_fee' => $wapi_shippingfee,
'charge_tax' => $wapi_chargetax,
'mode' => $wapi_mode,
'period' => $wapi_period,
'frequency' => $wapi_freq,
'start_time' => $wapi_start,
'end_time' => $wapi_end,
'auto_recur' => $wapi_autorecur,
));
} catch (WePayException $e) {$error = $e->getMessage();}  

///// https://www.wepay.com/developer/reference/preapproval#cancel
} elseif ($wapi_do=='/preapproval/cancel') {
try {
$call = $wepay->request($wapi_do, array(
'preapproval_id' => $wapi_preapprovalid,
));
} catch (WePayException $e) {$error = $e->getMessage();}  
}

print $call-> $wapi_response;
}
add_shortcode( 'wepay-api-preapproval', 'wepay_api_preapproval_sc' );




function wepay_api_withdrawal_sc($data) {
//// https://www.wepay.com/developer/reference/withdrawal
	
    extract( shortcode_atts( array('do' => '/account', 'response' => '',
    'withdrawalid' => '','accountid' => '','start' => '','limit' => '','state' => '',
    'amount' => '0.00','redirect' => '', 'callback' => '', 'note' => ''), $data ));

	$wapi_do = "{$do}";
	$wapi_response = "{$response}";
    $wapi_withdrawalid = "{$withdrawalid}";
    $wapi_accountid = "{$accountid}";
    $wapi_start = "{$start}";
    $wapi_limit = "{$limit}";
    $wapi_state = "{$state}";
    $wapi_amount = "{$amount}";
    $wapi_redirecturi = "{$redirect}";
    $wapi_callbackuri = "{$callback}";
    $wapi_note = "{$note}";
  
	
require WPWEPAY_PATH.'wepaysdk.php';

$client_id =  get_option('wepay_clientID');
$client_secret =  get_option('wepay_clientsecret');
$access_token =  get_option('wepay_accesstoken');
$account_id =  get_option('wepay_accountID');
 
if (get_option('wepay_mode') == 's') {$whattouse = useStaging;} elseif (get_option('wepay_mode') == 'p') {$whattouse = useProduction;}
Wepay::$whattouse($client_id, $client_secret); $wepay = new WePay($access_token);

///// https://www.wepay.com/developer/reference/withdrawal#lookup
if ($wapi_do == '/withdrawal') { 
try {
$call = $wepay->request($wapi_do, array(
'withdrawal_id' => $wapi_withdrawalid,
));
} catch (WePayException $e) {$error = $e->getMessage();}

///// https://www.wepay.com/developer/reference/withdrawal#find
} elseif ($wapi_do=='/withdrawal/find') {
try {
$call = $wepay->request($wapi_do, array(
'name' => $wapi_name,
'reference_id' => $wapi_referenceid,
));
} catch (WePayException $e) {$error = $e->getMessage();}

///// https://www.wepay.com/developer/reference/withdrawal#create
} elseif ($wapi_do=='/withdrawal/create') {
try {
$call = $wepay->request($wapi_do, array(
'account_id' => $wapi_accountid,
'amount' => $wapi_amount,
'redirect_uri' => $wapi_redirecturi,
'callback_uri' => $wapi_callbackuri,
'note' => $wapi_note,
));
} catch (WePayException $e) {$error = $e->getMessage();}  
}

print $call-> $wapi_response;
}
add_shortcode( 'wepay-api-withdrawal', 'wepay_api_withdrawal_sc' );

?>