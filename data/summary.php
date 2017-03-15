<?php
	include 'include/db.php';

	function summaryRowToObject($row) 
	{
		$object = new stdClass();
		$object->count 			= (int) 	$row->count;
		$object->avgHours 		= (float) 	$row->avgHours;
		$object->totHours 		= (float) 	$row->totHours;
		$object->avgWage 		= (float) 	$row->avgWage;
		$object->totWage 		= (float) 	$row->totWage;
		$object->avgTips 		= (float) 	$row->avgTips;
		$object->totTips 		= (int) 	$row->totTips;
		$object->avgEarned 		= (float) 	$row->avgEarned;
		$object->totEarned 		= (float) 	$row->totEarned;
		$object->avgTipout 		= (float) 	$row->avgTipout;
		$object->totTipout 		= (int) 	$row->totTipout;
		$object->avgTransfers 	= (float) 	$row->avgTransfers;
		$object->totTransfers 	= (int) 	$row->totTransfers;
		$object->avgSales 		= (float) 	$row->avgSales;
		$object->totSales 		= (float) 	$row->totSales;
		$object->avgCovers 		= (float) 	$row->avgCovers;
		$object->totCovers 		= (int) 	$row->totCovers;
		$object->avgCampHours 	= (float) 	$row->avgCampHours;
		$object->totCampHours 	= (float) 	$row->totCampHours;
		$object->salesPerHour 	= (float) 	$row->salesPerHour;
		$object->salesPerCover 	= (float) 	$row->salesPerCover;
		$object->tipsPercent 	= (float) 	$row->tipsPercent;
		$object->tipoutPercent 	= (float) 	$row->tipoutPercent;
		$object->tipsVsWage 	= (int) 	$row->tipsVsWage;
		$object->hourly 		= (float) 	$row->hourly;
		return $object;
	}
	function getSummary($db, $p_user_id, $p_dateFrom, $p_dateTo, $p_lunchDinner)
	{
		$summary = new stdClass();
		if($stmt = $db->prepare('CALL getSummary(?,?,?,?)'))
		{
			$stmt->bind_param('isss', $p_user_id, $p_dateFrom, $p_dateTo, $p_lunchDinner);
			$stmt->execute();
			$row = $stmt->get_result()->fetch_object();
			$summary = summaryRowToObject($row);
			header('Content-Type: application/json');
			echo json_encode($summary);
			$stmt->close();
		}
		else {http_response_code(500);}
	}
	function getSummaryFiltered($db, $p_user_id, $p_dateFrom, $p_dateTo, $p_lunchDinner, $p_mon, $p_tue, $p_wed, $p_thu, $p_fri, $p_sat, $p_sun) 
	{
		$summary = new stdClass();
		if($stmt = $db->prepare('CALL getSummaryFiltered(?,?,?,?,?,?,?,?,?,?,?)'))
		{
			$stmt->bind_param('issssssssss', $p_user_id, $p_dateFrom, $p_dateTo, $p_lunchDinner, $p_mon, $p_tue, $p_wed, $p_thu, $p_fri, $p_sat, $p_sun);
			$stmt->execute();
			$row = $stmt->get_result()->fetch_object();
			$summary = summaryRowToObject($row);
			header('Content-Type: application/json');
			echo json_encode($summary);
			$stmt->close();
		}
		else {http_response_code(500);}
	}
	function getSummaryByLunchDinner($db, $p_user_id, $p_dateFrom, $p_dateTo)
	{
		$summaries = [];
		if($stmt = $db->prepare('CALL getSummaryByLunchDinner(?,?,?)'))
		{
			$stmt->bind_param('iss', $p_user_id, $p_dateFrom, $p_dateTo);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_object())
			{
				$summary = summaryRowToObject($row);
				$summary->lunchDinner = $row->lunchDinner; 
				$summaries[] = $summary;
			}
			$stmt->close();
		}
		else {http_response_code(500);}
		header('Content-Type: application/json');
		echo json_encode($summaries);
	}
	function getSummaryBySection($db, $p_user_id, $p_dateFrom, $p_dateTo)
	{
		$summaries = [];
		if($stmt = $db->prepare('CALL getSummaryBySection(?,?,?)'))
		{
			$stmt->bind_param('iss', $p_user_id, $p_dateFrom, $p_dateTo);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_object())
			{
				$summary = summaryRowToObject($row);
				$summary->section 		= $row->section;
				$summary->lunchDinner 	= $row->lunchDinner;
				$summaries[] = $summary;
			}
			$stmt->close();
		}
		else {http_response_code(500);}
		header('Content-Type: application/json');
		echo json_encode($summaries);
	}
	function getSummaryByStartTime($db, $p_user_id, $p_dateFrom, $p_dateTo)
	{
		$summaries = [];
		if($stmt = $db->prepare('CALL getSummaryByStartTime(?,?,?)'))
		{
			$stmt->bind_param('iss', $p_user_id, $p_dateFrom, $p_dateTo);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_object())
			{
				$summary = summaryRowToObject($row);
				$summary->startTime 	= $row->startTime;
				$summary->lunchDinner 	= $row->lunchDinner;
				$summaries[] = $summary;
			}
			$stmt->close();
		}
		else {http_response_code(500);}
		header('Content-Type: application/json');
		echo json_encode($summaries);
	}
	function getSummaryByCut($db, $p_user_id, $p_dateFrom, $p_dateTo)
	{
		$summaries = [];
		if($stmt = $db->prepare('CALL getSummaryByCut(?,?,?)'))
		{
			$stmt->bind_param('iss', $p_user_id, $p_dateFrom, $p_dateTo);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_object())
			{
				$summary = summaryRowToObject($row);
				$summary->cut 			= $row->cut;
				$summary->lunchDinner 	= $row->lunchDinner;
				$summaries[] = $summary;
			}
			$stmt->close();
		}
		else {http_response_code(500);}
		header('Content-Type: application/json');
		echo json_encode($summaries);
	}
	function getSummaryByHalfhours($db, $p_user_id, $p_dateFrom, $p_dateTo)
	{
		$summaries = [];
		if($stmt = $db->prepare('CALL getSummaryByHalfhours(?,?,?)'))
		{
			$stmt->bind_param('iss', $p_user_id, $p_dateFrom, $p_dateTo);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_object())
			{
				$summary = summaryRowToObject($row);
				$summary->halfhours 	= (float) $row->halfhours;
				$summary->lunchDinner 	= $row->lunchDinner;
				$summaries[] = $summary;
			}
			$stmt->close();
		}
		else {http_response_code(500);}
		header('Content-Type: application/json');
		echo json_encode($summaries);
	}
	function getSummaryByLocation($db, $p_user_id, $p_dateFrom, $p_dateTo)
	{
		$summaries = [];
		if($stmt = $db->prepare('CALL getSummaryByLocation(?,?,?)'))
		{
			$stmt->bind_param('iss', $p_user_id, $p_dateFrom, $p_dateTo);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_object())
			{
				$summary = summaryRowToObject($row);
				$summary->location 		= $row->location;
				$summary->lunchDinner 	= $row->lunchDinner;
				$summaries[] = $summary;
			}
			$stmt->close();
		}
		else {http_response_code(500);}
		header('Content-Type: application/json');
		echo json_encode($summaries);
	}
	function getSummaryByDayOfWeek($db, $p_user_id, $p_dateFrom, $p_dateTo)
	{
		$summaries = [];
		if($stmt = $db->prepare('CALL getSummaryByDayOfWeek(?,?,?)'))
		{
			$stmt->bind_param('iss', $p_user_id, $p_dateFrom, $p_dateTo);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_object())
			{
				$summary = summaryRowToObject($row);
				$summary->weekday 		= $row->weekday;
				$summary->dayOfWeek 	= $row->dayOfWeek;
				$summary->lunchDinner 	= $row->lunchDinner;
				$summaries[] = $summary;
			}
			$stmt->close();
		}
		else {http_response_code(500);}
		header('Content-Type: application/json');
		echo json_encode($summaries);
	}
	function getSummaryWeekly($db, $p_user_id, $p_dateFrom, $p_dateTo, $p_lunchDinner)
	{
		$return = new stdClass();
		$return->list = [];
		$return->summary = new stdClass();

		if($stmt = $db->prepare('CALL getWeeks(?,?,?,?);'))
		{
			$stmt->bind_param('isss', $p_user_id, $p_dateFrom, $p_dateTo, $p_lunchDinner);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_object())
			{
				$week = new stdClass();
				$week->yearweek 		= $row->yearweek;
				$week->startWeek 		= $row->startWeek;
				$week->endWeek 			= $row->endWeek;
				$week->shifts 			= (int)		$row->shifts;

				$week->avgHours 		= (float) 	$row->avgHours;
				$week->totHours 		= (float) 	$row->totHours;
				$week->avgWage 			= (float) 	$row->avgWage;
				$week->totWage 			= (float) 	$row->totWage;
				$week->avgTips 			= (float) 	$row->avgTips;
				$week->totTips 			= (int) 	$row->totTips;
				$week->avgEarned 		= (float) 	$row->avgEarned;
				$week->totEarned 		= (float) 	$row->totEarned;
				$week->avgTipout 		= (float) 	$row->avgTipout;
				$week->totTipout 		= (int) 	$row->totTipout;
				$week->avgTransfers 	= (float) 	$row->avgTransfers;
				$week->totTransfers 	= (int) 	$row->totTransfers;
				$week->avgSales 		= (float) 	$row->avgSales;
				$week->totSales 		= (float) 	$row->totSales;
				$week->avgCovers 		= (float) 	$row->avgCovers;
				$week->totCovers 		= (int) 	$row->totCovers;
				$week->avgCampHours 	= (float) 	$row->avgCampHours;
				$week->totCampHours 	= (float) 	$row->totCampHours;
				$week->salesPerHour 	= (float) 	$row->salesPerHour;
				$week->salesPerCover 	= (float) 	$row->salesPerCover;
				$week->tipsPercent 		= (float) 	$row->tipsPercent;
				$week->tipoutPercent 	= (float) 	$row->tipoutPercent;
				$week->tipsVsWage 		= (int) 	$row->tipsVsWage;
				$week->hourly 			= (float) 	$row->hourly;

				$return->list[] = $week;
			}
			$stmt->close();
		}
		else {http_response_code(500);}

		if($stmt = $db->prepare('CALL getSummaryWeekly(?,?,?,?);'))
		{
			$stmt->bind_param('isss', $p_user_id, $p_dateFrom, $p_dateTo, $p_lunchDinner);
			$stmt->execute();
			$row = $stmt->get_result()->fetch_object();
			$return->summary = summaryRowToObject($row);
			$return->summary->avgShifts 	= (float) 	$row->avgShifts;
			$return->summary->totShifts 	= (int) 	$row->totShifts;
			$stmt->close();
		}
		else {http_response_code(500);}
		header('Content-Type: application/json');
		echo json_encode($return);
	}
	function getSummaryMonthly($db, $p_user_id, $p_dateFrom, $p_dateTo, $p_lunchDinner)
	{
		$return = new stdClass();
		$return->list = [];
		$return->summary = new stdClass();

		if($stmt = $db->prepare('CALL getMonths(?,?,?,?);'))
		{
			$stmt->bind_param('isss', $p_user_id, $p_dateFrom, $p_dateTo, $p_lunchDinner);
			$stmt->execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_object())
			{
				$month = new stdClass();
				$month->year 			= $row->year;
				$month->month 			= $row->month;
				$month->monthname 		= substr($row->monthname,0,3);
				$month->shifts 			= (int)		$row->shifts;

				$month->avgHours 		= (float) 	$row->avgHours;
				$month->totHours 		= (float) 	$row->totHours;
				$month->avgWage 		= (float) 	$row->avgWage;
				$month->totWage 		= (float) 	$row->totWage;
				$month->avgTips 		= (float) 	$row->avgTips;
				$month->totTips 		= (int) 	$row->totTips;
				$month->avgEarned 		= (float) 	$row->avgEarned;
				$month->totEarned 		= (float) 	$row->totEarned;
				$month->avgTipout 		= (float) 	$row->avgTipout;
				$month->totTipout 		= (int) 	$row->totTipout;
				$month->avgTransfers 	= (float) 	$row->avgTransfers;
				$month->totTransfers 	= (int) 	$row->totTransfers;
				$month->avgSales 		= (float) 	$row->avgSales;
				$month->totSales 		= (float) 	$row->totSales;
				$month->avgCovers 		= (float) 	$row->avgCovers;
				$month->totCovers 		= (int) 	$row->totCovers;
				$month->avgCampHours 	= (float) 	$row->avgCampHours;
				$month->totCampHours 	= (float) 	$row->totCampHours;
				$month->salesPerHour 	= (float) 	$row->salesPerHour;
				$month->salesPerCover 	= (float) 	$row->salesPerCover;
				$month->tipsPercent 	= (float) 	$row->tipsPercent;
				$month->tipoutPercent 	= (float) 	$row->tipoutPercent;
				$month->tipsVsWage 		= (int) 	$row->tipsVsWage;
				$month->hourly 			= (float) 	$row->hourly;

				$return->list[] = $month;
			}
			$stmt->close();
		}
		else {http_response_code(500);}

		if($stmt = $db->prepare('CALL getSummaryMonthly(?,?,?,?);'))
		{
			$stmt->bind_param('isss', $p_user_id, $p_dateFrom, $p_dateTo, $p_lunchDinner);
			$stmt->execute();
			$row = $stmt->get_result()->fetch_object();
			$return->summary = summaryRowToObject($row);
			$return->summary->avgShifts 	= (float) 	$row->avgShifts;
			$return->summary->totShifts 	= (int) 	$row->totShifts;
			$stmt->close();
		}
		else {http_response_code(500);}
		header('Content-Type: application/json');
		echo json_encode($return);
	}

	try
	{	
		if(!empty($_GET['uid']))
		{
			$p_uid = $_GET['uid'];
		}
		else
		{
			$p_uid = null;
		}

		//extract dates if set, or use defaults
		try { $dateTimeFrom = !empty($_GET['from']) ? new DateTime($_GET['from']) 	: null; } catch(Exception $e) { $dateTimeFrom 	= null; }
		try { $dateTimeTo 	= !empty($_GET['to']) 	? new DateTime($_GET['to']) 	: null; } catch(Exception $e) { $dateTimeTo 	= null; }
		$p_dateFrom = !empty($dateTimeFrom) ? $dateTimeFrom->format("Y-m-d") 	: '1000-01-01'; 
		$p_dateTo 	= !empty($dateTimeTo) 	? $dateTimeTo->format("Y-m-d") 		: '9999-12-31'; 
		//* DEBUG */ echo '<p>dateFrom: ' . $p_dateFrom . '</p><p>dateTo: ' . $p_dateTo . '</p>';

		//get if lunch/dinner is passed
		$p_lunchDinner = null;
		if(!empty($_GET['ld']))
		{
			if ($_GET['ld'] == 'L' || $_GET['ld'] == 'l')
			{
				$p_lunchDinner = 'L';
			}
			else if ($_GET['ld'] == 'D' || $_GET['ld'] == 'd')
			{
				$p_lunchDinner = 'D';
			}
		}
		//* DEBUG */ echo '<p>lunchDinner: ' . $p_lunchDinner . '</p>';

		//get days of week
		$p_mon = isset($_GET['mon']) ? 1 : null;
		$p_tue = isset($_GET['tue']) ? 1 : null;
		$p_wed = isset($_GET['wed']) ? 1 : null;
		$p_thu = isset($_GET['thu']) ? 1 : null;
		$p_fri = isset($_GET['fri']) ? 1 : null;
		$p_sat = isset($_GET['sat']) ? 1 : null;
		$p_sun = isset($_GET['sun']) ? 1 : null;
		//* DEBUG */ echo "<p>mon:$p_mon tue:$p_tue wed:$p_wed thu:$p_thu fri:$p_fri sat:$p_sat sun:$p_sun</p>";

		if($p_mon || $p_tue || $p_wed || $p_thu || $p_fri || $p_sat || $p_sun) 
		{
			getSummaryFiltered($db, $p_uid, $p_dateFrom, $p_dateTo, $p_lunchDinner, $p_mon, $p_tue, $p_wed, $p_thu, $p_fri, $p_sat, $p_sun);
		}
		else if(isset($_GET['lunchDinner']) 
			|| isset($_GET['shift']))
		{
			getSummaryByLunchDinner($db, $p_uid, $p_dateFrom, $p_dateTo);
		}
		else if(isset($_GET['day']) 
			|| isset($_GET['dayOfWeek']) 
			|| isset($_GET['days']) 
			|| isset($_GET['daily']))
		{
			getSummaryByDayOfWeek($db, $p_uid, $p_dateFrom, $p_dateTo);
		}
		else if(isset($_GET['section']))
		{
			getSummaryBySection($db, $p_uid, $p_dateFrom, $p_dateTo);
		}
		else if(isset($_GET['startTime']))
		{
			getSummaryByStartTime($db, $p_uid, $p_dateFrom, $p_dateTo);
		}
		else if(isset($_GET['cut']))
		{
			getSummaryByCut($db, $p_uid, $p_dateFrom, $p_dateTo);
		}
		else if(isset($_GET['halfhours']))
		{
			getSummaryByHalfhours($db, $p_uid, $p_dateFrom, $p_dateTo);
		}
		else if(isset($_GET['location']))
		{
			getSummaryByLocation($db, $p_uid, $p_dateFrom, $p_dateTo);
		}
		else if(isset($_GET['week']) 
			|| isset($_GET['weeks']) 
			|| isset($_GET['weekly']))
		{
			getSummaryWeekly($db, $p_uid, $p_dateFrom, $p_dateTo, $p_lunchDinner);
		}
		else if(isset($_GET['month']) 
			|| isset($_GET['months']) 
			|| isset($_GET['monthly']))
		{
			getSummaryMonthly($db, $p_uid, $p_dateFrom, $p_dateTo, $p_lunchDinner);
		}
		else
		{
			getSummary($db, $p_uid, $p_dateFrom, $p_dateTo, $p_lunchDinner);
		}
		$db->close();
	}
	catch (Exception $e) 
	{
		http_response_code(500);
		die();
	}
?>
