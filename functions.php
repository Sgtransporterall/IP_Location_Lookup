<?php
	/* Sending MySQL query and display error if there is */
	function mysqlQuery($connection, $query){
		if (!mysqli_query($connection, $query)) 			
				echo "<br />".$query." ERROR:".mysqli_errno($connection).mysqli_error($connection)."<br />";
	}
	/* Sending MySQL query, display the error if there is and return the query's output */
	function mysqlQuery_Result($connection, $query){
		if (!$result = mysqli_query($connection, $query)){			
				echo "<br />".$query." ERROR:".mysqli_errno($connection).mysqli_error($connection)."<br />";
		}else{
			return $result;
		}	
	}
	
	/* Check the database version by date */
	function check_update($dbname, $connection){
		
		date_default_timezone_set("Europe/Stockholm");
		
		/* Fetch the created time of the GeoIP table IP */
		$query = "SHOW TABLE STATUS FROM ".$dbname." WHERE NAME='IP'  ";	
		$result = mysqlQuery_Result($connection, $query);
		
		/* Check if the GeoIP table IP exists*/
		$if_exist = mysqli_num_rows($result);
		if( $if_exist == 0){
			
			/* If the GeoIP table does not exist, we are going to update the database*/
			updateDatabase($if_exist,$connection);
		
		/* If the GeoIP table exist, we are going to check the created time*/
		}else{
	
			$row = mysqli_fetch_assoc($result);	
			
			/* Get the created time with format ####-##-##, for example 2015-07-25 */
			$create_time = $row['Create_time'];
			
			/* Get the created time with format of Unix timestamp, for example 1437733146 */
			
			/* Get the CREATED month of the GeoIP table with numeric format, for example 7 means July */
			$latest_updated_date = date('n',strtotime( $row['Create_time']));
			
			/* Get the CURRENT month with numeric format */
			$date_month = date("n");
			
			/* Compare the current month and the created month of the table. If the current month larger than the created month, it means that the GeoIP table was created in the last month.*/
			if($date_month > $latest_updated_date){			
				
				$date_year = date("Y");	
				$date_month = date("F");
				
				/* As the table was created in the last month, we are going to check if the first Tuesday of the month is past, in case of updating database at the beginning of the month but the first Tuesday is still not past yet. By comparing the Unix timestamp between current time and the time of the first Tuesday. */
				if(time() > strtotime($date_month.$date_year."Tuesday")){
					
					/* Update the GeiIP table*/
					updateDatabase($if_exist, $connection);								
				}else{
					
					/* If we need not to update, display the database version */
					echo "GeoIP Database Version: ".$create_time;
				}
				
			}else{
				
				/* If we need not to update, display the database version */
				echo "GeoIP Database Version: ".$create_time;
			}
		}
			
	}
	
	/* Auto update the GeiIP database */
	function updateDatabase($if_exist, $connection){
		
		/* Download the zip file first */
		file_put_contents("GeoIP.zip", fopen("http://geolite.maxmind.com/download/geoip/database/GeoIPCountryCSV.zip", 'r'));
				/* Unzip the file */
				$zip = new ZipArchive;
				if ($zip->open('GeoIP.zip') === TRUE) {
					$zip->extractTo('./');
					$zip->close();
				} else {
					echo "The file unzip failed!";
				}
				
				/* Check if the Geoip table IP exists. Create the table if it does not exist */
				if($if_exist > 0){
				$query = "DROP TABLE IP";
				mysqlQuery($connection, $query);
				}			
				$query = "CREATE TABLE IP( ";
				$query.= "	IP_Start VARCHAR(15) NOT NULL, ";
				$query.= "	IP_End    VARCHAR(15) NOT NULL, ";
				$query.= "	Decimal_IP_Start INT UNSIGNED, ";
				$query.= "	Decimal_IP_End INT UNSIGNED NOT NULL, ";
				$query.= "	Country_Code VARCHAR(4) NOT NULL, ";
				$query.= "	Country_Name VARCHAR(20) NOT NULL, ";
				$query.= "	PRIMARY KEY (Decimal_IP_Start)) ";
				mysqlQuery($connection, $query); 			
	
				/* Load the downloaded file to the table */
				$query = "LOAD DATA LOCAL INFILE './GeoIPCountryWhois.csv ' ";
				$query.= "INTO TABLE IP  ";
				$query.= "FIELDS TERMINATED BY ',' ";
				$query.= "ENCLOSED BY '\"' ";
				$query.= "LINES TERMINATED BY '\n';";
				mysqlQuery($connection, $query);
					
	}	
	
	/* This is the function of checking if the IP lacation is same as the delivery country, which is the core function of this project.  */	
	function check($connection){
		
		$count_items = 0; // Will be used to count the items whose the two countries do not match
		
		/* Firstly, spliting the target IP address to four parts. Example: 85.45.1.122 -> part1='85', part2='45', part3='1' and part4='122' */
		$query = "SELECT SUBSTRING_INDEX(IP,'.',1), ";
		$query.= "SUBSTRING_INDEX(SUBSTRING_INDEX(IP,'.',2),'.',-1), ";
		$query.= "SUBSTRING_INDEX(SUBSTRING_INDEX(IP,'.',-2),'.',1), ";
		$query.= "SUBSTRING_INDEX(IP,'.',-1), ";
		$query.= "Country_Name, Purchase_ID "; 
		$query.= "FROM Purchase_List";
		$result = mysqlQuery_Result($connection, $query);
			
		while($row = mysqli_fetch_row($result)){
			
			$part1 = $row[0]; //part1 of the target IP address
			$part2 = $row[1];
			$part3 = $row[2];
			$part4 = $row[3];
			$country_name = $row[4]; // the delivery country of the purchase
			$Purchase_ID = $row[5];
			
			/* Core algorithm: convert the IP address to decimal, then compare */
			$Decimal_ip = $part1*256*256*256 + $part2*256*256 + $part3*256 + $part4;
			
			/* Find the actual country which the target IP belongs to.*/
			$query = "SELECT Country_Name FROM IP ";
			$query.= "WHERE Decimal_IP_Start <=".$Decimal_ip." ";
			$query.= "ORDER BY Decimal_IP_Start DESC ";
			$query.= "LIMIT 1 ";
			
			
			if (!$checking_result = mysqli_query($connection, $query)){		
				echo "<br />1 ".mysqli_errno($connection).":".mysqli_error($connection)."<br />";
			}else{
				
				$row = mysqli_fetch_row($checking_result);
				$actual_country_name = $row[0];
				
				/* Check if the actual country is same as the delivery country. If not, display the item on the webpage*/
				
				if($country_name != $actual_country_name){
					$count_items++;  //Count the items whose the two countries do not match
					echo "Purchase ID: ".$Purchase_ID."    | Delivering Country: ".$country_name."     | Country of the IP address: ".$actual_country_name." (".$part1.".".$part2.".".$part3.".".$part4.")";
					echo "<br />";					
				}
			}		
		}
		mysqli_free_result($result);
		
		echo "<br>Total items:".$count_items."<br><br>";
		
		
	}
	
	/* the function generate purchase list by randomly selecting items from table IP*/
	function generate_list($count, $percentage, $connection){
		
		/* Select items from table IP randomly and insert into the Purchase_List */
		$query = "INSERT INTO Purchase_List(IP, Country_Name) ";
		$query.= "SELECT IP_Start, Country_Name ";
		$query.= "FROM IP ORDER BY RAND() LIMIT ".$count." ";
		mysqlQuery($connection, $query);
		
		/* Calculate how many rows are going to be set to NOT match */
		$count_update = floor($count * (1 - $percentage));

		/* Set some of the purchases county names to be wrong, here I set the country name = "United States". Then the items have been changed will be the not-matched items */
		$query = "UPDATE Purchase_List ";
		$query.= "SET Country_Name='United States' ";
		$query.= " LIMIT ".$count_update." ";
		mysqlQuery($connection, $query);
	}
		
?>