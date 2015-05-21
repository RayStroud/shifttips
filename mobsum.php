<?php
	include 'include/dbconnect.php';

	//get startDate and endDate
	$startDate = '2014-10-20 11:45:00';
	$endDate = '2015-04-30 21:00:00';

	//set up variables in database
	$db->query("SET @startDate = '" . $db->real_escape_string($startDate) . "';");
	$db->query("SET @endDate = '" . $db->real_escape_string($endDate) . "';");

	//calculate summaries
	$summariesResult = $db->query('CALL calculateSummaries(@startDate, @endDate);');

	$summaries = []; 
	while($row = $summariesResult->fetch_assoc())
	{
		$lunchDinner = $row['lunchDinner'];
		$dayOfWeek = $row['dayOfWeek'];

		$summaries[$lunchDinner][$dayOfWeek]['id'] = $row['id'];
		$summaries[$lunchDinner][$dayOfWeek]['count'] = $row['count'];
		$summaries[$lunchDinner][$dayOfWeek]['avgHours'] = $row['avgHours'];
		$summaries[$lunchDinner][$dayOfWeek]['totalHours'] = $row['totalHours'];
		$summaries[$lunchDinner][$dayOfWeek]['avgWage'] = number_format($row['avgWage'],2);
		$summaries[$lunchDinner][$dayOfWeek]['totalWage'] = number_format($row['totalWage'],0);
		$summaries[$lunchDinner][$dayOfWeek]['avgTips'] = number_format($row['avgTips'],2);
		$summaries[$lunchDinner][$dayOfWeek]['totalTips'] = number_format($row['totalTips'],0);
		$summaries[$lunchDinner][$dayOfWeek]['avgTipout'] = number_format($row['avgTipout'],2);
		$summaries[$lunchDinner][$dayOfWeek]['totalTipout'] = number_format($row['totalTipout'],0);
		$summaries[$lunchDinner][$dayOfWeek]['avgSales'] = number_format($row['avgSales'],0);
		$summaries[$lunchDinner][$dayOfWeek]['totalSales'] = number_format($row['totalSales'],0);
		$summaries[$lunchDinner][$dayOfWeek]['avgCovers'] = $row['avgCovers'];
		$summaries[$lunchDinner][$dayOfWeek]['totalCovers'] = $row['totalCovers'];
		$summaries[$lunchDinner][$dayOfWeek]['avgCampHours'] = $row['avgCampHours'];
		$summaries[$lunchDinner][$dayOfWeek]['totalCampHours'] = $row['totalCampHours'];
		$summaries[$lunchDinner][$dayOfWeek]['salesPerHour'] = number_format($row['salesPerHour'],2);
		$summaries[$lunchDinner][$dayOfWeek]['salesPerCover'] = number_format($row['salesPerCover'],2);
		$summaries[$lunchDinner][$dayOfWeek]['tipsPercent'] = $row['tipsPercent'];
		$summaries[$lunchDinner][$dayOfWeek]['tipoutPercent'] = $row['tipoutPercent'];
		$summaries[$lunchDinner][$dayOfWeek]['tipsVsWage'] = $row['tipsVsWage'];
		$summaries[$lunchDinner][$dayOfWeek]['hourlyWage'] = number_format($row['hourlyWage'],2);
		$summaries[$lunchDinner][$dayOfWeek]['timedate'] = $row['timedate'];

		$summaries[$lunchDinner][$dayOfWeek]['avgEarned'] = number_format($row['avgWage'] + $row['avgTips'],2);
		$summaries[$lunchDinner][$dayOfWeek]['totalEarned'] = number_format($row['totalWage'] + $row['totalTips'],0);		
	}

	//* DEBUG */ echo 'NUM RESULTS: ' . $summariesResult->num_rows . '<pre>'; print_r($summaries); echo '</pre>';



	//close connection
	$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Summary - Shift Tips</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<div id="header">
		<div class="name"><a href=".">Shift Tips</a></div>
		<ul class="menu">
			<li><a href="all.php">View All</a></li>
			<li><a href="add.php">Add</a></li>
			<li><a class="active" href="summary.php">Summary</a></li>
		</ul>
	</div>
	<div id="content">
		<div id="wrapper">
			<h1>Summary</h1>

			<h2>Lunch vs Dinner</h2>

			<div class="mobile-table">
				<table class="summary-table">
					<tr>
						<th class="hdr-avg-cell">Average Total</th>
						<th class="hdr-avg-cell">#</th>
						<th class="hdr-avg-cell">Hours</th>
						<th class="hdr-avg-cell">Wage</th>
						<th class="hdr-avg-cell">Tips</th>
						<th class="hdr-avg-cell">Tipout</th>
						<th class="hdr-avg-cell">Earned</th>
						<th class="hdr-avg-cell">Sales</th>
						<th class="hdr-avg-cell">Covers</th>
						<th class="hdr-avg-cell">Camp Hours</th>
						<th class="hdr-avg-cell">Sales /hour</th>
						<th class="hdr-avg-cell">Sales /cover</th>
						<th class="hdr-avg-cell">%Tips</th>
						<th class="hdr-avg-cell">%Tip Out</th>
						<th class="hdr-avg-cell">%Tips vs Wage</th>
						<th class="hdr-avg-cell">Earned /hour</th>
					</tr>
					<tr>
						<td class="lun-avg-cell" rowspan="2">Lunch</td>
						<td class="lun-avg-cell" rowspan="2"><?php echo $summaries['L']['%']['count']; ?></td>
						<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgHours'] . ' h'; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgWage']; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgTips']; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgTipout']; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgEarned']; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgSales']; ?></td>
						<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgCovers'] . ' cov'; ?></td>
						<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgCampHours'] . ' h'; ?></td>
						<td class="lun-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['%']['salesPerHour'] . '/h'; ?></td>
						<td class="lun-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['%']['salesPerCover'] . '/cov'; ?></td>
						<td class="lun-avg-cell" rowspan="2"><?php echo $summaries['L']['%']['tipsPercent'] . '%'; ?></td>
						<td class="lun-avg-cell" rowspan="2"><?php echo $summaries['L']['%']['tipoutPercent'] . '%'; ?></td>
						<td class="lun-avg-cell" rowspan="2"><?php echo $summaries['L']['%']['tipsVsWage'] . '%'; ?></td>
						<td class="lun-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['%']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="lun-tot-cell"><?php echo $summaries['L']['%']['totalHours'] . ' h'; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totalWage']; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totalTips']; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totalTipout']; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totalEarned']; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totalSales']; ?></td>
						<td class="lun-tot-cell"><?php echo $summaries['L']['%']['totalCovers'] . ' cov'; ?></td>
						<td class="lun-tot-cell"><?php echo $summaries['L']['%']['totalCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="din-avg-cell" rowspan="2">Dinner</td>
						<td class="din-avg-cell" rowspan="2"><?php echo $summaries['D']['%']['count']; ?></td>
						<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgHours'] . ' h'; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgWage']; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgTips']; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgTipout']; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgEarned']; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgSales']; ?></td>
						<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgCovers'] . ' cov'; ?></td>
						<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgCampHours'] . ' h'; ?></td>
						<td class="din-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['%']['salesPerHour'] . '/h'; ?></td>
						<td class="din-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['%']['salesPerCover'] . '/cov'; ?></td>
						<td class="din-avg-cell" rowspan="2"><?php echo $summaries['D']['%']['tipsPercent'] . '%'; ?></td>
						<td class="din-avg-cell" rowspan="2"><?php echo $summaries['D']['%']['tipoutPercent'] . '%'; ?></td>
						<td class="din-avg-cell" rowspan="2"><?php echo $summaries['D']['%']['tipsVsWage'] . '%'; ?></td>
						<td class="din-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['%']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="din-tot-cell"><?php echo $summaries['D']['%']['totalHours'] . ' h'; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totalWage']; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totalTips']; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totalTipout']; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totalEarned']; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totalSales']; ?></td>
						<td class="din-tot-cell"><?php echo $summaries['D']['%']['totalCovers'] . ' cov'; ?></td>
						<td class="din-tot-cell"><?php echo $summaries['D']['%']['totalCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="bth-avg-cell" rowspan="2">Both</td>
						<td class="bth-avg-cell" rowspan="2"><?php echo $summaries['%']['%']['count']; ?></td>
						<td class="bth-avg-cell"><?php echo $summaries['%']['%']['avgHours'] . ' h'; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgWage']; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgTips']; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgTipout']; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgEarned']; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgSales']; ?></td>
						<td class="bth-avg-cell"><?php echo $summaries['%']['%']['avgCovers'] . ' cov'; ?></td>
						<td class="bth-avg-cell"><?php echo $summaries['%']['%']['avgCampHours'] . ' h'; ?></td>
						<td class="bth-avg-cell" rowspan="2"><?php echo '$' . $summaries['%']['%']['salesPerHour'] . '/h'; ?></td>
						<td class="bth-avg-cell" rowspan="2"><?php echo '$' . $summaries['%']['%']['salesPerCover'] . '/cov'; ?></td>
						<td class="bth-avg-cell" rowspan="2"><?php echo $summaries['%']['%']['tipsPercent'] . '%'; ?></td>
						<td class="bth-avg-cell" rowspan="2"><?php echo $summaries['%']['%']['tipoutPercent'] . '%'; ?></td>
						<td class="bth-avg-cell" rowspan="2"><?php echo $summaries['%']['%']['tipsVsWage'] . '%'; ?></td>
						<td class="bth-avg-cell" rowspan="2"><?php echo '$' . $summaries['%']['%']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="bth-tot-cell"><?php echo $summaries['%']['%']['totalHours'] . ' h'; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totalWage']; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totalTips']; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totalTipout']; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totalEarned']; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totalSales']; ?></td>
						<td class="bth-tot-cell"><?php echo $summaries['%']['%']['totalCovers'] . ' cov'; ?></td>
						<td class="bth-tot-cell"><?php echo $summaries['%']['%']['totalCampHours'] . ' h'; ?></td>
					</tr>
				</table>
			</div>

			<h2>Lunch vs Dinner</h2>

			<div class="mobile-table">
				<table class="summary-table">
					<tr>
						<th class="hdr-avg-cell"></th>
						<th class="hdr-avg-cell">#</th>
						<th class="hdr-avg-cell">Average Hours</th>
						<th class="hdr-tot-cell">Total Hours</th>
						<th class="hdr-avg-cell">Average Wage</th>
						<th class="hdr-tot-cell">Total Wage</th>
						<th class="hdr-avg-cell">Average Tips</th>
						<th class="hdr-tot-cell">Total Tips</th>
						<th class="hdr-avg-cell">Average Tipout</th>
						<th class="hdr-tot-cell">Total Tipout</th>
						<th class="hdr-avg-cell">Average Earned</th>
						<th class="hdr-tot-cell">Total Earned</th>
						<th class="hdr-avg-cell">Average Sales</th>
						<th class="hdr-tot-cell">Total Sales</th>
						<th class="hdr-avg-cell">Average Covers</th>
						<th class="hdr-tot-cell">Total Covers</th>
						<th class="hdr-avg-cell">Average Camp Hours</th>
						<th class="hdr-tot-cell">Total Camp Hours</th>
						<th class="hdr-avg-cell">Sales /hour</th>
						<th class="hdr-avg-cell">Sales /cover</th>
						<th class="hdr-avg-cell">%Tips</th>
						<th class="hdr-avg-cell">%Tip Out</th>
						<th class="hdr-avg-cell">%Tips vs Wage</th>
						<th class="hdr-avg-cell">Earned /hour</th>
					</tr>
					<tr>
						<td class="lun-avg-cell">Lunch</td>
						<td class="lun-avg-cell"><?php echo $summaries['L']['%']['count']; ?></td>
						<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgHours'] . ' h'; ?></td>
						<td class="lun-tot-cell"><?php echo $summaries['L']['%']['totalHours'] . ' h'; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgWage']; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totalWage']; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgTips']; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totalTips']; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgTipout']; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totalTipout']; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgEarned']; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totalEarned']; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgSales']; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totalSales']; ?></td>
						<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgCovers'] . ' cov'; ?></td>
						<td class="lun-tot-cell"><?php echo $summaries['L']['%']['totalCovers'] . ' cov'; ?></td>
						<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgCampHours'] . ' h'; ?></td>
						<td class="lun-tot-cell"><?php echo $summaries['L']['%']['totalCampHours'] . ' h'; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['salesPerHour'] . '/h'; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['salesPerCover'] . '/cov'; ?></td>
						<td class="lun-avg-cell"><?php echo $summaries['L']['%']['tipsPercent'] . '%'; ?></td>
						<td class="lun-avg-cell"><?php echo $summaries['L']['%']['tipoutPercent'] . '%'; ?></td>
						<td class="lun-avg-cell"><?php echo $summaries['L']['%']['tipsVsWage'] . '%'; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="din-avg-cell">Dinner</td>
						<td class="din-avg-cell"><?php echo $summaries['D']['%']['count']; ?></td>
						<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgHours'] . ' h'; ?></td>
						<td class="din-tot-cell"><?php echo $summaries['D']['%']['totalHours'] . ' h'; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgWage']; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totalWage']; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgTips']; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totalTips']; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgTipout']; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totalTipout']; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgEarned']; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totalEarned']; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgSales']; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totalSales']; ?></td>
						<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgCovers'] . ' cov'; ?></td>
						<td class="din-tot-cell"><?php echo $summaries['D']['%']['totalCovers'] . ' cov'; ?></td>
						<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgCampHours'] . ' h'; ?></td>
						<td class="din-tot-cell"><?php echo $summaries['D']['%']['totalCampHours'] . ' h'; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['salesPerHour'] . '/h'; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['salesPerCover'] . '/cov'; ?></td>
						<td class="din-avg-cell"><?php echo $summaries['D']['%']['tipsPercent'] . '%'; ?></td>
						<td class="din-avg-cell"><?php echo $summaries['D']['%']['tipoutPercent'] . '%'; ?></td>
						<td class="din-avg-cell"><?php echo $summaries['D']['%']['tipsVsWage'] . '%'; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="bth-avg-cell">Both</td>
						<td class="bth-avg-cell"><?php echo $summaries['%']['%']['count']; ?></td>
						<td class="bth-avg-cell"><?php echo $summaries['%']['%']['avgHours'] . ' h'; ?></td>
						<td class="bth-tot-cell"><?php echo $summaries['%']['%']['totalHours'] . ' h'; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgWage']; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totalWage']; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgTips']; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totalTips']; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgTipout']; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totalTipout']; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgEarned']; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totalEarned']; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgSales']; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totalSales']; ?></td>
						<td class="bth-avg-cell"><?php echo $summaries['%']['%']['avgCovers'] . ' cov'; ?></td>
						<td class="bth-tot-cell"><?php echo $summaries['%']['%']['totalCovers'] . ' cov'; ?></td>
						<td class="bth-avg-cell"><?php echo $summaries['%']['%']['avgCampHours'] . ' h'; ?></td>
						<td class="bth-tot-cell"><?php echo $summaries['%']['%']['totalCampHours'] . ' h'; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['salesPerHour'] . '/h'; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['salesPerCover'] . '/cov'; ?></td>
						<td class="bth-avg-cell"><?php echo $summaries['%']['%']['tipsPercent'] . '%'; ?></td>
						<td class="bth-avg-cell"><?php echo $summaries['%']['%']['tipoutPercent'] . '%'; ?></td>
						<td class="bth-avg-cell"><?php echo $summaries['%']['%']['tipsVsWage'] . '%'; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['hourlyWage'] . '/h'; ?></td>
					</tr>
				</table>
			</div>

			<h2>By Day - Dinner</h2>
			<div class="mobile-table">
				<table class="summary-table">
					<tr>
						<th class="hdr-avg-cell"></th>
						<th class="hdr-avg-cell">#</th>
						<th class="hdr-avg-cell">Average Hours</th>
						<th class="hdr-tot-cell">Total Hours</th>
						<th class="hdr-avg-cell">Average Wage</th>
						<th class="hdr-tot-cell">Total Wage</th>
						<th class="hdr-avg-cell">Average Tips</th>
						<th class="hdr-tot-cell">Total Tips</th>
						<th class="hdr-avg-cell">Average Tipout</th>
						<th class="hdr-tot-cell">Total Tipout</th>
						<th class="hdr-avg-cell">Average Earned</th>
						<th class="hdr-tot-cell">Total Earned</th>
						<th class="hdr-avg-cell">Average Sales</th>
						<th class="hdr-tot-cell">Total Sales</th>
						<th class="hdr-avg-cell">Average Covers</th>
						<th class="hdr-tot-cell">Total Covers</th>
						<th class="hdr-avg-cell">Average Camp Hours</th>
						<th class="hdr-tot-cell">Total Camp Hours</th>
						<th class="hdr-avg-cell">Sales /hour</th>
						<th class="hdr-avg-cell">Sales /cover</th>
						<th class="hdr-avg-cell">%Tips</th>
						<th class="hdr-avg-cell">%Tip Out</th>
						<th class="hdr-avg-cell">%Tips vs Wage</th>
						<th class="hdr-avg-cell">Earned /hour</th>
					</tr>
					<tr>
						<td class="mon-avg-cell">Monday</td>
						<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['count']; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['avgHours'] . ' h'; ?></td>
						<td class="mon-tot-cell"><?php echo $summaries['D']['Mon']['totalHours'] . ' h'; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgWage']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totalWage']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgTips']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totalTips']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgTipout']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totalTipout']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgEarned']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totalEarned']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgSales']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totalSales']; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['avgCovers'] . ' cov'; ?></td>
						<td class="mon-tot-cell"><?php echo $summaries['D']['Mon']['totalCovers'] . ' cov'; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['avgCampHours'] . ' h'; ?></td>
						<td class="mon-tot-cell"><?php echo $summaries['D']['Mon']['totalCampHours'] . ' h'; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['salesPerHour'] . '/h'; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['salesPerCover'] . '/cov'; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['tipsPercent'] . '%'; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['tipoutPercent'] . '%'; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['tipsVsWage'] . '%'; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="tue-avg-cell">Tuesday</td>
						<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['count']; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['avgHours'] . ' h'; ?></td>
						<td class="tue-tot-cell"><?php echo $summaries['D']['Tue']['totalHours'] . ' h'; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgWage']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totalWage']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgTips']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totalTips']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgTipout']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totalTipout']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgEarned']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totalEarned']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgSales']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totalSales']; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['avgCovers'] . ' cov'; ?></td>
						<td class="tue-tot-cell"><?php echo $summaries['D']['Tue']['totalCovers'] . ' cov'; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['avgCampHours'] . ' h'; ?></td>
						<td class="tue-tot-cell"><?php echo $summaries['D']['Tue']['totalCampHours'] . ' h'; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['salesPerHour'] . '/h'; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['salesPerCover'] . '/cov'; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['tipsPercent'] . '%'; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['tipoutPercent'] . '%'; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['tipsVsWage'] . '%'; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="wed-avg-cell">Wednesday</td>
						<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['count']; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['avgHours'] . ' h'; ?></td>
						<td class="wed-tot-cell"><?php echo $summaries['D']['Wed']['totalHours'] . ' h'; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgWage']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totalWage']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgTips']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totalTips']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgTipout']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totalTipout']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgEarned']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totalEarned']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgSales']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totalSales']; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['avgCovers'] . ' cov'; ?></td>
						<td class="wed-tot-cell"><?php echo $summaries['D']['Wed']['totalCovers'] . ' cov'; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['avgCampHours'] . ' h'; ?></td>
						<td class="wed-tot-cell"><?php echo $summaries['D']['Wed']['totalCampHours'] . ' h'; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['salesPerHour'] . '/h'; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['salesPerCover'] . '/cov'; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['tipsPercent'] . '%'; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['tipoutPercent'] . '%'; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['tipsVsWage'] . '%'; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="thu-avg-cell">Thursday</td>
						<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['count']; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['avgHours'] . ' h'; ?></td>
						<td class="thu-tot-cell"><?php echo $summaries['D']['Thu']['totalHours'] . ' h'; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgWage']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totalWage']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgTips']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totalTips']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgTipout']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totalTipout']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgEarned']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totalEarned']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgSales']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totalSales']; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['avgCovers'] . ' cov'; ?></td>
						<td class="thu-tot-cell"><?php echo $summaries['D']['Thu']['totalCovers'] . ' cov'; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['avgCampHours'] . ' h'; ?></td>
						<td class="thu-tot-cell"><?php echo $summaries['D']['Thu']['totalCampHours'] . ' h'; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['salesPerHour'] . '/h'; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['salesPerCover'] . '/cov'; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['tipsPercent'] . '%'; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['tipoutPercent'] . '%'; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['tipsVsWage'] . '%'; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="fri-avg-cell">Friday</td>
						<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['count']; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['avgHours'] . ' h'; ?></td>
						<td class="fri-tot-cell"><?php echo $summaries['D']['Fri']['totalHours'] . ' h'; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgWage']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totalWage']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgTips']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totalTips']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgTipout']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totalTipout']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgEarned']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totalEarned']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgSales']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totalSales']; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['avgCovers'] . ' cov'; ?></td>
						<td class="fri-tot-cell"><?php echo $summaries['D']['Fri']['totalCovers'] . ' cov'; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['avgCampHours'] . ' h'; ?></td>
						<td class="fri-tot-cell"><?php echo $summaries['D']['Fri']['totalCampHours'] . ' h'; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['salesPerHour'] . '/h'; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['salesPerCover'] . '/cov'; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['tipsPercent'] . '%'; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['tipoutPercent'] . '%'; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['tipsVsWage'] . '%'; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="sat-avg-cell">Saturday</td>
						<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['count']; ?></td>
						<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['avgHours'] . ' h'; ?></td>
						<td class="sat-tot-cell"><?php echo $summaries['D']['Sat']['totalHours'] . ' h'; ?></td>
						<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgWage']; ?></td>
						<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totalWage']; ?></td>
						<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgTips']; ?></td>
						<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totalTips']; ?></td>
						<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgTipout']; ?></td>
						<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totalTipout']; ?></td>
						<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgEarned']; ?></td>
						<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totalEarned']; ?></td>
						<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgSales']; ?></td>
						<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totalSales']; ?></td>
						<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['avgCovers'] . ' cov'; ?></td>
						<td class="sat-tot-cell"><?php echo $summaries['D']['Sat']['totalCovers'] . ' cov'; ?></td>
						<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['avgCampHours'] . ' h'; ?></td>
						<td class="sat-tot-cell"><?php echo $summaries['D']['Sat']['totalCampHours'] . ' h'; ?></td>
						<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['salesPerHour'] . '/h'; ?></td>
						<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['salesPerCover'] . '/cov'; ?></td>
						<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['tipsPercent'] . '%'; ?></td>
						<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['tipoutPercent'] . '%'; ?></td>
						<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['tipsVsWage'] . '%'; ?></td>
						<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="sun-avg-cell">Sunday</td>
						<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['count']; ?></td>
						<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['avgHours'] . ' h'; ?></td>
						<td class="sun-tot-cell"><?php echo $summaries['D']['Sun']['totalHours'] . ' h'; ?></td>
						<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgWage']; ?></td>
						<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totalWage']; ?></td>
						<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgTips']; ?></td>
						<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totalTips']; ?></td>
						<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgTipout']; ?></td>
						<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totalTipout']; ?></td>
						<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgEarned']; ?></td>
						<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totalEarned']; ?></td>
						<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgSales']; ?></td>
						<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totalSales']; ?></td>
						<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['avgCovers'] . ' cov'; ?></td>
						<td class="sun-tot-cell"><?php echo $summaries['D']['Sun']['totalCovers'] . ' cov'; ?></td>
						<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['avgCampHours'] . ' h'; ?></td>
						<td class="sun-tot-cell"><?php echo $summaries['D']['Sun']['totalCampHours'] . ' h'; ?></td>
						<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['salesPerHour'] . '/h'; ?></td>
						<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['salesPerCover'] . '/cov'; ?></td>
						<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['tipsPercent'] . '%'; ?></td>
						<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['tipoutPercent'] . '%'; ?></td>
						<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['tipsVsWage'] . '%'; ?></td>
						<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['hourlyWage'] . '/h'; ?></td>
					</tr>
				</table>
			</div>

			<h2>By Day - Lunch</h2>
			<div class="mobile-table">
				<table class="summary-table">
					<tr>
						<th class="hdr-avg-cell"></th>
						<th class="hdr-avg-cell">#</th>
						<th class="hdr-avg-cell">Average Hours</th>
						<th class="hdr-tot-cell">Total Hours</th>
						<th class="hdr-avg-cell">Average Wage</th>
						<th class="hdr-tot-cell">Total Wage</th>
						<th class="hdr-avg-cell">Average Tips</th>
						<th class="hdr-tot-cell">Total Tips</th>
						<th class="hdr-avg-cell">Average Tipout</th>
						<th class="hdr-tot-cell">Total Tipout</th>
						<th class="hdr-avg-cell">Average Earned</th>
						<th class="hdr-tot-cell">Total Earned</th>
						<th class="hdr-avg-cell">Average Sales</th>
						<th class="hdr-tot-cell">Total Sales</th>
						<th class="hdr-avg-cell">Average Covers</th>
						<th class="hdr-tot-cell">Total Covers</th>
						<th class="hdr-avg-cell">Average Camp Hours</th>
						<th class="hdr-tot-cell">Total Camp Hours</th>
						<th class="hdr-avg-cell">Sales /hour</th>
						<th class="hdr-avg-cell">Sales /cover</th>
						<th class="hdr-avg-cell">%Tips</th>
						<th class="hdr-avg-cell">%Tip Out</th>
						<th class="hdr-avg-cell">%Tips vs Wage</th>
						<th class="hdr-avg-cell">Earned /hour</th>
					</tr>
					<tr>
						<td class="mon-avg-cell">Monday</td>
						<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['count']; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['avgHours'] . ' h'; ?></td>
						<td class="mon-tot-cell"><?php echo $summaries['L']['Mon']['totalHours'] . ' h'; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgWage']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totalWage']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgTips']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totalTips']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgTipout']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totalTipout']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgEarned']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totalEarned']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgSales']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totalSales']; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['avgCovers'] . ' cov'; ?></td>
						<td class="mon-tot-cell"><?php echo $summaries['L']['Mon']['totalCovers'] . ' cov'; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['avgCampHours'] . ' h'; ?></td>
						<td class="mon-tot-cell"><?php echo $summaries['L']['Mon']['totalCampHours'] . ' h'; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['salesPerHour'] . '/h'; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['salesPerCover'] . '/cov'; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['tipsPercent'] . '%'; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['tipoutPercent'] . '%'; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['tipsVsWage'] . '%'; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="tue-avg-cell">Tuesday</td>
						<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['count']; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['avgHours'] . ' h'; ?></td>
						<td class="tue-tot-cell"><?php echo $summaries['L']['Tue']['totalHours'] . ' h'; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgWage']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totalWage']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgTips']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totalTips']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgTipout']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totalTipout']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgEarned']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totalEarned']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgSales']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totalSales']; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['avgCovers'] . ' cov'; ?></td>
						<td class="tue-tot-cell"><?php echo $summaries['L']['Tue']['totalCovers'] . ' cov'; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['avgCampHours'] . ' h'; ?></td>
						<td class="tue-tot-cell"><?php echo $summaries['L']['Tue']['totalCampHours'] . ' h'; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['salesPerHour'] . '/h'; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['salesPerCover'] . '/cov'; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['tipsPercent'] . '%'; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['tipoutPercent'] . '%'; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['tipsVsWage'] . '%'; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="wed-avg-cell">Wednesday</td>
						<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['count']; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['avgHours'] . ' h'; ?></td>
						<td class="wed-tot-cell"><?php echo $summaries['L']['Wed']['totalHours'] . ' h'; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgWage']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totalWage']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgTips']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totalTips']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgTipout']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totalTipout']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgEarned']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totalEarned']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgSales']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totalSales']; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['avgCovers'] . ' cov'; ?></td>
						<td class="wed-tot-cell"><?php echo $summaries['L']['Wed']['totalCovers'] . ' cov'; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['avgCampHours'] . ' h'; ?></td>
						<td class="wed-tot-cell"><?php echo $summaries['L']['Wed']['totalCampHours'] . ' h'; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['salesPerHour'] . '/h'; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['salesPerCover'] . '/cov'; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['tipsPercent'] . '%'; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['tipoutPercent'] . '%'; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['tipsVsWage'] . '%'; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="thu-avg-cell">Thursday</td>
						<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['count']; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['avgHours'] . ' h'; ?></td>
						<td class="thu-tot-cell"><?php echo $summaries['L']['Thu']['totalHours'] . ' h'; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgWage']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totalWage']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgTips']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totalTips']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgTipout']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totalTipout']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgEarned']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totalEarned']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgSales']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totalSales']; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['avgCovers'] . ' cov'; ?></td>
						<td class="thu-tot-cell"><?php echo $summaries['L']['Thu']['totalCovers'] . ' cov'; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['avgCampHours'] . ' h'; ?></td>
						<td class="thu-tot-cell"><?php echo $summaries['L']['Thu']['totalCampHours'] . ' h'; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['salesPerHour'] . '/h'; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['salesPerCover'] . '/cov'; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['tipsPercent'] . '%'; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['tipoutPercent'] . '%'; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['tipsVsWage'] . '%'; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="fri-avg-cell">Friday</td>
						<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['count']; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['avgHours'] . ' h'; ?></td>
						<td class="fri-tot-cell"><?php echo $summaries['L']['Fri']['totalHours'] . ' h'; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgWage']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totalWage']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgTips']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totalTips']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgTipout']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totalTipout']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgEarned']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totalEarned']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgSales']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totalSales']; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['avgCovers'] . ' cov'; ?></td>
						<td class="fri-tot-cell"><?php echo $summaries['L']['Fri']['totalCovers'] . ' cov'; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['avgCampHours'] . ' h'; ?></td>
						<td class="fri-tot-cell"><?php echo $summaries['L']['Fri']['totalCampHours'] . ' h'; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['salesPerHour'] . '/h'; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['salesPerCover'] . '/cov'; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['tipsPercent'] . '%'; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['tipoutPercent'] . '%'; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['tipsVsWage'] . '%'; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['hourlyWage'] . '/h'; ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div id="footer">
		<a class="debug" href="#" target="popup" onClick="wopen('#', 'popup', 320, 480); return false;">debug mobile popup</a>
	</div>
	<script>
		function wopen(url, name, w, h)
		{
			//Fudge factors for window decoration space.
			w += 32;
			h += 96;
			var win = window.open(url, name, 'width=' + w + ', height=' + h + ', ' +
				'location=no, menubar=no, ' + 'status=no, toolbar=no, scrollbars=no, resizable=no');
			win.resizeTo(w, h);
			win.focus();
		}
	</script>
</body>
</html>