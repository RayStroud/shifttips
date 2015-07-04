<?php
	//testing information
	$mysql_host = 'localhost';
	$mysql_database = 'shifttips';
	$mysql_user = 'root';
	$mysql_password = '';		//LORAX, Vader
	$mysql_password = 'root';	//Zeppelin

	//connect to db
	//* DEBUG */ echo '<p>Attempting to connect...</p>';
	$db = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database);

	//check if connected
	if($db->connect_errno > 0)
	{
		die('Unable to connect to database');
	}
	//* DEBUG */ echo '<p>Connected.</p>';
?>