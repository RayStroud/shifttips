<?php
	include 'include/dbconnect.php';

	$id = isset($_GET['id']) ? $_GET['id'] : null;

	if(isset($id))
	{
		//get shift
		$shiftSQL = $db->prepare("SELECT wage, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, noCampHourly, lunchDinner, dayOfWeek
			FROM shift
			WHERE id = ?");
		$shiftSQL->bind_param('i', $id);
		$shiftSQL->execute();
		$shiftSQL->bind_result($wage, $date, $startTime, $endTime, $firstTable, $campHours, $sales, $tipout, $transfers, $cash, $due, $covers, $cut, $section, $notes, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $earnedHourly, $noCampHourly, $lunchDinner, $dayOfWeek);
		$shiftSQL->fetch();

		//* DEBUG */ echo '<p>' . $id . '|' . $wage . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p><p>' . $hours . '|' . $earnedWage . '|' . $earnedTips . '|' . $earnedTotal . '|' . $tipsVsWage . '|' . $salesPerHour . '|' . $salesPerCover . '|' . $tipsPercent . '|' . $tipoutPercent . '|' . $earnedHourly . '|' . $noCampHourly . '|' . $lunchDinner . '|' . $dayOfWeek . '|</p>';

		//format values
		try { $date = !empty($date) ? (new DateTime($date))->format("D M jS, Y") : null; }
			catch(Exception $e) { $date = null; }
		try { $startTime = !empty($startTime) ? (new DateTime($startTime))->format("g:iA") : null; }
			catch(Exception $e) { $startTime = null; }
		try { $endTime = !empty($endTime) ? (new DateTime($endTime))->format("g:iA") : null; }
			catch(Exception $e) { $endTime = null; }
		try { $firstTable = !empty($firstTable) ? (new DateTime($firstTable))->format("g:iA") : null; }
			catch(Exception $e) { $firstTable = null; }

		//* DEBUG */ echo '<p>' . $id . '|' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|</p>';
	}
	else
	{
		//* DEBUG */ echo '<p>No shift ID provided.</p>'; 
	}

	//close connection
	$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>View Shift - Shift Tips</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<div id="header">
		<div class="name"><a href=".">Shift Tips</a></div>
		<ul class="menu">
			<li><a class="link-button" href="shifts.php">Shifts</a></li>
			<li><a class="link-button" href="summary.php">Summary</a></li>
			<li><a class="link-button" href="add.php">Add</a></li>
		</ul>
	</div>
	<div id="content">
		<div id="wrapper">
			<h1>Shift Details</h1>
			<div class="shift-detailed <?php echo (isset($dayOfWeek) ? ' ' . strtolower($dayOfWeek) . '-shift' : null) . (isset($lunchDinner) ? ' ' . strtolower($lunchDinner) . '-shift' : null); ?>">
				<div class="shift-datetime">
					<div class="shift-date"><?php echo (isset($date) ? $date : 'Unknown Date'); ?></div>

					<div class="shift-time"><?php echo (isset($startTime) ? $startTime : '?:??') . (isset($endTime) ? ' to ' . $endTime : ' to ?:??'); ?></div> <div class="shift-hours-wage"><?php echo (isset($hours) ? ' (' . $hours . ' hours)' : null) . (isset($wage) ? ' @ $' . $wage . '/h' : null); ?></div>
				</div>
				<div class="shift-details">
					<div class="shift-info">
						<div class="label">1st Tbl</div>
						<div class="value"><?php echo (isset($firstTable) ? $firstTable : '-'); ?></div>
					</div>
					<div class="shift-info">
						<div class="label">Camp</div>
						<div class="value"><?php echo (isset($campHours) ? $campHours . ' hours' : '-'); ?></div>
					</div>

					<div class="shift-info">
						<div class="label">Sales</div>
						<div class="value"><?php echo (isset($sales) ? '$' . $sales : '-'); ?></div>
					</div>
					<div class="shift-info">
						<div class="label">Covers</div>
						<div class="value"><?php echo (isset($covers) ? $covers : '-'); ?></div>
					</div>

					<div class="shift-info">
						<div class="label">Tipout</div>
						<div class="value"><?php echo (isset($tipout) ? '$' . $tipout : '-'); ?></div>
					</div>
					<div class="shift-info">
						<div class="label">Transfer</div>
						<div class="value"><?php echo (isset($transfers) ? '$' . $transfers : '-'); ?></div>
					</div>

					<div class="shift-info">
						<div class="label">Cash</div>
						<div class="value"><?php echo (isset($cash) ? '$' . $cash : '-'); ?></div>
					</div>
					<div class="shift-info">
						<div class="label">Due</div>
						<div class="value"><?php echo (isset($due) ? '$' . $due : '-'); ?></div>
					</div>

					<div class="shift-info">
						<div class="label">Section</div>
						<div class="value"><?php echo (isset($section) ? $section : '-'); ?></div>
					</div>
					<div class="shift-info">
						<div class="label">Cut</div>
						<div class="value"><?php echo (isset($cut) ? $cut : '-'); ?></div>
					</div>

					<div class="shift-info full-width">
						<div class="label">Notes</div>
						<div class="value"><?php echo (isset($notes) ? $notes : '-'); ?></div>
					</div>

					<hr>

					<div class="shift-info">
						<div class="label">Wage</div>
						<div class="value"><?php echo (isset($earnedWage) ? '$' . $earnedWage : '-'); ?></div>
					</div>
					<div class="shift-info">
						<div class="label">Tips</div>
						<div class="value"><?php echo (isset($earnedTips) ? '$' . $earnedTips : '-'); ?></div>
					</div>

					<div class="shift-info">
						<div class="label">Total</div>
						<div class="value"><?php echo (isset($earnedTotal) ? '$' . $earnedTotal : '-'); ?></div>
					</div>
					<div class="shift-info">
						<div class="label">%TvW</div>
						<div class="value"><?php echo (isset($tipsVsWage) ? $tipsVsWage . '%' : '-'); ?></div>
					</div>

					<div class="shift-info">
						<div class="label">Sale/h</div>
						<div class="value"><?php echo (isset($salesPerHour) ? '$' . $salesPerHour . '/h' : '-'); ?></div>
					</div>
					<div class="shift-info">
						<div class="label">Sale/cov</div>
						<div class="value"><?php echo (isset($salesPerCover) ? '$' . $salesPerCover . '/cov' : '-'); ?></div>
					</div>

					<div class="shift-info">
						<div class="label">%Tips</div>
						<div class="value"><?php echo (isset($tipsPercent) ? $tipsPercent . '%' : '-'); ?></div>
					</div>
					<div class="shift-info">
						<div class="label">%Tipout</div>
						<div class="value"><?php echo (isset($tipoutPercent) ? $tipoutPercent . '%' : '-'); ?></div>
					</div>

					<div class="shift-info">
						<div class="label">Earn/h</div>
						<div class="value"><?php echo (isset($earnedHourly) ? '$' . $earnedHourly . '/h'  : '-'); ?></div>
					</div>
				</div>
			</div>
			<div class="shift-buttons">
				<div class="form-group button-group half-width">
					<a class="link-button button-wide" href="edit.php?id=<?php echo isset($id) ? $id : null; ?>">Edit</a>
				</div>
				<div class="form-group button-group half-width">
					<a class="link-button button-warning button-wide" href="delete.php?id=<?php echo isset($id) ? $id : null; ?>" onclick="return confirm('Are you sure you want to delete this shift?')">Delete</a>
				</div>
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