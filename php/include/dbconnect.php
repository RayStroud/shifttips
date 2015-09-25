<?php
	mysqli_report(MYSQLI_REPORT_STRICT);
	//testing information
	$mysql_host = 'localhost';
	$mysql_database = 'shifttips';
	$mysql_user = 'root';
	$mysql_password = '';		//WAMP
	$mysql_password = 'root';	//MAMP

	//connect to db
	//* DEBUG */ echo '<p>Attempting to connect...</p>';
	try 
	{
		$db = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database);
		//* DEBUG */ echo '<p>Connected.</p>';
	} 
	catch (Exception $e) 
	{
		http_response_code(500);
		die();
	}
?>