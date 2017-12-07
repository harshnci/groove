<?php

	if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
		header('X-Frame-Options: SAMEORIGIN');
		header("X-XSS-Protection: 1; mode=block");
		header("Strict-Transport-Security: max-age=31536000");
		header('Access-Control-Allow-Origin: http://localhost/slotify', false);

		include("includes/config.php");
		include("includes/classes/User.php");
		include("includes/classes/Artist.php");
		include("includes/classes/Album.php");
		include("includes/classes/Song.php");
		
		//if(isset($_GET['userLoggedIn'])) {
		if(isset($_SESSION['userLoggedIn'])) {
			$userLoggedIn = new User($con, $_SESSION['userLoggedIn']);
		
		}
		else {
			echo "Something Went wrong";
		}
	}
	else {
		header('X-Frame-Options: SAMEORIGIN');
		header("X-XSS-Protection: 1; mode=block");
		header("Strict-Transport-Security: max-age=31536000");
		header('Access-Control-Allow-Origin: http://localhost/slotify', false);

		include("includes/header.php");
		include("includes/footer.php");

		$url = $_SERVER['REQUEST_URI']; 
		echo "<script>openPage('$url')</script>";
		exit();
	}

?>