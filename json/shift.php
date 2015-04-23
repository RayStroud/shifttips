<?php
	include 'dbconnect.php';

	$sid = isset($_GET['id']) ? $_GET['id'] : null;

	if(isset($sid))
	{
		//get shift
		$shiftSQL = $db->prepare("SELECT wage, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, noCampHourly, lunchDinner, dayOfWeek
			FROM shift
			WHERE sid = ?");
		$shiftSQL->bind_param('i', $sid);
		$shiftSQL->execute();
		$shiftSQL->bind_result($wage, $startTime, $endTime, $firstTable, $campHours, $sales, $tipout, $transfers, $cash, $due, $covers, $cut, $section, $notes, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $earnedHourly, $noCampHourly, $lunchDinner, $dayOfWeek);
		$shiftSQL->fetch();

		//* DEBUG */ echo '<p>' . $sid . '|' . $wage . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p><p>' . $hours . '|' . $earnedWage . '|' . $earnedTips . '|' . $earnedTotal . '|' . $tipsVsWage . '|' . $salesPerHour . '|' . $salesPerCover . '|' . $tipsPercent . '|' . $tipoutPercent . '|' . $earnedHourly . '|' . $noCampHourly . '|' . $lunchDinner . '|' . $dayOfWeek . '|</p>';

		//format values
		$date = !empty($startTime) ? (new DateTime($startTime))->format("D M jS, Y") : null;
		$startTime = !empty($startTime) ? (new DateTime($startTime))->format("g:iA") : null;
		$endTime = !empty($endTime) ? (new DateTime($endTime))->format("g:iA") : null;
		$firstTable = !empty($firstTable) ? (new DateTime($firstTable))->format("g:iA") : null;

		//* DEBUG */ echo '<p>' . $sid . '|' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|</p>';

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

		echo json_encode($shift);

	}
	else
	{
		//* DEBUG */ echo '<p>No shift ID provided.</p>'; 
	}

	//close connection
	$db->close();
?>