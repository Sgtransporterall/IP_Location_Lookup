<?php
	$dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$dbname = "test";
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

	//Test if link occurred
	if(mysqli_connect_errno()){
		die("Database1 link failed:".
			mysqli_connect_error().
			"(".mysqli_connect_errno().")"
			);
	}
?>