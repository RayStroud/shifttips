<?php
	include 'db.php';
	
	//extract dates if set, or use defaults
	try { $dateTimeFrom = !empty($_GET['from']) ? new DateTime($_GET['from']) 	: null; } catch(Exception $e) { $dateTimeFrom 	= null; }
	try { $dateTimeTo 	= !empty($_GET['to']) 	? new DateTime($_GET['to']) 	: null; } catch(Exception $e) { $dateTimeTo 	= null; }
	$p_dateFrom = !empty($dateTimeFrom) ? $dateTimeFrom->format("Y-m-d") 	: '1000-01-01'; 
	$p_dateTo 	= !empty($dateTimeTo) 	? $dateTimeTo->format("Y-m-d") 		: '9999-12-31'; 
	//* DEBUG */ echo '<p>|dateFrom:' . $p_dateFrom . '|dateTo:' . $p_dateTo . '|</p>';

	$stmt = $db->prepare('SELECT startWeek, endWeek, count, campHours, sales, tipout, transfers, covers, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, id FROM week WHERE startWeek BETWEEN ? AND ? OR endWeek BETWEEN ? and ?;');
	$stmt->bind_param('ssss', $p_dateFrom, $p_dateTo, $p_dateFrom, $p_dateTo);
	$stmt->execute();
	$stmt->bind_result($startWeek, $endWeek, $shifts, $campHours, $sales, $tipout, $transfers, $covers, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $earnedHourly, $id);
	$summary = new stdClass();
	$summary->weeks = [];
	while($stmt->fetch())
	{
		$week = new stdClass();
		$week->startWeek = $startWeek;
		$week->endWeek = $endWeek;
		$week->shifts = $shifts;
		$week->campHours = $campHours;
		$week->sales = $sales;
		$week->tipout = $tipout;
		$week->transfers = $transfers;
		$week->covers = $covers;

		$week->hours = $hours;
		$week->earnedWage = $earnedWage;
		$week->earnedTips = $earnedTips;
		$week->earnedTotal = $earnedTotal;
		$week->tipsVsWage = $tipsVsWage;
		$week->salesPerHour = $salesPerHour;
		$week->salesPerCover = $salesPerCover;
		$week->tipsPercent = $tipsPercent;
		$week->tipoutPercent = $tipoutPercent;
		$week->earnedHourly = $earnedHourly;

		$week->id = $id;
		$summary->weeks[] = $week;
	}
	$stmt->free_result();
	$stmt->close();

	$stmt = $db->prepare('CALL calculateWeeklySummary(?,?,NULL)');
	$stmt->bind_param('ss', $p_dateFrom, $p_dateTo);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$summary->summary->count = $row['count'];
	$summary->summary->avgShifts = $row['avgShifts'];
	$summary->summary->totShifts = $row['totShifts'];
	$summary->summary->avgHours = $row['avgHours'];
	$summary->summary->totHours = $row['totHours'];
	$summary->summary->avgWage = $row['avgWage'];
	$summary->summary->totWage = $row['totWage'];
	$summary->summary->avgTips = $row['avgTips'];
	$summary->summary->totTips = $row['totTips'];
	$summary->summary->avgEarned = $row['avgEarned'];
	$summary->summary->totEarned = $row['totEarned'];
	$summary->summary->avgTipout = $row['avgTipout'];
	$summary->summary->totTipout = $row['totTipout'];
	$summary->summary->avgSales = $row['avgSales'];
	$summary->summary->totSales = $row['totSales'];
	$summary->summary->avgCovers = $row['avgCovers'];
	$summary->summary->totCovers = $row['totCovers'];
	$summary->summary->avgCampHours = $row['avgCampHours'];
	$summary->summary->totCampHours = $row['totCampHours'];
	$summary->summary->salesPerHour = $row['salesPerHour'];
	$summary->summary->salesPerCover = $row['salesPerCover'];
	$summary->summary->tipsPercent = $row['tipsPercent'];
	$summary->summary->tipoutPercent = $row['tipoutPercent'];
	$summary->summary->tipsVsWage = $row['tipsVsWage'];
	$summary->summary->hourly = $row['hourly'];
	$stmt->free_result();
	$stmt->close();

	echo json_encode($summary);
?>
