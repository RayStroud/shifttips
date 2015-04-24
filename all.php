<?php
	include 'dbconnect.php';
	include 'functions.php';

	//get today's date as the end date range, and two weeks earlier for the start range
	$endDateRange = (new DateTime())->format('Y-m-d H:i:s');
	$startDateRange = new DateTime();
	$startDateRange->sub(new DateInterval('P2W'));
	$startDateRange = $startDateRange->format('Y-m-d H:i:s');
	//* DEBUG */ echo '<p>' . $startDateRange . '|' . $endDateRange . '|</p>';

	//get shift
	//TODO change this statement to accept date ranges
	$shiftSQL = $db->prepare("SELECT sid, wage, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, noCampHourly, lunchDinner, dayOfWeek
		FROM shift");
	//$shiftSQL->bind_param('ss', $startDateRange, $endDateRange);
	$shiftSQL->execute();
	$shiftSQL->bind_result($sid, $wage, $startTime, $endTime, $firstTable, $campHours, $sales, $tipout, $transfers, $cash, $due, $covers, $cut, $section, $notes, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $earnedHourly, $noCampHourly, $lunchDinner, $dayOfWeek);

	//TODO loop through all the records to show them all
	$shiftsHtml = '';
	while($shiftSQL->fetch())
	{
		//* DEBUG */ echo '<p>' . $wagde . '|' . $wage . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p><p>' . $hours . '|' . $earnedWage . '|' . $earnedTips . '|' . $earnedTotal . '|' . $tipsVsWage . '|' . $salesPerHour . '|' . $salesPerCover . '|' . $tipsPercent . '|' . $tipoutPercent . '|' . $earnedHourly . '|' . $noCampHourly . '|' . $lunchDinner . '|' . $dayOfWeek . '|</p>';

		//format values
		$startDateTime = new DateTime($startTime);
		$day = !empty($startTime) ? $startDateTime->format("D") : null;
		$date = !empty($startTime) ? $startDateTime->format("D M jS, Y") : null;
		$startTime = !empty($startTime) ? $startDateTime->format("g:iA") : null;
		$endTime = !empty($endTime) ? (new DateTime($endTime))->format("g:iA") : null;
		$firstTable = !empty($firstTable) ? (new DateTime($firstTable))->format("g:iA") : null;

		//* DEBUG */ echo '<p>' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|</p>';

		$shiftsHtml .= 	"\n\n\t\t\t\t" . '<div class="clickable shift-summary ' . (isset($day) ? strtolower($day) . '-shift' : null) . '">'
							. "\n\t\t\t\t\t" . '<div class="shift-datetime">'
								. "\n\t\t\t\t\t\t" . '<div class="shift-date">' . (isset($date) ? $date : 'Unknown Date') . '</div>'

								. "\n\t\t\t\t\t\t" . '<div class="shift-time">' . (isset($startTime) ? $startTime : '?:?? ??') . (isset($endTime) ? ' - ' . $endTime : null) . '</div>'
							. "\n\t\t\t\t\t" . '</div>'
							. "\n\t\t\t\t\t" . '<div class="shift-details">'
								. "\n\t\t\t\t\t" . '<div class="shift-info"><div class="label">Sales</div><div class="value">' . (isset($sales) ? '$' . $sales : null) . '</div></div>'
								. "\n\t\t\t\t\t" . '<div class="shift-info"><div class="label">T/O</div><div class="value">' . (isset($tipout) ? '$' . $tipout : null) . '</div></div>'
								. "\n\t\t\t\t\t" . '<div class="shift-info"><div class="label">Tips</div><div class="value">' . (isset($earnedTips) ? '$' . $earnedTips : null) . '</div></div>'
								. "\n\t\t\t\t\t" . '<div class="shift-info"><div class="label">$/h</div><div class="value">' . (isset($earnedHourly) ? '$' . $earnedHourly . '/h' : null) . '</div></div>'
								. "\n\t\t\t\t\t" . '<a href="view.php?id=' . (isset($sid) ? $sid : null) . '"><span class="link-spanner"></span></a>
'
							. "\n\t\t\t\t\t" . '</div>'
						. "\n\t\t\t\t" . '</div>';

	}

	//close connection
	$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>All Shifts - Shift Tips</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<div id="header" class="clickable">
		<div class="name">Shift Tips</div>
		<a href="."><span class="link-spanner"></span></a>
	</div>
	<div id="content">
		<div class="wrapper">
			<h1>All Shifts</h1>
			<div id="shifts">
				<?php echo (isset($shiftsHtml) ? $shiftsHtml : 'No shifts found'); ?>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
				<div class="filler"></div>
			</div>
		</div>
	</div>
</body>
</html>