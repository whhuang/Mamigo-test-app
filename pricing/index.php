<?php

include_once("login_gl/login.php");

//HTML page start
echo '<!DOCTYPE HTML><html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<link href="index.css" rel="stylesheet" type="text/css" />';
echo '<title>User Login</title>';
echo '</head>';
echo '<body><center>';
echo '<div class="box"> <div>';

if(isset($authUrl)) //user is not logged in, show login button
{
	echo '<h1>User Login</h1>';
	echo '<img src="images/user.png" width="100px" size="100px" /><br/>';
    echo '<a class="login" href="'.$authUrl.'"><img class="login" src="images/sign-in-with-google.png" width="250px" size="54px" onmousedown="this.style.opacity=\'0.4\'" onmouseup="this.style.opacity=\'1\'" /></a>';
	echo '<a class="login" href="login_fb/login.php"><img class="login" src="images/fblogin.png" width="244px" height="50px" onmousedown="this.style.opacity=\'0.4\'" onmouseup="this.style.opacity=\'1\'" /></a>';
	echo '</div> </div>'; 
} 
else // user logged in 
{
	echo '<h1>Welcome</h1>';
    //compare user id in our database
	$user_exist = $mysqli->query("SELECT COUNT(google_id) as usercount FROM google_users WHERE google_id=$gl_user_id")->fetch_object()->usercount; 
	if(!$user_exist)
	{
		$mysqli->query("INSERT INTO `google_users` (`google_id`, `google_name`, `google_fname`, `google_lname`, `gender`, `locale`, `google_link`, `google_picture_link`, `google_email`, `google_verified`) VALUES ('$gl_user_id', '$user_name', '$first_name', '$last_name', '$gender', '$locale', '$profile_url', '$profile_image_url', '$email', '$verified')");
		//$mysqli->query("INSERT INTO products (google_id, product_code, product_name, price) VALUES ($gl_user_id,'MAM001','Strive Broadcast',5.95)");
	}
	
	//$user_exist = $mysqli->query("SELECT COUNT(google_id) as usercount FROM google_users WHERE google_id=$gl_user_id")->fetch_object()->usercount; 

	//$_SESSION['products'] = $mysqli->query("SELECT * FROM products WHERE google_id = $gl_user_id");
	echo '<input type="hidden" name="google_id" value="'.$gl_user_id.'" />';
	
	$userData = $mysqli->query("SELECT * FROM google_users WHERE google_id=$gl_user_id")->fetch_object(); 
	echo '<p class="welcome"><a href="'.$userData->google_link.'" />'.$userData->google_name.'</a></p>';
	echo '<img class="circle-image" src="'.$userData->google_picture_link.'?sz=100" width="100px" size="100px" /><br/>';
	echo '<p class="oauthemail">'.$userData->google_email.'</p>';
	echo '<a href="?reset=1"><div class="logout"  onmousedown="this.style.opacity=\'0.4\'" onmouseup="this.style.opacity=\'1\'">Logout</div></a>';	
	echo '</div> </div>'; 
	
	echo '<form method="post" action="payment/process.php">';
	echo '<input type="hidden" id="name" name="name" value="1">';
	echo '<br /><br /><br /><input id="id_payBtn1" type="submit" value="1">';// onmousedown="this.style.opacity=\'0.4\'" onmouseup="this.style.opacity=\'1\'" />';
	echo '<a id="id_watchBtn1" href="player/index.html" style="display:none"><img src="images/watchnow.png" width="200" height="54" /></a></form>';
	
	echo '<form method="post" action="payment/process.php">';
	echo '<input type="hidden" id="name" name="name" value="2">';
	echo '<br /><input id="id_payBtn2" type="submit" value="2">';
	echo '<a id="id_watchBtn2" href="player/index.html" style="display:none"><img src="images/watchnow.png" width="200" height="54" /></a></form>';


	accessValidation($mysqli,$gl_user_id,$secretKey);
	
	//list all user details
	//echo '<pre>'; 
	//print_r($user);
	//echo '</pre>';	
}


if (isset($_SESSION['user_id'])) {
	echo "<span><img src='".$_SESSION['profile_pic_url']."' > </span>";
	echo "<span>Hi ".$_SESSION['name']." ( <a href='logout.php' >Log out</a>)</span><br>";
	echo $_SESSION['email'];
}
else {
	if ( isset($_GET['error_code'] ) && $_GET['error_code'] == 1 ) {
	echo "<p>Oops!! Something went wrong. Please try again</p>";
	}
}

echo '</center></body></html>';

include_once("payment/feedback.php");
?>

