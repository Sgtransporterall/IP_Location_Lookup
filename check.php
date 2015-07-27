<?php
	include('functions.php');
	include('connect.php');
?>

<html lang="en">
<head>
  <title>IP Location Lookup</title>
<meta charset="utf-8">

<link rel="stylesheet" href="css/style.css" type="text/css">
<script type="text/javascript" src="js/javascript.js" ></script>

</head>
<body id="page1">
<div class="main">
	<header>
		<div class="wrapper">
			<span id="slogan">IP Location Lookup</span>
		</div>

	</header>
	<div id="goback">
		<button onclick="goBack()">Back to Home Page</button>
	</div>
	<div id="result">
	<h3>Following items' IP do not match :</h3><br>
	
	<?php
	/* Display the checking result which */
	$count_items = 0; // Count the items in the result
	
	/* Check if the two countries are same */
	check($connection);
	
	mysqli_close($connection);
	?>	
	
</div>
		
	<footer>
		<div class="wrapper">
			<div class="links">
			Copyright &copy; 2015.  All rights reserved.
			</div>
		</div>
	</footer>
		
</body>
</html>
