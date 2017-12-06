var currentPlaylist = [];
var tempPlaylist = [];
var audioElement;
var currentIndex = 0;
var repeat = false;
var userLoggedIn;
var csrfToken;

$(document).click(function(click) {
	
})


function updateEmail(emailClass, csrfclass) {
	var emailValue = $("." + emailClass).val();

	$.post("includes/handlers/ajax/updateEmail.php", { email: emailValue, username: userLoggedIn, csrf: csrfToken }).done(function(response) {
		$("." + emailClass).nextAll(".message").text(response);
	})
}

function updatePassword(oldPasswordClass, newPasswordClass1, newPasswordClass2) {
	var oldPassword = $("." + oldPasswordClass).val();
	var newPassword1 = $("." + newPasswordClass1).val();
	var newPassword2 = $("." + newPasswordClass2).val();

	$.post("includes/handlers/ajax/updatePassword.php", { oldPassword: oldPassword, newPassword1: newPassword1, newPassword2: newPassword2, username: userLoggedIn, csrf: csrfToken }).done(function(response) {
		$("." + oldPasswordClass).nextAll(".message").text(response);
	})
}

function logout() {
	$.post("includes/handlers/ajax/logout.php", function() {
		location.reload();
	});
}

function openPage(url) {

	//if(url.indexOf("?") == -1) {
		//url = url + "?";
	//}
	//var encodedUrl = encodeURI(url + "&userLoggedIn=" + userLoggedIn);
	var encodedUrl = encodeURI(url);
	$("#mainContent").load(encodedUrl);
	history.pushState(null, null, url);
}

/*function createPlaylist() {
	var popup = prompt("please enter the name of your playlist");
	if(popup != null) {
		$.post("includes/handlers/ajax/createPlaylist.php", {name: popup, username: userLoggedIn}).done(function() {
			 //do something when ajax return
			 openPage("yourMusic.php");
		});
	}
}

function deletePlaylist(playlistId) {
	var prompt = confirm("Are you sure you want to delete this playlist");
	if(prompt) {
		$.post("includes/handlers/ajax/deletePlaylist.php", { playlistId: playlistId }).done(function() {
			 //do something when ajax return
			 openPage("yourMusic.php");
		});
	}
}

function hideOptionsMenu() {
	var menu = $(".optionsMenu");
	if(menu.css("display") != "none") {
		menu.css("display", "none");
	}
}

function showOptionsMenu(button) {

	 var menu = $(".optionsMenu");
	 var menuWidth = menu.width();
	 var scrollTop = $(window).scrollTop(); ///distance from top of window to top of document
	 var elementOffset = $(button).offset().top; //Distance from the top of th document
	 var top = elementOffset - scrollTop;
	 var left = $(button).position().left;
	 menu.css({ "top": top + "px", "left": left - menuWidth +"px", "display": "inline" }); 
}*/

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