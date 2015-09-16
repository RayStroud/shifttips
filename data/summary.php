<?php
	include 'include/db.php';

	function getSummary($db)
	{
		$stmt = $db->prepare('CALL getSummary(NULL,NULL)');
		$stmt->execute();
		$stmt->bind_result($count, $avgHours, $totHours, $avgWage, $totWage, $avgTips, $totTips, $avgEarned, $totEarned, $avgTipout, $totTipout, $avgSales, $totSales, $avgCovers, $totCovers, $avgCampHours, $totCampHours, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $tipsVsWage, $hourly);
		$stmt->fetch();
		$summary = new stdClass();
		$summary->count 		= (int) 	$count;
		$summary->avgHours 		= (float) 	$avgHours;
		$summary->totHours 		= (float) 	$totHours;
		$summary->avgWage 		= (float) 	$avgWage;
		$summary->totWage 		= (float) 	$totWage;
		$summary->avgTips 		= (float) 	$avgTips;
		$summary->totTips 		= (int) 	$totTips;
		$summary->avgEarned 	= (float) 	$avgEarned;
		$summary->totEarned 	= (float) 	$totEarned;
		$summary->avgTipout 	= (float) 	$avgTipout;
		$summary->totTipout 	= (int) 	$totTipout;
		$summary->avgSales 		= (float) 	$avgSales;
		$summary->totSales 		= (float) 	$totSales;
		$summary->avgCovers 	= (float) 	$avgCovers;
		$summary->totCovers 	= (int) 	$totCovers;
		$summary->avgCampHours 	= (float) 	$avgCampHours;
		$summary->totCampHours 	= (float) 	$totCampHours;
		$summary->salesPerHour 	= (float) 	$salesPerHour;
		$summary->salesPerCover = (float) 	$salesPerCover;
		$summary->tipsPercent 	= (float) 	$tipsPercent;
		$summary->tipoutPercent = (float) 	$tipoutPercent;
		$summary->tipsVsWage 	= (int) 	$tipsVsWage;
		$summary->hourly 		= (float) 	$hourly;
		$stmt->free_result();
		$stmt->close();

		echo json_encode($summary);
	}
	function getSummaryByLunchDinner($db)
	{
		$stmt = $db->prepare('CALL getSummaryByLunchDinner(NULL,NULL)');
		$stmt->execute();
		$stmt->bind_result($lunchDinner, $count, $avgHours, $totHours, $avgWage, $totWage, $avgTips, $totTips, $avgEarned, $totEarned, $avgTipout, $totTipout, $avgSales, $totSales, $avgCovers, $totCovers, $avgCampHours, $totCampHours, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $tipsVsWage, $hourly);
		$summaries = [];
		while($stmt->fetch())
		{
			$summary = new stdClass();
			$summary->lunchDinner 	= $lunchDinner;
			$summary->count 		= (int) 	$count;
			$summary->avgHours 		= (float) 	$avgHours;
			$summary->totHours 		= (float) 	$totHours;
			$summary->avgWage 		= (float) 	$avgWage;
			$summary->totWage 		= (float) 	$totWage;
			$summary->avgTips 		= (float) 	$avgTips;
			$summary->totTips 		= (int) 	$totTips;
			$summary->avgEarned 	= (float) 	$avgEarned;
			$summary->totEarned 	= (float) 	$totEarned;
			$summary->avgTipout 	= (float) 	$avgTipout;
			$summary->totTipout 	= (int) 	$totTipout;
			$summary->avgSales 		= (float) 	$avgSales;
			$summary->totSales 		= (float) 	$totSales;
			$summary->avgCovers 	= (float) 	$avgCovers;
			$summary->totCovers 	= (int) 	$totCovers;
			$summary->avgCampHours 	= (float) 	$avgCampHours;
			$summary->totCampHours 	= (float) 	$totCampHours;
			$summary->salesPerHour 	= (float) 	$salesPerHour;
			$summary->salesPerCover = (float) 	$salesPerCover;
			$summary->tipsPercent 	= (float) 	$tipsPercent;
			$summary->tipoutPercent = (float) 	$tipoutPercent;
			$summary->tipsVsWage 	= (int) 	$tipsVsWage;
			$summary->hourly 		= (float) 	$hourly;

			$summaries[] = $summary;
		}
		$stmt->free_result();
		$stmt->close();

		echo json_encode($summaries);
	}
	function getSummaryBySection($db)
	{
		$stmt = $db->prepare('CALL getSummaryBySection(NULL,NULL)');
		$stmt->execute();
		$stmt->bind_result($section, $lunchDinner, $count, $avgHours, $totHours, $avgWage, $totWage, $avgTips, $totTips, $avgEarned, $totEarned, $avgTipout, $totTipout, $avgSales, $totSales, $avgCovers, $totCovers, $avgCampHours, $totCampHours, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $tipsVsWage, $hourly);
		$summaries = [];
		while($stmt->fetch())
		{
			$summary = new stdClass();
			$summary->section 		= $section;
			$summary->lunchDinner 	= $lunchDinner;
			$summary->count 		= (int) 	$count;
			$summary->avgHours 		= (float) 	$avgHours;
			$summary->totHours 		= (float) 	$totHours;
			$summary->avgWage 		= (float) 	$avgWage;
			$summary->totWage 		= (float) 	$totWage;
			$summary->avgTips 		= (float) 	$avgTips;
			$summary->totTips 		= (int) 	$totTips;
			$summary->avgEarned 	= (float) 	$avgEarned;
			$summary->totEarned 	= (float) 	$totEarned;
			$summary->avgTipout 	= (float) 	$avgTipout;
			$summary->totTipout 	= (int) 	$totTipout;
			$summary->avgSales 		= (float) 	$avgSales;
			$summary->totSales 		= (float) 	$totSales;
			$summary->avgCovers 	= (float) 	$avgCovers;
			$summary->totCovers 	= (int) 	$totCovers;
			$summary->avgCampHours 	= (float) 	$avgCampHours;
			$summary->totCampHours 	= (float) 	$totCampHours;
			$summary->salesPerHour 	= (float) 	$salesPerHour;
			$summary->salesPerCover = (float) 	$salesPerCover;
			$summary->tipsPercent 	= (float) 	$tipsPercent;
			$summary->tipoutPercent = (float) 	$tipoutPercent;
			$summary->tipsVsWage 	= (int) 	$tipsVsWage;
			$summary->hourly 		= (float) 	$hourly;

			$summaries[] = $summary;
		}
		$stmt->free_result();
		$stmt->close();

		echo json_encode($summaries);
	}
	function getSummaryByStartTime($db)
	{
		$stmt = $db->prepare('CALL getSummaryByStartTime(NULL,NULL)');
		$stmt->execute();
		$stmt->bind_result($startTime, $lunchDinner, $count, $avgHours, $totHours, $avgWage, $totWage, $avgTips, $totTips, $avgEarned, $totEarned, $avgTipout, $totTipout, $avgSales, $totSales, $avgCovers, $totCovers, $avgCampHours, $totCampHours, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $tipsVsWage, $hourly);
		$summaries = [];
		while($stmt->fetch())
		{
			$summary = new stdClass();
			$summary->startTime 	= $startTime;
			$summary->lunchDinner 	= $lunchDinner;
			$summary->count 		= (int) 	$count;
			$summary->avgHours 		= (float) 	$avgHours;
			$summary->totHours 		= (float) 	$totHours;
			$summary->avgWage 		= (float) 	$avgWage;
			$summary->totWage 		= (float) 	$totWage;
			$summary->avgTips 		= (float) 	$avgTips;
			$summary->totTips 		= (int) 	$totTips;
			$summary->avgEarned 	= (float) 	$avgEarned;
			$summary->totEarned 	= (float) 	$totEarned;
			$summary->avgTipout 	= (float) 	$avgTipout;
			$summary->totTipout 	= (int) 	$totTipout;
			$summary->avgSales 		= (float) 	$avgSales;
			$summary->totSales 		= (float) 	$totSales;
			$summary->avgCovers 	= (float) 	$avgCovers;
			$summary->totCovers 	= (int) 	$totCovers;
			$summary->avgCampHours 	= (float) 	$avgCampHours;
			$summary->totCampHours 	= (float) 	$totCampHours;
			$summary->salesPerHour 	= (float) 	$salesPerHour;
			$summary->salesPerCover = (float) 	$salesPerCover;
			$summary->tipsPercent 	= (float) 	$tipsPercent;
			$summary->tipoutPercent = (float) 	$tipoutPercent;
			$summary->tipsVsWage 	= (int) 	$tipsVsWage;
			$summary->hourly 		= (float) 	$hourly;

			$summaries[] = $summary;
		}
		$stmt->free_result();
		$stmt->close();

		echo json_encode($summaries);
	}
	function getSummaryByCut($db)
	{
		$stmt = $db->prepare('CALL getSummaryByCut(NULL,NULL)');
		$stmt->execute();
		$stmt->bind_result($cut, $lunchDinner, $count, $avgHours, $totHours, $avgWage, $totWage, $avgTips, $totTips, $avgEarned, $totEarned, $avgTipout, $totTipout, $avgSales, $totSales, $avgCovers, $totCovers, $avgCampHours, $totCampHours, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $tipsVsWage, $hourly);
		$summaries = [];
		while($stmt->fetch())
		{
			$summary = new stdClass();
			$summary->cut 			= $cut;
			$summary->lunchDinner 	= $lunchDinner;
			$summary->count 		= (int) 	$count;
			$summary->avgHours 		= (float) 	$avgHours;
			$summary->totHours 		= (float) 	$totHours;
			$summary->avgWage 		= (float) 	$avgWage;
			$summary->totWage 		= (float) 	$totWage;
			$summary->avgTips 		= (float) 	$avgTips;
			$summary->totTips 		= (int) 	$totTips;
			$summary->avgEarned 	= (float) 	$avgEarned;
			$summary->totEarned 	= (float) 	$totEarned;
			$summary->avgTipout 	= (float) 	$avgTipout;
			$summary->totTipout 	= (int) 	$totTipout;
			$summary->avgSales 		= (float) 	$avgSales;
			$summary->totSales 		= (float) 	$totSales;
			$summary->avgCovers 	= (float) 	$avgCovers;
			$summary->totCovers 	= (int) 	$totCovers;
			$summary->avgCampHours 	= (float) 	$avgCampHours;
			$summary->totCampHours 	= (float) 	$totCampHours;
			$summary->salesPerHour 	= (float) 	$salesPerHour;
			$summary->salesPerCover = (float) 	$salesPerCover;
			$summary->tipsPercent 	= (float) 	$tipsPercent;
			$summary->tipoutPercent = (float) 	$tipoutPercent;
			$summary->tipsVsWage 	= (int) 	$tipsVsWage;
			$summary->hourly 		= (float) 	$hourly;

			$summaries[] = $summary;
		}
		$stmt->free_result();
		$stmt->close();

		echo json_encode($summaries);
	}
	function getSummaryByDayOfWeek($db)
	{
		$stmt = $db->prepare('CALL getSummaryByDayOfWeek(NULL,NULL)');
		$stmt->execute();
		$stmt->bind_result($weekday, $dayOfWeek, $lunchDinner, $count, $avgHours, $totHours, $avgWage, $totWage, $avgTips, $totTips, $avgEarned, $totEarned, $avgTipout, $totTipout, $avgSales, $totSales, $avgCovers, $totCovers, $avgCampHours, $totCampHours, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $tipsVsWage, $hourly);
		$summaries = [];
		while($stmt->fetch())
		{
			$summary = new stdClass();
			$summary->weekday 		= $weekday;
			$summary->dayOfWeek 	= $dayOfWeek;
			$summary->lunchDinner 	= $lunchDinner;
			$summary->count 		= (int) 	$count;
			$summary->avgHours 		= (float) 	$avgHours;
			$summary->totHours 		= (float) 	$totHours;
			$summary->avgWage 		= (float) 	$avgWage;
			$summary->totWage 		= (float) 	$totWage;
			$summary->avgTips 		= (float) 	$avgTips;
			$summary->totTips 		= (int) 	$totTips;
			$summary->avgEarned 	= (float) 	$avgEarned;
			$summary->totEarned 	= (float) 	$totEarned;
			$summary->avgTipout 	= (float) 	$avgTipout;
			$summary->totTipout 	= (int) 	$totTipout;
			$summary->avgSales 		= (float) 	$avgSales;
			$summary->totSales 		= (float) 	$totSales;
			$summary->avgCovers 	= (float) 	$avgCovers;
			$summary->totCovers 	= (int) 	$totCovers;
			$summary->avgCampHours 	= (float) 	$avgCampHours;
			$summary->totCampHours 	= (float) 	$totCampHours;
			$summary->salesPerHour 	= (float) 	$salesPerHour;
			$summary->salesPerCover = (float) 	$salesPerCover;
			$summary->tipsPercent 	= (float) 	$tipsPercent;
			$summary->tipoutPercent = (float) 	$tipoutPercent;
			$summary->tipsVsWage 	= (int) 	$tipsVsWage;
			$summary->hourly 		= (float) 	$hourly;

			$summaries[] = $summary;
		}
		$stmt->free_result();
		$stmt->close();

		echo json_encode($summaries);
	}
	function getSummaryWeeks($db)
	{
		$stmt = $db->prepare('SELECT startWeek, endWeek, count, campHours, sales, tipout, transfers, covers, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, id 
			FROM week 
			#WHERE startWeek BETWEEN ? AND ? OR endWeek BETWEEN ? and ?
			;');
		#$stmt->bind_param('ssss', $p_dateFrom, $p_dateTo, $p_dateFrom, $p_dateTo);
		$stmt->execute();
		$stmt->bind_result($startWeek, $endWeek, $shifts, $campHours, $sales, $tipout, $transfers, $covers, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $hourly, $id);
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
			$week->hourly 			= (float)	$hourly;

			$week->id 				= (int)		$id;
			$summary->weeks[] = $week;
		}
		$stmt->free_result();
		$stmt->close();

		$stmt = $db->prepare('CALL getSummaryWeekly(NULL,NULL)');
		#$stmt->bind_param('ss', $p_dateFrom, $p_dateTo);
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
	}
	function getSummaryMonths($db)
	{
		
	}
	
	try
	{	
		//extract dates if set, or use defaults
		try { $dateTimeFrom = !empty($_GET['from']) ? new DateTime($_GET['from']) 	: null; } catch(Exception $e) { $dateTimeFrom 	= null; }
		try { $dateTimeTo 	= !empty($_GET['to']) 	? new DateTime($_GET['to']) 	: null; } catch(Exception $e) { $dateTimeTo 	= null; }
		$p_dateFrom = !empty($dateTimeFrom) ? $dateTimeFrom->format("Y-m-d") 	: '1000-01-01'; 
		$p_dateTo 	= !empty($dateTimeTo) 	? $dateTimeTo->format("Y-m-d") 		: '9999-12-31'; 
		//* DEBUG */ echo '<p>|dateFrom:' . $p_dateFrom . '|dateTo:' . $p_dateTo . '|</p>';

		if(isset($_GET['lunchDinner']) 
			|| isset($_GET['shift']))
		{
			getSummaryByLunchDinner($db);
		}
		else if(isset($_GET['day']) 
			|| isset($_GET['dayOfWeek']) 
			|| isset($_GET['days']) 
			|| isset($_GET['daily']))
		{
			getSummaryByDayOfWeek($db);
		}
		else if(isset($_GET['section']))
		{
			getSummaryBySection($db);
		}
		else if(isset($_GET['startTime']))
		{
			getSummaryByStartTime($db);
		}
		else if(isset($_GET['cut']))
		{
			getSummaryByCut($db);
		}
		else if(isset($_GET['week']) 
			|| isset($_GET['weeks']) 
			|| isset($_GET['weekly']))
		{
			getSummaryWeeks($db);
		}
		else if(isset($_GET['month']) 
			|| isset($_GET['months']) 
			|| isset($_GET['monthly']))
		{
			getSummaryMonths($db);
		}
		else
		{
			getSummary($db);
		}
		$db->close();
	}
	catch (Exception $e) 
	{
		http_response_code(500);
		die();
	}
?>
