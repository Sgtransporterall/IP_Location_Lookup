
/* Check validation of the generate form */
function Validate_generate(){
	
	/* Get the input of the number of rows in the form */
	var count = document.getElementById("form_generate").count.value;
	
	/* Check if the input is an integer, if not, inform the user and return false */
	if( isNaN(count) || count % 1){
		alert("The number of rows has to be an integer.");
		return false;
	}
	
	/* Check if the input is in the range, if not, inform the user and return false */
	if( count > 10000 || count < 1){
		 alert("The number of rows is out of the range.");
		return false;
	}
	
	/* Get the input of the percentage in the form */
	var percentage = document.getElementById("form_generate").percentage.value;
	
	/* Check if the input is a number, if not, inform the user and return false*/
	if(isNaN(percentage)){
		alert("The percentage has to be a number.");
		return false;
	}
	
	/* Check if the input is in the range, if not, inform the user and return false */
	if( percentage < 0 || percentage > 1){
		 alert("The percentage is out of the range.");
		return false;
	}
}

/* Check validation of the create a purchase form */
function Validate_create(){
	
	/* Get the input of the IP address*/
	var ip = document.getElementById("form_create").ip.value;
	
	/* Return true if the IP is valid, otherwise return false  */
	return ValidateIPaddress(ip);
}

/* Check validation of the IP address */
function ValidateIPaddress(ip){  

	/* the format which the IP should has */
	var ip_format = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/; 
	
	/* return true if the format is matched, otherwise inform the user and return false */
	if(ip.match(ip_format)){
		return true;
	}else{
		alert("You have entered an invalid IP address.");
	return false;
	}  
}

/* Display a popup box to the user to confirm the operation  */
function clearTable(){	
	if (confirm("Do you want to clear the Purchase List?") == true) {
		return true;
	} else {
		return false;
	}	
}

/* The function used to go back the home page from the result page*/
function goBack(){
	window.history.back();
}
 