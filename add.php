<?php
	include 'include/dbconnect.php';
	include 'include/functions.php';

	//if submitted
	if( isset($_POST['submit']))
	{
		//get info: numbers
		$wage 		= !empty($_POST['wage']) 		&& is_numeric($_POST['wage'])		? $_POST['wage'] 		: null;
		$campHours 	= isset($_POST['campHours']) 	&& is_numeric($_POST['campHours']) 	? $_POST['campHours'] 	: null;
		$sales 		= isset($_POST['sales']) 		&& is_numeric($_POST['sales']) 		? $_POST['sales'] 		: null;
		$tipout 	= isset($_POST['tipout']) 		&& is_numeric($_POST['tipout']) 	? $_POST['tipout'] 		: null;
		$transfers 	= isset($_POST['transfers']) 	&& is_numeric($_POST['transfers']) 	? $_POST['transfers'] 	: null;
		$cash 		= isset($_POST['cash']) 		&& is_numeric($_POST['cash']) 		? $_POST['cash'] 		: null;
		$due 		= isset($_POST['due']) 			&& is_numeric($_POST['due']) 		? $_POST['due'] 		: null;
		$covers 	= isset($_POST['covers']) 		&& is_numeric($_POST['covers']) 	? $_POST['covers'] 		: null;

		//get info: text
		$cut 		= !empty($_POST['cut']) 		? "'" . $db->escape_string($_POST['cut']) . "'" 		: null;
		$section 	= !empty($_POST['section']) 	? "'" . $db->escape_string($_POST['section']) . "'" 	: null;
		$notes 		= !empty($_POST['notes']) 		? "'" . $db->escape_string($_POST['notes']) . "'" 		: null;

		//get info: date/time
		//TODO add validation in catch clause
		try { $date = !empty($_POST['date']) ? (new DateTime($_POST['date']))->format("'Y-m-d'") : null; }
			catch(Exception $e) { $date = null; }
		try { $startTime = !empty($_POST['startTime']) ? (new DateTime($_POST['startTime']))->format("'H:i'") : null; }
			catch(Exception $e) { $startTime = null; }
		try { $endTime = !empty($_POST['endTime']) ? (new DateTime($_POST['endTime']))->format("'H:i'") : null; }
			catch(Exception $e) { $endTime = null; }
		try { $firstTable = !empty($_POST['firstTable']) ? (new DateTime($_POST['firstTable']))->format("'H:i'") : null; }
			catch(Exception $e) { $firstTable = null; }

		//* DEBUG */ echo '<p>' . $wage . '|' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p>';

		//check if start date isn't null
		if(!isset($startTime))
		{
			//do something about the start time not being set
			//* DEBUG */ echo '<p>Start Time not set</p>';
		}
		else
		{
			//* DEBUG */ echo '<p>Start Time set</p>';
			//calculate values
			//TODO change to a stored proc
			// $hours = calculateHours($startTime, $endTime);
			// $earnedWage = calculateEarnedWage($hours, $wage);
			// $earnedTips = calculateEarnedTips($cash, $due);
			// $earnedTotal = calculateEarnedTotal($earnedWage, $earnedTips);
			// $tipsVsWage = calculateTipsVsWage($earnedWage, $earnedTips);
			// $salesPerHour = calculateSalesPerHour($sales, $hours);
			// $salesPerCover = calculateSalesPerCover($sales, $covers);
			// $tipsPercent = calculateTipsPercent($sales, $earnedTips);
			// $tipoutPercent = calculateTipoutPercent($sales, $tipout);
			// $earnedHourly = calculateEarnedHourly($earnedTotal, $hours);
			// $noCampHourly = calculateNoCampHourly($earnedTotal, $hours, $campHours);
			// $lunchDinner = calculateLunchDinner($startTime);
			// $dayOfWeek = calculateDayOfWeek($startTime);

			//* DEBUG */ echo '<p>' . $hours . '|' . $earnedWage . '|' . $earnedTips . '|' . $earnedTotal . '|' . $tipsVsWage . '|' . $salesPerHour . '|' . $salesPerCover . '|' . $tipsPercent . '|' . $tipoutPercent . '|' . $earnedHourly . '|' . $noCampHourly . '|' . $lunchDinner . '|' . $dayOfWeek . '|</p>';

			//insert record
			// $insertSQL = $db->prepare("INSERT INTO shift (wage, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, noCampHourly, lunchDinner, dayOfWeek) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
			// $insertSQL->bind_param('dssssddiidiisssdiiiiiiddiiss', $wage, $date, $startTime, $endTime, $firstTable, $campHours, $sales, $tipout, $transfers, $cash, $due, $covers, $cut, $section, $notes, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $earnedHourly, $noCampHourly, $lunchDinner, $dayOfWeek);
			// $insertSQL->execute();
			// $insertSQL->close();

			//set up variables in database
			$db->query("SET @wage 		= " . $wage 		. ";");
			$db->query("SET @date 		= " . $date 		. ";");
			$db->query("SET @startTime 	= " . $startTime 	. ";");
			$db->query("SET @endTime 	= " . $endTime 		. ";");
			$db->query("SET @firstTable = " . $firstTable 	. ";");
			$db->query("SET @campHours 	= " . $campHours 	. ";");
			$db->query("SET @sales 		= " . $sales 		. ";");
			$db->query("SET @tipout 	= " . $tipout 		. ";");
			$db->query("SET @transfers 	= " . $transfers 	. ";");
			$db->query("SET @cash 		= " . $cash 		. ";");
			$db->query("SET @due 		= " . $due 			. ";");
			$db->query("SET @covers 	= " . $covers 		. ";");
			$db->query("SET @cut 		= " . $cut 			. ";");
			$db->query("SET @section 	= " . $section 		. ";");
			$db->query("SET @notes 		= " . $notes 		. ";");

			//calculate summaries
			$result = $db->query('CALL addShift(@wage, @date, @startTime, @endTime, @firstTable, @campHours, @sales, @tipout, @transfers, @cash, @due, @covers, @cut, @section, @notes);');
			//* DEBUG */ echo '<p>DB INFO:' . $db->info . '</p>';

			//get insert id
			$row = $result->fetch_assoc();
			$id = $row['id'];

			//redirect to view page
			header('Location: view.php?id=' . $id);
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