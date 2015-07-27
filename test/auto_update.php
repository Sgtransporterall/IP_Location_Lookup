<?php
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
	
		
	$query = "show table status from ".$dbname." where name='ip'  ";
	
	if (!$result = mysqli_query($link, $query))		
			echo "<br />1 ".mysqli_errno($link).":".mysqli_error($link)."<br />";
	
	$row = mysqli_fetch_assoc($result);	
	
	$create_time = $row['Create_time'];
	
	$latest_updated_date = date('F',strtotime( $row['Create_time']));
	
	$date_month = date("n");
	
	if($date_month > $latest_updated_date){			
		
		$date_year = date("Y");	
		$date_month = date("F");
		
		if(time() > strtotime($date_month.$date_year."Tuesday")){
			echo "update!";
		}else{
			echo "Database Version: ".$create_time;
		}
	}else{
		echo "Database Version: ".$create_time;
	}
	
?>