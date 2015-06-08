<?php
include_once("paypal.class.php");
//Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
if(isset($_GET["token"]) && isset($_GET["PayerID"]))
{
	//we will be using these two variables to execute the "DoExpressCheckoutPayment"
	//Note: we haven't received any payment yet.
	$paypalmode = ($PayPalMode=='sandbox') ? '.sandbox' : '';
	
	$token = $_GET["token"];
	$payer_id = $_GET["PayerID"];
	
	//get session variables
	$paypal_product = $_SESSION["paypal_products"];
	$paypal_data = '';
	$ItemTotalPrice = 0;

    foreach($paypal_product['items'] as $key=>$p_item)
    {		
		$paypal_data .= '&L_PAYMENTREQUEST_0_QTY'.$key.'='. urlencode($p_item['itm_qty']);
        $paypal_data .= '&L_PAYMENTREQUEST_0_AMT'.$key.'='.urlencode($p_item['itm_price']);
        $paypal_data .= '&L_PAYMENTREQUEST_0_NAME'.$key.'='.urlencode($p_item['itm_name']);
        $paypal_data .= '&L_PAYMENTREQUEST_0_NUMBER'.$key.'='.urlencode($p_item['itm_code']);
        
		// item price X quantity
        $subtotal = ($p_item['itm_price']*$p_item['itm_qty']);
		
        //total price
        $ItemTotalPrice = ($ItemTotalPrice + $subtotal);
    }

	$padata = 	'&TOKEN='.urlencode($token).
				'&PAYERID='.urlencode($payer_id).
				'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE").
				$paypal_data.
				'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($ItemTotalPrice).
				'&PAYMENTREQUEST_0_TAXAMT='.urlencode($paypal_product['assets']['tax_total']).
				'&PAYMENTREQUEST_0_AMT='.urlencode($paypal_product['assets']['grand_total']).
				'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode);

	//We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
	$paypal= new MyPayPal();
	$httpParsedResponseAr = $paypal->PPHttpPost('DoExpressCheckoutPayment', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
	
	//Check if everything went ok..
	if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
	{
			header("Location: /pricing/index.php");
			echo '<center><h2>Success</h2>';
			echo 'Your Transaction ID : '.urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);
			
				/*
				//Sometimes Payment are kept pending even when transaction is complete. 
				//hence we need to notify user about it and ask him manually approve the transiction
				*/
				
				if('Completed' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"])
				{
					echo '<br /><br /><div style="color:green">Payment Received!</div>';
				}
				elseif('Pending' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"])
				{
					echo '<div style="color:red">Transaction Complete, but payment is still pending! '.'You need to manually authorize this payment in your <a target="_new" href="http://www.paypal.com">Paypal Account</a></div>';
				}

				// we can retrive transection details using either GetTransactionDetails or GetExpressCheckoutDetails
				// GetTransactionDetails requires a Transaction ID, and GetExpressCheckoutDetails requires Token returned by SetExpressCheckOut
				$padata = 	'&TOKEN='.urlencode($token);
				$paypal= new MyPayPal();
				$httpParsedResponseAr = $paypal->PPHttpPost('GetExpressCheckoutDetails', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);

				if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
				{
					$token = urldecode($httpParsedResponseAr["TOKEN"]);
					$transID = urldecode($httpParsedResponseAr["PAYMENTREQUEST_0_TRANSACTIONID"]);
					$timestamp = urldecode($httpParsedResponseAr["TIMESTAMP"]);
					
					$chkOutStatus = urldecode($httpParsedResponseAr["CHECKOUTSTATUS"]);
					$ack = urldecode($httpParsedResponseAr["ACK"]);
					$payerStatus = urldecode($httpParsedResponseAr["PAYERSTATUS"]);
					
					$firstName = urldecode($httpParsedResponseAr["FIRSTNAME"]);
					$lastName = urldecode($httpParsedResponseAr["LASTNAME"]);
					$emailID = urldecode($httpParsedResponseAr["EMAIL"]);
					$payerID = urldecode($httpParsedResponseAr["PAYERID"]);
					$cntryCode = urldecode($httpParsedResponseAr["COUNTRYCODE"]);
					$currCode = urldecode($httpParsedResponseAr["CURRENCYCODE"]);
					
					$itemName = urldecode($httpParsedResponseAr["L_NAME0"]);
					$itemCode = urldecode($httpParsedResponseAr["L_NUMBER0"]);
					$itemQty = urldecode($httpParsedResponseAr["L_QTY0"]);
					$itemAmt = urldecode($httpParsedResponseAr["ITEMAMT"]);
					$shipAmt = urldecode($httpParsedResponseAr["SHIPPINGAMT"]);
					$handlAmt = urldecode($httpParsedResponseAr["HANDLINGAMT"]);
					$taxAmt = urldecode($httpParsedResponseAr["TAXAMT"]);
					$insurAmt = urldecode($httpParsedResponseAr["INSURANCEAMT"]);
					$totAmt = urldecode($httpParsedResponseAr["AMT"]);
					$shipDisAmt = urldecode($httpParsedResponseAr["SHIPDISCAMT"]);
					
					$billAgrStatus = urldecode($httpParsedResponseAr["BILLINGAGREEMENTACCEPTEDSTATUS"]);
					$correlID = urldecode($httpParsedResponseAr["CORRELATIONID"]);
					$version = urldecode($httpParsedResponseAr["VERSION"]);
					$build = urldecode($httpParsedResponseAr["BUILD"]);
					$insurOffered = urldecode($httpParsedResponseAr["PAYMENTREQUEST_0_INSURANCEOPTIONOFFERED"]);
					$AddrNormStatus = urldecode($httpParsedResponseAr["PAYMENTREQUEST_0_ADDRESSNORMALIZATIONSTATUS"]);
					$errCode = urldecode($httpParsedResponseAr["PAYMENTREQUESTINFO_0_ERRORCODE"]);
					
					if ($mysqli->connect_error) {
						die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
					}		
					
					$columns = 'google_id,';						$values = '\''.$_SESSION['gid'].'\',';
					$columns .= 'token,';							$values .= '\''.$token.'\',';
					$columns .= 'transaction_id,';					$values .= '\''.$transID.'\',';
					$columns .= 'timestamp,';						$values .= '\''.$timestamp.'\',';
					$columns .= 'checkout_status,';					$values .= '\''.$chkOutStatus.'\',';
					$columns .= 'ack,';								$values .= '\''.$ack.'\',';
					$columns .= 'payer_status,';					$values .= '\''.$payerStatus.'\',';
					$columns .= 'first_name,';						$values .= '\''.$firstName.'\',';
					$columns .= 'last_name,';						$values .= '\''.$lastName.'\',';
					$columns .= 'email_id,';						$values .= '\''.$emailID.'\',';
					$columns .= 'payer_id,';						$values .= '\''.$payerID.'\',';
					$columns .= 'country_code,';					$values .= '\''.$cntryCode.'\',';
					$columns .= 'curr_code,';						$values .= '\''.$currCode.'\',';
					$columns .= 'item_name,';						$values .= '\''.$itemName.'\',';
					$columns .= 'item_code,';						$values .= '\''.$itemCode.'\',';
					$columns .= 'item_qty,';						$values .= '\''.$itemQty.'\',';
					$columns .= 'item_amt,';						$values .= '\''.$itemAmt.'\',';
					$columns .= 'ship_amt,';						$values .= '\''.$shipAmt.'\',';
					$columns .= 'handling_amt,';					$values .= '\''.$handlAmt.'\',';
					$columns .= 'tax_amt,';							$values .= '\''.$taxAmt.'\',';
					$columns .= 'insurance_amt,';					$values .= '\''.$insurAmt.'\',';
					$columns .= 'total_amt,';						$values .= '\''.$totAmt.'\',';
					$columns .= 'ship_discnt_amt,';					$values .= '\''.$shipDisAmt.'\',';			
					$columns .= 'bill_agree_accept_status,';		$values .= '\''.$billAgrStatus.'\',';
					$columns .= 'correlation_id,';					$values .= '\''.$correlID.'\',';
					$columns .= 'version,';							$values .= '\''.$version.'\',';			
					$columns .= 'build,';							$values .= '\''.$build.'\',';			
					$columns .= 'insurance_offered,';				$values .= '\''.$insurOffered.'\',';
					$columns .= 'addr_normalize_status,';			$values .= '\''.$AddrNormStatus.'\',';
					$columns .= 'error_code,';						$values .= '\''.$errCode.'\',';
					$columns .= 'event';							$values .= '\''.$_SESSION['event'].'\'';
					
					$insert_row = $mysqli->query("INSERT INTO transactions ($columns) VALUES ($values)");
					
					// if(!$insert_row){
						// print 'Success! ID of last inserted record is : ' .$mysqli->insert_id .'<br />'; 
					// }else{
						// die('Error : ('. $mysqli->errno .') '. $mysqli->error);
					// }
					
					// echo '<pre>';
					// print_r($httpParsedResponseAr);
					// echo '</pre>';
				} else  {
					echo '<div style="color:red"><b>GetTransactionDetails failed:</b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
					// echo '<pre>';
					// print_r($httpParsedResponseAr);
					// echo '</pre>';

				}
				echo '</center>';
	
	}else{
			echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
			// echo '<pre>';
			// print_r($httpParsedResponseAr);
			// echo '</pre>';
	}
}
?>