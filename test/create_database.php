<?php
	file_put_contents("GeoIP.zip", fopen("http://geolite.maxmind.com/download/geoip/database/GeoIPCountryCSV.zip", 'r'));


	$zip = new ZipArchive;
	if ($zip->open('GeoIP.zip') === TRUE) {
		$zip->extractTo('./');
		$zip->close();
	} else {
		echo "The file unpack failed!";
	}

	$link = mysqli_connect("localhost", "root", "", "test");
	
	//Test if connection occurred
	if(mysqli_connect_errno()){
		die("Database connection failed:".
			mysqli_connect_error().
			"(".mysqli_connect_errno().")"
			);
		}
	/*	
	$query = "DROP table ip ";
	mysqli_query($link, $query);
	*/
	
	$query = "create table IP( ";
	$query.= "	IP_Start varchar(15) NOT NULL, ";
	$query.= "	IP_End   varchar(15) NOT NULL, ";
	$query.= "	Decimal_IP_Start INT UNSIGNED, ";
	$query.= "	Decimal_IP_End INT UNSIGNED NOT NULL, ";
	$query.= "	Country_Code varchar(4) NOT NULL, ";
	$query.= "	Country_Name varchar(20) NOT NULL, ";
	$query.= "	PRIMARY KEY (Decimal_IP_Start)) ";
	if (!mysqli_query($link, $query)) 			
			echo "<br />1 ".mysqli_errno($link).":".mysqli_error($link)."<br />";	
	

	$query = "LOAD DATA LOCAL INFILE './GeoIPCountryWhois.csv ' ";
	$query.= "INTO TABLE ip  ";
	$query.= "FIELDS TERMINATED BY ',' ";
	$query.= "ENCLOSED BY '\"' ";
	$query.= "LINES TERMINATED BY '\n';";
	if (!mysqli_query($link, $query)) 			
			echo "<br />2 ".mysqli_errno($link).":".mysqli_error($link)."<br />";	
	
		


?>