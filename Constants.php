<?php

	/*$DB_HOST='localhost';
	$DB_USER='root';
	$DB_PASSWORD='';
	$DB_NAME='rentaspace';*/
	$DB_HOST='sorlag';
	$DB_USER='rentayib_ssv78';
	$DB_PASSWORD='2vaxkaWao=(h';
	$DB_NAME='rentayib_clients';
	
	$NO_REPLY_EMAIL_ADDRESS = "From:noreply@rent-a-space.co.za";
	$OUR_WEB_ADDRESS = "https://www.rent-a-space.co.za";
	
	function test_input($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	
	
?>