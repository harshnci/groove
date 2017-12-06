<?php
include("../../config.php");

if(!isset($_POST['username'])) {
	echo "Error";
	exit();
}

if($_SESSION['csrftoken'] == $_POST['csrf']) {

	if(isset($_POST['email']) && $_POST['email'] != "") {
		$email = $_POST['email'];
		$username = $_POST['username'];

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo "Email is invalid";
			exit();
		}


		$stmt = mysqli_prepare($con, "SELECT email FROM users WHERE email=? AND username != ?");
		mysqli_stmt_bind_param($stmt, "ss", $email, $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $mailee);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
		if($mailee) {
			echo "Email already in use";
			exit();
		}

		/*
		$emailcheck = mysqli_query($con, "SELECT email FROM users WHERE email='$email' AND username != '$username'");
		if(mysqli_num_rows($emailcheck) > 0) {
			echo "Email already in use";
			exit();
		}*/

		$stmt = mysqli_prepare($con, "UPDATE users SET email = ? WHERE username = ?");
		mysqli_stmt_bind_param($stmt, "ss", $email, $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
		echo "Update successful";


		//$updateQuery = mysqli_query($con, "UPDATE users SET email = '$email' WHERE username ='$username'");
		//echo "Update successful";
	}
	else {
		echo "you must provide an email";
	}
}

else {
	echo "Unauthorized";
}

?>

