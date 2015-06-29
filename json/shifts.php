<?php
	include '../include/dbconnect.php';

	//check if group-by-week is requested
	$weekly = isset($_GET['week']) ? $_GET['week'] : null;

	//extract dates if set
	try { $dateTimeFrom = !empty($_GET['from']) ? new DateTime($_GET['from']) : null; } catch(Exception $e) { $dateTimeFrom = null; }
	try { $dateTimeTo = !empty($_GET['to']) ? new DateTime($_GET['to']) : null; } catch(Exception $e) { $dateTimeTo = null; }
	$p_dateFrom = !empty($dateTimeFrom) ? "'" . $dateTimeFrom->format("Y-m-d") . "'" : null; 
	$p_dateTo = !empty($dateTimeTo) ? "'" . $dateTimeTo->format("Y-m-d") . "'" : null; 
	//* DEBUG */ echo '<p>|dateFrom:' . $p_dateFrom . '|dateTo:' . $p_dateTo . '|</p>';

	//get lunch, dinner, both, or neither
	$p_lunchDinner = isset($_GET['lun']) 
		? (isset($_GET['din']) ? null : "'L'") 
		: (isset($_GET['din']) ? "'D'" : null);
	//* DEBUG */ echo '<p>|lunchDinner:' . $p_lunchDinner . '|</p>';

	//get days, set 1 or 0 for mysql proc, add a string to array for message
	$aDayOfWeekNames = [];
	if (isset($_GET['mon'])) {$p_mon = 1; $aDayOfWeekNames[] = 'Mon';} else {$p_mon = 0;} 
	if (isset($_GET['tue'])) {$p_tue = 1; $aDayOfWeekNames[] = 'Tue';} else {$p_tue = 0;} 
	if (isset($_GET['wed'])) {$p_wed = 1; $aDayOfWeekNames[] = 'Wed';} else {$p_wed = 0;} 
	if (isset($_GET['thu'])) {$p_thu = 1; $aDayOfWeekNames[] = 'Thu';} else {$p_thu = 0;} 
	if (isset($_GET['fri'])) {$p_fri = 1; $aDayOfWeekNames[] = 'Fri';} else {$p_fri = 0;} 
	if (isset($_GET['sat'])) {$p_sat = 1; $aDayOfWeekNames[] = 'Sat';} else {$p_sat = 0;} 
	if (isset($_GET['sun'])) {$p_sun = 1; $aDayOfWeekNames[] = 'Sun';} else {$p_sun = 0;} 
	//* DEBUG */ echo '<p>|mon:' . $p_mon . '|tue:' . $p_tue . '|wed:' . $p_wed . '|thu:' . $p_thu . '|fri:' . $p_fri . '|sat:' . $p_sat . '|sun:' . $p_sun . '|</p>';
	//* DEBUG */ echo '<pre>'; print_r($aDayOfWeekNames); echo '</pre>';

	//set up variables in database
	$db->query("SET @p_dateFrom = " . $p_dateFrom . ";");
	$db->query("SET @p_dateTo = " . $p_dateTo . ";");
	$db->query("SET @p_lunchDinner = " . $p_lunchDinner . ";");
	$db->query("SET @p_mon = " . $p_mon . ";");
	$db->query("SET @p_tue = " . $p_tue . ";");
	$db->query("SET @p_wed = " . $p_wed . ";");
	$db->query("SET @p_thu = " . $p_thu . ";");
	$db->query("SET @p_fri = " . $p_fri . ";");
	$db->query("SET @p_sat = " . $p_sat . ";");
	$db->query("SET @p_sun = " . $p_sun . ";");

	//calculate summaries
	$result = $db->query('CALL getShifts(@p_dateFrom, @p_dateTo, @p_lunchDinner, @p_mon, @p_tue, @p_wed, @p_thu, @p_fri, @p_sat, @p_sun);');
	$shifts = []; 
	while($row = $result->fetch_assoc())
	{
		$shift = [];
		$shift['id'] = $row['id'];
		$shift['wage'] = $row['wage'];
		$shift['date'] = $row['date'];
		$shift['startTime'] = $row['startTime'];
		$shift['endTime'] = $row['endTime'];
		$shift['firstTable'] = $row['firstTable'];
		$shift['campHours'] = $row['campHours'];
		$shift['sales'] = $row['sales'];
		$shift['tipout'] = $row['tipout'];
		$shift['transfers'] = $row['transfers'];
		$shift['cash'] = $row['cash'];
		$shift['due'] = $row['due'];
		$shift['covers'] = $row['covers'];
		$shift['cut'] = $row['cut'];
		$shift['section'] = $row['section'];
		$shift['notes'] = $row['notes'];

		$shift['hours'] = $row['hours'];
		$shift['earnedWage'] = $row['earnedWage'];
		$shift['earnedTips'] = $row['earnedTips'];
		$shift['earnedTotal'] = $row['earnedTotal'];
		$shift['tipsVsWage'] = $row['tipsVsWage'];
		$shift['salesPerHour'] = $row['salesPerHour'];
		$shift['salesPerCover'] = $row['salesPerCover'];
		$shift['tipsPercent'] = $row['tipsPercent'];
		$shift['tipoutPercent'] = $row['tipoutPercent'];
		$shift['earnedHourly'] = $row['earnedHourly'];
		$shift['noCampHourly'] = $row['noCampHourly'];
		$shift['lunchDinner'] = $row['lunchDinner'];
		$shift['dayOfWeek'] = $row['dayOfWeek'];

		$shift['yearWeek'] = $row['yearWeek'];

		$shifts[] = $shift;
	}

	//close connection
	$db->close();

	echo json_encode($shifts);
?>