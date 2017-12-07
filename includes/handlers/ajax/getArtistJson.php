<?php
include("../../config.php");

if(isset($_POST['artistId'])) {
	$artistId = $_POST['artistId'];

	$stmt = mysqli_prepare($con, "SELECT * FROM artists WHERE id = ?");
	mysqli_stmt_bind_param($stmt, "s", $artistId);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $row[0], $row[1]);
	while (mysqli_stmt_fetch($stmt)) {
    }
	$results = array('id', 'name');
	$array = array();
	$array = array_combine($results, $row);

	echo json_encode($array);

	//$query = mysqli_query($con, "SELECT * FROM artists WHERE id='$artistId'");
	//$resultArray = mysqli_fetch_array($query);
	//echo json_encode($resultArray);
}

?>