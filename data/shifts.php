<?php
	include 'include/db.php';
	function shiftRowToObject($row) {
		$object = new stdClass();

		$object->wage 		= is_null($row->wage) 		? null : (float) $row->wage;
		$object->date 		= $row->date;
		$object->startTime 	= $row->startTime;
		$object->endTime 	= $row->endTime;
		$object->firstTable = $row->firstTable;
		$object->campHours 	= is_null($row->campHours) 	? null : (float) $row->campHours;
		$object->sales 		= is_null($row->sales) 		? null : (float) $row->sales;
		$object->tipout 	= is_null($row->tipout) 	? null : (int) $row->tipout;
		$object->transfers 	= is_null($row->transfers) 	? null : (int) $row->transfers;
		$object->cash 		= is_null($row->cash) 		? null : (int) $row->cash;
		$object->due 		= is_null($row->due) 		? null : (int) $row->due;
		$object->dueCheck 	= $row->dueCheck;
		$object->covers 	= is_null($row->covers) 	? null : (int) $row->covers;
		$object->cut 		= $row->cut;
		$object->section 	= $row->section;
		$object->notes 		= $row->notes;

		$object->hours 			= is_null($row->hours) 			? null : (float) $row->hours;
		$object->earnedWage 	= is_null($row->earnedWage) 	? null : (int) $row->earnedWage;
		$object->earnedTips 	= is_null($row->earnedTips) 	? null : (int) $row->earnedTips;
		$object->earnedTotal 	= is_null($row->earnedTotal) 	? null : (int) $row->earnedTotal;
		$object->tipsVsWage 	= is_null($row->tipsVsWage) 	? null : (int) $row->tipsVsWage;
		$object->salesPerHour 	= is_null($row->salesPerHour) 	? null : (float) $row->salesPerHour;
		$object->salesPerCover 	= is_null($row->salesPerCover) 	? null : (float) $row->salesPerCover;
		$object->tipsPercent 	= is_null($row->tipsPercent) 	? null : (float) $row->tipsPercent;
		$object->tipoutPercent 	= is_null($row->tipoutPercent) 	? null : (float) $row->tipoutPercent;
		$object->hourly 		= is_null($row->hourly) 		? null : (float) $row->hourly;
		$object->noCampHourly 	= is_null($row->noCampHourly) 	? null : (float) $row->noCampHourly;
		$object->lunchDinner 	= $row->lunchDinner;
		$object->dayOfWeek 		= $row->dayOfWeek;

		$object->id 		= $row->id;

		$object->yearweek 	= $row->yearweek;
		$object->weekday 	= (int) $row->weekday;

		return $object;
	}
	function selectAll($db, $p_dateFrom, $p_dateTo)
	{
		if($stmt = $db->prepare('CALL getShifts(?,?)'))
		{
			$stmt->bind_param('ss', $p_dateFrom, $p_dateTo);
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
		if($stmt = $db->prepare('CALL getShiftById(?)'))
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
		if($stmt = $db->prepare('CALL saveShift(NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
		{
			$stmt->bind_param('dssssddiiiisisss', $shift->wage, $shift->date, $shift->startTime, $shift->endTime, $shift->firstTable, $shift->campHours, $shift->sales, $shift->tipout, $shift->transfers, $shift->cash, $shift->due, $shift->dueCheck, $shift->covers, $shift->cut, $shift->section, $shift->notes);
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
		if($stmt = $db->prepare('CALL saveShift(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
		{
			$stmt->bind_param('idssssddiiiisisss', $id, $shift->wage, $shift->date, $shift->startTime, $shift->endTime, $shift->firstTable, $shift->campHours, $shift->sales, $shift->tipout, $shift->transfers, $shift->cash, $shift->due, $shift->dueCheck, $shift->covers, $shift->cut, $shift->section, $shift->notes);
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
	function setDueCheck($db, $id, $dueCheck)
	{
		if($stmt = $db->prepare('CALL setDueCheck(?,?);'))
		{
			$stmt->bind_param('is', $id, $dueCheck);
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
					if(isset($_GET['dueCheck']))
					{
						setDueCheck($db, $_GET['id'], $_GET['dueCheck']);
					}
					else
					{
						selectById($db, $_GET['id']);
					}
				}
				else
				{
					//extract dates if set, or use defaults
					try { $dateTimeFrom = !empty($_GET['from']) ? new DateTime($_GET['from']) 	: null; } catch(Exception $e) { $dateTimeFrom 	= null; }
					try { $dateTimeTo 	= !empty($_GET['to']) 	? new DateTime($_GET['to']) 	: null; } catch(Exception $e) { $dateTimeTo 	= null; }
					$p_dateFrom = !empty($dateTimeFrom) ? $dateTimeFrom->format("Y-m-d") 	: '1000-01-01'; 
					$p_dateTo 	= !empty($dateTimeTo) 	? $dateTimeTo->format("Y-m-d") 		: '9999-12-31'; 
					//* DEBUG */ echo '<p>dateFrom: ' . $p_dateFrom . '</p><p>dateTo: ' . $p_dateTo . '</p>';

					selectAll($db, $p_dateFrom, $p_dateTo);
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