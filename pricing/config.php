<?php

$currency = '$'; //Currency sumbol or code

//db settings
$db_username = 'root';
$db_password = '';
$db_name = 'test';
$db_host = '127.0.0.1';
$mysqli = new mysqli($db_host, $db_username, $db_password,$db_name);

########## Google Settings.. Client ID, Client Secret from https://cloud.google.com/console #############
$google_client_id 		= '766695400135-vs1l2onep24qg6scnrjaa1j6ogd1hi4h.apps.googleusercontent.com';
$google_client_secret 	= 'gT-zts4_lH7ocLN-OZ4MFuXI';
$google_redirect_url 	= 'https://localhost/pricing/index.php'; //path to your script
$google_developer_key 	= 'AIzaSyAMQrnhhuZaMVawCdY-vqreo7aMWdyomKU';

// facebook settings
$fb_app_id = "274859205971566";
$fb_app_secret = "5c4ff90ea9db883ce884cab9fcd7cccd";
$fb_app_url = "http://mamigoinc.com/game/login-with-facebook/login.php";

//paypal settings
$PayPalMode 			= 'sandbox'; // sandbox or live
$PayPalApiUsername 		= 'dummymailmi-facilitator_api1.gmail.com'; //PayPal API Username
$PayPalApiPassword 		= 'VV2TC9RKS6MHVV7T'; //Paypal API password
$PayPalApiSignature 	= 'AFcWxV21C7fd0v3bYYYRCpSSRl31A3v.Z-24JYv.ZEi5qD7BjqKM4NYn'; //Paypal API Signature
$PayPalCurrencyCode 	= 'USD'; //Paypal Currency Code
$PayPalReturnURL 		= 'http://localhost/pricing/index.php'; //Point to process.php page
$PayPalCancelURL 		= 'http://localhost/pricing/payment/cancel_url.html'; //Cancel URL if user clicks cancel

$secretKey = '835294716045';

?>