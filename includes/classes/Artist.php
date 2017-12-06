<?php
	class Artist {

		private $con;
		private $id;

		public function __construct($con, $id) {
			$this->con = $con;
			$this->id = $id;
		}

		public function getName() {

			$stmt = mysqli_prepare($this->con, "SELECT name FROM artists WHERE id = ?");
			mysqli_stmt_bind_param($stmt, "s", $this->id);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $artist);
			mysqli_stmt_fetch($stmt);
			return $artist;
			//$artistQuery = mysqli_query($this->con, "SELECT name FROM artists WHERE id='$this->id'");
			//$artist = mysqli_fetch_array($artistQuery);
			//return $artist['name']; 
		}
	}
?>