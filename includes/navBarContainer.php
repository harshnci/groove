<div id="navBarContainer">

	<nav class="navBar">
					
		<span  class="logo" onclick="openPage('index.php')">
			<img src="assets/images/icons/logo.png">
		</span> 

		<div class="group">

			<div class="navItem">
				<a href="search.php" class="navItemLink">Search
					<img src="assets/images/icons/search.png" class="icon" alt="search">
				</a>
			</div>
						
		</div>

		<div class="group">
			<div class="navItem">
				<span onclick="openPage('browse.php')" class="navItemLink">Browse</span>
			</div>


			<div class="navItem">
				<span onclick="openPage('settings.php')" class="navItemLink"><?php echo htmlspecialchars($userLoggedIn->getFirstAndLastName()); ?></span>
			</div>
						
		</div>
	</nav>
				
</div>