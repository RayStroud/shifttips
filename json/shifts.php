<?php
	include 'dbconnect.php';

	//get today's date as the end date range, and two weeks earlier for the start range
	$endDateRange = (new DateTime())->format('Y-m-d H:i:s');
	$startDateRange = new DateTime();
	$startDateRange->sub(new DateInterval('P2W'));
	$startDateRange = $startDateRange->format('Y-m-d H:i:s');
	//* DEBUG */ echo '<p>' . $startDateRange . '|' . $endDateRange . '|</p>';

	//get shift
	//TODO change this statement to accept date ranges
	$shiftSQL = $db->prepare("SELECT sid, wage, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, noCampHourly, lunchDinner, dayOfWeek
		FROM shift");
	//$shiftSQL->bind_param('ss', $startDateRange, $endDateRange);
	$shiftSQL->execute();
	//$stmt->store_result(); //not sure if I need this to loop through or not
	$shiftSQL->bind_result($sid, $wage, $startTime, $endTime, $firstTable, $campHours, $sales, $tipout, $transfers, $cash, $due, $covers, $cut, $section, $notes, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $earnedHourly, $noCampHourly, $lunchDinner, $dayOfWeek);
	//$shiftSQL->fetch(); //this would be to fetch only once

	//TODO loop through all the records to show them all
	$shifts = array();
	while($shiftSQL->fetch())
	{
		//* DEBUG */ echo '<p>' . $wage . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p><p>' . $hours . '|' . $earnedWage . '|' . $earnedTips . '|' . $earnedTotal . '|' . $tipsVsWage . '|' . $salesPerHour . '|' . $salesPerCover . '|' . $tipsPercent . '|' . $tipoutPercent . '|' . $earnedHourly . '|' . $noCampHourly . '|' . $lunchDinner . '|' . $dayOfWeek . '|</p>';

		//format values
		$date = !empty($startTime) ? (new DateTime($startTime))->format("D M jS, Y") : null;
		$startTime = !empty($startTime) ? (new DateTime($startTime))->format("g:iA") : null;
		$endTime = !empty($endTime) ? (new DateTime($endTime))->format("g:iA") : null;
		$firstTable = !empty($firstTable) ? (new DateTime($firstTable))->format("g:iA") : null;

		//* DEBUG */ echo '<p>' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|</p>';

		//make into a php array
		$shift['date'] = $date;

		$shift['sid'] = $sid;
		$shift['wage'] = $wage;
		$shift['startTime'] = $startTime;
		$shift['endTime'] = $endTime;
		$shift['firstTable'] = $firstTable;
		$shift['campHours'] = $campHours;
		$shift['sales'] = $sales;
		$shift['tipout'] = $tipout;
		$shift['transfers'] = $transfers;
		$shift['cash'] = $cash;
		$shift['due'] = $due;
		$shift['covers'] = $covers;
		$shift['cut'] = $cut;
		$shift['section'] = $section;
		$shift['notes'] = $notes;

		$shift['hours'] = $hours;
		$shift['earnedWage'] = $earnedWage;
		$shift['earnedTips'] = $earnedTips;
		$shift['earnedTotal'] = $earnedTotal;
		$shift['tipsVsWage'] = $tipsVsWage;
		$shift['salesPerHour'] = $salesPerHour;
		$shift['salesPerCover'] = $salesPerCover;
		$shift['tipsPercent'] = $tipsPercent;
		$shift['tipoutPercent'] = $tipoutPercent;
		$shift['earnedHourly'] = $earnedHourly;
		$shift['noCampHourly'] = $noCampHourly;
		$shift['lunchDinner'] = $lunchDinner; 
		$shift['dayOfWeek'] = $dayOfWeek; 

		//add shift to array of shifts
		array_push($shifts, $shift);
	}

	echo json_encode($shifts);

	//close connection
	$db->close();
?>