<?php
	mysqli_report(MYSQLI_REPORT_STRICT);
	$dbhost = 'localhost';
	$dbname = 'shifttips';
	$dbuser = 'root';
	$dbpass = '';			//WAMP
	//$dbpass = 'root'; 	//MAMP
	try 
	{
		$db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	} 
	catch (Exception $e) 
	{
		http_response_code(500);
		die();
	}
?>