<?php
if(isset($_POST['loginButton'])) {

	$username = $_POST['loginUsername'];
	$password = $_POST['loginPassword'];	
	$result = $account->login($username, $password);

	if($result) {
		session_regenerate_id();
		$_SESSION['userLoggedIn'] = $username;
		//$name = 'token-' . mt_rand();
		$token = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		$_SESSION['csrftoken'] = $token;
		//$result = mysqli_query($this->con, "CREATE EVENT slotify_ts ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 20 SECOND DO UPDATE users SET fail = '0' WHERE passworda = '$col1'");

		header("Location: index.php");
	}
}
?>