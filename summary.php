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
					<td colspan="2" class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['salesPerHour']; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['salesPerHour']; ?></td>
					<td colspan="2" class="both-avg-cell"><?php echo '$' . $summaries['%']['%']['salesPerHour']; ?></td>
				</tr>
				<tr>
					<th>Sales/Cov</th>
					<td colspan="2" class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['salesPerCover']; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['salesPerCover']; ?></td>
					<td colspan="2" class="both-avg-cell"><?php echo '$' . $summaries['%']['%']['salesPerCover']; ?></td>
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
					<th>%TipsVsWage</th>
					<td colspan="2" class="lun-avg-cell"><?php echo $summaries['L']['%']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo $summaries['D']['%']['tipsVsWage'] . '%'; ?></td>
					<td colspan="2" class="both-avg-cell"><?php echo $summaries['%']['%']['tipsVsWage'] . '%'; ?></td>
				</tr>
				<tr>
					<th>$/h</th>
					<td colspan="2" class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['hourlyWage']; ?></td>
					<td colspan="2" class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['hourlyWage']; ?></td>
					<td colspan="2" class="both-avg-cell"><?php echo '$' . $summaries['%']['%']['hourlyWage']; ?></td>
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
				</tr>
				<tr>
					<th colspan="2" class="mon-avg-cell">Mon</th>
					<th colspan="2" class="tue-avg-cell">Tue</th>
					<th colspan="2" class="wed-avg-cell">Wed</th>
					<th colspan="2" class="thu-avg-cell">Thu</th>
					<th colspan="2" class="fri-avg-cell">Fri</th>
				</tr>
				<tr>
					<th>Hours</th>
					<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['avgHours']; ?></td>
					<td class="mon-total-cell"><?php echo $summaries['D']['Mon']['totalHours']; ?></td>
					<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['avgHours']; ?></td>
					<td class="tue-total-cell"><?php echo $summaries['D']['Tue']['totalHours']; ?></td>
					<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['avgHours']; ?></td>
					<td class="wed-total-cell"><?php echo $summaries['D']['Wed']['totalHours']; ?></td>
					<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['avgHours']; ?></td>
					<td class="thu-total-cell"><?php echo $summaries['D']['Thu']['totalHours']; ?></td>
					<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['avgHours']; ?></td>
					<td class="fri-total-cell"><?php echo $summaries['D']['Fri']['totalHours']; ?></td>
				</tr>
				<tr>
					<th>$/h</th>
					<td colspan="2" class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['hourlyWage']; ?></td>
					<td colspan="2" class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['hourlyWage']; ?></td>
					<td colspan="2" class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['hourlyWage']; ?></td>
					<td colspan="2" class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['hourlyWage']; ?></td>
					<td colspan="2" class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['hourlyWage']; ?></td>
				</tr>
			</table>

			<h2>Days of the Week - Lunch</h2>
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