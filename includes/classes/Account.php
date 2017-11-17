<?php
	class Account {

		private $con;
		private $errorArray;

		public function __construct($con) {
			$this->con = $con;
			$this->errorArray = array();
		}

		public function login($un, $pw){

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
					}

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

			$checkUsernameQuery = mysqli_query($this->con, "SELECT username FROM users WHERE username='$un'");
			if(mysqli_num_rows($checkUsernameQuery) != 0) {
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

			$checkEmailQuery = mysqli_query($this->con, "SELECT email FROM users WHERE email='$em'");
			if(mysqli_num_rows($checkEmailQuery) != 0) {
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