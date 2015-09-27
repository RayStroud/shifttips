<?php
	include 'include/db.php';

	function getSummary($db, $p_dateFrom, $p_dateTo)
	{
		$stmt = $db->prepare('CALL getSummary(?,?)');
		$stmt->bind_param('ss', $p_dateFrom, $p_dateTo);
		$stmt->execute(); 
		$stmt->store_result();
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
	function getSummaryByLunchDinner($db, $p_dateFrom, $p_dateTo)
	{
		$stmt = $db->prepare('CALL getSummaryByLunchDinner(?,?)');
		$stmt->bind_param('ss', $p_dateFrom, $p_dateTo);
		$stmt->execute(); 
		$stmt->store_result();
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
	function getSummaryBySection($db, $p_dateFrom, $p_dateTo)
	{
		$stmt = $db->prepare('CALL getSummaryBySection(?,?)');
		$stmt->bind_param('ss', $p_dateFrom, $p_dateTo);
		$stmt->execute(); 
		$stmt->store_result();
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
	function getSummaryByStartTime($db, $p_dateFrom, $p_dateTo)
	{
		$stmt = $db->prepare('CALL getSummaryByStartTime(?,?)');
		$stmt->bind_param('ss', $p_dateFrom, $p_dateTo);
		$stmt->execute(); 
		$stmt->store_result();
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
	function getSummaryByCut($db, $p_dateFrom, $p_dateTo)
	{
		$stmt = $db->prepare('CALL getSummaryByCut(?,?)');
		$stmt->bind_param('ss', $p_dateFrom, $p_dateTo);
		$stmt->execute(); 
		$stmt->store_result();
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
	function getSummaryByDayOfWeek($db, $p_dateFrom, $p_dateTo)
	{
		$stmt = $db->prepare('CALL getSummaryByDayOfWeek(?,?)');
		$stmt->bind_param('ss', $p_dateFrom, $p_dateTo);
		$stmt->execute(); 
		$stmt->store_result();
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
	function getWeeks($db, $p_dateFrom, $p_dateTo, $p_lunchDinner)
	{
		$stmt = $db->prepare('CALL getWeeks(?,?,?);');
		$stmt->bind_param('sss', $p_dateFrom, $p_dateTo, $p_lunchDinner);
		#$stmt = $db->prepare('SELECT yearweek, startWeek, endWeek, shifts, campHours, sales, tipout, transfers, covers, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly FROM week;');
		$stmt->execute(); 
		$stmt->store_result();
		$stmt->bind_result($yearweek, $startWeek, $endWeek, $shifts, $campHours, $sales, $tipout, $transfers, $covers, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $hourly);
		$weeks = [];
		while($stmt->fetch())
		{
			$week = new stdClass();
			$week->yearweek 		= $yearweek;
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
			$weeks[] = $week;
		}
		$stmt->free_result();
		$stmt->close();

		echo json_encode($weeks);
	}
	function getSummaryWeeklySingle($db, $p_dateFrom, $p_dateTo, $p_lunchDinner)
	{
		$stmt = $db->prepare('CALL getSummaryWeekly(?,?,?);');
		$stmt->bind_param('sss', $p_dateFrom, $p_dateTo, $p_lunchDinner);
		$stmt->execute(); 
		$stmt->store_result();
		$stmt->bind_result($count, $avgShifts, $totShifts, $avgHours, $totHours, $avgWage, $totWage, $avgTips, $totTips, $avgEarned, $totEarned, $avgTipout, $totTipout, $avgSales, $totSales, $avgCovers, $totCovers, $avgCampHours, $totCampHours, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $tipsVsWage, $hourly);
		$stmt->fetch();
		$summary = new stdClass();
		$summary->count 		= (int) 	$count;
		$summary->avgShifts 	= (float) 	$avgShifts;
		$summary->totShifts 	= (int) 	$totShifts;
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
	function getSummaryWeeklyOld($db, $p_dateFrom, $p_dateTo, $p_lunchDinner)
	{
		$return = new stdClass();
		$return->weeks = [];
		$return->summary = new stdClass();

		if($stmt = $db->prepare('CALL getWeeks(?,?,?);'))
		#if($stmt = $db->prepare('SELECT yearweek, startWeek, endWeek, shifts, campHours, sales, tipout, transfers, covers, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly FROM week;'))
		{
			$stmt->bind_param('sss', $p_dateFrom, $p_dateTo, $p_lunchDinner);
			
			$stmt->execute(); 
			$stmt->store_result();
			$stmt->bind_result($yearweek, $startWeek, $endWeek, $shifts, $campHours, $sales, $tipout, $transfers, $covers, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $hourly);
			while($stmt->fetch())
			{
				$week = new stdClass();
				$week->yearweek 		= $yearweek;
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
				$return->weeks[] = $week;
			}
			$stmt->free_result();
			$stmt->close();
		}

		while($db->more_results()) { $db->next_result(); }

		if($stmt = $db->prepare('CALL getSummaryWeekly(?,?,?);'))
		{
			$stmt->bind_param('sss', $p_dateFrom, $p_dateTo, $p_lunchDinner);
			$stmt->execute(); 
			$stmt->store_result();
			$stmt->bind_result($count, $avgShifts, $totShifts, $avgHours, $totHours, $avgWage, $totWage, $avgTips, $totTips, $avgEarned, $totEarned, $avgTipout, $totTipout, $avgSales, $totSales, $avgCovers, $totCovers, $avgCampHours, $totCampHours, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $tipsVsWage, $hourly);
			$stmt->fetch();
			$return->summary->count 		= (int) 	$count;
			$return->summary->avgShifts 	= (float) 	$avgShifts;
			$return->summary->totShifts 	= (int) 	$totShifts;
			$return->summary->avgHours 		= (float) 	$avgHours;
			$return->summary->totHours 		= (float) 	$totHours;
			$return->summary->avgWage 		= (float) 	$avgWage;
			$return->summary->totWage 		= (float) 	$totWage;
			$return->summary->avgTips 		= (float) 	$avgTips;
			$return->summary->totTips 		= (int) 	$totTips;
			$return->summary->avgEarned 	= (float) 	$avgEarned;
			$return->summary->totEarned 	= (float) 	$totEarned;
			$return->summary->avgTipout 	= (float) 	$avgTipout;
			$return->summary->totTipout 	= (int) 	$totTipout;
			$return->summary->avgSales 		= (float) 	$avgSales;
			$return->summary->totSales 		= (float) 	$totSales;
			$return->summary->avgCovers 	= (float) 	$avgCovers;
			$return->summary->totCovers 	= (int) 	$totCovers;
			$return->summary->avgCampHours 	= (float) 	$avgCampHours;
			$return->summary->totCampHours 	= (float) 	$totCampHours;
			$return->summary->salesPerHour 	= (float) 	$salesPerHour;
			$return->summary->salesPerCover = (float) 	$salesPerCover;
			$return->summary->tipsPercent 	= (float) 	$tipsPercent;
			$return->summary->tipoutPercent = (float) 	$tipoutPercent;
			$return->summary->tipsVsWage 	= (int) 	$tipsVsWage;
			$return->summary->hourly 		= (float) 	$hourly;
			$stmt->free_result();
			$stmt->close();

			echo json_encode($return);
		}
	}
	function getSummaryWeekly($db, $p_dateFrom, $p_dateTo, $p_lunchDinner)
	{
		$return = new stdClass();
		$return->list = [];
		$return->summary = new stdClass();
		
		if ($stmt = $db->prepare('SET @p_dateFrom = ?, @p_dateTo = ?, @p_lunchDinner = ?;'))
		{
			$stmt->bind_param('sss', $p_dateFrom, $p_dateTo, $p_lunchDinner);
			$stmt->execute(); 
			$stmt->store_result();
			$stmt->free_result();
			$stmt->close();	
		}

		if($result = $db->query('CALL getWeeks(@p_dateFrom, @p_dateTo, @p_lunchDinner);'))
		{
			while($row = $result->fetch_assoc())
			{
				$week = new stdClass();
				$week->yearweek 		= $row['yearweek'];
				$week->startWeek 		= $row['startWeek'];
				$week->endWeek 			= $row['endWeek'];
				$week->shifts 			= (int)		$row['shifts'];

				$week->campHours 		= (float)	$row['campHours'];
				$week->sales 			= (float)	$row['sales'];
				$week->tipout 			= (int)		$row['tipout'];
				$week->transfers 		= (int)		$row['transfers'];
				$week->covers 			= (int)		$row['covers'];

				$week->hours 			= (float)	$row['hours'];
				$week->earnedWage 		= (float)	$row['earnedWage'];
				$week->earnedTips 		= (int)		$row['earnedTips'];
				$week->earnedTotal 		= (float)	$row['earnedTotal'];

				$week->tipsVsWage 		= (int)		$row['tipsVsWage'];
				$week->salesPerHour 	= (float)	$row['salesPerHour'];
				$week->salesPerCover 	= (float)	$row['salesPerCover'];
				$week->tipsPercent 		= (float)	$row['tipsPercent'];
				$week->tipoutPercent 	= (float)	$row['tipoutPercent'];
				$week->hourly 			= (float)	$row['hourly'];
				$return->list[] = $week;
			}
			$result->free();
		}

		while($db->more_results()) { $db->next_result(); }

		if($result = $db->query('CALL getSummaryWeekly(@p_dateFrom, @p_dateTo, @p_lunchDinner);'))
		{
			$row = $result->fetch_assoc();
			$return->summary->count 		= (int) 	$row['count'];
			$return->summary->avgShifts 	= (float) 	$row['avgShifts'];
			$return->summary->totShifts 	= (int) 	$row['totShifts'];
			$return->summary->avgHours 		= (float) 	$row['avgHours'];
			$return->summary->totHours 		= (float) 	$row['totHours'];
			$return->summary->avgWage 		= (float) 	$row['avgWage'];
			$return->summary->totWage 		= (float) 	$row['totWage'];
			$return->summary->avgTips 		= (float) 	$row['avgTips'];
			$return->summary->totTips 		= (int) 	$row['totTips'];
			$return->summary->avgEarned 	= (float) 	$row['avgEarned'];
			$return->summary->totEarned 	= (float) 	$row['totEarned'];
			$return->summary->avgTipout 	= (float) 	$row['avgTipout'];
			$return->summary->totTipout 	= (int) 	$row['totTipout'];
			$return->summary->avgSales 		= (float) 	$row['avgSales'];
			$return->summary->totSales 		= (float) 	$row['totSales'];
			$return->summary->avgCovers 	= (float) 	$row['avgCovers'];
			$return->summary->totCovers 	= (int) 	$row['totCovers'];
			$return->summary->avgCampHours 	= (float) 	$row['avgCampHours'];
			$return->summary->totCampHours 	= (float) 	$row['totCampHours'];
			$return->summary->salesPerHour 	= (float) 	$row['salesPerHour'];
			$return->summary->salesPerCover = (float) 	$row['salesPerCover'];
			$return->summary->tipsPercent 	= (float) 	$row['tipsPercent'];
			$return->summary->tipoutPercent = (float) 	$row['tipoutPercent'];
			$return->summary->tipsVsWage 	= (int) 	$row['tipsVsWage'];
			$return->summary->hourly 		= (float) 	$row['hourly'];
			$result->free();
		}

		echo json_encode($return);
	}
	function getSummaryMonthly($db, $p_dateFrom, $p_dateTo, $p_lunchDinner)
	{
		
	}
	
	try
	{	
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

		if(isset($_GET['lunchDinner']) 
			|| isset($_GET['shift']))
		{
			getSummaryByLunchDinner($db, $p_dateFrom, $p_dateTo);
		}
		else if(isset($_GET['day']) 
			|| isset($_GET['dayOfWeek']) 
			|| isset($_GET['days']) 
			|| isset($_GET['daily']))
		{
			getSummaryByDayOfWeek($db, $p_dateFrom, $p_dateTo);
		}
		else if(isset($_GET['section']))
		{
			getSummaryBySection($db, $p_dateFrom, $p_dateTo);
		}
		else if(isset($_GET['startTime']))
		{
			getSummaryByStartTime($db, $p_dateFrom, $p_dateTo);
		}
		else if(isset($_GET['cut']))
		{
			getSummaryByCut($db, $p_dateFrom, $p_dateTo);
		}
		else if(isset($_GET['week']) 
			|| isset($_GET['weeks']) 
			|| isset($_GET['weekly']))
		{
			getSummaryWeekly($db, $p_dateFrom, $p_dateTo, $p_lunchDinner);
		}
		else if(isset($_GET['month']) 
			|| isset($_GET['months']) 
			|| isset($_GET['monthly']))
		{
			getSummaryMonthly($db, $p_dateFrom, $p_dateTo, $p_lunchDinner);
		}
		else
		{
			getSummary($db, $p_dateFrom, $p_dateTo);
		}
		$db->close();
	}
	catch (Exception $e) 
	{
		http_response_code(500);
		die();
	}
?>
