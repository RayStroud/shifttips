<?php
	include 'include/dbconnect.php';
	include 'include/functions.php';

	$id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null;

	if(isset($id))
	{
		//get shift
		$shiftSQL = $db->prepare("SELECT wage, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek
			FROM shift
			WHERE id = ?");
		$shiftSQL->bind_param('i', $id);
		$shiftSQL->execute();
		$shiftSQL->bind_result($wage, $date, $startTime, $endTime, $firstTable, $campHours, $sales, $tipout, $transfers, $cash, $due, $covers, $cut, $section, $notes, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $hourly, $noCampHourly, $lunchDinner, $dayOfWeek);
		$shiftSQL->fetch();
		$shiftSQL->close();

		//* DEBUG */ echo '<p>' . $id . '|' . $wage . '|' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p><p>' . $hours . '|' . $earnedWage . '|' . $earnedTips . '|' . $earnedTotal . '|' . $tipsVsWage . '|' . $salesPerHour . '|' . $salesPerCover . '|' . $tipsPercent . '|' . $tipoutPercent . '|' . $hourly . '|' . $noCampHourly . '|' . $lunchDinner . '|' . $dayOfWeek . '|</p>';

		//format values
		try { $date = !empty($date) ? (new DateTime($date))->format("Y-m-d") : null; }
			catch(Exception $e) { $date = null; }
		try { $startTime = !empty($startTime) ? (new DateTime($startTime))->format("H:i") : null; }
			catch(Exception $e) { $startTime = null; }
		try { $endTime = !empty($endTime) ? (new DateTime($endTime))->format("H:i") : null; }
			catch(Exception $e) { $endTime = null; }
		try { $firstTable = !empty($firstTable) ? (new DateTime($firstTable))->format("H:i") : null; }
			catch(Exception $e) { $firstTable = null; }

		//* DEBUG */ echo '<p>' . $id . '|' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|</p>';
	}
	else
	{
		//* DEBUG */ echo '<p>No shift ID provided.</p>'; 
	}

	//if submitted
	if( isset($_POST['submit']))
	{
		//get info: numbers
		$wage 		= isset($_POST['wage']) 		&& is_numeric($_POST['wage'])		? $_POST['wage'] 		: null;
		$campHours 	= isset($_POST['campHours']) 	&& is_numeric($_POST['campHours']) 	? $_POST['campHours'] 	: null;
		$sales 		= isset($_POST['sales']) 		&& is_numeric($_POST['sales']) 		? $_POST['sales'] 		: null;
		$tipout 	= isset($_POST['tipout']) 		&& is_numeric($_POST['tipout']) 	? $_POST['tipout'] 		: null;
		$transfers 	= isset($_POST['transfers']) 	&& is_numeric($_POST['transfers']) 	? $_POST['transfers'] 	: null;
		$cash 		= isset($_POST['cash']) 		&& is_numeric($_POST['cash']) 		? $_POST['cash'] 		: null;
		$due 		= isset($_POST['due']) 			&& is_numeric($_POST['due']) 		? $_POST['due'] 		: null;
		$covers 	= isset($_POST['covers']) 		&& is_numeric($_POST['covers']) 	? $_POST['covers'] 		: null;

		//get info: text
		$cut 		= !empty($_POST['cut']) 		? "'" . $db->escape_string($_POST['cut']) 		. "'" 	: null;
		$section 	= !empty($_POST['section']) 	? "'" . $db->escape_string($_POST['section']) 	. "'" 	: null;
		$notes 		= !empty($_POST['notes']) 		? "'" . $db->escape_string($_POST['notes']) 	. "'" 	: null;

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

		//* DEBUG */ echo '<p>' . $id . '|' . $wage . '|' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p>';

		//set up variables in database
		$db->query("SET @id 		= " . $id 			. ";");
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
		$result = $db->query('CALL saveShift(@id, @wage, @date, @startTime, @endTime, @firstTable, @campHours, @sales, @tipout, @transfers, @cash, @due, @covers, @cut, @section, @notes);');
		//* DEBUG */ echo '<p>DB INFO:' . $db->info . '</p>';

		//redirect to view page
		header('Location: view.php?id=' . $id);
	}

	//close connection
	$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Edit Shift - Shift Tips</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	
	<link rel="stylesheet" href="../assets/css/style.css">
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
			<h1>Edit Shift</h1>
			<form class="edit-form" role="form" method="post" action="#">
				<input hidden id="id" name="id" value="<?php echo isset($id) ? $id : null; ?>" />
				<div class="form-group half-width">
					<label for="wage">Wage</label>
					<input class="form-control" type="number" id="wage" name="wage" min="0" step="0.01" value="<?php echo isset($wage) ? $wage : 9; ?>" />
				</div>
				<div class="form-group half-width">
					<label for="date">Date</label>
					<input required class="form-control" type="date" id="date" name="date" placeholder="yyyy-mm-dd" value="<?php echo isset($date) ? $date : null; ?>" />
				</div>
				<div class="form-group half-width">
					<label for="startTime">Start</label>
					<input required class="form-control" type="time" id="startTime" name="startTime" placeholder="hh:mm (24h)" value="<?php echo isset($startTime) ? $startTime : null; ?>" />
				</div>
				<div class="form-group half-width">
					<label for="endTime">End</label>
					<input class="form-control" type="time" id="endTime" name="endTime" placeholder="hh:mm (24h)" value="<?php echo isset($endTime) ? $endTime : null; ?>" />
				</div>
				<div class="form-group half-width">
					<label for="endTime">First Table</label>
					<input class="form-control" type="time" id="firstTable" name="firstTable" placeholder="hh:mm (24h)" value="<?php echo isset($firstTable) ? $firstTable : null; ?>" />
				</div>
				<div class="form-group half-width">
					<label for="campHours">Camp Hours</label>
					<input class="form-control" type="number" min="0" step="0.5" id="campHours" name="campHours" placeholder="#" value="<?php echo isset($campHours) ? $campHours : null; ?>" />
				</div>
				<div class="form-group half-width">
					<label for="sales">Sales</label>
					<input class="form-control" type="number" min="0" step="any" id="sales" name="sales" placeholder="$" value="<?php echo isset($sales) ? $sales : null; ?>" />
				</div>
				<div class="form-group half-width">
					<label for="covers">Covers</label>
					<input class="form-control" type="number" min="0" step="1" id="covers" name="covers" placeholder="#" value="<?php echo isset($covers) ? $covers : null; ?>" />
				</div>
				<div class="form-group half-width">
					<label for="tipout">Tipout</label>
					<input class="form-control" type="number" min="0" step="1" id="tipout" name="tipout" placeholder="$" value="<?php echo isset($tipout) ? $tipout : null; ?>" />
				</div>
				<div class="form-group half-width">
					<label for="transfers">Transfers</label>
					<input class="form-control" type="number" min="0" step="1" id="transfers" name="transfers" placeholder="#" value="<?php echo isset($transfers) ? $transfers : null; ?>" />
				</div>
				<div class="form-group half-width">
					<label for="cash">Cash</label>
					<input class="form-control" type="number" min="0" step="1" id="cash" name="cash" placeholder="$" value="<?php echo isset($cash) ? $cash : null; ?>" />
				</div>
				<div class="form-group half-width">
					<label for="due">Due</label>
					<input class="form-control" type="number" min="0" step="1" id="due" name="due" placeholder="$" value="<?php echo isset($due) ? $due : null; ?>" />
				</div>
				<div class="form-group half-width">
					<label for="section">Section</label>
					<input class="form-control" type="text" maxlength="25" id="section" name="section" placeholder="#,#,#" value="<?php echo isset($section) ? $section : null; ?>" />
				</div>
				<div class="form-group half-width">
					<label for="cut">Cut <!--<div class="abbr-wrap"><div id="abbr" title="[S]tay, [G]o, [O]pen, [C]lose, [/]Split, [X]None">?</div></div>--></label>
					<input class="form-control" type="text" maxlength="1" id="cut" name="cut" placeholder="[S]tay, [G]o, etc" value="<?php echo isset($cut) ? $cut : null; ?>" />
				</div>
				<div class="form-group full-width">
					<label for="notes">Notes</label>
					<textarea class="form-control" maxlength="250" name="notes" rows="3"><?php echo isset($notes) ? $notes : null; ?></textarea>
				</div>
				<div class="form-group button-group half-width">
					<button class="link-button button-wide" type="submit" name="submit">Submit</button>
				</div>
				<div class="form-group button-group half-width">
					<a class="link-button button-inverse button-wide" type="button" href="view.php?id=<?php echo isset($id) ? $id : null;?>">Cancel</a>
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