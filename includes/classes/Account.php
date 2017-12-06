<?php
	class Account {

		private $con;
		private $errorArray;

		public function __construct($con) {
			$this->con = $con;
			$this->errorArray = array();
		}

		public function login($un, $pw){

			$stmt = mysqli_prepare($this->con, "SELECT salt,passworda, fail FROM users WHERE username = ?");
			mysqli_stmt_bind_param($stmt, "s", $un);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $sess, $col1, $failatt);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_fetch($stmt);
	

			$pword = hash('sha256', $pw );
			$revv2 = strrev($sess . $pword);
			$encryPw = hash('sha256', $revv2 );



			/*
			$stmt = mysqli_prepare($this->con, "SELECT passworda, fail FROM users WHERE username = ?");
			mysqli_stmt_bind_param($stmt, "s", $un);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $col1, $failatt);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_fetch($stmt);*/

			if($col1){
				if($failatt < 3) {
					if ($encryPw == $col1) {
					//if (password_verify($pw, $col1)) {
						$stmt = mysqli_prepare($this->con, "UPDATE users SET fail = '0' WHERE username = ?");
						mysqli_stmt_bind_param($stmt, "s", $un);
						mysqli_stmt_execute($stmt);
			    		return true;
					} 
					else {
						$failatt = $failatt + 1;
						if($failatt == 3){
							mysqli_query($this->con, "CREATE EVENT slotify_ts ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 20 SECOND DO UPDATE users SET fail = '0' WHERE passworda = '$col1'");
						}
						$stmt = mysqli_prepare($this->con, "UPDATE users SET fail = '$failatt' WHERE username = ?");
						mysqli_stmt_bind_param($stmt, "s", $un);
						mysqli_stmt_execute($stmt);
						array_push($this->errorArray, Constants::$loginFailed);
						return false;
					}
				}
				else{
					array_push($this->errorArray, Constants::$loginLock);
					return false;
				}
			}
			else{
				array_push($this->errorArray, Constants::$loginFailed);
				return false;
			}	
		}

		public function register($un, $fn, $ln, $em, $em2, $pw, $pw2) {
			$this->validateUsername($un);
			$this->validateFirstName($fn);
			$this->validateLastName($ln);
			$this->validateEmails($em, $em2);
			$this->validatePasswords($pw, $pw2);

			if(empty($this->errorArray) == true) {
				$this->insertUserDetails($un, $fn, $ln, $em, $pw);
				return true;
			}
			else {
				return false;
			}
		}

		public function getError($error) {
			if(!in_array($error, $this->errorArray)) {
				$error = "";
			}
			return "<span class='errorMessage'>$error</span>";
		}

		private function insertUserDetails($un, $fn, $ln, $em, $pw) {
			//$saltplain = $this->rand_str();
			$salthash = bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
			//$salthash = hash('sha256', '$saltplain' );
			$pwordhash = hash('sha256', $pw);
			$revv = strrev($salthash . $pwordhash);
			$encryptedPw = hash('sha256', $revv );
			$profilePic = "assets/images/profile-pics/head_emerald.png";
			$date = date("Y-m-d");
			$stmt = mysqli_prepare($this->con, "INSERT INTO users (username, salt, firstname, lastname, email, passworda, signUpDate, profilepic) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
			mysqli_stmt_bind_param($stmt, "ssssssss", $un, $salthash, $fn, $ln, $em, $encryptedPw, $date, $profilePic);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_fetch($stmt);
			return;			

			/*
			$encryptedPw = password_hash($pw, PASSWORD_BCRYPT);
			$profilePic = "assets/images/profile-pics/head_emerald.png";
			$date = date("Y-m-d");
			$stmt = mysqli_prepare($this->con, "INSERT INTO users (username, firstname, lastname, email, passworda, signUpDate, profilepic) VALUES (?, ?, ?, ?, ?, ?, ?)");
			mysqli_stmt_bind_param($stmt, "sssssss", $un, $fn, $ln, $em, $encryptedPw, $date, $profilePic);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_fetch($stmt);
			return;*/
		}

		
		/*private function rand_str() {
    		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  
    		$size = strlen( $chars );
    		$str = "";
    		for( $i = 0; $i < 10; $i++ ) {
        		$str .= $chars[ rand( 0, $size - 1 ) ];
   	 		}
    		return $str;
		}*/
		

		private function validateUsername($un) {

			if(strlen($un) > 25 || strlen($un) < 5) {
				array_push($this->errorArray, Constants::$usernameCharacters);
				return;
			}
			$stmt = mysqli_prepare($this->con, "SELECT username FROM users WHERE username= ?");
			mysqli_stmt_bind_param($stmt, "s", $un);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $uname);
			mysqli_stmt_fetch($stmt);
			if($uname) {
				array_push($this->errorArray, Constants::$usernameTaken);
				return;
			}
		}

		private function validateFirstName($fn) {
			if(strlen($fn) > 25 || strlen($fn) < 2) {
				array_push($this->errorArray, Constants::$firstNameCharacters);
				return;
			}
		}

		private function validateLastName($ln) {
			if(strlen($ln) > 25 || strlen($ln) < 2) {
				array_push($this->errorArray, Constants::$lastNameCharacters);
				return;
			}
		}

		private function validateEmails($em, $em2) {
			if($em != $em2) {
				array_push($this->errorArray, Constants::$emailsDoNotMatch);
				return;
			}

			if(!filter_var($em, FILTER_VALIDATE_EMAIL)) {
				array_push($this->errorArray, Constants::$emailInvalid);
				return;
			}

			$stmt = mysqli_prepare($this->con, "SELECT email FROM users WHERE email=?");
			mysqli_stmt_bind_param($stmt, "s", $em);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $maile);
			mysqli_stmt_fetch($stmt);
			if($maile) {
				array_push($this->errorArray, Constants::$emailTaken);
				return;
			}
		}

		private function validatePasswords($pw, $pw2) {
			if($pw != $pw2) {
				array_push($this->errorArray, Constants::$passwordsDoNoMatch);
				return;
			}
			if(preg_match('/[^A-Za-z0-9]/', $pw)) {
				array_push($this->errorArray, Constants::$passwordNotAlphanumeric);
				return;
			}
			if(strlen($pw) > 30 || strlen($pw) < 5) {
				array_push($this->errorArray, Constants::$passwordCharacters);
				return;
			}
		}
	}
?>