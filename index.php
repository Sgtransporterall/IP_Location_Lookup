<?php
	include('functions.php');
	include('connect.php'); //Include database connection
	
	/* Create the table Purchase_List if it does not exist.*/
	$query = " SHOW TABLES LIKE 'Purchase_List' ";
	$result = mysqlQuery_Result($connection, $query);
		if( mysqli_num_rows($result) == 0){	
			$query = "CREATE TABLE Purchase_List ( ";
			$query.= "	Purchase_ID INT UNSIGNED AUTO_INCREMENT, ";
			$query.= "	IP VARCHAR(15) , ";
			$query.= "	Country_Name VARCHAR(20), ";
			$query.= "	PRIMARY KEY(Purchase_ID)) ";
			mysqlQuery($connection, $query);
		}
	
	/* auto update GeoIP database, if it is not latest one. */
	check_update($dbname, $connection);
?>

<html lang="en">
<head>
  <title>IP Location Lookup</title>
<meta charset="utf-8">

<link rel="stylesheet" href="css/style.css" type="text/css" media="all">
<script type="text/javascript" src="js/javascript.js" ></script>

</head>
<body id="page1">
<div class="main">
	<header>
		<div class="wrapper">
			<span id="slogan">IP Location Lookup</span>
		</div>

	</header>

<div id="generate">
<form id="form_generate" action="index.php" onsubmit="return Validate_generate();" method="post" >
			<h4>Generate a purchase list randomly</h4>
			The number of rows:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input  name="count" type="value"  placeholder="1~10000, eg:1000" required><br><br>
			The percentage of match:
			<input  name="percentage" type="value"  placeholder="0~1, eg:0.95" required><br><br>
			<input class="button" type="submit" id="check" value="Generate">
	</form>
</div>

<div id="create">
<form id="form_create" action="index.php" onsubmit="return Validate_create();" method="post" >
			<h4>Create a purchase</h4>
			Your IP address:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input  name="ip" type="text"  placeholder=" eg:85.120.3.10" required><br><br>
			Your delivery country :
			<select  name="delivery_country" >
				<option value="Sweden">Sweden</option>
				<?php 
					/* Select the country names form table IP as the select options */
					$query = "SELECT DISTINCT Country_Name ";	
					$query.= "FROM IP ";
					$query.= "ORDER BY Country_Name ";
					 $result = mysqlQuery_Result($connection, $query);
					 
					  while($row= mysqli_fetch_assoc($result)){
						  echo "<option value=\"{$row['Country_Name']}\">{$row['Country_Name']}</option>";
					  }
		      ?>		          
		      </select><br><br>
			  
			<input  type="submit" id="check" value="Create">
	</form>
</div>


<form id="form_check" action="check.php"  method="post" >
	<input type="submit" name="clear" value="Check IP Address"  />
</form>
<form id="form_clear" action="index.php" onsubmit="return clearTable();" method="post" >
	<input type="submit" name="clear" value="Clear Purchase List"  />
</form>


<div id="database">
 <h4>Current purchase list:</h4>
	<?php 
		/* update table Purchase_List according to the user input */
		
		/* Generate a purchase list randomly */
	    if(isset($_POST['count'])&& isset($_POST['percentage'])){
			
			$count = $_POST['count']; //The number of rows
			$percentage = $_POST['percentage']; //The percentage of match:
			generate_list($count, $percentage, $connection);
			
		/* Create a purchase by user */
		}else if(isset($_POST['ip'])&& isset($_POST['delivery_country'])){
			$ip = $_POST['ip']; //Your IP address
			$delivery_country = $_POST['delivery_country']; //Your delivery country 
			
			/* Insert the pruchase to the table Purchase_List */
			$query = "INSERT INTO Purchase_List(IP, Country_Name) ";
			$query.= "VALUES ('".$ip."', '".$delivery_country."') " ;
			mysqlQuery($connection, $query);		

		/* If the user want to empty the table Purchase_List */
		}else if(isset($_POST['clear'])){

			$query = "TRUNCATE TABLE Purchase_List";
			mysqlQuery($connection, $query);
		}
		
		/* Display the current Purchase_List on the page  */	   
		$query = "SELECT * FROM Purchase_List ";
		$query.= "ORDER BY Purchase_ID DESC ";
		$result = mysqlQuery_Result($connection, $query);
		$count = mysqli_num_rows($result);
		
		/* Check if the table is empty */
		if( $count == 0){
			echo "Current purchase list is empty";
		}else{
			while($count){
				$row= mysqli_fetch_assoc($result);
				 echo "Purchase ID: ".$row['Purchase_ID']."   | IP Address: ".$row['IP']."   | Delivery Country: ".$row['Country_Name']."<br >";
				  $count--;
			}
		 }
		mysqli_free_result($result);
	    mysqli_close($connection);
	?>
	
</div><br>
	
	<footer>
		<div class="wrapper">
			<div class="links">
			Copyright &copy; 2015.  All rights reserved.
			</div>
		</div>
	</footer>
		
</body>
</html>
