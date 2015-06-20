<?php
	include 'include/dbconnect.php';
	include 'include/functions.php';

	//if submitted
	if( isset($_POST['submit']))
	{
		//get information
		$wage 		= !empty($_POST['wage']) 		? $_POST['wage'] 		: null;
		$date 		= !empty($_POST['date']) 		? $_POST['date'] 		: null;
		$startTime 	= !empty($_POST['startTime']) 	? $_POST['startTime'] 	: null;
		$endTime 	= !empty($_POST['endTime']) 	? $_POST['endTime'] 	: null;
		$firstTable = !empty($_POST['firstTable']) 	? $_POST['firstTable'] 	: null;
		$campHours 	= isset($_POST['campHours']) 	&& is_numeric($_POST['campHours']) 	? $_POST['campHours'] 	: null;
		$sales 		= isset($_POST['sales']) 		&& is_numeric($_POST['sales']) 		? $_POST['sales'] 		: null;
		$tipout 	= isset($_POST['tipout']) 		&& is_numeric($_POST['tipout']) 	? $_POST['tipout'] 		: null;
		$transfers 	= isset($_POST['transfers']) 	&& is_numeric($_POST['transfers']) 	? $_POST['transfers'] 	: null;
		$cash 		= isset($_POST['cash']) 		&& is_numeric($_POST['cash']) 		? $_POST['cash'] 		: null;
		$due 		= isset($_POST['due']) 			&& is_numeric($_POST['due']) 		? $_POST['due'] 		: null;
		$covers 	= isset($_POST['covers']) 		&& is_numeric($_POST['covers']) 	? $_POST['covers'] 		: null;
		$cut 		= !empty($_POST['cut']) 		? $_POST['cut'] 		: null;
		$section 	= !empty($_POST['section']) 	? $_POST['section'] 	: null;
		$notes 		= !empty($_POST['notes']) 		? $_POST['notes'] 		: null;

		//* DEBUG */ echo '<p>' . $wage . '|' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p>';

		//check if start date isn't null
		if(!isset($startTime))
		{
			//do something about the start time not being set
		}
		else
		{
			//calculate values
			//TODO change to a stored proc
			$hours = calculateHours($startTime, $endTime);
			$earnedWage = calculateEarnedWage($hours, $wage);
			$earnedTips = calculateEarnedTips($cash, $due);
			$earnedTotal = calculateEarnedTotal($earnedWage, $earnedTips);
			$tipsVsWage = calculateTipsVsWage($earnedWage, $earnedTips);
			$salesPerHour = calculateSalesPerHour($sales, $hours);
			$salesPerCover = calculateSalesPerCover($sales, $covers);
			$tipsPercent = calculateTipsPercent($sales, $earnedTips);
			$tipoutPercent = calculateTipoutPercent($sales, $tipout);
			$earnedHourly = calculateEarnedHourly($earnedTotal, $hours);
			$noCampHourly = calculateNoCampHourly($earnedTotal, $hours, $campHours);
			$lunchDinner = calculateLunchDinner($startTime);
			$dayOfWeek = calculateDayOfWeek($startTime);

			//* DEBUG */ echo '<p>' . $hours . '|' . $earnedWage . '|' . $earnedTips . '|' . $earnedTotal . '|' . $tipsVsWage . '|' . $salesPerHour . '|' . $salesPerCover . '|' . $tipsPercent . '|' . $tipoutPercent . '|' . $earnedHourly . '|' . $noCampHourly . '|' . $lunchDinner . '|' . $dayOfWeek . '|</p>';

			//insert record
			$insertSQL = $db->prepare("INSERT INTO shift (wage, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, noCampHourly, lunchDinner, dayOfWeek) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
			$insertSQL->bind_param('dssssddiidiisssdiiiiiiddiiss', $wage, $date, $startTime, $endTime, $firstTable, $campHours, $sales, $tipout, $transfers, $cash, $due, $covers, $cut, $section, $notes, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $earnedHourly, $noCampHourly, $lunchDinner, $dayOfWeek);
			$insertSQL->execute();
			$insertSQL->close();
			/* DEBUG */ echo '<p>' . $db->info . '</p>';

			//redirect to view page
			header('Location: view.php?id=' . $db->insert_id);
		}
	}

	//close connection
	$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Add Shift - Shift Tips</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<div id="header">
		<div class="name"><a href=".">Shift Tips</a></div>
		<ul class="menu">
			<li><a class="link-button" href="shifts.php">Shifts</a></li>
			<li><a class="link-button" href="summary.php">Summary</a></li>
			<li><a class="active link-button" href="add.php">Add</a></li>
		</ul>
	</div>
	<div id="content">
		<div id="wrapper">
			<h1>Add Shift</h1>
			<form class="add-form" role="form" method="post" action="#">
				<div class="form-group half-width">
					<label for="wage">Wage</label>
					<input class="form-control" type="number" id="wage" name="wage" value="9" min="0" step="0.01"/>
				</div>
				<div class="form-group half-width">
					<label for="date">Date</label>
					<input required class="form-control" type="date" id="date" name="date" placeholder="yyyy-mm-dd" />
				</div>
				<div class="form-group half-width">
					<label for="startTime">Start</label>
					<input required class="form-control" type="time" id="startTime" name="startTime" placeholder="hh:mm (24h)" />
				</div>
				<div class="form-group half-width">
					<label for="endTime">End</label>
					<input class="form-control" type="time" id="endTime" name="endTime" placeholder="hh:mm (24h)" />
				</div>
				<div class="form-group half-width">
					<label for="endTime">First Table</label>
					<input class="form-control" type="time" id="firstTable" name="firstTable" placeholder="hh:mm (24h)" />
				</div>
				<div class="form-group half-width">
					<label for="campHours">Camp Hours</label>
					<input class="form-control" type="number" min="0" step="0.5" id="campHours" name="campHours" placeholder="#" />
				</div>
				<div class="form-group half-width">
					<label for="sales">Sales</label>
					<input class="form-control" type="number" min="0" step="any" id="sales" name="sales" placeholder="$" />
				</div>
				<div class="form-group half-width">
					<label for="covers">Covers</label>
					<input class="form-control" type="number" min="0" step="1" id="covers" name="covers" placeholder="#" />
				</div>
				<div class="form-group half-width">
					<label for="tipout">Tipout</label>
					<input class="form-control" type="number" min="0" step="1" id="tipout" name="tipout" placeholder="$" />
				</div>
				<div class="form-group half-width">
					<label for="transfers">Transfers</label>
					<input class="form-control" type="number" min="0" step="1" id="transfers" name="transfers" placeholder="#" />
				</div>
				<div class="form-group half-width">
					<label for="cash">Cash</label>
					<input class="form-control" type="number" min="0" step="1" id="cash" name="cash" placeholder="$" />
				</div>
				<div class="form-group half-width">
					<label for="due">Due</label>
					<input class="form-control" type="number" min="0" step="1" id="due" name="due" placeholder="$" />
				</div>
				<div class="form-group half-width">
					<label for="section">Section</label>
					<input class="form-control" type="text" maxlength="25" id="section" name="section" placeholder="#,#,#" />
				</div>
				<div class="form-group half-width">
					<label for="cut">Cut <!--<div class="abbr-wrap"><div id="abbr" title="[S]tay, [G]o, [O]pen, [C]lose, [/]Split, [X]None">?</div></div>--></label>
					<input class="form-control" type="text" maxlength="1" id="cut" name="cut" placeholder="[S]tay, [G]o, etc" />
				</div>
				<div class="form-group full-width">
					<label for="notes">Notes</label>
					<textarea class="form-control" maxlength="250" name="notes" rows="3"></textarea>
				</div>
				<div class="form-group button-group full-width">
					<button class="link-button button-narrow" type="submit" name="submit">Submit</button>
				</div>
			</form>
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