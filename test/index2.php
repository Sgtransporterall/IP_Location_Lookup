<?php
		// 200 queries cost 1.4min
		$dbhost = "localhost";
		$dbuser = "root";
		$dbpass = "";
		$dbname = "test";
		$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
		
		//Test if connection occurred
		if(mysqli_connect_errno()){
			die("Database connenction failed:".
				mysqli_connect_error().
				"(".mysqli_connect_errno().")"
				);
		}
		
		/*
			SELECT SUBSTRING_INDEX(ip_address1,'.',1) as p1, SUBSTRING_INDEX(SUBSTRING_INDEX(ip_address1,'.',2),'.',-1) as p2, SUBSTRING_INDEX(SUBSTRING_INDEX(ip_address1,'.',-2),'.',1) as p3, SUBSTRING_INDEX(ip_address1,'.',-1) as p4, country_name FROM ip limit 5;
		*/
		
		
		$query_List = "SELECT SUBSTRING_INDEX(ip_address1,'.',1) as part1, ";
		$query_List.= "SUBSTRING_INDEX(SUBSTRING_INDEX(ip_address1,'.',2),'.',-1) as part2, ";
		$query_List .= "SUBSTRING_INDEX(SUBSTRING_INDEX(ip_address1,'.',-2),'.',1) as part3, ";
		$query_List.= "SUBSTRING_INDEX(ip_address1,'.',-1) as part4, ";  
		$query_List.= "country_name FROM ip ORDER BY RAND() LIMIT 200";
		$result_List = mysqli_query($connection, $query_List);
		
		if(!$result_List)
			die("Database query failed");
		
		if (mysqli_num_rows($result_List) > 0) {
			
			while($row_List = mysqli_fetch_assoc($result_List)){

				$part1 = $row_List['part1'];
				$part2 = $row_List['part2'];
				$part3 = $row_List['part3'];
				$part4 = $row_List['part4'];

				$country_List = $row_List['country_name'];
				
				$query = "SELECT * from ipp ";
				$query.= "WHERE part1=".$part1." and part2=".$part2." ";	
				$result = mysqli_query($connection, $query);
				
				if(!$result)
					die("Database query failed");
				
				$count = mysqli_num_rows($result);	
				
				if($count > 0){
					$query = "SELECT * from ipp ";
					$query.= "WHERE part1=".$part1." and part2=".$part2." and part3=".$part3." ";
					$result = mysqli_query($connection, $query);
					$count = mysqli_num_rows($result);
					
					if( $count == 1){
						$row = mysqli_fetch_assoc($result);
						echo $row['country'];
						
					}else if($count == 0){
						$query = "SELECT * from ipp ";
						$query.= "WHERE part1=".$part1." and part2=".$part2." and part3<".$part3." ";
						$query.= "ORDER BY part3 DESC, part4 DESC LIMIT 1;";
						$result = mysqli_query($connection, $query); 
						$row = mysqli_fetch_assoc($result);
						echo $row['country'] ;
						
						
					}else if($count > 0){
						$query = "SELECT * from ipp ";
						$query.= "WHERE part1=".$part1." and part2=".$part2." and part3=".$part3." and part4=".$part4." ";
						$result = mysqli_query($connection, $query);
						$row = mysqli_fetch_assoc($result);
						echo $row['country'];
					}
				}else if( $count == 1){
					$row = mysqli_fetch_assoc($result);
					echo $row['country'];
					
				}else if($count == 0){
					$query = "SELECT * from ipp ";
					$query.= "WHERE part1=".$part1." and part2<".$part2." ";
					$query.= "ORDER BY part2 DESC, part3 DESC LIMIT 1";
					$result = mysqli_query($connection, $query); 
					$row = mysqli_fetch_assoc($result);
					echo $row['country'];
					
				}
				
			}
		}else{
			echo "The list is empty";
		}
		mysqli_close($connection);



		
		
?>