<?php
	//live information
	$mysql_host = "mysql2.000webhost.com";
	$mysql_database = 'a1587239_click';
	$mysql_user = 'a1587239_ray';
	$mysql_password = 'pass19';

	//testing information
	/*DEBUG*/ $mysql_host = 'localhost';
	/*DEBUG*/ $mysql_database = 'shifttips';
	/*DEBUG*/ $mysql_user = 'root';
	/*DEBUG*/ $mysql_password = '';

	//connect to db
	//*DEBUG*/ echo '<p>Attempting to connect...</p>';
	$db = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database);

	//check if connected
	if($db->connect_errno > 0)
	{
		die('Unable to connect to database');
	}
	//*DEBUG*/ echo '<p>Connected.</p>';
?>