<?php

include("includes/includedFiles.php");

if(isset($_GET['term'])) {
	$term = urldecode($_GET['term']);
}
else {
	$term = "";
}
?>

<div class="searchContainer">

	<h4>Search for a song</h4>
	<input type="text" class="searchInput" value="<?php echo htmlspecialchars($term); ?>" placeholder="start typing...">
	
</div>

<script>
	
	$(function() {

		var timer;

		$(".searchInput").keyup(function(){
			clearTimeout(timer);
			timer = setTimeout(function() {
				var val = $(".searchInput").val();
				openPage("search.php?term=" + val);
			}, 2000);
		})
	})
</script>

<div class="tracklistContainer borderBottom">
	<h2>SONGS</h2>
	<ul class="tracklist">
		
		<?php

		$term = $term . "%";		
		$stmt = mysqli_prepare($con, "SELECT id FROM songs WHERE title LIKE ? LIMIT 3" );
		mysqli_stmt_bind_param($stmt, "s", $term);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $res);
		$songIdArray = array();
		while (mysqli_stmt_fetch($stmt)) {
			array_push($songIdArray, $res);	
		}
		mysqli_stmt_close($stmt);
		$j = sizeof($songIdArray);
		$k = 1;


		for ($i=0; $i < $j; $i++) { 
			
			$albumSong = new Song($con, $songIdArray[$i]);
			$albumArtist = $albumSong->getArtist();

			echo "<li class='tracklistRow'>
					<div class='trackCount'>
						<img class='play' src='assets/images/icons/play-white.png' onclick='setTrack(\"" . $albumSong->getId() . "\", tempPlaylist, true)'>
						<span class='trackNumber'>$k</span>
					</div>


					<div class='trackInfo'>
						<span class='trackName'>" . $albumSong->getTitle() . "</span>
						<span class='artistName'>" . $albumArtist->getName() . "</span>
					</div>


					<div class='trackDuration'>
						<span class='duration'>" . $albumSong->getDuration() . "</span>
					</div>


				</li>";	
				$k = $k + 1;
		}
				
		//echo json_encode($songIdArray);
		//exit();


		//$songsQuery = mysqli_query($con, "SELECT id FROM songs WHERE title LIKE '$term%' LIMIT 10");

		//if(!mysqli_num_rows($songsQuery)) {
		//	echo "<span class='noResults'>No songs found " . $term . "</span>";
		//}

		//$songIdArray = array();

		//$i = 1;

		//while($row = mysqli_fetch_array($songsQuery)) {

		//	if($i > 5) {
		//		break;
		//	}

		//	array_push($songIdArray, $row['id']);
		/*
			$albumSong = new Song($con, $row['id']);
			$albumArtist = $albumSong->getArtist();

			echo "<li class='tracklistRow'>
					<div class='trackCount'>
						<img class='play' src='assets/images/icons/play-white.png' onclick='setTrack(\"" . $albumSong->getId() . "\", tempPlaylist, true)'>
						<span class='trackNumber'>$i</span>
					</div>


					<div class='trackInfo'>
						<span class='trackName'>" . $albumSong->getTitle() . "</span>
						<span class='artistName'>" . $albumArtist->getName() . "</span>
					</div>

					<div class='trackOptions'>
						<input type='hidden' class='songId' value='" . $albumSong->getId() . "'>
						<img class='optionsButton' src='assets/images/icons/more.png' onclick='showOptionsMenu(this)'>
					</div>

					<div class='trackDuration'>
						<span class='duration'>" . $albumSong->getDuration() . "</span>
					</div>


				</li>";

			$i = $i + 1;*/
		//}

		?>

		<script>
			var tempSongIds = '<?php echo json_encode($songIdArray); ?>';
			tempPlaylist = JSON.parse(tempSongIds);
		</script>

	</ul>
</div>

