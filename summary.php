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
	<div id="header" class="clickable">
		<div class="name">Shift Tips</div>
		<a href="."><span class="link-spanner"></span></a>
	</div>
	<div id="content">
		<div id="wrapper">
			<h1>Summary</h1>

			<h2>Lunch vs Dinner</h2>

			<table class="summary-table">
				<tr>
					<th rowspan="2"></th>
					<th>Total</th>
					<th>Average</th>
					<th>Total</th>
					<th>Average</th>
					<th>Total</th>
					<th>Average</th>
				</tr>
				<tr>
					<th colspan="2" class="lun-avg-cell">Lunch</th>
					<th colspan="2" class="din-avg-cell">Dinner</th>
					<th colspan="2" class="both-avg-cell">Both</th>
				</tr>
				<tr>
					<th>#</th>
					<td colspan="2" class="lun-avg-cell"><?php echo $summaries['L']['%']['count']; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo $summaries['D']['%']['count']; ?></td>
					<td colspan="2" class="both-avg-cell"><?php echo $summaries['%']['%']['count']; ?></td>
				</tr>
				<tr>
					<th>Hours</th>
					<td class="lun-total-cell"><?php echo $summaries['L']['%']['totalHours']; ?></td>
					<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgHours']; ?></td>
					<td class="din-total-cell"><?php echo $summaries['D']['%']['totalHours']; ?></td>
					<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgHours']; ?></td>
					<td class="both-total-cell"><?php echo $summaries['%']['%']['totalHours']; ?></td>
					<td class="both-avg-cell"><?php echo $summaries['%']['%']['avgHours']; ?></td>
				</tr>
				<tr>
					<th>Wage</th>
					<td class="lun-total-cell"><?php echo '$' . $summaries['L']['%']['totalWage']; ?></td>
					<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgWage']; ?></td>
					<td class="din-total-cell"><?php echo '$' . $summaries['D']['%']['totalWage']; ?></td>
					<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgWage']; ?></td>
					<td class="both-total-cell"><?php echo '$' . $summaries['%']['%']['totalWage']; ?></td>
					<td class="both-avg-cell"><?php echo '$' . $summaries['%']['%']['avgWage']; ?></td>
				</tr>
				<tr>
					<th>Tips</th>
					<td class="lun-total-cell"><?php echo '$' . $summaries['L']['%']['totalTips']; ?></td>
					<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgTips']; ?></td>
					<td class="din-total-cell"><?php echo '$' . $summaries['D']['%']['totalTips']; ?></td>
					<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgTips']; ?></td>
					<td class="both-total-cell"><?php echo '$' . $summaries['%']['%']['totalTips']; ?></td>
					<td class="both-avg-cell"><?php echo '$' . $summaries['%']['%']['avgTips']; ?></td>
				</tr>
				<tr>
					<th>Tipout</th>
					<td class="lun-total-cell"><?php echo '$' . $summaries['L']['%']['totalTipout']; ?></td>
					<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgTipout']; ?></td>
					<td class="din-total-cell"><?php echo '$' . $summaries['D']['%']['totalTipout']; ?></td>
					<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgTipout']; ?></td>
					<td class="both-total-cell"><?php echo '$' . $summaries['%']['%']['totalTipout']; ?></td>
					<td class="both-avg-cell"><?php echo '$' . $summaries['%']['%']['avgTipout']; ?></td>
				</tr>
				<tr>
					<th>Earned</th>
					<td class="lun-total-cell"><?php echo '$' . $summaries['L']['%']['totalEarned']; ?></td>
					<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgEarned']; ?></td>
					<td class="din-total-cell"><?php echo '$' . $summaries['D']['%']['totalEarned']; ?></td>
					<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgEarned']; ?></td>
					<td class="both-total-cell"><?php echo '$' . $summaries['%']['%']['totalEarned']; ?></td>
					<td class="both-avg-cell"><?php echo '$' . $summaries['%']['%']['avgEarned']; ?></td>
				</tr>
				<tr>
					<th>Sales</th>
					<td class="lun-total-cell"><?php echo '$' . $summaries['L']['%']['totalSales']; ?></td>
					<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgSales']; ?></td>
					<td class="din-total-cell"><?php echo '$' . $summaries['D']['%']['totalSales']; ?></td>
					<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgSales']; ?></td>
					<td class="both-total-cell"><?php echo '$' . $summaries['%']['%']['totalSales']; ?></td>
					<td class="both-avg-cell"><?php echo '$' . $summaries['%']['%']['avgSales']; ?></td>
				</tr>
				<tr>
					<th>Covers</th>
					<td class="lun-total-cell"><?php echo $summaries['L']['%']['totalCovers']; ?></td>
					<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgCovers']; ?></td>
					<td class="din-total-cell"><?php echo $summaries['D']['%']['totalCovers']; ?></td>
					<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgCovers']; ?></td>
					<td class="both-total-cell"><?php echo $summaries['%']['%']['totalCovers']; ?></td>
					<td class="both-avg-cell"><?php echo $summaries['%']['%']['avgCovers']; ?></td>
				</tr>
				<tr>
					<th>CampHrs</th>
					<td class="lun-total-cell"><?php echo $summaries['L']['%']['totalCampHours']; ?></td>
					<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgCampHours']; ?></td>
					<td class="din-total-cell"><?php echo $summaries['D']['%']['totalCampHours']; ?></td>
					<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgCampHours']; ?></td>
					<td class="both-total-cell"><?php echo $summaries['%']['%']['totalCampHours']; ?></td>
					<td class="both-avg-cell"><?php echo $summaries['%']['%']['avgCampHours']; ?></td>
				</tr>
				<tr>
					<th>Sales/h</th>
					<td colspan="2" class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="both-avg-cell"><?php echo '$' . $summaries['%']['%']['salesPerHour'] . '/h'; ?></td>
				</tr>
				<tr>
					<th>Sales/cov</th>
					<td colspan="2" class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="both-avg-cell"><?php echo '$' . $summaries['%']['%']['salesPerCover'] . '/cov'; ?></td>
				</tr>
				<tr>
					<th>%Tips</th>
					<td colspan="2" class="lun-avg-cell"><?php echo $summaries['L']['%']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo $summaries['D']['%']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="both-avg-cell"><?php echo $summaries['%']['%']['tipsPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th>%Tipout</th>
					<td colspan="2" class="lun-avg-cell"><?php echo $summaries['L']['%']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo $summaries['D']['%']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="both-avg-cell"><?php echo $summaries['%']['%']['tipoutPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th>%TvW</th>
					<td colspan="2" class="lun-avg-cell"><?php echo $summaries['L']['%']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo $summaries['D']['%']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="both-avg-cell"><?php echo $summaries['%']['%']['tipsVsWage'] . '%'; ?></td>
				</tr>
				<tr>
					<th>Earn/h</th>
					<td colspan="2" class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="both-avg-cell"><?php echo '$' . $summaries['%']['%']['hourlyWage'] . '/h'; ?></td>
				</tr>
			</table>

			<h2>Days of the Week - Dinner</h2>

			<table class="summary-table">
				<tr>
					<th rowspan="2"></th>
					<th>Total</th>
					<th>Average</th>
					<th>Total</th>
					<th>Average</th>
					<th>Total</th>
					<th>Average</th>
					<th>Total</th>
					<th>Average</th>
					<th>Total</th>
					<th>Average</th>
					<th>Total</th>
					<th>Average</th>
					<th>Total</th>
					<th>Average</th>
				</tr>
				<tr>
					<th colspan="2" class="mon-avg-cell">Mon</th>
					<th colspan="2" class="tue-avg-cell">Tue</th>
					<th colspan="2" class="wed-avg-cell">Wed</th>
					<th colspan="2" class="thu-avg-cell">Thu</th>
					<th colspan="2" class="fri-avg-cell">Fri</th>
					<th colspan="2" class="sat-avg-cell">Sat</th>
					<th colspan="2" class="sun-avg-cell">Sun</th>
				</tr>
				<tr>
					<th>#</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['D']['Mon']['count']; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['D']['Tue']['count']; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['D']['Wed']['count']; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['D']['Thu']['count']; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['D']['Fri']['count']; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo $summaries['D']['Sat']['count']; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo $summaries['D']['Sun']['count']; ?></td>
				</tr>
				<tr>
					<th>Hours</th>
					<td class="mon-total-cell"><?php echo $summaries['D']['Mon']['totalHours']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['avgHours']; ?></td>
					<td class="tue-total-cell"><?php echo $summaries['D']['Tue']['totalHours']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['avgHours']; ?></td>
					<td class="wed-total-cell"><?php echo $summaries['D']['Wed']['totalHours']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['avgHours']; ?></td>
					<td class="thu-total-cell"><?php echo $summaries['D']['Thu']['totalHours']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['avgHours']; ?></td>
					<td class="fri-total-cell"><?php echo $summaries['D']['Fri']['totalHours']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['avgHours']; ?></td>
					<td class="sat-total-cell"><?php echo $summaries['D']['Sat']['totalHours']; ?></td>
					<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['avgHours']; ?></td>
					<td class="sun-total-cell"><?php echo $summaries['D']['Sun']['totalHours']; ?></td>
					<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['avgHours']; ?></td>
				</tr>
				<tr>
					<th>Wage</th>
					<td class="mon-total-cell"><?php echo '$' . $summaries['D']['Mon']['totalWage']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgWage']; ?></td>
					<td class="tue-total-cell"><?php echo '$' . $summaries['D']['Tue']['totalWage']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgWage']; ?></td>
					<td class="wed-total-cell"><?php echo '$' . $summaries['D']['Wed']['totalWage']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgWage']; ?></td>
					<td class="thu-total-cell"><?php echo '$' . $summaries['D']['Thu']['totalWage']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgWage']; ?></td>
					<td class="fri-total-cell"><?php echo '$' . $summaries['D']['Fri']['totalWage']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgWage']; ?></td>
					<td class="sat-total-cell"><?php echo '$' . $summaries['D']['Sat']['totalWage']; ?></td>
					<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgWage']; ?></td>
					<td class="sun-total-cell"><?php echo '$' . $summaries['D']['Sun']['totalWage']; ?></td>
					<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgWage']; ?></td>
				</tr>
				<tr>
					<th>Tips</th>
					<td class="mon-total-cell"><?php echo '$' . $summaries['D']['Mon']['totalTips']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgTips']; ?></td>
					<td class="tue-total-cell"><?php echo '$' . $summaries['D']['Tue']['totalTips']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgTips']; ?></td>
					<td class="wed-total-cell"><?php echo '$' . $summaries['D']['Wed']['totalTips']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgTips']; ?></td>
					<td class="thu-total-cell"><?php echo '$' . $summaries['D']['Thu']['totalTips']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgTips']; ?></td>
					<td class="fri-total-cell"><?php echo '$' . $summaries['D']['Fri']['totalTips']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgTips']; ?></td>
					<td class="sat-total-cell"><?php echo '$' . $summaries['D']['Sat']['totalTips']; ?></td>
					<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgTips']; ?></td>
					<td class="sun-total-cell"><?php echo '$' . $summaries['D']['Sun']['totalTips']; ?></td>
					<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgTips']; ?></td>
				</tr>
				<tr>
					<th>Tipout</th>
					<td class="mon-total-cell"><?php echo '$' . $summaries['D']['Mon']['totalTipout']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgTipout']; ?></td>
					<td class="tue-total-cell"><?php echo '$' . $summaries['D']['Tue']['totalTipout']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgTipout']; ?></td>
					<td class="wed-total-cell"><?php echo '$' . $summaries['D']['Wed']['totalTipout']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgTipout']; ?></td>
					<td class="thu-total-cell"><?php echo '$' . $summaries['D']['Thu']['totalTipout']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgTipout']; ?></td>
					<td class="fri-total-cell"><?php echo '$' . $summaries['D']['Fri']['totalTipout']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgTipout']; ?></td>
					<td class="sat-total-cell"><?php echo '$' . $summaries['D']['Sat']['totalTipout']; ?></td>
					<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgTipout']; ?></td>
					<td class="sun-total-cell"><?php echo '$' . $summaries['D']['Sun']['totalTipout']; ?></td>
					<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgTipout']; ?></td>
				</tr>
				<tr>
					<th>Earned</th>
					<td class="mon-total-cell"><?php echo '$' . $summaries['D']['Mon']['totalEarned']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgEarned']; ?></td>
					<td class="tue-total-cell"><?php echo '$' . $summaries['D']['Tue']['totalEarned']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgEarned']; ?></td>
					<td class="wed-total-cell"><?php echo '$' . $summaries['D']['Wed']['totalEarned']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgEarned']; ?></td>
					<td class="thu-total-cell"><?php echo '$' . $summaries['D']['Thu']['totalEarned']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgEarned']; ?></td>
					<td class="fri-total-cell"><?php echo '$' . $summaries['D']['Fri']['totalEarned']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgEarned']; ?></td>
					<td class="sat-total-cell"><?php echo '$' . $summaries['D']['Sat']['totalEarned']; ?></td>
					<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgEarned']; ?></td>
					<td class="sun-total-cell"><?php echo '$' . $summaries['D']['Sun']['totalEarned']; ?></td>
					<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgEarned']; ?></td>
				</tr>
				<tr>
					<th>Sales</th>
					<td class="mon-total-cell"><?php echo '$' . $summaries['D']['Mon']['totalSales']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgSales']; ?></td>
					<td class="tue-total-cell"><?php echo '$' . $summaries['D']['Tue']['totalSales']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgSales']; ?></td>
					<td class="wed-total-cell"><?php echo '$' . $summaries['D']['Wed']['totalSales']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgSales']; ?></td>
					<td class="thu-total-cell"><?php echo '$' . $summaries['D']['Thu']['totalSales']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgSales']; ?></td>
					<td class="fri-total-cell"><?php echo '$' . $summaries['D']['Fri']['totalSales']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgSales']; ?></td>
					<td class="sat-total-cell"><?php echo '$' . $summaries['D']['Sat']['totalSales']; ?></td>
					<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgSales']; ?></td>
					<td class="sun-total-cell"><?php echo '$' . $summaries['D']['Sun']['totalSales']; ?></td>
					<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgSales']; ?></td>
				</tr>
				<tr>
					<th>Covers</th>
					<td class="mon-total-cell"><?php echo $summaries['D']['Mon']['totalCovers']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['avgCovers']; ?></td>
					<td class="tue-total-cell"><?php echo $summaries['D']['Tue']['totalCovers']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['avgCovers']; ?></td>
					<td class="wed-total-cell"><?php echo $summaries['D']['Wed']['totalCovers']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['avgCovers']; ?></td>
					<td class="thu-total-cell"><?php echo $summaries['D']['Thu']['totalCovers']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['avgCovers']; ?></td>
					<td class="fri-total-cell"><?php echo $summaries['D']['Fri']['totalCovers']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['avgCovers']; ?></td>
					<td class="sat-total-cell"><?php echo $summaries['D']['Sat']['totalCovers']; ?></td>
					<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['avgCovers']; ?></td>
					<td class="sun-total-cell"><?php echo $summaries['D']['Sun']['totalCovers']; ?></td>
					<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['avgCovers']; ?></td>
				</tr>
				<tr>
					<th>CampsHrs</th>
					<td class="mon-total-cell"><?php echo $summaries['D']['Mon']['totalCampHours']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['avgCampHours']; ?></td>
					<td class="tue-total-cell"><?php echo $summaries['D']['Tue']['totalCampHours']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['avgCampHours']; ?></td>
					<td class="wed-total-cell"><?php echo $summaries['D']['Wed']['totalCampHours']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['avgCampHours']; ?></td>
					<td class="thu-total-cell"><?php echo $summaries['D']['Thu']['totalCampHours']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['avgCampHours']; ?></td>
					<td class="fri-total-cell"><?php echo $summaries['D']['Fri']['totalCampHours']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['avgCampHours']; ?></td>
					<td class="sat-total-cell"><?php echo $summaries['D']['Sat']['totalCampHours']; ?></td>
					<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['avgCampHours']; ?></td>
					<td class="sun-total-cell"><?php echo $summaries['D']['Sun']['totalCampHours']; ?></td>
					<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['avgCampHours']; ?></td>
				</tr>
				<tr>
					<th>Sales/h</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['salesPerHour'] . '/h'; ?></td>
				</tr>
				<tr>
					<th>Sales/cov</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['salesPerCover'] . '/cov'; ?></td>
				</tr>
				<tr>
					<th>%Tips</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['D']['Mon']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['D']['Tue']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['D']['Wed']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['D']['Thu']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['D']['Fri']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo $summaries['D']['Sat']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo $summaries['D']['Sun']['tipsPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th>%Tipout</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['D']['Mon']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['D']['Tue']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['D']['Wed']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['D']['Thu']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['D']['Fri']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo $summaries['D']['Sat']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo $summaries['D']['Sun']['tipoutPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th>%TvW</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['D']['Mon']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['D']['Tue']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['D']['Wed']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['D']['Thu']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['D']['Fri']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo $summaries['D']['Sat']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo $summaries['D']['Sun']['tipsVsWage'] . '%'; ?></td>
				</tr>
				<tr>
					<th>$/h</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['hourlyWage'] . '/h'; ?></td>
				</tr>
			</table>

			<h2>Days of the Week - Lunch</h2>

			<table class="summary-table">
				<tr>
					<th rowspan="2"></th>
					<th>Total</th>
					<th>Average</th>
					<th>Total</th>
					<th>Average</th>
					<th>Total</th>
					<th>Average</th>
					<th>Total</th>
					<th>Average</th>
					<th>Total</th>
					<th>Average</th>
				</tr>
				<tr>
					<th colspan="2" class="mon-avg-cell">Mon</th>
					<th colspan="2" class="tue-avg-cell">Tue</th>
					<th colspan="2" class="wed-avg-cell">Wed</th>
					<th colspan="2" class="thu-avg-cell">Thu</th>
					<th colspan="2" class="fri-avg-cell">Fri</th>
				</tr>
				<tr>
					<th>#</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['L']['Mon']['count']; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['L']['Tue']['count']; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['L']['Wed']['count']; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['L']['Thu']['count']; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['L']['Fri']['count']; ?></td>
				</tr>
				<tr>
					<th>Hours</th>
					<td class="mon-total-cell"><?php echo $summaries['L']['Mon']['totalHours']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['avgHours']; ?></td>
					<td class="tue-total-cell"><?php echo $summaries['L']['Tue']['totalHours']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['avgHours']; ?></td>
					<td class="wed-total-cell"><?php echo $summaries['L']['Wed']['totalHours']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['avgHours']; ?></td>
					<td class="thu-total-cell"><?php echo $summaries['L']['Thu']['totalHours']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['avgHours']; ?></td>
					<td class="fri-total-cell"><?php echo $summaries['L']['Fri']['totalHours']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['avgHours']; ?></td>
				</tr>
				<tr>
					<th>Wage</th>
					<td class="mon-total-cell"><?php echo '$' . $summaries['L']['Mon']['totalWage']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgWage']; ?></td>
					<td class="tue-total-cell"><?php echo '$' . $summaries['L']['Tue']['totalWage']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgWage']; ?></td>
					<td class="wed-total-cell"><?php echo '$' . $summaries['L']['Wed']['totalWage']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgWage']; ?></td>
					<td class="thu-total-cell"><?php echo '$' . $summaries['L']['Thu']['totalWage']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgWage']; ?></td>
					<td class="fri-total-cell"><?php echo '$' . $summaries['L']['Fri']['totalWage']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgWage']; ?></td>
				</tr>
				<tr>
					<th>Tips</th>
					<td class="mon-total-cell"><?php echo '$' . $summaries['L']['Mon']['totalTips']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgTips']; ?></td>
					<td class="tue-total-cell"><?php echo '$' . $summaries['L']['Tue']['totalTips']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgTips']; ?></td>
					<td class="wed-total-cell"><?php echo '$' . $summaries['L']['Wed']['totalTips']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgTips']; ?></td>
					<td class="thu-total-cell"><?php echo '$' . $summaries['L']['Thu']['totalTips']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgTips']; ?></td>
					<td class="fri-total-cell"><?php echo '$' . $summaries['L']['Fri']['totalTips']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgTips']; ?></td>
				</tr>
				<tr>
					<th>Tipout</th>
					<td class="mon-total-cell"><?php echo '$' . $summaries['L']['Mon']['totalTipout']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgTipout']; ?></td>
					<td class="tue-total-cell"><?php echo '$' . $summaries['L']['Tue']['totalTipout']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgTipout']; ?></td>
					<td class="wed-total-cell"><?php echo '$' . $summaries['L']['Wed']['totalTipout']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgTipout']; ?></td>
					<td class="thu-total-cell"><?php echo '$' . $summaries['L']['Thu']['totalTipout']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgTipout']; ?></td>
					<td class="fri-total-cell"><?php echo '$' . $summaries['L']['Fri']['totalTipout']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgTipout']; ?></td>
				</tr>
				<tr>
					<th>Earned</th>
					<td class="mon-total-cell"><?php echo '$' . $summaries['L']['Mon']['totalEarned']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgEarned']; ?></td>
					<td class="tue-total-cell"><?php echo '$' . $summaries['L']['Tue']['totalEarned']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgEarned']; ?></td>
					<td class="wed-total-cell"><?php echo '$' . $summaries['L']['Wed']['totalEarned']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgEarned']; ?></td>
					<td class="thu-total-cell"><?php echo '$' . $summaries['L']['Thu']['totalEarned']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgEarned']; ?></td>
					<td class="fri-total-cell"><?php echo '$' . $summaries['L']['Fri']['totalEarned']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgEarned']; ?></td>
				</tr>
				<tr>
					<th>Sales</th>
					<td class="mon-total-cell"><?php echo '$' . $summaries['L']['Mon']['totalSales']; ?></td>
					<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgSales']; ?></td>
					<td class="tue-total-cell"><?php echo '$' . $summaries['L']['Tue']['totalSales']; ?></td>
					<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgSales']; ?></td>
					<td class="wed-total-cell"><?php echo '$' . $summaries['L']['Wed']['totalSales']; ?></td>
					<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgSales']; ?></td>
					<td class="thu-total-cell"><?php echo '$' . $summaries['L']['Thu']['totalSales']; ?></td>
					<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgSales']; ?></td>
					<td class="fri-total-cell"><?php echo '$' . $summaries['L']['Fri']['totalSales']; ?></td>
					<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgSales']; ?></td>
				</tr>
				<tr>
					<th>Covers</th>
					<td class="mon-total-cell"><?php echo $summaries['L']['Mon']['totalCovers']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['avgCovers']; ?></td>
					<td class="tue-total-cell"><?php echo $summaries['L']['Tue']['totalCovers']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['avgCovers']; ?></td>
					<td class="wed-total-cell"><?php echo $summaries['L']['Wed']['totalCovers']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['avgCovers']; ?></td>
					<td class="thu-total-cell"><?php echo $summaries['L']['Thu']['totalCovers']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['avgCovers']; ?></td>
					<td class="fri-total-cell"><?php echo $summaries['L']['Fri']['totalCovers']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['avgCovers']; ?></td>
				</tr>
				<tr>
					<th>CampsHrs</th>
					<td class="mon-total-cell"><?php echo $summaries['L']['Mon']['totalCampHours']; ?></td>
					<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['avgCampHours']; ?></td>
					<td class="tue-total-cell"><?php echo $summaries['L']['Tue']['totalCampHours']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['avgCampHours']; ?></td>
					<td class="wed-total-cell"><?php echo $summaries['L']['Wed']['totalCampHours']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['avgCampHours']; ?></td>
					<td class="thu-total-cell"><?php echo $summaries['L']['Thu']['totalCampHours']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['avgCampHours']; ?></td>
					<td class="fri-total-cell"><?php echo $summaries['L']['Fri']['totalCampHours']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['avgCampHours']; ?></td>
				</tr>
				<tr>
					<th>Sales/h</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['salesPerHour'] . '/h'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['salesPerHour'] . '/h'; ?></td>
				</tr>
				<tr>
					<th>Sales/cov</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['salesPerCover'] . '/cov'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['salesPerCover'] . '/cov'; ?></td>
				</tr>
				<tr>
					<th>%Tips</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['L']['Mon']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['L']['Tue']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['L']['Wed']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['L']['Thu']['tipsPercent'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['L']['Fri']['tipsPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th>%Tipout</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['L']['Mon']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['L']['Tue']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['L']['Wed']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['L']['Thu']['tipoutPercent'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['L']['Fri']['tipoutPercent'] . '%'; ?></td>
				</tr>
				<tr>
					<th>%TvW</th>
					<td colspan="2" class="mon-avg-cell"><?php echo $summaries['L']['Mon']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo $summaries['L']['Tue']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo $summaries['L']['Wed']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo $summaries['L']['Thu']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo $summaries['L']['Fri']['tipsVsWage'] . '%'; ?></td>
				</tr>
				<tr>
					<th>$/h</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['hourlyWage'] . '/h'; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['hourlyWage'] . '/h'; ?></td>
				</tr>
			</table>
		</div>
	</div>
	<div id="footer">
		<a href="#" target="popup" onClick="wopen('#', 'popup', 320, 480); return false;">DEBUG MOBILE - POPUP</a>
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