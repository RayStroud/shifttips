<?php
	include '../include/dbconnect.php';

	$shift = json_decode(file_get_contents("php://input"));
	//* DEBUG */ var_dump($shift);
	$id = 0;
	
	//get info: numbers
	$id 		= isset($shift->id) 		&& is_numeric($shift->id)			? $shift->id 		: null;
	$wage 		= isset($shift->wage) 		&& is_numeric($shift->wage)			? $shift->wage 		: null;
	$campHours 	= isset($shift->campHours) 	&& is_numeric($shift->campHours) 	? $shift->campHours : null;
	$sales 		= isset($shift->sales) 		&& is_numeric($shift->sales) 		? $shift->sales 	: null;
	$tipout 	= isset($shift->tipout) 	&& is_numeric($shift->tipout) 		? $shift->tipout 	: null;
	$transfers 	= isset($shift->transfers) 	&& is_numeric($shift->transfers) 	? $shift->transfers : null;
	$cash 		= isset($shift->cash) 		&& is_numeric($shift->cash) 		? $shift->cash 		: null;
	$due 		= isset($shift->due) 		&& is_numeric($shift->due) 			? $shift->due 		: null;
	$covers 	= isset($shift->covers) 	&& is_numeric($shift->covers) 		? $shift->covers 	: null;

	//get info: text
	$cut 		= !empty($shift->cut) 		? "'" . $db->escape_string($shift->cut) 	. "'" 	: null;
	$section 	= !empty($shift->section) 	? "'" . $db->escape_string($shift->section) . "'" 	: null;
	$notes 		= !empty($shift->notes) 	? "'" . $db->escape_string($shift->notes) 	. "'" 	: null;

	//get info: date/time
	//TODO add validation in catch clause
	try { $date = 		!empty($shift->date) 			
						? "'" . (new DateTime($shift->date))->format("Y-m-d") . "'"
						: null; 
	} 					catch(Exception $e) { $date = null; }
	try { $startTime = 	!empty($shift->startTime) 	
						? "'" . (new DateTime($shift->startTime))->format("H:i") . "'"
						: null; 
	} 					catch(Exception $e) { $startTime = null; }
	try { $endTime = 	!empty($shift->endTime) 		
						? "'" . (new DateTime($shift->endTime))->format("H:i") . "'"
						: null; 
	} 					catch(Exception $e) { $endTime = null; }
	try { $firstTable = !empty($shift->firstTable) 	
						? "'" . (new DateTime($shift->firstTable))->format("H:i") . "'" 
						: null; 
	} 					catch(Exception $e) { $firstTable = null; }

	//* DEBUG */ echo '<p>' . $wage . '|' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p>';

	//check if start date isn't null
	if(!isset($startTime))
	{
		//do something about the start time not being set
		//* DEBUG */ echo '<p>Start Time not set</p>';
	}
	else
	{
		//set up variables in database
		$db->query("SET @id 		= " . $id 			. ";");
		$db->query("SET @wage 		= " . $wage 		. ";");
		$db->query("SET @date 		= " . $date 		. ";");
		$db->query("SET @startTime 	= " . $startTime 	. ";");
		$db->query("SET @endTime 	= " . $endTime 		. ";");
		$db->query("SET @firstTable = " . $firstTable 	. ";");
		$db->query("SET @campHours 	= " . $campHours 	. ";");
		$db->query("SET @sales 		= " . $sales 		. ";");
		$db->query("SET @tipout 	= " . $tipout 		. ";");
		$db->query("SET @transfers 	= " . $transfers 	. ";");
		$db->query("SET @cash 		= " . $cash 		. ";");
		$db->query("SET @due 		= " . $due 			. ";");
		$db->query("SET @covers 	= " . $covers 		. ";");
		$db->query("SET @cut 		= " . $cut 			. ";");
		$db->query("SET @section 	= " . $section 		. ";");
		$db->query("SET @notes 		= " . $notes 		. ";");

		//calculate summaries
		$result = $db->query('CALL insertShift(@id, @wage, @date, @startTime, @endTime, @firstTable, @campHours, @sales, @tipout, @transfers, @cash, @due, @covers, @cut, @section, @notes);');
		//* DEBUG */ echo '<p>DB INFO:' . $db->info . '</p>';
	}

	//close connection
	$db->close();

	//return $id
	echo $id;
?>