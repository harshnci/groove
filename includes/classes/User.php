<?php
	class User {

		private $con;
		private $username;

		public function __construct($con, $username) {
			$this->con = $con;
			$this->username = $username;
		}

		public function getUsername() {
			return $this->username;
		}

		public function getFirstAndLastName() {

			$stmt = mysqli_prepare($this->con, "SELECT concat(firstName, ' ', lastName) as 'name' FROM users WHERE username = ?");
			mysqli_stmt_bind_param($stmt, "s", $this->username);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $name);
			mysqli_stmt_fetch($stmt);
			return $name;

			//$query = mysqli_query($this->con, "SELECT concat(firstName, ' ', lastName) as 'name' FROM users WHERE username='$this->username'");
			//$row=mysqli_fetch_array($query);
			//return $row['name'];
		}

		public function getEmail() {
			$stmt = mysqli_prepare($this->con, "SELECT email FROM users WHERE username = ?");
			mysqli_stmt_bind_param($stmt, "s", $this->username);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $email);
			mysqli_stmt_fetch($stmt);
			return $email;

			//$query = mysqli_query($this->con, "SELECT email FROM users WHERE username='$this->username'");
			//$row=mysqli_fetch_array($query);
			//return $row['email'];
		}

	}
?>