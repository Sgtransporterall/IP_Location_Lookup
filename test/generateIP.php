<?php
	$count = 19880;
	$link = mysqli_connect("localhost", "root", "", "test");

	//Test if connection occurred
	if(mysqli_connect_errno())
		die("Database connection failed:".mysqli_connect_error()."(".mysqli_connect_errno().")");

	$query = "CREATE TABLE purchase_list ( ";
	$query.= "Purchase_ID INT UNSIGNED AUTO_INCREMENT, ";
	$query.= "IP varchar(15) , ";
	$query.= "Country_Name varchar(20), ";
	$query.= "PRIMARY KEY(Purchase_ID)) ";
	if (!mysqli_query($link, $query)) 			
		echo "<br />".mysqli_errno($link) . ": ".mysqli_error($link);

	
	$query = "Insert into purchase_list(ip, Country_Name) ";
	$query.= "SELECT IP_Start, Country_Name ";
	$query.= "FROM IP ORDER BY RAND() LIMIT ".$count." ";
	if (!mysqli_query($link, $query)) 			
		echo "<br />".mysqli_errno($link) . ": ".mysqli_error($link);
	
	$count_update = floor($count/20);

	$query = "UPDATE purchase_list ";
	$query.= "SET Country_Name='United States' ";
	$query.= " LIMIT ".$count_update." ";
	if (!mysqli_query($link, $query)) 			
		echo "<br />".mysqli_errno($link) . ": ".mysqli_error($link);
	

?>