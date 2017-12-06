<?php

$songQuery = mysqli_query($con, "SELECT id FROM songs ORDER BY RAND() LIMIT 10");

$resultArray = array();

while($row = mysqli_fetch_array($songQuery)) {
	array_push($resultArray, $row['id']);
}

$jsonArray = json_encode($resultArray);

?>

<script>
	
	$(document).ready(function() {
		currentPlaylist = <?php echo $jsonArray; ?>;
		audioElement = new Audio();
		setTrack(currentPlaylist[0], currentPlaylist, false);

	});

	function prevSong() {
		if(audioElement.audio.currentTime >= 3 || currentIndex == 0) {
			audioElement.audio.currentTime = 0;
		}
		else {
			currentIndex = currentIndex - 1;
			setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
		}
	}

	function nextSong() {
		if(repeat == true) {
			audioElement.audio.currentTime = 0;
			return;
		}
		if(currentIndex == currentPlaylist.length - 1) {
			currentIndex = 0;
		}
		else {
			currentIndex = currentIndex + 1;
		}
		var trackToPlay = currentPlaylist[currentIndex];
		setTrack(trackToPlay, currentPlaylist, true);
		console.log(currentIndex);
	}

	function setRepeat() {
		repeat = !repeat;
		var imageName = repeat ? "repeat-active.png" : "repeat.png";
		$(".controlButton.repeat img").attr("src", "assets/images/icons/" + imageName);
	}

	function setTrack(trackId, newPlaylist, play) {

		if(currentPlaylist != newPlaylist) {
			currentPlaylist = newPlaylist;
		}
		currentIndex = currentPlaylist.indexOf(trackId);
		console.log(currentIndex);
		pauseSong();

		$.post("includes/handlers/ajax/getSongJson.php", { songId: trackId}, function(data){

			var track = JSON.parse(data);
			currentIndex = currentPlaylist.indexOf(trackId);
			$(".trackName span").text(track.title);

			$.post("includes/handlers/ajax/getArtistJson.php", { artistId: track.artist}, function(data){
				var artist = JSON.parse(data);
				$(".artistName span").text(artist.name);
			});

			$.post("includes/handlers/ajax/getAlbumJson.php", { albumId: track.album}, function(data){
				var album = JSON.parse(data);
				//console.log(album);
				$(".albumLink img").attr("src", album.artworkPath);
			});

			audioElement.setTrack(track.path);
			if(play == true) {
				playSong();	
			}
		});
	}

	function playSong() {
		$(".controlButton.play").hide();
		$(".controlButton.pause").show();
		audioElement.play();
	}

	function pauseSong() {
		$(".controlButton.play").show();
		$(".controlButton.pause").hide();
		audioElement.pause();
	}


</script>

<div id="nowPlayingBarContainer"> 
	<div id="nowPlayingBar">

		<div id="nowPlayingLeft">
			<div class="content">

				<span class="albumLink">
					<img src="" class="albumArtwork">
				</span>
				<div class="trackInfo">

					<span class="trackName">

						<span></span>
						
					</span>
					<span class="artistName">

						<span></span>
						
					</span>
					
				</div>
				
			</div>
		</div>

		<div id="nowPlayingCenter">
			
			<div class="content playerControls">
				<div class="buttons">

					<button class="controlButton shuffle" title="Shuffle button">
						<img src="assets/images/icons/shuffle.png" alt="Shuffle">
					</button>

					<button class="controlButton previous" title="Previous button" onclick="prevSong()">
						<img src="assets/images/icons/previous.png" alt="Previous">
					</button>

					<button class="controlButton play" title="Play button" onclick="playSong()">
						<img src="assets/images/icons/play.png" alt="Play">
					</button>

					<button class="controlButton pause" title="Pause button" style="display: none;" onclick="pauseSong()">
						<img src="assets/images/icons/pause.png" alt="pause">
					</button>

					<button class="controlButton next" title="Next button" onclick="nextSong()">
						<img src="assets/images/icons/next.png" alt="Next">
					</button>

					<button class="controlButton repeat" title="Repeat button" onclick="setRepeat()">
						<img src="assets/images/icons/repeat.png" alt="Repeat">
					</button>
					
				</div>
				<div class="playbackBar">

					<span class="progressTime current">0.00</span>
					
					<div class="progressBar">
						<div class="progressBarBg">
							<div class="progress"></div>
						</div>
					</div>
					
					<span class="progressTime remaining">0.00</span>
					
				</div>
			</div>
		</div>

		<div id="nowPlayingRight">
			
			<div class="volumeBar">

				<button class="controlButton volume" title="Volume button">
					<img src="assets/images/icons/volume.png" alt="Volume">
				</button>
				<div class="progressBar">
					<div class="progressBarBg">
						<div class="progress"></div>
					</div>
				</div>
				
			</div>
		</div>
		
	</div>
	
</div>