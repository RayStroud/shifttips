<?php
	include 'include/dbconnect.php';
	include 'include/functions.php';

	$id = isset($_GET['id']) ? $_GET['id'] : null;

	if(isset($id))
	{
		//get shift
		$shiftSQL = $db->prepare("SELECT wage, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, noCampHourly, lunchDinner, dayOfWeek
			FROM shift
			WHERE id = ?");
		$shiftSQL->bind_param('i', $id);
		$shiftSQL->execute();
		$shiftSQL->bind_result($wage, $startTime, $endTime, $firstTable, $campHours, $sales, $tipout, $transfers, $cash, $due, $covers, $cut, $section, $notes, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $earnedHourly, $noCampHourly, $lunchDinner, $dayOfWeek);
		$shiftSQL->fetch();
		$shiftSQL->close();

		//* DEBUG */ echo '<p>' . $id . '|' . $wage . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p><p>' . $hours . '|' . $earnedWage . '|' . $earnedTips . '|' . $earnedTotal . '|' . $tipsVsWage . '|' . $salesPerHour . '|' . $salesPerCover . '|' . $tipsPercent . '|' . $tipoutPercent . '|' . $earnedHourly . '|' . $noCampHourly . '|' . $lunchDinner . '|' . $dayOfWeek . '|</p>';

		//split out date and times
		if(!empty($startTime))
		{
			$startDateTime = new DateTime($startTime);
			$date = $startDateTime->format("Y-m-d");
			$startTime = $startDateTime->format("H:i");
			$endTime = !empty($endTime) ? (new DateTime($endTime))->format("H:i") : null;
			$firstTable = !empty($firstTable) ? (new DateTime($firstTable))->format("H:i") : null;
		}

		//* DEBUG */ echo '<p>' . $id . '|' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|</p>';
	}
	else
	{
		//* DEBUG */ echo '<p>No shift ID provided.</p>'; 
	}

	//if submitted
	if( isset($_POST['submit']))
	{
		//get information
		$id = (!empty($_POST['id']) ? $_POST['id'] : null);
		$wage = (!empty($_POST['wage']) ? $_POST['wage'] : null);
		$date = (!empty($_POST['date']) ? $_POST['date'] : null);
		$startTime = (!empty($_POST['date']) && !empty($_POST['startTime']) ? $_POST['date'] . ' ' . $_POST['startTime'] : null);
		$endTime = (!empty($_POST['date']) && !empty($_POST['endTime']) ? $_POST['date'] . ' ' . $_POST['endTime'] : null);
		$firstTable = (!empty($_POST['date']) && !empty($_POST['firstTable']) ? $_POST['date'] . ' ' . $_POST['firstTable'] : null);
		$campHours = ((isset($_POST['campHours']) && is_numeric($_POST['campHours'])) ? $_POST['campHours'] : null);
		$sales = ((isset($_POST['sales']) && is_numeric($_POST['sales'])) ? $_POST['sales'] : null);
		$tipout = ((isset($_POST['tipout']) && is_numeric($_POST['tipout'])) ? $_POST['tipout'] : null);
		$transfers = ((isset($_POST['transfers']) && is_numeric($_POST['transfers'])) ? $_POST['transfers'] : null);
		$cash = ((isset($_POST['cash']) && is_numeric($_POST['cash'])) ? $_POST['cash'] : null);
		$due = ((isset($_POST['due']) && is_numeric($_POST['due'])) ? $_POST['due'] : null);
		$covers = ((isset($_POST['covers']) && is_numeric($_POST['covers'])) ? $_POST['covers'] : null);
		$cut = (!empty($_POST['cut']) ? $_POST['cut'] : null);
		$section = (!empty($_POST['section']) ? $_POST['section'] : null);
		$notes = (!empty($_POST['notes']) ? $_POST['notes'] : null);

		//* DEBUG */ echo '<p>' . $id . '|' . $wage . '|' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p>';

		//adjust endTime if rolls over to next day
		if(isset($endTime) && $endTime < $startTime)
		{
			//add a day to endTime
			$endTimeDateTime = new DateTime($endTime);
			$endTimeDateTime->modify("+ 1day");
			$endTime = $endTimeDateTime->format('Y-m-d H:i:s');
			//* DEBUG */ echo '<p>Changed endTime to: ' . $endTime . '</p>';
		}

		//calculate values
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
		$updateSQL = $db->prepare("UPDATE shift SET wage = ?, startTime = ?, endTime = ?, firstTable = ?, campHours = ?, sales = ?, tipout = ?, transfers = ?, cash = ?, due = ?, covers = ?, cut = ?, section = ?, notes = ?, hours = ?, earnedWage = ?, earnedTips = ?, earnedTotal = ?, tipsVsWage = ?, salesPerHour = ?, salesPerCover = ?, tipsPercent = ?, tipoutPercent = ?, earnedHourly = ?, noCampHourly = ?, lunchDinner = ?, dayOfWeek = ? WHERE id = ?");
		$updateSQL->bind_param('dsssddiidiisssdiiiiiiddiissi', $wage, $startTime, $endTime, $firstTable, $campHours, $sales, $tipout, $transfers, $cash, $due, $covers, $cut, $section, $notes, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $earnedHourly, $noCampHourly, $lunchDinner, $dayOfWeek, $id);
		$updateSQL->execute();
		$updateSQL->close();
		//* DEBUG */ echo '<p>INFO: ' . $db->info . '</p>';

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
	
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">
	<script>
		var abbr = document.getElementById('abbr');
		abbr.onclick = function() {
			abbr.
		};
	</script>
</head>
<body>
	<div id="header">
		<div class="name"><a href=".">Shift Tips</a></div>
		<ul class="menu">
			<li><a href="all.php">View All</a></li>
			<li><a class="active" href="add.php">Add</a></li>
			<li><a href="summary.php">Summary</a></li>
		</ul>
	</div>
	<div id="content">
		<div id="wrapper">
			<h1>Edit Shift</h1>
			<form role="form" method="post" action="#">
				<input hidden id="id" name="id" value="<?php echo isset($id) ? $id : null; ?>" />
				<div class="form-group col-xs-6">
					<label for="wage">Wage</label>
					<input class="form-control" type="number" id="wage" name="wage" min="0" step="0.01" value="<?php echo isset($wage) ? $wage : 9; ?>" />
				</div>
				<div class="form-group col-xs-6">
					<label for="date">Date</label>
					<input required class="form-control" type="date" id="date" name="date" placeholder="yyyy-mm-dd" value="<?php echo isset($date) ? $date : 9; ?>" />
				</div>
				<div class="form-group col-xs-6">
					<label for="startTime">Start</label>
					<input required class="form-control" type="time" id="startTime" name="startTime" placeholder="hh:mm (24h)" value="<?php echo isset($startTime) ? $startTime : 9; ?>" />
				</div>
				<div class="form-group col-xs-6">
					<label for="endTime">End</label>
					<input class="form-control" type="time" id="endTime" name="endTime" placeholder="hh:mm (24h)" value="<?php echo isset($endTime) ? $endTime : 9; ?>" />
				</div>
				<div class="form-group col-xs-6">
					<label for="endTime">First Table</label>
					<input class="form-control" type="time" id="firstTable" name="firstTable" placeholder="hh:mm (24h)" value="<?php echo isset($firstTable) ? $firstTable : 9; ?>" />
				</div>
				<div class="form-group col-xs-6">
					<label for="campHours">Camp Hours</label>
					<input class="form-control" type="number" min="0" step="0.5" id="campHours" name="campHours" placeholder="#" value="<?php echo isset($campHours) ? $campHours : 9; ?>" />
				</div>
				<div class="form-group col-xs-6">
					<label for="sales">Sales</label>
					<input class="form-control" type="number" min="0" step="any" id="sales" name="sales" placeholder="$" value="<?php echo isset($sales) ? $sales : 9; ?>" />
				</div>
				<div class="form-group col-xs-6">
					<label for="covers">Covers</label>
					<input class="form-control" type="number" min="0" step="1" id="covers" name="covers" placeholder="#" value="<?php echo isset($covers) ? $covers : 9; ?>" />
				</div>
				<div class="form-group col-xs-6">
					<label for="tipout">Tipout</label>
					<input class="form-control" type="number" min="0" step="1" id="tipout" name="tipout" placeholder="$" value="<?php echo isset($tipout) ? $tipout : 9; ?>" />
				</div>
				<div class="form-group col-xs-6">
					<label for="transfers">Transfers</label>
					<input class="form-control" type="number" min="0" step="1" id="transfers" name="transfers" placeholder="#" value="<?php echo isset($transfers) ? $transfers : 9; ?>" />
				</div>
				<div class="form-group col-xs-6">
					<label for="cash">Cash</label>
					<input class="form-control" type="number" min="0" step="1" id="cash" name="cash" placeholder="$" value="<?php echo isset($cash) ? $cash : 9; ?>" />
				</div>
				<div class="form-group col-xs-6">
					<label for="due">Due</label>
					<input class="form-control" type="number" min="0" step="1" id="due" name="due" placeholder="$" value="<?php echo isset($due) ? $due : 9; ?>" />
				</div>
				<div class="form-group col-xs-6">
					<label for="section">Section</label>
					<input class="form-control" maxlength="25" id="section" name="section" placeholder="#,#,#" value="<?php echo isset($section) ? $section : 9; ?>" />
				</div>
				<div class="form-group col-xs-6">
					<label for="cut">Cut <!--<div class="abbr-wrap"><div id="abbr" title="[S]tay, [G]o, [O]pen, [C]lose, [/]Split, [X]None">?</div></div>--></label>
					<input class="form-control" maxlength="1" id="cut" name="cut" placeholder="[S]tay, [G]o, etc" value="<?php echo isset($cut) ? $cut : 9; ?>" />
				</div>
				<div class="form-group col-xs-12">
					<label for="notes">Notes</label>
					<textarea class="form-control" maxlength="250" name="notes" rows="3"><?php echo isset($notes) ? $notes : 9; ?></textarea>
				</div>
				<div class="form-group col-xs-12">
					<button class="btn btn-default" type="submit" name="submit">Submit</button>
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
				'location=no, menubar=no, ' + 'status=no, toolbar=no, scrollbars=no, resizable=no');
			win.resizeTo(w, h);
			win.focus();
		}
	</script>
</body>
</html>