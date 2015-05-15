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
		$summary['id'] = $row['id'];
		$summary['avgHours'] = $row['avgHours'];
		$summary['totalHours'] = $row['totalHours'];
		$summary['avgWage'] = $row['avgWage'];
		$summary['totalWage'] = $row['totalWage'];
		$summary['avgTips'] = $row['avgTips'];
		$summary['totalTips'] = $row['totalTips'];
		$summary['avgTipout'] = $row['avgTipout'];
		$summary['totalTipout'] = $row['totalTipout'];
		$summary['avgSales'] = $row['avgSales'];
		$summary['totalSales'] = $row['totalSales'];
		$summary['avgCovers'] = $row['avgCovers'];
		$summary['totalCovers'] = $row['totalCovers'];
		$summary['avgCampHours'] = $row['avgCampHours'];
		$summary['totalCampHours'] = $row['totalCampHours'];
		$summary['salesPerHour'] = $row['salesPerHour'];
		$summary['salesPerCover'] = $row['salesPerCover'];
		$summary['tipsPercent'] = $row['tipsPercent'];
		$summary['tipoutPercent'] = $row['tipoutPercent'];
		$summary['tipsVsWage'] = $row['tipsVsWage'];
		$summary['hourlyWage'] = $row['hourlyWage'];
		$summary['lunchDinner'] = $row['lunchDinner'];
		$summary['dayOfWeek'] = $row['dayOfWeek'];
		$summary['timedate'] = $row['timedate'];

		$summaries[] = $summary;
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

			<h2>Days of the Week - Dinner</h2>

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