<?php
	include 'include/dbconnect.php';

	//check if group-by-week is requested
	$weekly = isset($_GET['week']) ? $_GET['week'] : null;

	//extract dates if set
	try { $dateTimeFrom = !empty($_GET['from']) ? new DateTime($_GET['from']) : null; } catch(Exception $e) { $dateTimeFrom = null; }
	try { $dateTimeTo = !empty($_GET['to']) ? new DateTime($_GET['to']) : null; } catch(Exception $e) { $dateTimeTo = null; }
	$p_dateFrom = !empty($dateTimeFrom) ? "'" . $dateTimeFrom->format("Y-m-d") . "'" : null; 
	$p_dateTo = !empty($dateTimeTo) ? "'" . $dateTimeTo->format("Y-m-d") . "'" : null; 
	//* DEBUG */ echo '<p>|dateFrom:' . $p_dateFrom . '|dateTo:' . $p_dateTo . '|</p>';

	//get lunch, dinner, both, or neither
	$p_lunchDinner = isset($_GET['lun']) 
		? (isset($_GET['din']) ? null : "'L'") 
		: (isset($_GET['din']) ? "'D'" : null);
	//* DEBUG */ echo '<p>|lunchDinner:' . $p_lunchDinner . '|</p>';

	//get days, set 1 or 0 for mysql proc, add a string to array for message
	$aDayOfWeekNames = [];
	if (isset($_GET['mon'])) {$p_mon = 1; $aDayOfWeekNames[] = 'Mon';} else {$p_mon = 0;} 
	if (isset($_GET['tue'])) {$p_tue = 1; $aDayOfWeekNames[] = 'Tue';} else {$p_tue = 0;} 
	if (isset($_GET['wed'])) {$p_wed = 1; $aDayOfWeekNames[] = 'Wed';} else {$p_wed = 0;} 
	if (isset($_GET['thu'])) {$p_thu = 1; $aDayOfWeekNames[] = 'Thu';} else {$p_thu = 0;} 
	if (isset($_GET['fri'])) {$p_fri = 1; $aDayOfWeekNames[] = 'Fri';} else {$p_fri = 0;} 
	if (isset($_GET['sat'])) {$p_sat = 1; $aDayOfWeekNames[] = 'Sat';} else {$p_sat = 0;} 
	if (isset($_GET['sun'])) {$p_sun = 1; $aDayOfWeekNames[] = 'Sun';} else {$p_sun = 0;} 
	//* DEBUG */ echo '<p>|mon:' . $p_mon . '|tue:' . $p_tue . '|wed:' . $p_wed . '|thu:' . $p_thu . '|fri:' . $p_fri . '|sat:' . $p_sat . '|sun:' . $p_sun . '|</p>';
	//* DEBUG */ echo '<pre>'; print_r($aDayOfWeekNames); echo '</pre>';

	//set up variables in database
	$db->query("SET @p_dateFrom = " . $p_dateFrom . ";");
	$db->query("SET @p_dateTo = " . $p_dateTo . ";");
	$db->query("SET @p_lunchDinner = " . $p_lunchDinner . ";");
	$db->query("SET @p_mon = " . $p_mon . ";");
	$db->query("SET @p_tue = " . $p_tue . ";");
	$db->query("SET @p_wed = " . $p_wed . ";");
	$db->query("SET @p_thu = " . $p_thu . ";");
	$db->query("SET @p_fri = " . $p_fri . ";");
	$db->query("SET @p_sat = " . $p_sat . ";");
	$db->query("SET @p_sun = " . $p_sun . ";");

	//calculate summaries
	$result = $db->query('CALL getShifts(@p_dateFrom, @p_dateTo, @p_lunchDinner, @p_mon, @p_tue, @p_wed, @p_thu, @p_fri, @p_sat, @p_sun);');
	$aWeekShifts = []; 
	while($row = $result->fetch_assoc())
	{
		$shift = [];
		$shift['id'] = $row['id'];
		$shift['wage'] = $row['wage'];
		$shift['date'] = $row['date'];
		$shift['startTime'] = $row['startTime'];
		$shift['endTime'] = $row['endTime'];
		$shift['firstTable'] = $row['firstTable'];
		$shift['campHours'] = $row['campHours'];
		$shift['sales'] = $row['sales'];
		$shift['tipout'] = $row['tipout'];
		$shift['transfers'] = $row['transfers'];
		$shift['cash'] = $row['cash'];
		$shift['due'] = $row['due'];
		$shift['covers'] = $row['covers'];
		$shift['cut'] = $row['cut'];
		$shift['section'] = $row['section'];
		$shift['notes'] = $row['notes'];

		$shift['hours'] = $row['hours'];
		$shift['earnedWage'] = $row['earnedWage'];
		$shift['earnedTips'] = $row['earnedTips'];
		$shift['earnedTotal'] = $row['earnedTotal'];
		$shift['tipsVsWage'] = $row['tipsVsWage'];
		$shift['salesPerHour'] = $row['salesPerHour'];
		$shift['salesPerCover'] = $row['salesPerCover'];
		$shift['tipsPercent'] = $row['tipsPercent'];
		$shift['tipoutPercent'] = $row['tipoutPercent'];
		$shift['earnedHourly'] = $row['earnedHourly'];
		$shift['noCampHourly'] = $row['noCampHourly'];
		$shift['lunchDinner'] = $row['lunchDinner'];
		$shift['dayOfWeek'] = $row['dayOfWeek'];

		$aWeekShifts[$row['yearWeek']][] = $shift;

		//get first and last week numbers
		if (!isset($firstYearWeek)) { $firstYearWeek = $row['yearWeek']; }	//only runs first loop for first value
		$lastYearWeek = $row['yearWeek'];									//runs every loop, last loop will be last value
	}
	$firstYear = substr($firstYearWeek, 0, 4);
	$lastYear = substr($lastYearWeek, 0, 4);
	$firstWeek = substr($firstYearWeek, 4, 2);
	$lastWeek = substr($lastYearWeek, 4, 2);
	//* DEBUG */ echo "<p>FirstWeek:$firstYearWeek SPLIT: $firstYear/$firstWeek | LastWeek: $lastYearWeek SPLIT: $lastYear/$lastWeek</p>";
	//* DEBUG */ echo '<pre>'; print_r($aWeekShifts); echo '</pre>';

	$shiftsHtml = '';
	$nWeeks = 0;
	$nShifts = 0;
	//loop first through years, then through weeks
	$year = $firstYear;
	while ($year <= $lastYear)
	{
		//if this is the firstYear, start on the first week, if not start on 0
		$week = ($year == $firstYear) ? $firstWeek : 1;

		//if this is the last year, set the max week to lastWeek, if not then calculate last week in year
		//last day in the last week of a year is... the Sunday before Dec 31, because Dec 31 might be in the first week of the next year
		$maxWeek = ($year == $lastYear) ? $lastWeek : date('W', strtotime('last Sunday', mktime(0, 0, 0, 12, 31, $year)));

		//* DEBUG */ echo "<p>YEAR: $year | WEEK: $week | MAXWEEK: $maxWeek</p>";

		while ($week <= $maxWeek)
		{
			//count weeks
			$nWeeks = $nWeeks + 1;

			//use str_pad to make it a two-digit week
			$week = str_pad($week, 2, "0", STR_PAD_LEFT);

			//concatenate year-week
			$yearweek = $year . $week;

			//* DEBUG */ echo "<p>YEAR: $year | WEEK: $week | YEARWEEK: $yearweek</p>";

			//add the week container if requested by user
			if (isset($weekly))
			{
				//find the date ranges of this week
				$firstDayOfWeek = date('D M jS, Y', strtotime($year . 'W' . $week));
				$lastDayOfWeek = date('D M jS, Y', strtotime('+ 6 days', strtotime($year . 'W' . $week)));
				//* DEBUG */ echo "<p>FirstDayOfWeek: $firstDayOfWeek | LastDayOfWeek: $lastDayOfWeek</p>";

				$shiftsHtml .= "\n" . '<div class="filter-group week-group">';
				$shiftsHtml .= "\n\t" . '<div class="filter-group-wrap">';
				$shiftsHtml .= "\n\t\t" . '<div class="filter-group-header">' . $firstDayOfWeek . ' to ' . $lastDayOfWeek . '</div>' ;
				$shiftsHtml .= "\n\t\t" . '<div class="filter-group-body">';
			}
			
			//if the week has shifts
			if (isset($aWeekShifts[$yearweek]))
			{				
				//loop through all shifts in week
				foreach ($aWeekShifts[$yearweek] as $shift)
				{
					$nShifts = $nShifts + 1;
					//* DEBUG */ echo '<p>' . $wagde . '|' . $wage . '|' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p><p>' . $hours . '|' . $earnedWage . '|' . $earnedTips . '|' . $earnedTotal . '|' . $tipsVsWage . '|' . $salesPerHour . '|' . $salesPerCover . '|' . $tipsPercent . '|' . $tipoutPercent . '|' . $earnedHourly . '|' . $noCampHourly . '|' . $lunchDinner . '|' . $dayOfWeek . '|</p>';

					//format values
					try { $date = !empty($shift['date']) ? (new DateTime($shift['date']))->format("D M jS, Y") : null; }
						catch(Exception $e) { $date = null; }
					try { $startTime = !empty($shift['startTime']) ? (new DateTime($shift['startTime']))->format("g:iA") : null; }
						catch(Exception $e) { $startTime = null; }
					try { $endTime = !empty($shift['endTime']) ? (new DateTime($shift['endTime']))->format("g:iA") : null; }
						catch(Exception $e) { $endTime = null; }
					try { $firstTable = !empty($shift['firstTable']) ? (new DateTime($shift['firstTable']))->format("g:iA") : null; }
						catch(Exception $e) { $firstTable = null; }

					//* DEBUG */ echo '<p>' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|</p>';

					$shiftsHtml .= 	"\n\n\t\t\t\t" . '<div class="clickable shift-summary' . (isset($shift['dayOfWeek']) ? ' ' . strtolower($shift['dayOfWeek']) . '-shift' : null) . (isset($shift['lunchDinner']) ? ' ' . strtolower($shift['lunchDinner']) . '-shift' : null) . '">'
										. "\n\t\t\t\t\t" . '<div class="shift-datetime">'
											. "\n\t\t\t\t\t\t" . '<div class="shift-date">' . (isset($date) ? $date : 'Unknown Date') . '</div>'

											. "\n\t\t\t\t\t\t" . '<div class="shift-time">' . (isset($startTime) ? $startTime : '?:?? ??') . (isset($endTime) ? ' - ' . $endTime : null) . '</div>'
										. "\n\t\t\t\t\t" . '</div>'
										. "\n\t\t\t\t\t" . '<div class="shift-details">'
											. "\n\t\t\t\t\t" . '<div class="shift-info"><div class="label">Sales</div><div class="value">' . (isset($shift['sales']) ? '$' . $shift['sales'] : null) . '</div></div>'
											. "\n\t\t\t\t\t" . '<div class="shift-info"><div class="label">T/O</div><div class="value">' . (isset($shift['tipout']) ? '$' . $shift['tipout'] : null) . '</div></div>'
											. "\n\t\t\t\t\t" . '<div class="shift-info"><div class="label">Tips</div><div class="value">' . (isset($shift['earnedTips']) ? '$' . $shift['earnedTips'] : null) . '</div></div>'
											. "\n\t\t\t\t\t" . '<div class="shift-info"><div class="label">$/h</div><div class="value">' . (isset($shift['earnedHourly']) ? '$' . $shift['earnedHourly'] . '/h' : null) . '</div></div>'
											. "\n\t\t\t\t\t" . '<a href="view.php?id=' . (isset($shift['id']) ? $shift['id'] : null) . '"><span class="link-spanner"></span></a>
			'
										. "\n\t\t\t\t\t" . '</div>'
									. "\n\t\t\t\t" . '</div>';
				}
			}
			//if no shifts in that week
			else
			{
				$shiftsHtml .= isset($weekly) ? "\n" . '<h4>No shifts this week</h4>' : null;
			}

			//finish the week container if requested by user
			$shiftsHtml .= isset($weekly) ? "\n" . '</div></div></div>' : null;

			//move on to next week
			$week++;
		}

		//move on to next year
		$year++;
	}

	//close connection
	$db->close();

	//make nice message to display the filter parameters
	$filterMessage = '';
	switch($p_lunchDinner)
	{
		case "'L'":
			$filterMessage .= 'Viewing ' . $nShifts . ' <b>Lunch</b> shifts ';
			break; 
		case "'D'":
			$filterMessage .= 'Viewing ' . $nShifts . ' <b>Dinner</b> shifts ';
			break; 
		default:
			$filterMessage .= 'Viewing ' . $nShifts . ' shifts ';
			break; 
	}
	$filterMessage .= isset($weekly) ? 'in ' . $nWeeks . ' weeks on ' : 'on ';
	if(sizeof($aDayOfWeekNames) > 0)
	{
		foreach ($aDayOfWeekNames as $k => $v)
		{
			if($k == 0)
			{
				$filterMessage .= '<b>' . $v . '</b>';
			}
			else if ($k == sizeof($aDayOfWeekNames) - 1)
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
	if(!empty($dateTimeFrom))
	{
		$filterMessage .= ' from <b>' . $dateTimeFrom->format('D M jS, Y') . '</b>';
	}
	else
	{
		$filterMessage .= ' from any date';
	}
	if(!empty($dateTimeTo))
	{
		$filterMessage .= ' to <b>' . $dateTimeTo->format('D M jS, Y') . '</b>';
	}
	else
	{
		$filterMessage .= ' to any date';
	}
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
		<div class="name"><a href="index.php">Shift Tips</a></div>
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
				<form class="filter-form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
					<div class="filter-group" id="date-range">
						<div class="filter-group-wrap">
							<div class="filter-group-header">Date Range</div>
							<div class="filter-group-body">
								<input type="date" name="from" placeholder="yyyy-mm-dd" value="<?php echo !empty($_GET['from']) ? $_GET['from'] : null; ?>" />
								<input type="date" name="to" placeholder="yyyy-mm-dd" value="<?php echo !empty($_GET['to']) ? $_GET['to'] : null; ?>" />
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
					<div class="filter-group" id="filter-buttons">
						<div class="filter-group-wrap">
							<div class="filter-group-header">Filter</div>
							<div class="filter-group-body">
								<button class="link-button" type="submit">Filter</button> 
								<button class="link-button" type="submit" name="week">Weekly</button>
								<a class="link-button button-inverse" href="<?php echo $_SERVER['PHP_SELF']; ?>">Reset</a>
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