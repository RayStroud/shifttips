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
		$week->startWeek 		= $startWeek;
		$week->endWeek 			= $endWeek;
		$week->shifts 			= (int)		$shifts;
		$week->campHours 		= (float)	$campHours;
		$week->sales 			= (float)	$sales;
		$week->tipout 			= (int)		$tipout;
		$week->transfers 		= (int)		$transfers;
		$week->covers 			= (int)		$covers;

		$week->hours 			= (float)	$hours;
		$week->earnedWage 		= (float)	$earnedWage;
		$week->earnedTips 		= (int)		$earnedTips;
		$week->earnedTotal 		= (float)	$earnedTotal;
		$week->tipsVsWage 		= (int)		$tipsVsWage;
		$week->salesPerHour 	= (float)	$salesPerHour;
		$week->salesPerCover 	= (float)	$salesPerCover;
		$week->tipsPercent 		= (float)	$tipsPercent;
		$week->tipoutPercent 	= (float)	$tipoutPercent;
		$week->earnedHourly 	= (float)	$earnedHourly;

		$week->id 				= (int)		$id;
		$summary->weeks[] = $week;
	}
	$stmt->free_result();
	$stmt->close();

	$stmt = $db->prepare('CALL calculateWeeklySummary(?,?,NULL)');
	$stmt->bind_param('ss', $p_dateFrom, $p_dateTo);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$summary->summary = new stdClass();
	$summary->summary->count 			= (int) 	$row['count'];
	$summary->summary->avgShifts 		= (float) 	$row['avgShifts'];
	$summary->summary->totShifts 		= (int) 	$row['totShifts'];
	$summary->summary->avgHours 		= (float) 	$row['avgHours'];
	$summary->summary->totHours 		= (float) 	$row['totHours'];
	$summary->summary->avgWage 			= (float) 	$row['avgWage'];
	$summary->summary->totWage 			= (float) 	$row['totWage'];
	$summary->summary->avgTips 			= (float) 	$row['avgTips'];
	$summary->summary->totTips 			= (int) 	$row['totTips'];
	$summary->summary->avgEarned 		= (float) 	$row['avgEarned'];
	$summary->summary->totEarned 		= (float) 	$row['totEarned'];
	$summary->summary->avgTipout 		= (float) 	$row['avgTipout'];
	$summary->summary->totTipout 		= (int) 	$row['totTipout'];
	$summary->summary->avgSales 		= (float) 	$row['avgSales'];
	$summary->summary->totSales 		= (float) 	$row['totSales'];
	$summary->summary->avgCovers 		= (float) 	$row['avgCovers'];
	$summary->summary->totCovers 		= (int) 	$row['totCovers'];
	$summary->summary->avgCampHours 	= (float) 	$row['avgCampHours'];
	$summary->summary->totCampHours 	= (float) 	$row['totCampHours'];
	$summary->summary->salesPerHour 	= (float) 	$row['salesPerHour'];
	$summary->summary->salesPerCover 	= (float) 	$row['salesPerCover'];
	$summary->summary->tipsPercent 		= (float) 	$row['tipsPercent'];
	$summary->summary->tipoutPercent 	= (float) 	$row['tipoutPercent'];
	$summary->summary->tipsVsWage 		= (int) 	$row['tipsVsWage'];
	$summary->summary->hourly 			= (float) 	$row['hourly'];
	$stmt->free_result();
	$stmt->close();

	echo json_encode($summary);
?>
