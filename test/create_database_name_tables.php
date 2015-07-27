<?php
	
	set_time_limit(300);
	
	$link = mysqli_connect("localhost", "root", "", "test");
	
	//Test if connection occurred
	if(mysqli_connect_errno()){
		die("Database connenction failed:".
			mysqli_connect_error().
			"(".mysqli_connect_errno().")"
			);
		}
		
	$query = "create table IP( ";
	$query.= "IP_Start varchar(13), ";
	$query.= "IP_End   varchar(13), ";
	$query.= "Location_ID1 tinytext, ";
	$query.= "Location_ID2 tinytext, ";
	$query.= "Country_Code varchar(3), ";
	$query.= "Country_Name varchar(20) NOT NULL, ";
	$query.= "PRIMARY KEY (Country_Code, IP_Start)) ";
	
	if (!mysqli_query($link, $query)) 			
			echo "<br />1".mysqli_errno($link) . ": " .			mysqli_error($link)."<br />";	
	

	$query = "LOAD DATA LOCAL INFILE 'C:/Users/Zijian/Desktop/GeoIPCountryCSV/GeoIPCountryWhois.csv' ";
	$query.= "INTO TABLE ip  ";
	$query.= "FIELDS TERMINATED BY ',' ";
	$query.= "ENCLOSED BY '\"' ";
	$query.= "LINES TERMINATED BY '\n';";
	if (!mysqli_query($link, $query)) 			
			echo "<br />2".mysqli_errno($link) . ": " .			mysqli_error($link)."<br />";	
	
	
	$query = "SELECT DISTINCT Country_Name, Country_Code FROM ip ";
	$query.= "WHERE Country_Name like '%\'%' ";
	if (!$result = mysqli_query($link, $query)){ 			
			echo "<br />3".mysqli_errno($link) . ": " .			mysqli_error($link)."<br />";
	}else{
		$count = mysqli_num_rows($result);
		while($count){
			$row = mysqli_fetch_assoc($result);
			$code = $row['Country_Code'];
			$changed_name = str_replace('\'', '', $row['Country_Name']);
			$count--;
			$query = "update ip ";
			$query.= "SET country_name='".$changed_name."' ";
			$query.= "WHERE Country_Code='".$code."' "; 
			if (!mysqli_query($link, $query)) 			
				echo "<br />4".mysqli_errno($link) . ": " .			mysqli_error($link)."<br />";
		}
	}
	
	
	
	$query = "create table Country( ";
	$query.= "Code varchar(3), ";
	$query.= "Table_Name tinytext NOT NULL, ";
	$query.= "PRIMARY KEY (Code), ";
	$query.= "FOREIGN KEY (Code) REFERENCES ip(Country_Code)) ";
	if (!mysqli_query($link, $query)) 		
		echo "<br />5".mysqli_errno($link).": ".mysqli_error($link)."<br />";	
	
	
	$query = "insert into Country(Code, Table_Name) ";
	$query.= "select distinct Country_Code, Country_Name ";
	$query.= "from ip ";
	if (!mysqli_query($link, $query)) 		
		echo "<br />6".mysqli_errno($link) . ": " .			mysqli_error($link)."<br />";
	
	
	
	$query = "SELECT DISTINCT Country_Name, Country_Code FROM ip ";
	$result = mysqli_query($link, $query);
	$count = mysqli_num_rows($result);
	
	while($count){
		$row = mysqli_fetch_assoc($result);
		$code = $row['Country_Code'];
		$country_name = $row['Country_Name'];
		$table_country_name = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', $country_name));
		$count--;
		$query = "UPDATE Country ";
		$query.= "SET Name='".$table_country_name."' ";
		$query.= "WHERE Code='".$code."' ";
		if (!mysqli_query($link, $query)) 			
			echo "<br />7".mysqli_errno($link).":".mysqli_error($link)."<br />";
		
		$query = "CREATE TABLE ".$table_country_name." ( ";
		$query.= "part1 tinyint unsigned NOT NULL, ";
		$query.= "part2 tinyint unsigned NOT NULL, ";
		$query.= "part3 tinyint unsigned NOT NULL, ";
		$query.= "part4 tinyint unsigned NOT NULL); ";
		if (!mysqli_query($link, $query)) 			
			echo "<br />8".mysqli_errno($link).":".mysqli_error($link)."<br />";
		
		
		$query = "INSERT INTO ".$table_country_name." (part1, part2, part3, part4) ";
		$query.= "SELECT SUBSTRING_INDEX(IP_Start,'.',1),  ";
		$query.= "SUBSTRING_INDEX(SUBSTRING_INDEX(IP_Start,'.',2),'.',-1), ";
		$query.= "SUBSTRING_INDEX(SUBSTRING_INDEX(IP_Start,'.',-2),'.',1), ";
		$query.= "SUBSTRING_INDEX(IP_Start,'.',-1) ";
		$query.= "FROM ip ";
		$query.= "WHERE country_name='".$country_name."' ";
		if (!mysqli_query($link, $query)) 			
			echo "<br />9".mysqli_errno($link) . ": ".mysqli_error($link);
		
	}	
	
?>