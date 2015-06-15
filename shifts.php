<?php
	include 'include/dbconnect.php';

	//get dates, or keeping null to keep form inputs blank
	//TODO validate somehow
	$p_from = !empty($_GET['from']) ? $_GET['from'] : null;
	$p_to = !empty($_GET['to']) ? $_GET['to'] : null;

	//extract dates if set, or use defaults
	$p_startDate = !empty($p_from) ? $p_from : '1970-01-01';
	$p_endDate = !empty($p_to) ? $p_to : '2038-01-01';

	//get lunch, dinner, both, or neither
	$p_lunchDinner = isset($_GET['lun']) 
		? (isset($_GET['din']) ? '%' : 'L') 
		: (isset($_GET['din']) ? 'D' : '%');
	//* DEBUG */ echo '<p>|from:' . $p_from . '|to:' . $p_to . '|</p>';

	//get days
	$p_daySqlValue = [];
	$p_dayStringValue = [];
	if (isset($_GET['mon'])) {$p_daySqlValue['mon'] = 'Mon'; $p_dayStringValue[] = 'Mondays';}
	if (isset($_GET['tue'])) {$p_daySqlValue['tue'] = 'Tue'; $p_dayStringValue[] = 'Tuesdays';}
	if (isset($_GET['wed'])) {$p_daySqlValue['wed'] = 'Wed'; $p_dayStringValue[] = 'Wednesdays';}
	if (isset($_GET['thu'])) {$p_daySqlValue['thu'] = 'Thu'; $p_dayStringValue[] = 'Thursdays';}
	if (isset($_GET['fri'])) {$p_daySqlValue['fri'] = 'Fri'; $p_dayStringValue[] = 'Fridays';}
	if (isset($_GET['sat'])) {$p_daySqlValue['sat'] = 'Sat'; $p_dayStringValue[] = 'Saturdays';}
	if (isset($_GET['sun'])) {$p_daySqlValue['sun'] = 'Sun'; $p_dayStringValue[] = 'Sundays';}
	//* DEBUG */ echo '<pre>'; print_r($p_daySqlValue); print_r($p_dayStringValue); echo '</pre>';

	//if no days are selected, search all
	if(empty($p_daySqlValue['mon']) && empty($p_daySqlValue['tue']) && empty($p_daySqlValue['wed']) && empty($p_daySqlValue['thu']) && empty($p_daySqlValue['fri']) && empty($p_daySqlValue['sat']) && empty($p_daySqlValue['sun']))
	{
		$p_allDaysSqlValue = '%';
	}
	else
	{
		$p_allDaysSqlValue = null;
	}

	//make nice message to display the filter parameters
	$filterMessage = '';
	switch($p_lunchDinner)
	{
		case 'L':
			$filterMessage .= 'Viewing <b>lunch</b> shifts on ';
			break; 
		case 'D':
			$filterMessage .= 'Viewing <b>dinner</b> shifts on ';
			break; 
		default:
			$filterMessage .= 'Viewing any shifts on ';
			break; 
	}
	if(sizeof($p_dayStringValue) > 0)
	{
		foreach ($p_dayStringValue as $k => $v)
		{
			if($k == 0)
			{
				$filterMessage .= '<b>' . $v . '</b>';
			}
			else if ($k == sizeof($p_dayStringValue) - 1)
			{
				$filterMessage .= ', and <b>' . $v . '</b>';
			}
			else
			{
				$filterMessage .= ', <b>' . $v . '</b>';
			}
		}
	}
	else
	{
		$filterMessage .= 'any day';
	}
	if(!empty($p_from))
	{
		$fromDate = (new DateTime($p_from))->format('l M jS, Y');
		$filterMessage .= ' from <b>' . $fromDate . '</b>';
	}
	else
	{
		$filterMessage .= ' from any date';
	}
	if(!empty($p_to))
	{
		$toDate = (new DateTime($p_to))->format('l M jS, Y');
		$filterMessage .= ' to <b>' . $toDate . '</b>';
	}
	else
	{
		$filterMessage .= ' to any date';
	}


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
			AND (UPPER(dayOfWeek) IN (UPPER(?),UPPER(?),UPPER(?),UPPER(?),UPPER(?),UPPER(?),UPPER(?)) OR dayOfWeek LIKE ?)
		ORDER BY startTime ASC';
	//* DEBUG */ echo '<p>' . $sql . '</p>';
	//* DEBUG */ echo '<p>' . $p_startDate . ',' . $p_endDate . ',' . $p_type . ',' . $p_daySqlValue['mon'] . ',' . $p_daySqlValue['tue'] . ',' . $p_daySqlValue['wed'] . ',' . $p_daySqlValue['thu'] . ',' . $p_daySqlValue['fri'] . ',' . $p_daySqlValue['sat'] . ',' . $p_daySqlValue['sun'] . ',' . $p_allDaysSqlValue . '</p>';

	//query database
	$stmt = $db->prepare($sql);
	$stmt->bind_param('sssssssssss', $p_startDate, $p_endDate, $p_lunchDinner, $p_daySqlValue['mon'], $p_daySqlValue['tue'], $p_daySqlValue['wed'], $p_daySqlValue['thu'], $p_daySqlValue['fri'], $p_daySqlValue['sat'], $p_daySqlValue['sun'], $p_allDaysSqlValue);
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
				<form class="filter-form" method="get" action="shifts.php">
					<div class="filter-group" id="date-range">
						<div class="filter-group-wrap">
							<div class="filter-group-header">Date Range</div>
							<div class="filter-group-body">
								<input type="date" name="from" placeholder="yyyy-mm-dd" value="<?php echo !empty($p_from) ? $p_from : null; ?>" />
								<input type="date" name="to" placeholder="yyyy-mm-dd" value="<?php echo !empty($p_to) ? $p_to : null; ?>" />
							</div>
						</div>
					</div>
					<div class="filter-group" id="day-of-week">
						<div class="filter-group-wrap">
							<div class="filter-group-header">Day of Week</div>
							<div class="filter-group-body">
								<input type="checkbox" id="mon-check" name="mon" value=""<?php echo isset($_GET['mon']) ? ' checked' : null; ?>>
								<label for="mon-check">Mon</label>
								<input type="checkbox" id="tue-check" name="tue" value=""<?php echo isset($_GET['tue']) ? ' checked' : null; ?>>
								<label for="tue-check">Tue</label>
								<input type="checkbox" id="wed-check" name="wed" value=""<?php echo isset($_GET['wed']) ? ' checked' : null; ?>>
								<label for="wed-check">Wed</label>
								<input type="checkbox" id="thu-check" name="thu" value=""<?php echo isset($_GET['thu']) ? ' checked' : null; ?>>
								<label for="thu-check">Thu</label>
								<input type="checkbox" id="fri-check" name="fri" value=""<?php echo isset($_GET['fri']) ? ' checked' : null; ?>>
								<label for="fri-check">Fri</label>
								<input type="checkbox" id="sat-check" name="sat" value=""<?php echo isset($_GET['sat']) ? ' checked' : null; ?>>
								<label for="sat-check">Sat</label>
								<input type="checkbox" id="sun-check" name="sun" value=""<?php echo isset($_GET['sun']) ? ' checked' : null; ?>>
								<label for="sun-check">Sun</label>
							</div>
						</div>
					</div>
					<div class="filter-group" id="shift-time">
						<div class="filter-group-wrap">
							<div class="filter-group-header">Time</div>
							<div class="filter-group-body">
								<input type="checkbox" id="lun-check" name="lun"<?php echo isset($_GET['lun']) ? ' checked' : null; ?>>
								<label for="lun-check">AM</label>
								<input type="checkbox" id="din-check" name="din"<?php echo isset($_GET['din']) ? ' checked' : null; ?>>
								<label for="din-check">PM</label>
							</div>
						</div>
					</div>
					<div class="filter-group" id="filter">
						<div class="filter-group-wrap">
							<div class="filter-group-header">Filter</div>
							<div class="filter-group-body">
								<button class="link-button" type="submit">Filter</button> 
								<!--a class="link-button" href="shifts.php?week=on">Weekly</a-->
								<a class="link-button" href="shifts.php">All</a>
							</div>
						</div>
					</div>
				</form>
			</div>
			<h3>
				<?php echo !empty($filterMessage) ? $filterMessage : 'null'; ?> 
			</h3>
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
				'location=no, menubar=no, ' + 'status=no, toolbar=no, scrollbars=yes, resizable=yes');
			win.resizeTo(w, h);
			win.focus();
		}
	</script>
</body>
</html>