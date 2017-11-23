var currentPlaylist = [];
var tempPlaylist = [];
var audioElement;
var currentIndex = 0;
var repeat = false;

function formatTime(seconds) {
	var time = Math.round(seconds);
	var minutes = Math.floor(time / 60);
	var seconds = time - minutes * 60;

	var extraZero;
	if(seconds < 10) {
		extraZero = "0";
		return minutes + ":" + extraZero + seconds;
	}
	else {
		return minutes + ":" + seconds;
	}
	
}

function updateTimeProgressBar(audio) {
	$(".progressTime.current").text(formatTime(audio.currentTime));

	var progress = audio.currentTime / audio.duration * 100;
	$(".playbackBar .progress").css("width", progress + "%");
}

function Audio() {

	this.currentlyPlaying;
	this.audio = document.createElement('audio');

	this.audio.addEventListener("ended", function() {
		nextSong();
	});

	this.audio.addEventListener("canplay", function() {

		var duration = formatTime(this.duration);
		$(".progressTime.remaining").text(duration);
	});

	this.audio.addEventListener("timeupdate", function() {
		if(this.duration) {
			updateTimeProgressBar(this);
		}
	});

	this.setTrack = function(src) {
		this.audio.src = src;
	}

	this.play = function() {
		this.audio.play();
	}

	this.pause = function() {
		this.audio.pause();
	}

	this.setTime = function() {
		this.audio.currentTime = 0;
	}
}