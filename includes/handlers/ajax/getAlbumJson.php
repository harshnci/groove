<?php
include("../../config.php");

if(isset($_POST['albumId'])) {
	$albumId = $_POST['albumId'];

	$stmt = mysqli_prepare($con, "SELECT * FROM albums WHERE id = ?");
	mysqli_stmt_bind_param($stmt, "s", $albumId);
	mysqli_stmt_execute($stmt);
	//mysqli_stmt_store_result($stmt);
	mysqli_stmt_bind_result($stmt, $row[0], $row[1], $row[2], $row[3], $row[4]);
	while (mysqli_stmt_fetch($stmt)) {
    }
	$results = array('id', 'title', 'artist', 'genre', 'artworkPath');
	$array = array();
	$array = array_combine($results, $row);

	echo json_encode($array);

}

?>

					