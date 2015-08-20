<?php
	include 'include/dbconnect.php';

	//get startDate and endDate
	$startDate = '2014-10-20 11:45:00';
	$endDate = '2015-04-30 21:00:00';

	//calculate weekly values
	$db->query("CALL calculateWeeks();");

	//set up variables in database
	$db->query("SET @startDate = '" . $db->real_escape_string($startDate) . "';");
	$db->query("SET @endDate = '" . $db->real_escape_string($endDate) . "';");

	//calculate summaries
	//TODO put WHERE ...@startDate, @endDate
	$weeksResult = $db->query('SELECT * FROM week;');

	$weekRowsHtml = '';
	while($row = $weeksResult->fetch_assoc())
	{
		$id = $row['id'];

		$yearweek = $row['yearweek'];	
		$startWeek = $row['startWeek'];	
		$endWeek = $row['endWeek'];	
		$count = $row['count'];

		$campHours = $row['campHours'];
		$sales = $row['sales'];	
		$tipout = $row['tipout'];	
		$transfers = $row['transfers'];	
		$covers = $row['covers'];	

		$hours = $row['hours'];	
		$earnedWage = $row['earnedWage'];	
		$earnedTips = $row['earnedTips'];	
		$earnedTotal = $row['earnedTotal'];	
		$tipsVsWage = $row['tipsVsWage'];	
		$salesPerHour = $row['salesPerHour'];	
		$salesPerCover = $row['salesPerCover'];	
		$tipsPercent = $row['tipsPercent'];	
		$tipoutPercent = $row['tipoutPercent'];	
		$earnedHourly = $row['earnedHourly'];	

		$startWeek = (new DateTime($startWeek))->format("M jS, Y");
		$endWeek = (new DateTime($endWeek))->format("M jS, Y");
		
		$weekRowsHtml .= "\n\t" . '<tr>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . $startWeek . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . $endWeek . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . $count . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . $hours . ' h' . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . '$' . $earnedWage . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . '$' . $earnedTips . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . '$' . $tipout . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . '$' . $earnedTotal . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . '$' . $sales . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . $covers . ' cov' . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . $campHours . ' h' . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . '$' . $salesPerHour . '/h' . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . '$' . $salesPerCover . '/cov' . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . $tipsPercent . '%' . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . $tipoutPercent . '%' . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . $tipsVsWage . '%' . '</td>'
		. "\n\t\t" . '<td class="wkl-avg-cell">' . '$' . $earnedHourly . '/h' . '</td>'
		. "\n\t" . '</tr>';
	}

	//refresh connection
	$db->close();
	include 'include/dbconnect.php';

	//TODO get summary, averages

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
			<h1>Weekly</h1>

			<div class="mobile-table">
				<table class="summary-table">
					<tr>
						<th class="hdr-avg-cell">Start (Mon)</th>
						<th class="hdr-avg-cell">End (Sun)</th>
						<th class="hdr-avg-cell">#</th>
						<th class="hdr-avg-cell">Hours</th>
						<th class="hdr-avg-cell">Wage</th>
						<th class="hdr-avg-cell">Tips</th>
						<th class="hdr-avg-cell">Tipout</th>
						<th class="hdr-avg-cell">Earned</th>
						<th class="hdr-avg-cell">Sales</th>
						<th class="hdr-avg-cell">Covers</th>
						<th class="hdr-avg-cell">Camp</th>
						<th class="hdr-avg-cell">Sales/h</th>
						<th class="hdr-avg-cell">Sales/cov</th>
						<th class="hdr-avg-cell">%Tips</th>
						<th class="hdr-avg-cell">%T/O</th>
						<th class="hdr-avg-cell">%TvsW</th>
						<th class="hdr-avg-cell">Earn/h</th>
					</tr>
					<?php echo $weekRowsHtml; ?>
					<tr>
						<td class="wkl-tot-cell">Total</td>
						<td class="wkl-tot-cell"></td>
						<td class="wkl-tot-cell"></td>
						<td class="wkl-tot-cell">Hours</td>
						<td class="wkl-tot-cell">Wage</td>
						<td class="wkl-tot-cell">Tips</td>
						<td class="wkl-tot-cell">Tipout</td>
						<td class="wkl-tot-cell">Earned</td>
						<td class="wkl-tot-cell">Sales</td>
						<td class="wkl-tot-cell">Covers</td>
						<td class="wkl-tot-cell">Camp</td>
						<td class="wkl-tot-cell"></td>
						<td class="wkl-tot-cell"></td>
						<td class="wkl-tot-cell"></td>
						<td class="wkl-tot-cell"></td>
						<td class="wkl-tot-cell"></td>
						<td class="wkl-tot-cell"></td>
					</tr>
					<tr>
						<td class="wkl-tot-cell">Average</td>
						<td class="wkl-tot-cell"></td>
						<td class="wkl-tot-cell"></td>
						<td class="wkl-tot-cell">Hours</td>
						<td class="wkl-tot-cell">Wage</td>
						<td class="wkl-tot-cell">Tips</td>
						<td class="wkl-tot-cell">Tipout</td>
						<td class="wkl-tot-cell">Earned</td>
						<td class="wkl-tot-cell">Sales</td>
						<td class="wkl-tot-cell">Covers</td>
						<td class="wkl-tot-cell">Camp</td>
						<td class="wkl-tot-cell">Sales/h</td>
						<td class="wkl-tot-cell">Sales/cov</td>
						<td class="wkl-tot-cell">%Tips</td>
						<td class="wkl-tot-cell">%T/O</td>
						<td class="wkl-tot-cell">%TvsW</td>
						<td class="wkl-tot-cell">Earn/h</td>
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
				'location=no, menubar=no, ' + 'status=no, toolbar=no, scrollbars=yes, resizable=yes');
			win.resizeTo(w, h);
			win.focus();
		}
	</script>
</body>
</html>