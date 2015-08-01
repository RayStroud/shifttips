<?php
	include 'db.php';

	$stmt = $db->prepare('SELECT startWeek, endWeek, count, campHours, sales, tipout, transfers, covers, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, id FROM week;');
	$stmt->execute();
	$stmt->bind_result($startWeek, $endWeek, $shifts, $campHours, $sales, $tipout, $transfers, $covers, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $earnedHourly, $id);
	$summary = new stdClass();
	$summary->weeks = [];
	$summary->total = new stdClass();
	$summary->total->count = 0;
	$summary->total->shifts = 0;
	$summary->total->campHours = 0;
	$summary->total->sales = 0;
	$summary->total->tipout = 0;
	$summary->total->transfers = 0;
	$summary->total->covers = 0;
	$summary->total->hours = 0;
	$summary->total->earnedWage = 0;
	$summary->total->earnedTips = 0;
	$summary->total->earnedTotal = 0;
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

		$summary->total->count++;
		$summary->total->shifts += $shifts;
		$summary->total->campHours += $campHours;
		$summary->total->sales += $sales;
		$summary->total->tipout += $tipout;
		$summary->total->transfers += $transfers;
		$summary->total->covers += $covers;
		$summary->total->hours += $hours;
		$summary->total->earnedWage += $earnedWage;
		$summary->total->earnedTips += $earnedTips;
		$summary->total->earnedTotal += $earnedTotal;
	}
	$stmt->free_result();
	$stmt->close();

	$summary->average->shifts 		= number_format($summary->total->shifts 		/ $summary->total->count,1,'.','');
	$summary->average->campHours 	= number_format($summary->total->campHours 		/ $summary->total->count,2,'.','');
	$summary->average->sales 		= number_format($summary->total->sales 			/ $summary->total->count,2,'.','');
	$summary->average->tipout 		= number_format($summary->total->tipout 		/ $summary->total->count,1,'.','');
	$summary->average->transfers 	= number_format($summary->total->transfers 		/ $summary->total->count,1,'.','');
	$summary->average->covers 		= number_format($summary->total->covers 		/ $summary->total->count,1,'.','');
	$summary->average->hours 		= number_format($summary->total->hours 			/ $summary->total->count,2,'.','');
	$summary->average->earnedWage 	= number_format($summary->total->earnedWage 	/ $summary->total->count,2,'.','');
	$summary->average->earnedTips 	= number_format($summary->total->earnedTips 	/ $summary->total->count,2,'.','');
	$summary->average->earnedTotal 	= number_format($summary->total->earnedTotal 	/ $summary->total->count,2,'.','');

	$summary->average->tipsVsWage 		= number_format(($summary->total->earnedTips * 100) / $summary->total->earnedWage,0,'.','');
	$summary->average->salesPerHour 	= number_format($summary->total->sales / $summary->total->hours,2,'.','');
	$summary->average->salesPerCover 	= number_format($summary->total->sales / $summary->total->covers,2,'.','');
	$summary->average->tipsPercent 		= number_format(($summary->total->earnedTips * 100) / $summary->total->sales,1,'.','');
	$summary->average->tipoutPercent 	= number_format(($summary->total->tipout * 100) / $summary->total->sales,1,'.','');
	$summary->average->earnedHourly 	= number_format($summary->total->earnedTotal / $summary->total->hours,2,'.','');

	$summary->total->campHours 		= number_format($summary->total->campHours,2,'.','');
	$summary->total->sales 			= number_format($summary->total->sales,2,'.','');
	$summary->total->tipout 		= number_format($summary->total->tipout,0,'.','');
	$summary->total->transfers 		= number_format($summary->total->transfers,0,'.','');
	$summary->total->covers 		= number_format($summary->total->covers,0,'.','');
	$summary->total->hours 			= number_format($summary->total->hours,2,'.','');
	$summary->total->earnedWage 	= number_format($summary->total->earnedWage,0,'.','');
	$summary->total->earnedTips 	= number_format($summary->total->earnedTips,0,'.','');
	$summary->total->earnedTotal 	= number_format($summary->total->earnedTotal,0,'.','');

	echo json_encode($summary);
?>
