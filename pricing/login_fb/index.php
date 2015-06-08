<?php
session_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title></title>
</head>
<body>
<?php

if (isset($_SESSION['user_id'])) {
	echo "<span><img src='".$_SESSION['profile_pic_url']."' > </span>";
	echo "<span>Hi ".$_SESSION['name']." ( <a href='logout.php' >Log out</a>)</span><br>";
	echo $_SESSION['email'];
}
else {
	if ( isset($_GET['error_code'] ) && $_GET['error_code'] == 1 ) {
	echo "<p>Oops!! Something went wrong. Please try again</p>";
	}
	echo "<a href='login.php'><img src='login_with_facebook.png' alt='Sign in with Facebook'/></a>";
}
?>
</body>
</html>