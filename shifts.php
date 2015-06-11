<?php
	include 'include/dbconnect.php';

	$p_from = isset($_GET['from']) ? $_GET['from'] : null;
	$p_to = isset($_GET['to']) ? $_GET['to'] : null;
	$p_type = isset($_GET['type']) ? $_GET['type'] : null;
	$p_day = isset($_GET['day']) ? $_GET['day'] : null;

	//TODO validate the input somehow
	$p_startDate = !empty($p_from) ? $p_from : '1970-01-01';
	$p_endDate = !empty($p_to) ? $p_to : '2038-01-01';
	$p_lunchDinner = !empty($p_type) ? $p_type : '%';
	$p_dayOfWeek = !empty($p_day) ? $p_day : '%';

	//get today's date as the end date range, and two weeks earlier for the start range
	//TODO maybe have a button that searches for all, which makes the startDate selector pick the earliest shift
	$todayDateTime = (new DateTime())->format('Y-m-d H:i:s');
	$twoWeeksAgoDateTime = new DateTime();
	$twoWeeksAgoDateTime->sub(new DateInterval('P2W'));
	$twoWeeksAgoDateTime = $twoWeeksAgoDateTime->format('Y-m-d H:i:s');
	//* DEBUG */ echo '<p>' . $twoWeeksAgoDateTime . '|' . $todayDateTime . '|</p>';

	//make sql statement
	$sql = 'SELECT id, wage, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, noCampHourly, lunchDinner, dayOfWeek
		FROM shift
		WHERE startTime > ?
			AND endTime < ?
			AND UPPER(lunchDinner) LIKE UPPER(?)
			AND UPPER(dayOfWeek) LIKE UPPER(?)
		ORDER BY startTime ASC';

	//query database
	$stmt = $db->prepare($sql);
	$stmt->bind_param('ssss', $p_startDate, $p_endDate, $p_lunchDinner, $p_dayOfWeek);
	$stmt->execute();
	$stmt->bind_result($id, $wage, $startTime, $endTime, $firstTable, $campHours, $sales, $tipout, $transfers, $cash, $due, $covers, $cut, $section, $notes, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $earnedHourly, $noCampHourly, $lunchDinner, $dayOfWeek);

	//TODO loop through all the records to show them all
	$shiftsHtml = '';
	while($stmt->fetch())
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

		$shiftsHtml .= 	"\n\n\t\t\t\t" . '<div class="clickable shift-summary' . (isset($day) ? ' ' . strtolower($day) . '-shift' : null) . (isset($lunchDinner) ? ' ' . strtolower($lunchDinner) . '-shift' : null) . '">'
							. "\n\t\t\t\t\t" . '<div class="shift-datetime">'
								. "\n\t\t\t\t\t\t" . '<div class="shift-date">' . (isset($date) ? $date : 'Unknown Date') . '</div>'

								. "\n\t\t\t\t\t\t" . '<div class="shift-time">' . (isset($startTime) ? $startTime : '?:?? ??') . (isset($endTime) ? ' - ' . $endTime : null) . '</div>'
							. "\n\t\t\t\t\t" . '</div>'
							. "\n\t\t\t\t\t" . '<div class="shift-details">'
								. "\n\t\t\t\t\t" . '<div class="shift-info"><div class="label">Sales</div><div class="value">' . (isset($sales) ? '$' . $sales : null) . '</div></div>'
								. "\n\t\t\t\t\t" . '<div class="shift-info"><div class="label">T/O</div><div class="value">' . (isset($tipout) ? '$' . $tipout : null) . '</div></div>'
								. "\n\t\t\t\t\t" . '<div class="shift-info"><div class="label">Tips</div><div class="value">' . (isset($earnedTips) ? '$' . $earnedTips : null) . '</div></div>'
								. "\n\t\t\t\t\t" . '<div class="shift-info"><div class="label">$/h</div><div class="value">' . (isset($earnedHourly) ? '$' . $earnedHourly . '/h' : null) . '</div></div>'
								. "\n\t\t\t\t\t" . '<a href="view.php?id=' . (isset($id) ? $id : null) . '"><span class="link-spanner"></span></a>
'
							. "\n\t\t\t\t\t" . '</div>'
						. "\n\t\t\t\t" . '</div>';

	}
	$stmt->close();

	//close connection
	$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>All Shifts - Shift Tips</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<div id="header">
		<div class="name"><a href=".">Shift Tips</a></div>
		<ul class="menu">
			<li><a class="active link-button" href="shifts.php">Shifts</a></li>
			<li><a class="link-button" href="summary.php">Summary</a></li>
			<li><a class="link-button" href="add.php">Add</a></li>
		</ul>
	</div>
	<div id="content">
		<div id="wrapper">
			<h1>Shifts</h1>
			<div>
				<form class="date-form" method="get" action="shifts.php">
					<input type="date" name="from" placeholder="yyyy-mm-dd" value="<?php echo !empty($p_from) ? $p_from : null; ?>" />
					<input type="date" name="to" placeholder="yyyy-mm-dd" value="<?php echo !empty($p_to) ? $p_to : null; ?>" />
					<button class="link-button" type="submit">Submit</button> 
					<a class="link-button" href="shifts.php">View All</a>
				</form>
			</div>
			<div>
				Viewing <?php echo !empty($p_type) ? $p_type : 'all'; ?> shifts on <?php echo !empty($p_day) ? $p_day : 'any day'; ?> from <?php echo !empty($p_from) ? $p_from : 'anytime'; ?> to <?php echo !empty($p_to) ? $p_to : 'anytime'; ?>
			</div>
			<?php echo (empty($shiftsHtml) ? '<div>No shifts found</div>' :
			'<div id="shifts">'
				. $shiftsHtml
				. '<div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div><div class="filler"></div>'
			. '</div>'); ?>
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