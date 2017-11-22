<?php
	class Album {

		private $con;
		private $id;
		private $title;
		private $artistId;
		private $genre;
		private $artworkPath;



		public function __construct($con, $id) {
			$this->con = $con;
			$this->id = $id;


			$stmt = mysqli_prepare($this->con, "SELECT title,artist,genre,artworkPath FROM albums WHERE id=?");
			mysqli_stmt_bind_param($stmt, "s", $this->id);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $this->title, $this->artistId, $this->genre, $this->artworkPath);
			while (mysqli_stmt_fetch($stmt)) {
    		}
			//$query = mysqli_query($this->con, "SELECT * FROM albums WHERE id='$this->id'");
			//$album = mysqli_fetch_array($query);
			//$this->title = $album['title'];
			//$this->artistId = $album['artist'];
			//$this->genre = $album['genre'];
			//$this->artworkPath = $album['artworkPath'];

		}

		public function getTitle() {
			
			return $this->title; 
		}

		public function getArtist() {
			return new Artist($this->con, $this->artistId);
		}

		public function getGenre() {
			return $this->genre;
		}

		public function getArtworkPath() {
			return $this->artworkPath;
		}

		public function getNumberOfSongs() {

			$stmt = mysqli_prepare($this->con, "SELECT id FROM songs WHERE album=?");
			mysqli_stmt_bind_param($stmt, "s", $this->id);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			return mysqli_stmt_num_rows($stmt);
			//$query = mysqli_query($this->con, "SELECT id FROM songs WHERE album='$this->id'");
			//return mysqli_num_rows($query);
		}

		public function getSongIds() {

			$stmt = mysqli_prepare($this->con, "SELECT id FROM songs WHERE album=? ORDER BY albumOrder ASC");
			mysqli_stmt_bind_param($stmt, "s", $this->id);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			$row = array();
			$array = array();
			$i = 0;
			mysqli_stmt_bind_result($stmt, $row[0]);
			while (mysqli_stmt_fetch($stmt)) {
				mysqli_stmt_bind_result($stmt, $row[$i]);
				array_push($array, $row[$i]);
    		}
			return $array;	
			/*$query = mysqli_query($this->con, "SELECT id FROM songs WHERE album='$this->id' ORDER BY albumOrder ASC");
			$array = array();
			while($row = mysqli_fetch_array($query)) {
				array_push($array, $row['id']);
			}
			 print_r($array);
			 exit(0);*/
		}
	}

?>