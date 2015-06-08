<?php
// Code from http://www.sanwebe.com/2012/07/paypal-expresscheckout-with-php

session_start();
include_once("../config.php");
include_once("paypal.class.php");

$paypalmode = ($PayPalMode=='sandbox') ? '.sandbox' : '';

if($_POST) //Post Data received from product list page.
{

	//Other important variables like tax, shipping cost
	$TotalTaxAmount 	= 0.05;  //Sum of tax for all items in this order. 

	//we need 4 variables from product page Item Name, Item Price, Item Number and Item Quantity.
	//Please Note : People can manipulate hidden field amounts in form,
	//In practical world you must fetch actual price from database using item id. 
	//eg : $ItemPrice = $mysqli->query("SELECT item_price FROM products WHERE id = Product_Number");
	$paypal_data ='';
	$ItemTotalPrice = 0;
		
        $google_id 	= filter_var($_SESSION['gid'], FILTER_SANITIZE_STRING); 
        $event = $_POST["name"];
		
		$results = $mysqli->query("SELECT product_code, product_name, price FROM products WHERE product_code='$event' LIMIT 1");
		$obj = $results->fetch_object();
		
		$paypal_data .= '&L_PAYMENTREQUEST_0_NAME0='.urlencode($obj->product_name);
        $paypal_data .= '&L_PAYMENTREQUEST_0_NUMBER0='.urlencode($obj->product_code);
        $paypal_data .= '&L_PAYMENTREQUEST_0_AMT0='.urlencode($obj->price);		
		$paypal_data .= '&L_PAYMENTREQUEST_0_QTY0='. urlencode('1');
        
		// item price X quantity
        $subtotal = ($obj->price*1);
		
        //total price
        $ItemTotalPrice = $ItemTotalPrice + $subtotal;
		
		//create items for session
		$paypal_product['items'][] = array('itm_name'=>$obj->product_name, 'itm_price'=>$obj->price, 'itm_code'=>$obj->product_code, 'itm_qty'=>'1' );
		
	//Grand total including all tax, insurance, shipping cost and discount
	$GrandTotal = ($ItemTotalPrice + $TotalTaxAmount);
	
								
	$paypal_product['assets'] = array('tax_total'=>$TotalTaxAmount, 'grand_total'=>$GrandTotal);
	
	//create session array for later use
	$_SESSION["paypal_products"] = $paypal_product;
	$_SESSION["event"] = $event;
	
	//Parameters for SetExpressCheckout, which will be sent to PayPal
	$padata = 	'&METHOD=SetExpressCheckout'.
				'&RETURNURL='.urlencode($PayPalReturnURL).
				'&CANCELURL='.urlencode($PayPalCancelURL).
				'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE").
				$paypal_data.				
				'&NOSHIPPING=1'. //set 1 to hide buyer's shipping address, in-case products that does not require shipping
				'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($ItemTotalPrice).
				'&PAYMENTREQUEST_0_TAXAMT='.urlencode($TotalTaxAmount).
				'&PAYMENTREQUEST_0_AMT='.urlencode($GrandTotal).
				'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode).
				'&LOCALECODE=GB'. //PayPal pages to match the language on your website.
				'&CARTBORDERCOLOR=FFFFFF'. //border color of cart
				'&ALLOWNOTE=0'.
				'&SOLUTIONTYPE=Sole';
		
		//We need to execute the "SetExpressCheckOut" method to obtain paypal token
		$paypal= new MyPayPal();
		$httpParsedResponseAr = $paypal->PPHttpPost('SetExpressCheckout', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
		
		//Respond according to message we receive from Paypal
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
		{
				//Redirect user to PayPal store with Token received.
			 	$paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
				header('Location: '.$paypalurl);
		}
		else
		{
			//Show error message
			echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
			echo '<pre>';
			print_r($httpParsedResponseAr);
			echo '</pre>';
		}
}
?>
