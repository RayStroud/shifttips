<?php
	include '../include/mongo-connect.php';

	//get shifts
	$collection = $db->shift;
	$result = $collection->find();
	$shifts = []; 
	foreach($result as $row)
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

		//$shift['yearWeek'] = $row['yearWeek'];

		$shifts[] = $shift;
	}
	//* DEBUG */ echo '<pre>'; print_r($shifts); echo '</pre>';

	//close connection
	$mongo->close();

	echo json_encode($shifts);
?>