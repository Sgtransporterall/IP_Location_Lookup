<?php
	include('functions.php');
	include('connect.php');
?>
<?php

	$link = mysqli_connect("localhost", "root", "", "test");

	//Test if link occurred
	if(mysqli_connect_errno()){
		die("Database1 link failed:".
			mysqli_connect_error().
			"(".mysqli_connect_errno().")"
			);
	}

		
$query = "SELECT DISTINCT Country_Name FROM ip";	
					 $result = mysqlQuery_Result($connection, $query);
					 $count = mysqli_num_rows($result);
					  while($count){
						$row= mysqli_fetch_assoc($date_set);
						echo $row['Country_Name'];
						 // echo "<option value=\"{$row['Country_Name']}\">{$row['Country_Name']}</option>";
						  $count--;
					  }

?>