<?php
$link = mysqli_connect("localhost", "root", "", "test");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
		
		$query_List = "SELECT SUBSTRING_INDEX(ip_address1,'.',1) as part1, ";
		$query_List.= "SUBSTRING_INDEX(SUBSTRING_INDEX(ip_address1,'.',2),'.',-1) as part2, ";
		$query_List .= "SUBSTRING_INDEX(SUBSTRING_INDEX(ip_address1,'.',-2),'.',1) as part3, ";
		$query_List.= "SUBSTRING_INDEX(ip_address1,'.',-1) as part4, ";  
		$query_List.= "country_name FROM ip LIMIT 50;";
		$result_List = mysqli_query($link, $query_List);
		
		if(!$result_List)
			die("Database query failed");
		
		if (mysqli_num_rows($result_List) > 0) {
			
			while($row_List = mysqli_fetch_assoc($result_List)){
				$part1 = $row_List['part1'];
				$part2 = $row_List['part2'];
				$part3 = $row_List['part3'];
				$part4 = $row_List['part4'];
					/*	
				$part1 = "85";
				$part2 = "31";
				$part3 = "34";
				$part4 = "59";
				*/

				//0
				$query= "SELECT * from ipp ";
				$query.= "WHERE part1=".$part1." and part2=".$part2."; ";
				
				//1 count==0
				$query.= "SELECT * from ipp ";
				$query.= "WHERE part1=".$part1." and part2<".$part2." ";
				$query.= "ORDER BY part2 DESC, part3 DESC LIMIT 1;";
				
				//2 count>0  actual count = 0
				$query.= "SELECT * from ipp ";
				$query.= "WHERE part1=".$part1." and part2=".$part2." and part3=".$part3.";";
				
				//3 -> count==0    actual acount=1 
				$query.= "SELECT * from ipp ";
				$query.= "WHERE part1=".$part1." and part2=".$part2." and part3<".$part3." ";
				$query.= "ORDER BY part3 DESC, part4 DESC LIMIT 1;";
				
				//4 -> count>0
				$query.= "SELECT * from ipp ";
				$query.= "WHERE part1=".$part1." and part2=".$part3." and part3=".$part3." ";
				$query.= "ORDER BY part3 DESC, part4 DESC LIMIT 1;";
				
				$i = 0;
				/* execute multi query */
				if (mysqli_multi_query($link, $query)) {
					do {
						$result[$i]  = mysqli_store_result($link);
					/*
						if ($result[$i]  = mysqli_store_result($link)) {
							while ($row = mysqli_fetch_row($result[$i])) {
								printf("%s\n", $row[0]);
							}
						}
						if (mysqli_more_results($link)) {
							printf("-----------------\n");
						}
						*/
						$i++;
					} while (mysqli_more_results($link) && mysqli_next_result($link));
				}

				$count = mysqli_num_rows($result[0]);
				if( $count == 1){
					$row = mysqli_fetch_assoc($result[0]);
					echo $row['country'];
					
				}else if($count == 0){
					$row = mysqli_fetch_assoc($result[1]);
					echo $row['country'];
						
				}else if($count > 0){
					$count = mysqli_num_rows($result[2]);

					
					if( $count == 1){
						$row = mysqli_fetch_assoc($result[2]);
						echo $row['country'];
						
					}else if($count == 0){
						$count = mysqli_num_rows($result[3]);
						$row = mysqli_fetch_assoc($result[3]);
						echo $row['country'] ;
						
					}else if($count > 0){
						$row = mysqli_fetch_assoc($result[4]);
						echo $row['country'];
					}
				}
			}
		}


/* close connection */
mysqli_close($link);
?>