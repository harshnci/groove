<?php
include("../../config.php");

if(!isset($_POST['username'])) {
	echo "ERROR: User not found";
	exit();
}

if(!isset($_POST['oldPassword']) || !isset($_POST['newPassword1'])  || !isset($_POST['newPassword2'])) {
	echo "Not all passwords have been set";
	exit();
}

if($_POST['oldPassword'] == "" || $_POST['newPassword1'] == ""  || $_POST['newPassword2'] == "") {
	echo "Please fill in all fields";
	exit();
}

$username = $_POST['username'];
$oldPassword = $_POST['oldPassword'];
$newPassword1 = $_POST['newPassword1'];
$newPassword2 = $_POST['newPassword2'];

$stmt = mysqli_prepare($con, "SELECT sessionvar FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $sess);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if($sess == $_POST['csrf']) {
	
	//$newencryptedPw = password_hash($newPassword1, PASSWORD_BCRYPT);

	$stmt = mysqli_prepare($con, "SELECT salt, passworda FROM users WHERE username = ?");
	mysqli_stmt_bind_param($stmt, "s", $username);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $salt, $col2);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_close($stmt);

	$pword = hash('sha256', $oldPassword );
	$revv2 = strrev($salt . $pword);
	$encryPw = hash('sha256', $revv2 );

	if ($encryPw == $col2) {
		if($newPassword1 != $newPassword2) {
			echo "Your new passwords do not match";
			exit();
		}
		$salthash = bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
		$pwordhash = hash('sha256', $newPassword1);
		$revv = strrev($salthash . $pwordhash);
		$encryptedPw = hash('sha256', $revv );

		$stmt = mysqli_prepare($con, "UPDATE users SET passworda = '$encryptedPw' WHERE username = ?" );
		mysqli_stmt_bind_param($stmt, "s", $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		$stmt = mysqli_prepare($con, "UPDATE users SET salt = '$salthash' WHERE username = ?" );
		mysqli_stmt_bind_param($stmt, "s", $username);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		echo "Update successful";
	}
	else {
		echo "Password is incorrect";
	}
}
else {
	echo "Unauthorized";
}
?>