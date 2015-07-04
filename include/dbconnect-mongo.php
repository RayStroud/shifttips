<?php
	/* DEBUG */ error_reporting(E_ALL | E_STRICT);
	/* DEBUG */ ini_set("display_errors", 2);

	//create mongo connection
	try 
	{
		$mongo = new MongoClient();
	} 
	catch(MongoConnectionException $e) 
	{
		var_dump($e);
	}

	//pick database
	$db = $mongo->shifttips;
?>