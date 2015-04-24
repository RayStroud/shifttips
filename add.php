<?php
	include 'dbconnect.php';
	include 'functions.php';

	//if submitted
	if( isset($_POST['submit']))
	{
		//get information
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

		//* DEBUG */ echo '<p>' . $wage . '|' . $date . '|' . $startTime . '|' . $endTime . '|' . $firstTable . '|' . $campHours . '|' . $sales . '|' . $tipout . '|' . $transfers . '|' . $cash . '|' . $due . '|' . $covers . '|' . $cut . '|' . $section . '|' . $notes . '|</p>';

		//check if start date isn't null and already exists
		if(isset($startTime))
		{
			$checkSQL = $db->prepare("SELECT startTime FROM shift WHERE startTime = ?");
			$checkSQL->bind_param('s', $startTime);
			$checkSQL->execute();
			$checkSQL->bind_result($checkSQLResult);
			$checkSQL->store_result();
			$checkSQLNumber = $checkSQL->num_rows;
			//* DEBUG */ echo '<p>Number of results: ' . $checkSQLNumber . '</p>';

			//don't insert a duplicate record
			//if($checkSQLNumber == 0)
			/* DEBUG */ if(true)
			{
				//refresh connection -- it needed this before it was in the conditional
				$db->close();
				include 'dbconnect.php';

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
				$insertSQL = $db->prepare("INSERT INTO shift (wage, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, noCampHourly, lunchDinner, dayOfWeek) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
				$insertSQL->bind_param('dsssddiidiisssdiiiiiiddiiss', $wage, $startTime, $endTime, $firstTable, $campHours, $sales, $tipout, $transfers, $cash, $due, $covers, $cut, $section, $notes, $hours, $earnedWage, $earnedTips, $earnedTotal, $tipsVsWage, $salesPerHour, $salesPerCover, $tipsPercent, $tipoutPercent, $earnedHourly, $noCampHourly, $lunchDinner, $dayOfWeek);
				$insertSQL->execute();
				//* DEBUG */ echo '<p>Row inserted</p>';
			}
		}
	}

	//close connection
	$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Add Shift - Shift Tips</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="stylesheet" type="text/css" href="bootstrap.min.css">
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<div id="header" class="clickable">
		<div class="name">Shift Tips</div>
		<a href="."><span class="link-spanner"></span></a>
	</div>
	<div id="content">
		<div id="wrapper">
			<h1>Add Shift</h1>
			<form role="form" method="post" action="#">
				<div class="form-group col-xs-6">
					<label for="wage">Wage</label>
					<input class="form-control" type="number" id="wage" name="wage" value="9" min="0" step="0.01"/>
				</div>
				<div class="form-group col-xs-6">
					<label for="date">Date</label>
					<div class="form-control">
						<input required type="date" id="date" name="date" />
					</div>
				</div>
				<div class="form-group col-xs-6">
					<label for="startTime">Start</label>
					<div class="form-control">
						<input required type="time" id="startTime" name="startTime" />
					</div>
				</div>
				<div class="form-group col-xs-6">
					<label for="endTime">End</label>
					<div class="form-control">
						<input type="time" id="endTime" name="endTime" />
					</div>
				</div>
				<div class="form-group col-xs-6">
					<label for="endTime">First Table</label>
					<div class="form-control">
						<input type="time" id="firstTable" name="firstTable" />
					</div>
				</div>
				<div class="form-group col-xs-6">
					<label for="campHours">Camp Hours</label>
					<input class="form-control" type="number" min="0" step="0.5" id="campHours" name="campHours"/>
				</div>
				<div class="form-group col-xs-6">
					<label for="sales">Sales</label>
					<input class="form-control" type="number" min="0" step="any" id="sales" name="sales"/>
				</div>
				<div class="form-group col-xs-6">
					<label for="covers">Covers</label>
					<input class="form-control" type="number" min="0" step="1" id="covers" name="covers"/>
				</div>
				<div class="form-group col-xs-6">
					<label for="tipout">Tipout</label>
					<input class="form-control" type="number" min="0" step="1" id="tipout" name="tipout"/>
				</div>
				<div class="form-group col-xs-6">
					<label for="transfers">Transfers</label>
					<input class="form-control" type="number" min="0" step="1" id="transfers" name="transfers"/>
				</div>
				<div class="form-group col-xs-6">
					<label for="cash">Cash</label>
					<input class="form-control" type="number" min="0" step="1" id="cash" name="cash"/>
				</div>
				<div class="form-group col-xs-6">
					<label for="due">Due</label>
					<input class="form-control" type="number" min="0" step="1" id="due" name="due"/>
				</div>
				<div class="form-group col-xs-6">
					<label for="section">Section</label>
					<input class="form-control" maxlength="25" id="section" name="section"/>
				</div>
				<div class="form-group col-xs-6">
					<label for="cut">Cut</label>
					<input class="form-control" maxlength="1" id="cut" name="cut"/>
				</div>
				<div class="form-group col-xs-12">
					<label for="notes">Notes</label>
					<textarea class="form-control" maxlength="250" name="notes" rows="3"></textarea>
				</div>
				<div class="form-group col-xs-12">
					<button class="btn btn-default" type="submit" name="submit">Submit</button>
				</div>
			</form>
		</div>
	</div>
	<div id="footer">
		<a href="#" target="popup" onClick="wopen('#', 'popup', 320, 480); return false;">DEBUG MOBILE - POPUP</a>
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