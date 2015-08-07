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
		$summaries[$lunchDinner][$dayOfWeek]['totHours'] = $row['totHours'];
		$summaries[$lunchDinner][$dayOfWeek]['avgWage'] = number_format($row['avgWage'],2);
		$summaries[$lunchDinner][$dayOfWeek]['totWage'] = number_format($row['totWage'],0);
		$summaries[$lunchDinner][$dayOfWeek]['avgTips'] = number_format($row['avgTips'],2);
		$summaries[$lunchDinner][$dayOfWeek]['totTips'] = number_format($row['totTips'],0);
		$summaries[$lunchDinner][$dayOfWeek]['avgTipout'] = number_format($row['avgTipout'],2);
		$summaries[$lunchDinner][$dayOfWeek]['totTipout'] = number_format($row['totTipout'],0);
		$summaries[$lunchDinner][$dayOfWeek]['avgSales'] = number_format($row['avgSales'],0);
		$summaries[$lunchDinner][$dayOfWeek]['totSales'] = number_format($row['totSales'],0);
		$summaries[$lunchDinner][$dayOfWeek]['avgCovers'] = $row['avgCovers'];
		$summaries[$lunchDinner][$dayOfWeek]['totCovers'] = $row['totCovers'];
		$summaries[$lunchDinner][$dayOfWeek]['avgCampHours'] = $row['avgCampHours'];
		$summaries[$lunchDinner][$dayOfWeek]['totCampHours'] = $row['totCampHours'];
		$summaries[$lunchDinner][$dayOfWeek]['salesPerHour'] = number_format($row['salesPerHour'],0);
		$summaries[$lunchDinner][$dayOfWeek]['salesPerCover'] = number_format($row['salesPerCover'],0);
		$summaries[$lunchDinner][$dayOfWeek]['tipsPercent'] = $row['tipsPercent'];
		$summaries[$lunchDinner][$dayOfWeek]['tipoutPercent'] = $row['tipoutPercent'];
		$summaries[$lunchDinner][$dayOfWeek]['tipsVsWage'] = $row['tipsVsWage'];
		$summaries[$lunchDinner][$dayOfWeek]['hourlyWage'] = number_format($row['hourlyWage'],2);
		$summaries[$lunchDinner][$dayOfWeek]['timedate'] = $row['timedate'];

		$summaries[$lunchDinner][$dayOfWeek]['avgEarned'] = number_format($row['avgWage'] + $row['avgTips'],2);
		$summaries[$lunchDinner][$dayOfWeek]['totEarned'] = number_format($row['totWage'] + $row['totTips'],0);		
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
	
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
	<div id="header">
		<div class="name"><a href=".">Shift Tips</a></div>
		<ul class="menu">
			<li><a class="link-button" href="shifts.php">Shifts</a></li>
			<li><a class="active link-button" href="summary.php">Summary</a></li>
			<li><a class="link-button" href="add.php">Add</a></li>
		</ul>
	</div>
	<div id="content">
		<div id="wrapper">
			<h1>Summary</h1>

			<h2>Lunch vs Dinner</h2>

			<table class="summary-table">
				<tr>
					<th class="hdr-avg-cell" rowspan="2"></th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
				</tr>
				<tr>
					<th class="hdr-avg-cell" colspan="2" class="lun-avg-cell">Lunch</th>
					<th class="hdr-avg-cell" colspan="2" class="din-avg-cell">Dinner</th>
					<th class="hdr-avg-cell" colspan="2" class="bth-avg-cell">Both</th>
				</tr>
				<tr>
					<th class="hdr-avg-cell">#</th>
					<td colspan="2" class="lun-avg-cell"><?php echo $summaries['L']['%']['count']; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo $summaries['D']['%']['count']; ?></td>
					<td colspan="2" class="bth-avg-cell"><?php echo $summaries['%']['%']['count']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Hours</th>
					<td class="lun-tot-cell"><?php echo $summaries['L']['%']['totHours']; ?></td>
					<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgHours']; ?></td>
					<td class="din-tot-cell"><?php echo $summaries['D']['%']['totHours']; ?></td>
					<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgHours']; ?></td>
					<td class="bth-tot-cell"><?php echo $summaries['%']['%']['totHours']; ?></td>
					<td class="bth-avg-cell"><?php echo $summaries['%']['%']['avgHours']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Wage</th>
					<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totWage']; ?></td>
					<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgWage']; ?></td>
					<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totWage']; ?></td>
					<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgWage']; ?></td>
					<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totWage']; ?></td>
					<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgWage']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Tips</th>
					<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totTips']; ?></td>
					<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgTips']; ?></td>
					<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totTips']; ?></td>
					<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgTips']; ?></td>
					<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totTips']; ?></td>
					<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgTips']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Tipout</th>
					<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totTipout']; ?></td>
					<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgTipout']; ?></td>
					<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totTipout']; ?></td>
					<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgTipout']; ?></td>
					<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totTipout']; ?></td>
					<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgTipout']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Earned</th>
					<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totEarned']; ?></td>
					<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgEarned']; ?></td>
					<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totEarned']; ?></td>
					<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgEarned']; ?></td>
					<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totEarned']; ?></td>
					<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgEarned']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Sales</th>
					<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totSales']; ?></td>
					<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgSales']; ?></td>
					<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totSales']; ?></td>
					<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgSales']; ?></td>
					<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totSales']; ?></td>
					<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgSales']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Covers</th>
					<td class="lun-tot-cell"><?php echo $summaries['L']['%']['totCovers']; ?></td>
					<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgCovers']; ?></td>
					<td class="din-tot-cell"><?php echo $summaries['D']['%']['totCovers']; ?></td>
					<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgCovers']; ?></td>
					<td class="bth-tot-cell"><?php echo $summaries['%']['%']['totCovers']; ?></td>
					<td class="bth-avg-cell"><?php echo $summaries['%']['%']['avgCovers']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">CampHrs</th>
					<td class="lun-tot-cell"><?php echo $summaries['L']['%']['totCampHours']; ?></td>
					<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgCampHours']; ?></td>
					<td class="din-tot-cell"><?php echo $summaries['D']['%']['totCampHours']; ?></td>
					<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgCampHours']; ?></td>
					<td class="bth-tot-cell"><?php echo $summaries['%']['%']['totCampHours']; ?></td>
					<td class="bth-avg-cell"><?php echo $summaries['%']['%']['avgCampHours']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Sales/h</th>
					<td colspan="2" class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['salesPerHour'] . '/h'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Sales/cov</th>
					<td colspan="2" class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['salesPerCover'] . '/cov'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">%Tips</th>
					<td colspan="2" class="lun-avg-cell"><?php echo $summaries['L']['%']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo $summaries['D']['%']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="bth-avg-cell"><?php echo $summaries['%']['%']['tipsPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">%Tipout</th>
					<td colspan="2" class="lun-avg-cell"><?php echo $summaries['L']['%']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo $summaries['D']['%']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="bth-avg-cell"><?php echo $summaries['%']['%']['tipoutPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">%TvW</th>
					<td colspan="2" class="lun-avg-cell"><?php echo $summaries['L']['%']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo $summaries['D']['%']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="bth-avg-cell"><?php echo $summaries['%']['%']['tipsVsWage'] . '%'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Earn/h</th>
					<td colspan="2" class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['hourlyWage'] . '/h'; ?></td>
				</tr>
			</table>

			<h2>By Day - Dinner</h2>

			<table class="summary-table">
				<tr>
					<th class="hdr-avg-cell" rowspan="2"></th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
				</tr>
				<tr>
					<th class="hdr-avg-cell" colspan="2" class="mon-avg-cell">Mon</th>
					<th class="hdr-avg-cell" colspan="2" class="tue-avg-cell">Tue</th>
					<th class="hdr-avg-cell" colspan="2" class="wed-avg-cell">Wed</th>
					<th class="hdr-avg-cell" colspan="2" class="thu-avg-cell">Thu</th>
					<th class="hdr-avg-cell" colspan="2" class="fri-avg-cell">Fri</th>
					<th class="hdr-avg-cell" colspan="2" class="sat-avg-cell">Sat</th>
					<th class="hdr-avg-cell" colspan="2" class="sun-avg-cell">Sun</th>
				</tr>
				<tr>
					<th class="hdr-avg-cell">#</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['D']['Mon']['count']; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['D']['Tue']['count']; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['D']['Wed']['count']; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['D']['Thu']['count']; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['D']['Fri']['count']; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo $summaries['D']['Sat']['count']; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo $summaries['D']['Sun']['count']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Hours</th>
					<td class="mon-tot-cell"><?php echo $summaries['D']['Mon']['totHours']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['avgHours']; ?></td>
					<td class="tue-tot-cell"><?php echo $summaries['D']['Tue']['totHours']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['avgHours']; ?></td>
					<td class="wed-tot-cell"><?php echo $summaries['D']['Wed']['totHours']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['avgHours']; ?></td>
					<td class="thu-tot-cell"><?php echo $summaries['D']['Thu']['totHours']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['avgHours']; ?></td>
					<td class="fri-tot-cell"><?php echo $summaries['D']['Fri']['totHours']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['avgHours']; ?></td>
					<td class="sat-tot-cell"><?php echo $summaries['D']['Sat']['totHours']; ?></td>
					<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['avgHours']; ?></td>
					<td class="sun-tot-cell"><?php echo $summaries['D']['Sun']['totHours']; ?></td>
					<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['avgHours']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Wage</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totWage']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgWage']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totWage']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgWage']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totWage']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgWage']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totWage']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgWage']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totWage']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgWage']; ?></td>
					<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totWage']; ?></td>
					<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgWage']; ?></td>
					<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totWage']; ?></td>
					<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgWage']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Tips</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totTips']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgTips']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totTips']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgTips']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totTips']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgTips']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totTips']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgTips']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totTips']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgTips']; ?></td>
					<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totTips']; ?></td>
					<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgTips']; ?></td>
					<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totTips']; ?></td>
					<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgTips']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Tipout</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totTipout']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgTipout']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totTipout']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgTipout']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totTipout']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgTipout']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totTipout']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgTipout']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totTipout']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgTipout']; ?></td>
					<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totTipout']; ?></td>
					<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgTipout']; ?></td>
					<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totTipout']; ?></td>
					<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgTipout']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Earned</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totEarned']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgEarned']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totEarned']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgEarned']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totEarned']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgEarned']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totEarned']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgEarned']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totEarned']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgEarned']; ?></td>
					<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totEarned']; ?></td>
					<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgEarned']; ?></td>
					<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totEarned']; ?></td>
					<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgEarned']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Sales</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totSales']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgSales']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totSales']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgSales']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totSales']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgSales']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totSales']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgSales']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totSales']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgSales']; ?></td>
					<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totSales']; ?></td>
					<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgSales']; ?></td>
					<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totSales']; ?></td>
					<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgSales']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Covers</th>
					<td class="mon-tot-cell"><?php echo $summaries['D']['Mon']['totCovers']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['avgCovers']; ?></td>
					<td class="tue-tot-cell"><?php echo $summaries['D']['Tue']['totCovers']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['avgCovers']; ?></td>
					<td class="wed-tot-cell"><?php echo $summaries['D']['Wed']['totCovers']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['avgCovers']; ?></td>
					<td class="thu-tot-cell"><?php echo $summaries['D']['Thu']['totCovers']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['avgCovers']; ?></td>
					<td class="fri-tot-cell"><?php echo $summaries['D']['Fri']['totCovers']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['avgCovers']; ?></td>
					<td class="sat-tot-cell"><?php echo $summaries['D']['Sat']['totCovers']; ?></td>
					<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['avgCovers']; ?></td>
					<td class="sun-tot-cell"><?php echo $summaries['D']['Sun']['totCovers']; ?></td>
					<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['avgCovers']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">CampsHrs</th>
					<td class="mon-tot-cell"><?php echo $summaries['D']['Mon']['totCampHours']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['avgCampHours']; ?></td>
					<td class="tue-tot-cell"><?php echo $summaries['D']['Tue']['totCampHours']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['avgCampHours']; ?></td>
					<td class="wed-tot-cell"><?php echo $summaries['D']['Wed']['totCampHours']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['avgCampHours']; ?></td>
					<td class="thu-tot-cell"><?php echo $summaries['D']['Thu']['totCampHours']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['avgCampHours']; ?></td>
					<td class="fri-tot-cell"><?php echo $summaries['D']['Fri']['totCampHours']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['avgCampHours']; ?></td>
					<td class="sat-tot-cell"><?php echo $summaries['D']['Sat']['totCampHours']; ?></td>
					<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['avgCampHours']; ?></td>
					<td class="sun-tot-cell"><?php echo $summaries['D']['Sun']['totCampHours']; ?></td>
					<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['avgCampHours']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Sales/h</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['salesPerHour'] . '/h'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Sales/cov</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['salesPerCover'] . '/cov'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">%Tips</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['D']['Mon']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['D']['Tue']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['D']['Wed']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['D']['Thu']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['D']['Fri']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo $summaries['D']['Sat']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo $summaries['D']['Sun']['tipsPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">%Tipout</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['D']['Mon']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['D']['Tue']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['D']['Wed']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['D']['Thu']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['D']['Fri']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo $summaries['D']['Sat']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo $summaries['D']['Sun']['tipoutPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">%TvW</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['D']['Mon']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['D']['Tue']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['D']['Wed']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['D']['Thu']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['D']['Fri']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo $summaries['D']['Sat']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo $summaries['D']['Sun']['tipsVsWage'] . '%'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">$/h</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['hourlyWage'] . '/h'; ?></td>
				</tr>
			</table>

			<h2>By Day - Lunch</h2>

			<table class="summary-table">
				<tr>
					<th class="hdr-avg-cell" rowspan="2"></th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
				</tr>
				<tr>
					<th class="hdr-avg-cell" colspan="2" class="mon-avg-cell">Mon</th>
					<th class="hdr-avg-cell" colspan="2" class="tue-avg-cell">Tue</th>
					<th class="hdr-avg-cell" colspan="2" class="wed-avg-cell">Wed</th>
					<th class="hdr-avg-cell" colspan="2" class="thu-avg-cell">Thu</th>
					<th class="hdr-avg-cell" colspan="2" class="fri-avg-cell">Fri</th>
				</tr>
				<tr>
					<th class="hdr-avg-cell">#</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['L']['Mon']['count']; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['L']['Tue']['count']; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['L']['Wed']['count']; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['L']['Thu']['count']; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['L']['Fri']['count']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Hours</th>
					<td class="mon-tot-cell"><?php echo $summaries['L']['Mon']['totHours']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['avgHours']; ?></td>
					<td class="tue-tot-cell"><?php echo $summaries['L']['Tue']['totHours']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['avgHours']; ?></td>
					<td class="wed-tot-cell"><?php echo $summaries['L']['Wed']['totHours']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['avgHours']; ?></td>
					<td class="thu-tot-cell"><?php echo $summaries['L']['Thu']['totHours']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['avgHours']; ?></td>
					<td class="fri-tot-cell"><?php echo $summaries['L']['Fri']['totHours']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['avgHours']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Wage</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totWage']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgWage']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totWage']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgWage']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totWage']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgWage']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totWage']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgWage']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totWage']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgWage']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Tips</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totTips']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgTips']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totTips']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgTips']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totTips']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgTips']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totTips']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgTips']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totTips']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgTips']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Tipout</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totTipout']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgTipout']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totTipout']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgTipout']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totTipout']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgTipout']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totTipout']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgTipout']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totTipout']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgTipout']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Earned</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totEarned']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgEarned']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totEarned']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgEarned']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totEarned']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgEarned']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totEarned']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgEarned']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totEarned']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgEarned']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Sales</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totSales']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgSales']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totSales']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgSales']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totSales']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgSales']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totSales']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgSales']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totSales']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgSales']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Covers</th>
					<td class="mon-tot-cell"><?php echo $summaries['L']['Mon']['totCovers']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['avgCovers']; ?></td>
					<td class="tue-tot-cell"><?php echo $summaries['L']['Tue']['totCovers']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['avgCovers']; ?></td>
					<td class="wed-tot-cell"><?php echo $summaries['L']['Wed']['totCovers']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['avgCovers']; ?></td>
					<td class="thu-tot-cell"><?php echo $summaries['L']['Thu']['totCovers']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['avgCovers']; ?></td>
					<td class="fri-tot-cell"><?php echo $summaries['L']['Fri']['totCovers']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['avgCovers']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">CampsHrs</th>
					<td class="mon-tot-cell"><?php echo $summaries['L']['Mon']['totCampHours']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['avgCampHours']; ?></td>
					<td class="tue-tot-cell"><?php echo $summaries['L']['Tue']['totCampHours']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['avgCampHours']; ?></td>
					<td class="wed-tot-cell"><?php echo $summaries['L']['Wed']['totCampHours']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['avgCampHours']; ?></td>
					<td class="thu-tot-cell"><?php echo $summaries['L']['Thu']['totCampHours']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['avgCampHours']; ?></td>
					<td class="fri-tot-cell"><?php echo $summaries['L']['Fri']['totCampHours']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['avgCampHours']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Sales/h</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['salesPerHour'] . '/h'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Sales/cov</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['salesPerCover'] . '/cov'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">%Tips</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['L']['Mon']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['L']['Tue']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['L']['Wed']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['L']['Thu']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['L']['Fri']['tipsPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">%Tipout</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['L']['Mon']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['L']['Tue']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['L']['Wed']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['L']['Thu']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['L']['Fri']['tipoutPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">%TvW</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['L']['Mon']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['L']['Tue']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['L']['Wed']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['L']['Thu']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['L']['Fri']['tipsVsWage'] . '%'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">$/h</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['hourlyWage'] . '/h'; ?></td>
				</tr>
			</table>

			<h2>By Day - Split</h2>

			<table class="summary-table">
				<tr>
					<th class="hdr-avg-cell" rowspan="2"></th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
					<th class="hdr-avg-cell">Total</th>
					<th class="hdr-avg-cell">Average</th>
				</tr>
				<tr>
					<th class="hdr-avg-cell" colspan="2" class="mon-avg-cell">Mon</th>
					<th class="hdr-avg-cell" colspan="2" class="tue-avg-cell">Tue</th>
					<th class="hdr-avg-cell" colspan="2" class="wed-avg-cell">Wed</th>
					<th class="hdr-avg-cell" colspan="2" class="thu-avg-cell">Thu</th>
					<th class="hdr-avg-cell" colspan="2" class="fri-avg-cell">Fri</th>
				</tr>
				<tr>
					<th class="hdr-avg-cell">#</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['S']['Mon']['count']; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['S']['Tue']['count']; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['S']['Wed']['count']; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['S']['Thu']['count']; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['S']['Fri']['count']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Hours</th>
					<td class="mon-tot-cell"><?php echo $summaries['S']['Mon']['totHours']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['S']['Mon']['avgHours']; ?></td>
					<td class="tue-tot-cell"><?php echo $summaries['S']['Tue']['totHours']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['S']['Tue']['avgHours']; ?></td>
					<td class="wed-tot-cell"><?php echo $summaries['S']['Wed']['totHours']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['S']['Wed']['avgHours']; ?></td>
					<td class="thu-tot-cell"><?php echo $summaries['S']['Thu']['totHours']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['S']['Thu']['avgHours']; ?></td>
					<td class="fri-tot-cell"><?php echo $summaries['S']['Fri']['totHours']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['S']['Fri']['avgHours']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Wage</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['S']['Mon']['totWage']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['S']['Mon']['avgWage']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['S']['Tue']['totWage']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['S']['Tue']['avgWage']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['S']['Wed']['totWage']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['S']['Wed']['avgWage']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['S']['Thu']['totWage']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['S']['Thu']['avgWage']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['S']['Fri']['totWage']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['S']['Fri']['avgWage']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Tips</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['S']['Mon']['totTips']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['S']['Mon']['avgTips']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['S']['Tue']['totTips']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['S']['Tue']['avgTips']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['S']['Wed']['totTips']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['S']['Wed']['avgTips']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['S']['Thu']['totTips']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['S']['Thu']['avgTips']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['S']['Fri']['totTips']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['S']['Fri']['avgTips']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Tipout</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['S']['Mon']['totTipout']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['S']['Mon']['avgTipout']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['S']['Tue']['totTipout']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['S']['Tue']['avgTipout']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['S']['Wed']['totTipout']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['S']['Wed']['avgTipout']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['S']['Thu']['totTipout']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['S']['Thu']['avgTipout']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['S']['Fri']['totTipout']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['S']['Fri']['avgTipout']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Earned</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['S']['Mon']['totEarned']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['S']['Mon']['avgEarned']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['S']['Tue']['totEarned']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['S']['Tue']['avgEarned']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['S']['Wed']['totEarned']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['S']['Wed']['avgEarned']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['S']['Thu']['totEarned']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['S']['Thu']['avgEarned']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['S']['Fri']['totEarned']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['S']['Fri']['avgEarned']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Sales</th>
					<td class="mon-tot-cell"><?php echo '$' . $summaries['S']['Mon']['totSales']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['S']['Mon']['avgSales']; ?></td>
					<td class="tue-tot-cell"><?php echo '$' . $summaries['S']['Tue']['totSales']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['S']['Tue']['avgSales']; ?></td>
					<td class="wed-tot-cell"><?php echo '$' . $summaries['S']['Wed']['totSales']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['S']['Wed']['avgSales']; ?></td>
					<td class="thu-tot-cell"><?php echo '$' . $summaries['S']['Thu']['totSales']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['S']['Thu']['avgSales']; ?></td>
					<td class="fri-tot-cell"><?php echo '$' . $summaries['S']['Fri']['totSales']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['S']['Fri']['avgSales']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Covers</th>
					<td class="mon-tot-cell"><?php echo $summaries['S']['Mon']['totCovers']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['S']['Mon']['avgCovers']; ?></td>
					<td class="tue-tot-cell"><?php echo $summaries['S']['Tue']['totCovers']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['S']['Tue']['avgCovers']; ?></td>
					<td class="wed-tot-cell"><?php echo $summaries['S']['Wed']['totCovers']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['S']['Wed']['avgCovers']; ?></td>
					<td class="thu-tot-cell"><?php echo $summaries['S']['Thu']['totCovers']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['S']['Thu']['avgCovers']; ?></td>
					<td class="fri-tot-cell"><?php echo $summaries['S']['Fri']['totCovers']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['S']['Fri']['avgCovers']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">CampsHrs</th>
					<td class="mon-tot-cell"><?php echo $summaries['S']['Mon']['totCampHours']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['S']['Mon']['avgCampHours']; ?></td>
					<td class="tue-tot-cell"><?php echo $summaries['S']['Tue']['totCampHours']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['S']['Tue']['avgCampHours']; ?></td>
					<td class="wed-tot-cell"><?php echo $summaries['S']['Wed']['totCampHours']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['S']['Wed']['avgCampHours']; ?></td>
					<td class="thu-tot-cell"><?php echo $summaries['S']['Thu']['totCampHours']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['S']['Thu']['avgCampHours']; ?></td>
					<td class="fri-tot-cell"><?php echo $summaries['S']['Fri']['totCampHours']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['S']['Fri']['avgCampHours']; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Sales/h</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['S']['Mon']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['S']['Tue']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['S']['Wed']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['S']['Thu']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['S']['Fri']['salesPerHour'] . '/h'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">Sales/cov</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['S']['Mon']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['S']['Tue']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['S']['Wed']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['S']['Thu']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['S']['Fri']['salesPerCover'] . '/cov'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">%Tips</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['S']['Mon']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['S']['Tue']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['S']['Wed']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['S']['Thu']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['S']['Fri']['tipsPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">%Tipout</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['S']['Mon']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['S']['Tue']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['S']['Wed']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['S']['Thu']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['S']['Fri']['tipoutPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">%TvW</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['S']['Mon']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['S']['Tue']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['S']['Wed']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['S']['Thu']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['S']['Fri']['tipsVsWage'] . '%'; ?></td>
				</tr>
				<tr>
					<th class="hdr-avg-cell">$/h</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['S']['Mon']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['S']['Tue']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['S']['Wed']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['S']['Thu']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['S']['Fri']['hourlyWage'] . '/h'; ?></td>
				</tr>
			</table>
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
				'location=no, menubar=no, ' + 'status=no, toolbar=no, scrollbars=yes, resizable=yes');
			win.resizeTo(w, h);
			win.focus();
		}
	</script>
</body>
</html>