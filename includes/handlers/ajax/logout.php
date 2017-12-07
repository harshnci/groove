<?php
include("../../config.php");
$update = $_SESSION['userLoggedIn'];

session_destroy();

$string = "UPDATE users SET sessionvar = '0' WHERE username = '$update'";
$stmt = mysqli_query($con, $string);


?>