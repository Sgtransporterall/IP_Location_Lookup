<?php

	set_time_limit(180);
	
	$link = mysqli_connect("localhost", "root", "", "test");
	
	//Test if connection occurred
	if(mysqli_connect_errno()){
		die("Database connenction failed:".
			mysqli_connect_error().
			"(".mysqli_connect_errno().")"
			);
		}
		
	$query = "create table IP( ";
	$query.= "IP_Start varchar(15), ";
	$query.= "IP_End   varchar(15), ";
	$query.= "Location_ID1 tinytext, ";
	$query.= "Location_ID2 tinytext, ";
	$query.= "Country_Code varchar(4) NOT NULL, ";
	$query.= "Country_Name varchar(20) NOT NULL, ";
	$query.= "PRIMARY KEY (ip_start)) ";
	if (!mysqli_query($link, $query)) 			
			echo "<br />1 ".mysqli_errno($link).":".mysqli_error($link)."<br />";	
	

	$query = "LOAD DATA LOCAL INFILE 'C:/Users/Zijian/Desktop/GeoIPCountryCSV/GeoIPCountryWhois.csv' ";
	$query.= "INTO TABLE ip  ";
	$query.= "FIELDS TERMINATED BY ',' ";
	$query.= "ENCLOSED BY '\"' ";
	$query.= "LINES TERMINATED BY '\n';";
	if (!mysqli_query($link, $query)) 			
			echo "<br />2 ".mysqli_errno($link).":".mysqli_error($link)."<br />";	
	
	
	$query = "CREATE TABLE Country (";
	$query.= "	Code VARCHAR(4), ";
	$query.= "	Name VARCHAR(20), ";
	$query.= "	PRIMARY KEY(Code)) ";
	if (!mysqli_query($link, $query)) 			
			echo "<br />2 ".mysqli_errno($link) . ": " .mysqli_error($link)."<br />";
	
	$query = "INSERT INTO Country ";
	$query.= "SELECT DISTINCT Country_Code, Country_Name ";
	$query.= "FROM ip ";
	if (!mysqli_query($link, $query)) 			
			echo "<br />2 ".mysqli_errno($link) . ": " .mysqli_error($link)."<br />";
	
	$query = "ALTER TABLE ip "; 
	$query.= "ADD FOREIGN KEY (Country_Code) REFERENCES Country(Code) ";
	if (!mysqli_query($link, $query)) 			
			echo "<br />2 ".mysqli_errno($link) . ": " .mysqli_error($link)."<br />";
		
	$query = "SELECT Code FROM Country ";
	$result = mysqli_query($link, $query);
	$count = mysqli_num_rows($result);
	while($count){
		$row = mysqli_fetch_assoc($result);
		$code = $row['Code'];
		$count--;
		
		$query = "CREATE TABLE `".$code."` ( ";
		$query.= "part1 tinyint unsigned , ";
		$query.= "part2 tinyint unsigned , ";
		$query.= "part3 tinyint unsigned , ";
		$query.= "part4 tinyint unsigned , ";
		$query.= "PRIMARY KEY(part1, part2, part3, part4)) ";
		if (!mysqli_query($link, $query)) 			
			echo "<br />3 ".mysqli_errno($link).":".mysqli_error($link)."<br />";
				
		$query = "INSERT INTO `".$code."` (part1, part2, part3, part4) ";
		$query.= "SELECT SUBSTRING_INDEX(IP_Start,'.',1),  ";
		$query.= "SUBSTRING_INDEX(SUBSTRING_INDEX(IP_Start,'.',2),'.',-1), ";
		$query.= "SUBSTRING_INDEX(SUBSTRING_INDEX(IP_Start,'.',-2),'.',1), ";
		$query.= "SUBSTRING_INDEX(IP_Start,'.',-1) ";
		$query.= "FROM ip ";
		$query.= "WHERE country_code='".$code."' ";
		if (!mysqli_query($link, $query)) 			
			echo "<br />4 ".mysqli_errno($link) . ": ".mysqli_error($link);
		
	}	
	
	mysql_close($link);

?>