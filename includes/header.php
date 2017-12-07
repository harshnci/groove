<?php

include("includes/config.php");
include("includes/classes/User.php");
include("includes/classes/Artist.php");
include("includes/classes/Album.php");
include("includes/classes/Song.php");

if(isset($_SESSION['userLoggedIn'])) {
	$userLoggedIn = new User($con, $_SESSION['userLoggedIn']);
	$username = $userLoggedIn->getUsername();
	$csrftoken = $_SESSION['csrftoken'];

	$stmt = mysqli_prepare($con, "UPDATE users SET sessionvar = '$csrftoken' WHERE username = ?");
	mysqli_stmt_bind_param($stmt, "s", $username);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);

	echo "<script>userLoggedIn = '$username';</script>";
	echo "<script>csrfToken = '$csrftoken';</script>";
}
else {
	header("Location: register.php");
}
?>

<html>
<head>
	<title>Welcome to Slotify!</title>

	<link rel="stylesheet" type="text/css" href="assets/css/style.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="assets/js/script.js"></script>
</head>

<body>

	<div id="mainContainer">

		<div id="topContainer">

			<?php include("includes/navBarContainer.php"); ?>

			<div id="mainViewContainer">
				
				<div id="mainContent">