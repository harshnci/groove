<?php
	class Account {

		private $con;
		private $errorArray;

		public function __construct($con) {
			$this->con = $con;
			$this->errorArray = array();
		}

		public function login($un, $pw){

			$stmt = mysqli_prepare($this->con, "SELECT password, fail FROM users WHERE username = ?");
			mysqli_stmt_bind_param($stmt, "s", $un);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $col1, $failatt);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_fetch($stmt);
			if($col1){
				if($failatt < 3) {
					if (password_verify($pw, $col1)) {
						$stmt = mysqli_prepare($this->con, "UPDATE users SET fail = '0' WHERE username = ?");
						mysqli_stmt_bind_param($stmt, "s", $un);
						mysqli_stmt_execute($stmt);
						//mysqli_query($this->con, "UPDATE users SET fail = '0' WHERE username = '$un'");
			    		return true;
					} 
					else {
						$failatt = $failatt + 1;
						if($failatt == 3){
							/*$stmt = mysqli_prepare($this->con, "CREATE EVENT slotify_ts ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 20 SECOND DO UPDATE users SET fail = '0' WHERE username = ?");
							mysqli_stmt_bind_param($stmt, "s", $un);
							mysqli_stmt_execute($stmt);*/
							mysqli_query($this->con, "CREATE EVENT slotify_ts ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 20 SECOND DO UPDATE users SET fail = '0' WHERE password = '$col1'");
							//mysqli_query($this->con, "CREATE EVENT slotify_ts ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 20 SECOND DO UPDATE users SET fail = '0' WHERE username = '$un'");
						}
						$stmt = mysqli_prepare($this->con, "UPDATE users SET fail = '$failatt' WHERE username = ?");
						mysqli_stmt_bind_param($stmt, "s", $un);
						mysqli_stmt_execute($stmt);
						//mysqli_query($this->con, "UPDATE users SET fail = '$failatt' WHERE username = '$un'");
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
		/*public function login($un, $pw){

			$stmt = mysqli_prepare($this->con, "SELECT password FROM users WHERE username = ?");
			mysqli_stmt_bind_param($stmt, "s", $un);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $col1);
			mysqli_stmt_fetch($stmt);
			if($col1) {
				if (password_verify($pw, $col1)) {
			    	return true;
				} 
				else {
					array_push($this->errorArray, Constants::$loginFailed);
					return false;
				}
			}
			else {
				array_push($this->errorArray, Constants::$loginFailed);
				return false;
			}
		}*/

		public function register($un, $fn, $ln, $em, $em2, $pw, $pw2) {
			$this->validateUsername($un);
			$this->validateFirstName($fn);
			$this->validateLastName($ln);
			$this->validateEmails($em, $em2);
			$this->validatePasswords($pw, $pw2);

			if(empty($this->errorArray) == true) {
				//Insert into db
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
			//$salt = $this->rand_str();
			$encryptedPw = password_hash($pw, PASSWORD_BCRYPT);
			$profilePic = "assets/images/profile-pics/head_emerald.png";
			$date = date("Y-m-d");
			$stmt = mysqli_prepare($this->con, "INSERT INTO users (username, firstname, lastname, email, password, signUpDate, profilepic) VALUES (?, ?, ?, ?, ?, ?, ?)");
			mysqli_stmt_bind_param($stmt, "sssssss", $un, $fn, $ln, $em, $encryptedPw, $date, $profilePic);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_fetch($stmt);
			return;
		}

		/*
		private function rand_str() {
    		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  
    		$size = strlen( $chars );
    		for( $i = 0; $i < 10; $i++ ) {
        		$str .= $chars[ rand( 0, $size - 1 ) ];
   	 		}
    		return $str;
		}
		*/

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

			//$checkUsernameQuery = mysqli_query($this->con, "SELECT username FROM users WHERE username='$un'");
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
			//$checkEmailQuery = mysqli_query($this->con, "SELECT email FROM users WHERE email='$em'");
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