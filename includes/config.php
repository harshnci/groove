<?php
	ob_start();
	//ini_set( 'session.cookie_httponly', 1 );
	session_start();

	//session_regenerate_id();

	$timezone = date_default_timezone_set("Europe/London");

	$con = mysqli_connect("localhost", "root", "", "slotify");

	if(mysqli_connect_errno()) {
		echo "Failed to connect: " . mysqli_connect_errno();
	}
?>