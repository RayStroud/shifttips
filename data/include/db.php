<?php
	//* DEBUG */ error_reporting(E_ALL);
	//* DEBUG */ ini_set('display_errors', 1);
	mysqli_report(MYSQLI_REPORT_STRICT);	//to suppress WAMP mysqli warnings
	$dbhost = 'localhost';
	$dbname = 'shifttips';
	$dbuser = 'root';
	$dbpass = '';			//WAMP
	$dbpass = 'root'; 	//MAMP
	try 
	{
		$db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		if ($db->connect_error) 
		{
			http_response_code(500);
			die('Connect Error (' . $db->connect_errno . ') '. $db->connect_error);
		}
		//* DEBUG */ else {echo 'php version: ' . phpversion() . '<br/>mysql version: ' . $db->get_server_info() . '<hr>';}
	} 
	catch (Exception $e) 
	{
		http_response_code(500);
		die();
	}
?>