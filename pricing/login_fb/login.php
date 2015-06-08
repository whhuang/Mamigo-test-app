<?php
session_start();
require_once('../config.php');
if (isset($_REQUEST['error'])) {
  header('Location: ./index.php?error_code=1');
}
if (!isset($_SESSION['fb_user_id'])) {
    $code = $_REQUEST["code"]; 
   // auth user
   if(empty($code)) {
      $dialog_url = 'https://www.facebook.com/dialog/oauth?client_id='.$fb_app_id.'&redirect_uri='.urlencode($fb_app_url).'&scope=email';
      echo("<script>top.location.href='".$dialog_url."'</script>");
    }

    // get user access_token
    $token_url = 'https://graph.facebook.com/oauth/access_token?client_id='.$fb_app_id.'&redirect_uri='.urlencode($fb_app_url).'&client_secret='.$fb_app_secret.'&code='.$code; 

    // response is of the format "access_token=AAAC..."
    $access_token = substr(file_get_contents($token_url), 13);

    // Exchanging temporary short lived access token with long lived access toke so it can be used later
    // response is of the format "access_token=AAAC...&expires=XXX"
    $long_lived_token_url = "https://graph.facebook.com/oauth/access_token?client_id=$fb_app_id&client_secret=$fb_app_secret&grant_type=fb_exchange_token&fb_exchange_token=$access_token"; 
    $long_lived_token = substr(file_get_contents($long_lived_token_url), 13);

    $start_pos = strrpos($long_lived_token, "&expires");
    $str_len = strlen($long_lived_token);
    $long_lived_token = substr($long_lived_token, -$str_len, $start_pos-$str_len);
    
    // Call Facebook Graph API to get user data
    $fql_query_url = 'https://graph.facebook.com/v2.0/me?fields=picture,id,name,email'.'&access_token='.$access_token;
    $fql_query_result = file_get_contents($fql_query_url);
    $fql_query_obj = json_decode($fql_query_result, true);
   
    $fb_user_id = $_SESSION['fid'] = $fql_query_obj['id'];
    $name = $_SESSION['name'] = $fql_query_obj['name'];
    $email = $_SESSION['email'] = $fql_query_obj['email'];
    $profile_pi_url = $_SESSION['profile_pic_url'] = $fql_query_obj['picture']['data']['url'];
    $access_token = $_SESSION['access_token'] = $long_lived_token;

    // Checks DB and inserts the user if he is not already registered
	$fb_user_exist = $mysqli->query("SELECT COUNT(fb_id) as usercount FROM facebook_users WHERE fb_id=$fb_user_id")->fetch_object()->usercount; 
	if(!$fb_user_exist)
	{
		$mysqli->query("INSERT into facebook_users VALUES ($fb_user_id,'$name','$email','$profile_pi_url','$access_token');");
    }
}
 header('Location: ../index.php');
?>
