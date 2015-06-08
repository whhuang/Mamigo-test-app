<?php

	include_once("config.php");
		
	//include google api files
	require_once 'login_gl/src/Google_Client.php';
	require_once 'login_gl/src/contrib/Google_Oauth2Service.php';
	
	//start session
	session_start();

	$gClient = new Google_Client();
	$gClient->setApplicationName('Login to mamigoinc.com');
	$gClient->setClientId($google_client_id);
	$gClient->setClientSecret($google_client_secret);
	$gClient->setRedirectUri($google_redirect_url);
	$gClient->setDeveloperKey($google_developer_key);
	$gClient->setAccessType('online');
	$gClient->setApprovalPrompt('auto');
	$google_oauthV2 = new Google_Oauth2Service($gClient);

	function accessValidation($mysql,$gid,$key)
	{
		try {
			$total_products = $mysql->query("SELECT COUNT(*) as count FROM products")->fetch_object()->count;
			$test_query = $mysql->query("SELECT * FROM transactions WHERE google_id=$gid LIMIT $total_products");
			while($transaction = $test_query->fetch_object()) {
				if(isset($transaction))
				{
					if($transaction->checkout_status=='PaymentActionCompleted')
					{	
						echo '<script> document.getElementById("id_payBtn' . $transaction->event . '").style.display="none";</script>';
						echo '<script> document.getElementById("id_watchBtn' . $transaction->event . '").style.display="initial";</script>';
							
						$ipAddr = (strpos($_SERVER['REMOTE_ADDR'],"."))?$_SERVER['REMOTE_ADDR']:'127.0.0.1';
						$hash =	sha1($ipAddr.$key.$gid);
						echo '<script> document.getElementById("id_watchBtn' . $transaction->event . '").href="player/index.html?id='.$gid.'-key='.$hash.'";</script>';
					}
				}
			}
		} catch (Exception $e) {
			break;
		}
		//echo '<script>alert("'.$ipAddr.'  '.$key.'  '.$gid.'\n'.$_SESSION["qryString"].'");</script>';
	}

	//If user wish to log out, we just unset Session variable
	if (isset($_REQUEST['reset'])) 
	{
	  unset($_SESSION['token']);
	  unset($_SESSION['google_data']); //Google session data unset
	  $gClient->revokeToken();
	  header('Location: ' . filter_var('https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue='.$google_redirect_url, FILTER_SANITIZE_URL)); //redirect user back to page
	}

	//If code is empty, redirect user to google authentication page for code.
	//Code is required to aquire Access Token from google
	//Once we have access token, assign token to session variable
	//and we can redirect user back to page and login.
	if (isset($_GET['code'])) 
	{ 
		$gClient->authenticate($_GET['code']);
		$_SESSION['token'] = $gClient->getAccessToken();
		header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
		return;
	}


	if (isset($_SESSION['token'])) 
	{ 
		$gClient->setAccessToken($_SESSION['token']);
	}


	if ($gClient->getAccessToken() && !$gClient->isAccessTokenExpired()) 
	{
		  //For logged in user, get details from google using access token
		  $user 				= $google_oauthV2->userinfo->get();
		  $tokenObj 			= json_decode($gClient->getAccessToken(),true);
		  $access_token			= $tokenObj['access_token'];
		  $gl_user_id 			= $user['id'];
		  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
		  $first_name			= filter_var($user['given_name'], FILTER_SANITIZE_SPECIAL_CHARS);
		  $last_name			= filter_var($user['family_name'], FILTER_SANITIZE_SPECIAL_CHARS);
		  $gender				= filter_var($user['gender'], FILTER_SANITIZE_SPECIAL_CHARS);
		  $locale				= filter_var($user['locale'], FILTER_SANITIZE_SPECIAL_CHARS);
		  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
		  $profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
		  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
		  $verified				= $user['verified_email'];
		  $personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";

		  $_SESSION['gid'] 		= $user['id'];
		  $_SESSION['token'] 	= $gClient->getAccessToken();
	}
	else 
	{
		//For Guest user, get google login url
		$authUrl = $gClient->createAuthUrl();
	}

?>