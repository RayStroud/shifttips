<?php
	include 'include/db.php';
	function shiftRowToObject($row) {
		$object = new stdClass();

		$object->wage 		= (float) $row->wage;
		$object->date 		= $row->date;
		$object->startTime 	= $row->startTime;
		$object->endTime 	= $row->endTime;
		$object->firstTable = $row->firstTable;
		$object->campHours 	= (float) $row->campHours;
		$object->sales 		= (float) $row->sales;
		$object->tipout 	= (int) $row->tipout;
		$object->transfers 	= (int) $row->transfers;
		$object->cash 		= (int) $row->cash;
		$object->due 		= (int) $row->due;
		$object->covers 	= (int) $row->covers;
		$object->cut 		= $row->cut;
		$object->section 	= $row->section;
		$object->notes 		= $row->notes;

		$object->hours 			= (float) $row->hours;
		$object->earnedWage 	= (int) $row->earnedWage;
		$object->earnedTips 	= (int) $row->earnedTips;
		$object->earnedTotal 	= (int) $row->earnedTotal;
		$object->tipsVsWage 	= (int) $row->tipsVsWage;
		$object->salesPerHour 	= (float) $row->salesPerHour;
		$object->salesPerCover 	= (float) $row->salesPerCover;
		$object->tipsPercent 	= (float) $row->tipsPercent;
		$object->tipoutPercent 	= (float) $row->tipoutPercent;
		$object->hourly 		= (float) $row->hourly;
		$object->noCampHourly 	= (float) $row->noCampHourly;
		$object->lunchDinner 	= $row->lunchDinner;
		$object->dayOfWeek 		= $row->dayOfWeek;

		$object->id 		= $row->id;

		$object->yearweek 	= $row->yearweek;
		$object->weekday 	= (int) $row->weekday;

		return $object;
	}
	function selectAll($db)
	{
		if($stmt = $db->prepare('SELECT wage, YEARWEEK(date,3) as yearweek, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek, WEEKDAY(date) as weekday, id FROM shift;'))
		{
			$stmt->execute(); 
			$shifts = [];
			$result = $stmt->get_result();
			while($row = $result->fetch_object())
			{
				$shifts[] = shiftRowToObject($row);
			}
			echo json_encode($shifts);
			$stmt->free_result();
			$stmt->close();
		}
		else {http_response_code(500);}
	}
	function selectById($db, $id)
	{
		$shift = new stdClass();
		if($stmt = $db->prepare('SELECT wage, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek, id FROM shift WHERE id = ? LIMIT 1;'))
		{
			$stmt->bind_param('i', $id);
			$stmt->execute();
			$row = $stmt->get_result()->fetch_object();
			$shift = shiftRowToObject($row);
			echo json_encode($shift);
			$stmt->free_result();
			$stmt->close();
		}
		else {http_response_code(500);}
	}
	function insert($db, $shift)
	{
		if($stmt = $db->prepare('CALL saveShift(NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);'))
		{
			$stmt->bind_param('dssssddiiiiisss', $shift->wage, $shift->date, $shift->startTime, $shift->endTime, $shift->firstTable, $shift->campHours, $shift->sales, $shift->tipout, $shift->transfers, $shift->cash, $shift->due, $shift->covers, $shift->cut, $shift->section, $shift->notes);
			$stmt->execute();
			$stmt->bind_result($id);
			$stmt->fetch();
			echo $id;
			$stmt->free_result();
			$stmt->close();
		}
		else {http_response_code(500);}
	}
	function update($db, $id, $shift)
	{
		if($stmt = $db->prepare('CALL saveShift(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);'))
		{
			$stmt->bind_param('idssssddiiiiisss', $id, $shift->wage, $shift->date, $shift->startTime, $shift->endTime, $shift->firstTable, $shift->campHours, $shift->sales, $shift->tipout, $shift->transfers, $shift->cash, $shift->due, $shift->covers, $shift->cut, $shift->section, $shift->notes);
			$stmt->execute();
			echo $stmt->affected_rows;
			$stmt->free_result();
			$stmt->close();
		}
		else {http_response_code(500);}
	}
	function delete($db, $id)
	{
		if($stmt = $db->prepare('CALL deleteShift(?);'))
		{
			$stmt->bind_param('i', $id);
			$stmt->execute();
			echo $stmt->affected_rows;
			$stmt->free_result();
			$stmt->close();
		}
		else {http_response_code(500);}
	}

	try 
	{
		switch($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				if(isset($_GET['id']))
				{
					selectById($db, $_GET['id']);
				}
				else
				{
					selectAll($db);
				}
				break;
			case 'POST':
				$data = json_decode(file_get_contents("php://input"));
				insert($db, $data);
				break;
			case 'PUT':
				if(isset($_GET['id']))
				{
					$data = json_decode(file_get_contents("php://input"));
					update($db, $_GET['id'], $data);
				}
				break;
			case 'DELETE':
				if(isset($_GET['id']))
				{
					delete($db, $_GET['id']);
				}
				break;
		}
		$db->close();
	} 
	catch (Exception $e) 
	{
		http_response_code(500);
		die();
	}
?>