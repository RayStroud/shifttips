<?php
	include 'include/db.php';
	function selectAll($db)
	{
		$stmt = $db->prepare('SELECT wage, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek, id FROM shift;');
		$stmt->execute(); 
		$stmt->store_result();
		$stmt->bind_result($wage, $date, $startTime, $endTime, $firstTable, $campHours, $sales, $tipout, $transfers, $cash, $due, $covers, $cut, $section, $notes, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $hourly, $noCampHourly, $lunchDinner, $dayOfWeek, $id);
		$shifts = [];
		while($stmt->fetch())
		{
			$shift = new stdClass();
			$shift->wage = $wage;
			$shift->date = $date;
			$shift->startTime = $startTime;
			$shift->endTime = $endTime;
			$shift->firstTable = $firstTable;
			$shift->campHours = $campHours;
			$shift->sales = $sales;
			$shift->tipout = $tipout;
			$shift->transfers = $transfers;
			$shift->cash = $cash;
			$shift->due = $due;
			$shift->covers = $covers;
			$shift->cut = $cut;
			$shift->section = $section;
			$shift->notes = $notes;

			$shift->hours = $hours;
			$shift->earnedWage = $earnedWage;
			$shift->earnedTips = $earnedTips;
			$shift->earnedTotal = $earnedTotal;
			$shift->tipsVsWage = $tipsVsWage;
			$shift->salesPerHour = $salesPerHour;
			$shift->salesPerCover = $salesPerCover;
			$shift->tipsPercent = $tipsPercent;
			$shift->tipoutPercent = $tipoutPercent;
			$shift->hourly = $hourly;
			$shift->noCampHourly = $noCampHourly;
			$shift->lunchDinner = $lunchDinner;
			$shift->dayOfWeek = $dayOfWeek;

			$shift->id = $id;
			$shifts[] = $shift;
		}
		echo json_encode($shifts);
		$stmt->free_result();
		$stmt->close();
	}
	function selectById($db, $id)
	{
		$shift = new stdClass();
		$stmt = $db->prepare('SELECT wage, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek, id FROM shift WHERE id = ? LIMIT 1;');
		$stmt->bind_param('i', $id);
		$stmt->execute(); 
		$stmt->store_result();
		$stmt->bind_result($shift->wage, $shift->date, $shift->startTime, $shift->endTime, $shift->firstTable, $shift->campHours, $shift->sales, $shift->tipout, $shift->transfers, $shift->cash, $shift->due, $shift->covers, $shift->cut, $shift->section, $shift->notes, $shift->hours, $shift->earnedWage, $shift->earnedTips, $shift->earnedTotal, $shift->tipsVsWage, $shift->salesPerHour, $shift->salesPerCover, $shift->tipsPercent, $shift->tipoutPercent, $shift->hourly, $shift->noCampHourly, $shift->lunchDinner, $shift->dayOfWeek, $shift->id);
		$stmt->fetch();
		echo json_encode($shift);
		$stmt->free_result();
		$stmt->close();
	}
	function insert($db, $shift)
	{
		$stmt = $db->prepare('CALL saveShift(NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		$stmt->bind_param('dssssddiiiiisss', $shift->wage, $shift->date, $shift->startTime, $shift->endTime, $shift->firstTable, $shift->campHours, $shift->sales, $shift->tipout, $shift->transfers, $shift->cash, $shift->due, $shift->covers, $shift->cut, $shift->section, $shift->notes);
		$stmt->execute(); 
		$stmt->store_result();
		$stmt->bind_result($id);
		$stmt->fetch();
		echo $id;
		$stmt->free_result();
		$stmt->close();
	}
	function update($db, $id, $shift)
	{
		$stmt = $db->prepare('CALL saveShift(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);');
		$stmt->bind_param('idssssddiiiiisss', $id, $shift->wage, $shift->date, $shift->startTime, $shift->endTime, $shift->firstTable, $shift->campHours, $shift->sales, $shift->tipout, $shift->transfers, $shift->cash, $shift->due, $shift->covers, $shift->cut, $shift->section, $shift->notes);
		$stmt->execute(); 
		$stmt->store_result();
		echo $stmt->affected_rows;
		$stmt->free_result();
		$stmt->close();
	}
	function delete($db, $id)
	{
		$stmt = $db->prepare('CALL deleteShift(?);');
		$stmt->bind_param('i', $id);
		$stmt->execute(); 
		$stmt->store_result();
		echo $stmt->affected_rows;
		$stmt->free_result();
		$stmt->close();
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