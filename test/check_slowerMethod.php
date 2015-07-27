<?php
// 200 queries cost 1.4min
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "test";
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

//Test if link occurred
if(mysqli_connect_errno()){
	die("Database1 link failed:".
		mysqli_connect_error().
		"(".mysqli_connect_errno().")"
		);
}

	
$query_List = "SELECT SUBSTRING_INDEX(ip_start,'.',1) as part1, ";
$query_List.= "SUBSTRING_INDEX(SUBSTRING_INDEX(ip_start,'.',2),'.',-1) as part2, ";
$query_List .= "SUBSTRING_INDEX(SUBSTRING_INDEX(ip_start,'.',-2),'.',1) as part3, ";
$query_List.= "SUBSTRING_INDEX(ip_start,'.',-1) as part4, ";
$query_List.= "Country_Code ";
$query_List.= "FROM test_ip ";
$result_List = mysqli_query($link, $query_List);

if(!$result_List)
	die("Database2 query failed");

if (mysqli_num_rows($result_List) > 0) {
	
	while($row_List = mysqli_fetch_assoc($result_List)){

		$part1 = $row_List['part1'];
		$part2 = $row_List['part2'];
		$part3 = $row_List['part3'];
		$part4 = $row_List['part4'];

		$country_code_List = $row_List['Country_Code'];
		
		$query = "SELECT * from `".$country_code_List."` ";
		$query.= "WHERE part1=".$part1." and part2=".$part2." ";	
		$result = mysqli_query($link, $query);
		
		if(!$result)
			die("Database3 query failed");
		
		$count = mysqli_num_rows($result);	
		
		if($count > 0){
			$query = "SELECT * from `".$country_code_List."` ";
			$query.= "WHERE part1=".$part1." and part2=".$part2." and part3=".$part3." ";
			$result = mysqli_query($link, $query);
			$count = mysqli_num_rows($result);
			
			if( $count == 1){
				$row = mysqli_fetch_assoc($result);
				echo "Yes";
				
			}else if($count == 0){
				$query = "SELECT * from `".$country_code_List."` ";
				$query.= "WHERE part1=".$part1." and part2=".$part2." and part3<".$part3." ";
				$query.= "ORDER BY part3 DESC, part4 DESC LIMIT 1;";
				$result = mysqli_query($link, $query); 
				$row = mysqli_fetch_assoc($result);
				echo "Yes"; ;
				
				
			}else if($count > 0){
				$query = "SELECT * from `".$country_code_List."` ";
				$query.= "WHERE part1=".$part1." and part2=".$part2." and part3=".$part3." and part4=".$part4." ";
				$result = mysqli_query($link, $query);
				$row = mysqli_fetch_assoc($result);
				echo "Yes";;
			}else{
				echo "NO";
			}
		}else if( $count == 1){
			$row = mysqli_fetch_assoc($result);
			echo "Yes";
			
		}else if($count == 0){
			$query = "SELECT * from `".$country_code_List."` ";
			$query.= "WHERE part1=".$part1." and part2<".$part2." ";
			$query.= "ORDER BY part2 DESC, part3 DESC LIMIT 1";
			$result = mysqli_query($link, $query); 
			$row = mysqli_fetch_assoc($result);
			echo "Yes";;
			
		}else{
			echo "NO";
		}
		
	}
}else{
	echo "There is nothing to check now, please press generate ";
}
mysqli_close($link);

?>