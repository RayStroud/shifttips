<?php
	include 'dbconnect.php';
	include 'functions.php';

	$sid = isset($_GET['id']) ? $_GET['id'] : null;

	if(isset($sid))
	{
		//get shift
		$shiftSQL = $db->prepare("SELECT wage, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, noCampHourly, lunchDinner, dayOfWeek
			FROM shift
			WHERE sid = ?");
		$shiftSQL->bind_param('i', $sid);
		$shiftSQL->execute();
		$shiftSQL->bind_result($wage, $startTime, $endTime, $firstTable, $campHours, $sales, $tipout, $transfers, $cash, $due, $covers, $cut, $section, $notes, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $earnedHourly, $noCampHourly, $lunchDinner, $dayOfWeek);
		$shiftSQL->fetch();

		//* DEBUG */ echo '<p>' . $sid . '|' . $wage . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p><p>' . $hours . '|' . $earnedWage . '|' . $earnedTips . '|' . $earnedTotal . '|' . $tipsVsWage . '|' . $salesPerHour . '|' . $salesPerCover . '|' . $tipsPercent . '|' . $tipoutPercent . '|' . $earnedHourly . '|' . $noCampHourly . '|' . $lunchDinner . '|' . $dayOfWeek . '|</p>';

		//format values
		$date = !empty($startTime) ? (new DateTime($startTime))->format("D M jS, Y") : null;
		$startTime = !empty($startTime) ? (new DateTime($startTime))->format("g:iA") : null;
		$endTime = !empty($endTime) ? (new DateTime($endTime))->format("g:iA") : null;
		$firstTable = !empty($firstTable) ? (new DateTime($firstTable))->format("g:iA") : null;

		//* DEBUG */ echo '<p>' . $sid . '|' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|</p>';
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
			<h1>Shift Details</h1>
			<div class="shift-details">
				<div class="shift-date"><?php echo (isset($date) ? $date : 'Unknown Date'); ?></div>

				<div class="shift-time"><?php echo (isset($startTime) ? $startTime : '?:?? ??') . (isset($endTime) ? ' - ' . $endTime : null) . (isset($hours) ? ' (' . $hours . ' hours)' : null) . (isset($wage) ? ' @ $' . $wage . '/h' : null); ?></div>

				<hr>

				<div class="shift-info">
					<div class="label">1st Table</div>
					<div class="value"><?php echo (isset($firstTable) ? $firstTable : null); ?></div>
				</div>
				<div class="shift-info">
					<div class="label">Camp Hrs</div>
					<div class="value"><?php echo (isset($campHours) ? $campHours . ' hours' : null); ?></div>
				</div>

				<div class="shift-info">
					<div class="label">Sales</div>
					<div class="value"><?php echo (isset($sales) ? '$' . $sales : null); ?></div>
				</div>
				<div class="shift-info">
					<div class="label">Covers</div>
					<div class="value"><?php echo (isset($covers) ? $covers : null); ?></div>
				</div>

				<div class="shift-info">
					<div class="label">Tipout</div>
					<div class="value"><?php echo (isset($tipout) ? '$' . $tipout : null); ?></div>
				</div>
				<div class="shift-info">
					<div class="label">Transfers</div>
					<div class="value"><?php echo (isset($transfers) ? '$' . $transfers : null); ?></div>
				</div>

				<div class="shift-info">
					<div class="label">Cash</div>
					<div class="value"><?php echo (isset($cash) ? '$' . $cash : null); ?></div>
				</div>
				<div class="shift-info">
					<div class="label">Due</div>
					<div class="value"><?php echo (isset($due) ? '$' . $due : null); ?></div>
				</div>

				<div class="shift-info">
					<div class="label">Section</div>
					<div class="value"><?php echo (isset($section) ? $section : null); ?></div>
				</div>
				<div class="shift-info">
					<div class="label">Cut</div>
					<div class="value"><?php echo (isset($cut) ? $cut : null); ?></div>
				</div>

				<div class="shift-info full-width">
					<div class="label">Notes</div>
					<div class="value"><?php echo (isset($notes) ? $notes : null); ?></div>
				</div>

				<hr>

				<div class="shift-info">
					<div class="label">Wage</div>
					<div class="value"><?php echo (isset($earnedWage) ? '$' . $earnedWage : null); ?></div>
				</div>
				<div class="shift-info">
					<div class="label">Tips</div>
					<div class="value"><?php echo (isset($earnedTips) ? '$' . $earnedTips : null); ?></div>
				</div>

				<div class="shift-info">
					<div class="label">Total</div>
					<div class="value"><?php echo (isset($earnedTotal) ? '$' . $earnedTotal : null); ?></div>
				</div>
				<div class="shift-info">
					<div class="label">%TvW</div>
					<div class="value"><?php echo (isset($tipsVsWage) ? $tipsVsWage . '%' : null); ?></div>
				</div>

				<div class="shift-info">
					<div class="label">Sales/Hr</div>
					<div class="value"><?php echo (isset($salesPerHour) ? '$' . $salesPerHour . '/h' : null); ?></div>
				</div>
				<div class="shift-info">
					<div class="label">Sales/Cov</div>
					<div class="value"><?php echo (isset($salesPerCover) ? '$' . $salesPerCover . '/cov' : null); ?></div>
				</div>

				<div class="shift-info">
					<div class="label">%Tips</div>
					<div class="value"><?php echo (isset($tipsPercent) ? $tipsPercent . '%' : null); ?></div>
				</div>
				<div class="shift-info">
					<div class="label">%Tipout</div>
					<div class="value"><?php echo (isset($tipoutPercent) ? $tipoutPercent . '%' : null); ?></div>
				</div>

				<div class="shift-info">
					<div class="label">$/Hour</div>
					<div class="value"><?php echo (isset($earnedHourly) ? '$' . $earnedHourly . '/h'  : null); ?></div>
				</div>
			</div>
		</div>

	</div>
</body>
</html>