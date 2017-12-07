<?php
include("../../config.php");

if(isset($_POST['songId'])) {
	$songId = $_POST['songId'];

	$stmt = mysqli_prepare($con, "SELECT * FROM songs WHERE id=?");
	mysqli_stmt_bind_param($stmt, "s", $songId);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]);
	while (mysqli_stmt_fetch($stmt)) {
    }
	$results = array('id', 'title', 'artist', 'album', 'genre', 'duration', 'path', 'albumorder');
	$array = array();
	$array = array_combine($results, $row);

	echo json_encode($array);

	//$query = mysqli_query($con, "SELECT * FROM songs WHERE id='$songId'");
	//$resultArray = mysqli_fetch_array($query);
	//echo json_encode($resultArray);
}

?>