<?php
	include 'include/dbconnect.php';

	//extract dates if set, or use defaults
	try { $dateTimeFrom = !empty($_GET['from']) ? new DateTime($_GET['from']) : null; } catch(Exception $e) { $dateTimeFrom = null; }
	try { $dateTimeTo = !empty($_GET['to']) ? new DateTime($_GET['to']) : null; } catch(Exception $e) { $dateTimeTo = null; }
	$p_dateFrom = !empty($dateTimeFrom) ? "'" . $dateTimeFrom->format("Y-m-d") . "'" : null; 
	$p_dateTo = !empty($dateTimeTo) ? "'" . $dateTimeTo->format("Y-m-d") . "'" : null; 
	//* DEBUG */ echo '<p>|dateFrom:' . $p_dateFrom . '|dateTo:' . $p_dateTo . '|</p>';

	//set up variables in database
	$db->query("SET @p_dateFrom = " . $p_dateFrom . ";");
	$db->query("SET @p_dateTo = " . $p_dateTo . ";");

	//calculate summaries
	$summariesResult = $db->query('CALL calculateSummaries(@p_dateFrom, @p_dateTo);');

	$summaries = []; 
	while($row = $summariesResult->fetch_assoc())
	{
		$lunchDinner = $row['lunchDinner'];
		$dayOfWeek = $row['dayOfWeek'];

		$summaries[$lunchDinner][$dayOfWeek]['id'] = $row['id'];
		$summaries[$lunchDinner][$dayOfWeek]['count'] = $row['count'];
		$summaries[$lunchDinner][$dayOfWeek]['avgHours'] = $row['avgHours'];
		$summaries[$lunchDinner][$dayOfWeek]['totHours'] = $row['totHours'];
		$summaries[$lunchDinner][$dayOfWeek]['avgWage'] = number_format($row['avgWage'],2);
		$summaries[$lunchDinner][$dayOfWeek]['totWage'] = number_format($row['totWage'],0);
		$summaries[$lunchDinner][$dayOfWeek]['avgTips'] = number_format($row['avgTips'],2);
		$summaries[$lunchDinner][$dayOfWeek]['totTips'] = number_format($row['totTips'],0);
		$summaries[$lunchDinner][$dayOfWeek]['avgTipout'] = number_format($row['avgTipout'],2);
		$summaries[$lunchDinner][$dayOfWeek]['totTipout'] = number_format($row['totTipout'],0);
		$summaries[$lunchDinner][$dayOfWeek]['avgSales'] = number_format($row['avgSales'],0);
		$summaries[$lunchDinner][$dayOfWeek]['totSales'] = number_format($row['totSales'],0);
		$summaries[$lunchDinner][$dayOfWeek]['avgCovers'] = $row['avgCovers'];
		$summaries[$lunchDinner][$dayOfWeek]['totCovers'] = $row['totCovers'];
		$summaries[$lunchDinner][$dayOfWeek]['avgCampHours'] = $row['avgCampHours'];
		$summaries[$lunchDinner][$dayOfWeek]['totCampHours'] = $row['totCampHours'];
		$summaries[$lunchDinner][$dayOfWeek]['salesPerHour'] = number_format($row['salesPerHour'],0);
		$summaries[$lunchDinner][$dayOfWeek]['salesPerCover'] = number_format($row['salesPerCover'],0);
		$summaries[$lunchDinner][$dayOfWeek]['tipsPercent'] = $row['tipsPercent'];
		$summaries[$lunchDinner][$dayOfWeek]['tipoutPercent'] = $row['tipoutPercent'];
		$summaries[$lunchDinner][$dayOfWeek]['tipsVsWage'] = $row['tipsVsWage'];
		$summaries[$lunchDinner][$dayOfWeek]['hourlyWage'] = number_format($row['hourlyWage'],2);
		$summaries[$lunchDinner][$dayOfWeek]['timestamp'] = $row['timestamp'];

		$summaries[$lunchDinner][$dayOfWeek]['avgEarned'] = number_format($row['avgWage'] + $row['avgTips'],2);
		$summaries[$lunchDinner][$dayOfWeek]['totEarned'] = number_format($row['totWage'] + $row['totTips'],0);		
	}

	//* DEBUG */ echo 'NUM RESULTS: ' . $summariesResult->num_rows . '<pre>'; print_r($summaries); echo '</pre>';

	//close connection
	$db->close();

	//make nice message to display the filter parameters
	$filterMessage = 'Viewing ';
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
	<title>Summary - Shift Tips</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<div id="header">
		<div class="name"><a href="index.php">Shift Tips</a></div>
		<ul class="menu">
			<li><a class="link-button" href="shifts.php">Shifts</a></li>
			<li><a class="active link-button" href="summary.php">Summary</a></li>
			<li><a class="link-button" href="add.php">Add</a></li>
		</ul>
	</div>
	<div id="content">
		<div id="wrapper">
			<h1>Summary</h1>

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
					<div class="filter-group" id="filter">
						<div class="filter-group-wrap">
							<div class="filter-group-header">Filter</div>
							<div class="filter-group-body">
								<button class="link-button" type="submit">Filter</button> 
								<!--a class="link-button" href="shifts.php?week=on">Weekly</a-->
								<a class="link-button" href="<?php echo $_SERVER['PHP_SELF']; ?>">All</a>
							</div>
						</div>
					</div>
				</form>
			</div>
			
			<h3>
				<?php echo !empty($filterMessage) ? $filterMessage : 'null'; ?> 
			</h3>

			<div class="mobile-table">
				<table class="summary-table">
					<tr>
						<th class="hdr-avg-cell">Type</th>
						<th class="hdr-avg-cell">#</th>
						<th class="hdr-avg-cell">Hours</th>
						<th class="hdr-avg-cell">Wage</th>
						<th class="hdr-avg-cell">Tips</th>
						<th class="hdr-avg-cell">Tipout</th>
						<th class="hdr-avg-cell">Earned</th>
						<th class="hdr-avg-cell">Sales</th>
						<th class="hdr-avg-cell">Covers</th>
						<th class="hdr-avg-cell">Camp</th>
						<th class="hdr-avg-cell">Sales/h</th>
						<th class="hdr-avg-cell">Sales/cov</th>
						<th class="hdr-avg-cell">%Tips</th>
						<th class="hdr-avg-cell">%T/O</th>
						<th class="hdr-avg-cell">%TvsW</th>
						<th class="hdr-avg-cell">Earn/h</th>
					</tr>
					<tr>
						<td class="bth-avg-cell" rowspan="2">All</td>
						<td class="bth-avg-cell" rowspan="2"><?php echo $summaries['%']['%']['count']; ?></td>
						<td class="bth-avg-cell"><?php echo $summaries['%']['%']['avgHours'] . ' h'; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgWage']; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgTips']; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgTipout']; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgEarned']; ?></td>
						<td class="bth-avg-cell"><?php echo '$' . $summaries['%']['%']['avgSales']; ?></td>
						<td class="bth-avg-cell"><?php echo $summaries['%']['%']['avgCovers'] . ' cov'; ?></td>
						<td class="bth-avg-cell"><?php echo $summaries['%']['%']['avgCampHours'] . ' h'; ?></td>
						<td class="bth-avg-cell" rowspan="2"><?php echo '$' . $summaries['%']['%']['salesPerHour'] . '/h'; ?></td>
						<td class="bth-avg-cell" rowspan="2"><?php echo '$' . $summaries['%']['%']['salesPerCover'] . '/cov'; ?></td>
						<td class="bth-avg-cell" rowspan="2"><?php echo $summaries['%']['%']['tipsPercent'] . '%'; ?></td>
						<td class="bth-avg-cell" rowspan="2"><?php echo $summaries['%']['%']['tipoutPercent'] . '%'; ?></td>
						<td class="bth-avg-cell" rowspan="2"><?php echo $summaries['%']['%']['tipsVsWage'] . '%'; ?></td>
						<td class="bth-avg-cell" rowspan="2"><?php echo '$' . $summaries['%']['%']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="bth-tot-cell"><?php echo $summaries['%']['%']['totHours'] . ' h'; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totWage']; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totTips']; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totTipout']; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totEarned']; ?></td>
						<td class="bth-tot-cell"><?php echo '$' . $summaries['%']['%']['totSales']; ?></td>
						<td class="bth-tot-cell"><?php echo $summaries['%']['%']['totCovers'] . ' cov'; ?></td>
						<td class="bth-tot-cell"><?php echo $summaries['%']['%']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="din-avg-cell" rowspan="2">PM</td>
						<td class="din-avg-cell" rowspan="2"><?php echo $summaries['D']['%']['count']; ?></td>
						<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgHours'] . ' h'; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgWage']; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgTips']; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgTipout']; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgEarned']; ?></td>
						<td class="din-avg-cell"><?php echo '$' . $summaries['D']['%']['avgSales']; ?></td>
						<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgCovers'] . ' cov'; ?></td>
						<td class="din-avg-cell"><?php echo $summaries['D']['%']['avgCampHours'] . ' h'; ?></td>
						<td class="din-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['%']['salesPerHour'] . '/h'; ?></td>
						<td class="din-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['%']['salesPerCover'] . '/cov'; ?></td>
						<td class="din-avg-cell" rowspan="2"><?php echo $summaries['D']['%']['tipsPercent'] . '%'; ?></td>
						<td class="din-avg-cell" rowspan="2"><?php echo $summaries['D']['%']['tipoutPercent'] . '%'; ?></td>
						<td class="din-avg-cell" rowspan="2"><?php echo $summaries['D']['%']['tipsVsWage'] . '%'; ?></td>
						<td class="din-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['%']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="din-tot-cell"><?php echo $summaries['D']['%']['totHours'] . ' h'; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totWage']; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totTips']; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totTipout']; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totEarned']; ?></td>
						<td class="din-tot-cell"><?php echo '$' . $summaries['D']['%']['totSales']; ?></td>
						<td class="din-tot-cell"><?php echo $summaries['D']['%']['totCovers'] . ' cov'; ?></td>
						<td class="din-tot-cell"><?php echo $summaries['D']['%']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="lun-avg-cell" rowspan="2">AM</td>
						<td class="lun-avg-cell" rowspan="2"><?php echo $summaries['L']['%']['count']; ?></td>
						<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgHours'] . ' h'; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgWage']; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgTips']; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgTipout']; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgEarned']; ?></td>
						<td class="lun-avg-cell"><?php echo '$' . $summaries['L']['%']['avgSales']; ?></td>
						<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgCovers'] . ' cov'; ?></td>
						<td class="lun-avg-cell"><?php echo $summaries['L']['%']['avgCampHours'] . ' h'; ?></td>
						<td class="lun-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['%']['salesPerHour'] . '/h'; ?></td>
						<td class="lun-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['%']['salesPerCover'] . '/cov'; ?></td>
						<td class="lun-avg-cell" rowspan="2"><?php echo $summaries['L']['%']['tipsPercent'] . '%'; ?></td>
						<td class="lun-avg-cell" rowspan="2"><?php echo $summaries['L']['%']['tipoutPercent'] . '%'; ?></td>
						<td class="lun-avg-cell" rowspan="2"><?php echo $summaries['L']['%']['tipsVsWage'] . '%'; ?></td>
						<td class="lun-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['%']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="lun-tot-cell"><?php echo $summaries['L']['%']['totHours'] . ' h'; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totWage']; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totTips']; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totTipout']; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totEarned']; ?></td>
						<td class="lun-tot-cell"><?php echo '$' . $summaries['L']['%']['totSales']; ?></td>
						<td class="lun-tot-cell"><?php echo $summaries['L']['%']['totCovers'] . ' cov'; ?></td>
						<td class="lun-tot-cell"><?php echo $summaries['L']['%']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="spl-avg-cell" rowspan="2">Split /</td>
						<td class="spl-avg-cell" rowspan="2"><?php echo $summaries['S']['%']['count']; ?></td>
						<td class="spl-avg-cell"><?php echo $summaries['S']['%']['avgHours'] . ' h'; ?></td>
						<td class="spl-avg-cell"><?php echo '$' . $summaries['S']['%']['avgWage']; ?></td>
						<td class="spl-avg-cell"><?php echo '$' . $summaries['S']['%']['avgTips']; ?></td>
						<td class="spl-avg-cell"><?php echo '$' . $summaries['S']['%']['avgTipout']; ?></td>
						<td class="spl-avg-cell"><?php echo '$' . $summaries['S']['%']['avgEarned']; ?></td>
						<td class="spl-avg-cell"><?php echo '$' . $summaries['S']['%']['avgSales']; ?></td>
						<td class="spl-avg-cell"><?php echo $summaries['S']['%']['avgCovers'] . ' cov'; ?></td>
						<td class="spl-avg-cell"><?php echo $summaries['S']['%']['avgCampHours'] . ' h'; ?></td>
						<td class="spl-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['%']['salesPerHour'] . '/h'; ?></td>
						<td class="spl-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['%']['salesPerCover'] . '/cov'; ?></td>
						<td class="spl-avg-cell" rowspan="2"><?php echo $summaries['S']['%']['tipsPercent'] . '%'; ?></td>
						<td class="spl-avg-cell" rowspan="2"><?php echo $summaries['S']['%']['tipoutPercent'] . '%'; ?></td>
						<td class="spl-avg-cell" rowspan="2"><?php echo $summaries['S']['%']['tipsVsWage'] . '%'; ?></td>
						<td class="spl-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['%']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="spl-tot-cell"><?php echo $summaries['S']['%']['totHours'] . ' h'; ?></td>
						<td class="spl-tot-cell"><?php echo '$' . $summaries['S']['%']['totWage']; ?></td>
						<td class="spl-tot-cell"><?php echo '$' . $summaries['S']['%']['totTips']; ?></td>
						<td class="spl-tot-cell"><?php echo '$' . $summaries['S']['%']['totTipout']; ?></td>
						<td class="spl-tot-cell"><?php echo '$' . $summaries['S']['%']['totEarned']; ?></td>
						<td class="spl-tot-cell"><?php echo '$' . $summaries['S']['%']['totSales']; ?></td>
						<td class="spl-tot-cell"><?php echo $summaries['S']['%']['totCovers'] . ' cov'; ?></td>
						<td class="spl-tot-cell"><?php echo $summaries['S']['%']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="wkl-avg-cell" rowspan="2">Weekly</td>
						<td class="wkl-avg-cell" rowspan="2"><?php echo $summaries['-']['Wkl']['count']; ?></td>
						<td class="wkl-avg-cell"><?php echo $summaries['-']['Wkl']['avgHours'] . ' h'; ?></td>
						<td class="wkl-avg-cell"><?php echo '$' . $summaries['-']['Wkl']['avgWage']; ?></td>
						<td class="wkl-avg-cell"><?php echo '$' . $summaries['-']['Wkl']['avgTips']; ?></td>
						<td class="wkl-avg-cell"><?php echo '$' . $summaries['-']['Wkl']['avgTipout']; ?></td>
						<td class="wkl-avg-cell"><?php echo '$' . $summaries['-']['Wkl']['avgEarned']; ?></td>
						<td class="wkl-avg-cell"><?php echo '$' . $summaries['-']['Wkl']['avgSales']; ?></td>
						<td class="wkl-avg-cell"><?php echo $summaries['-']['Wkl']['avgCovers'] . ' cov'; ?></td>
						<td class="wkl-avg-cell"><?php echo $summaries['-']['Wkl']['avgCampHours'] . ' h'; ?></td>
						<td class="wkl-avg-cell" rowspan="2"><?php echo '$' . $summaries['-']['Wkl']['salesPerHour'] . '/h'; ?></td>
						<td class="wkl-avg-cell" rowspan="2"><?php echo '$' . $summaries['-']['Wkl']['salesPerCover'] . '/cov'; ?></td>
						<td class="wkl-avg-cell" rowspan="2"><?php echo $summaries['-']['Wkl']['tipsPercent'] . '%'; ?></td>
						<td class="wkl-avg-cell" rowspan="2"><?php echo $summaries['-']['Wkl']['tipoutPercent'] . '%'; ?></td>
						<td class="wkl-avg-cell" rowspan="2"><?php echo $summaries['-']['Wkl']['tipsVsWage'] . '%'; ?></td>
						<td class="wkl-avg-cell" rowspan="2"><?php echo '$' . $summaries['-']['Wkl']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="wkl-tot-cell"><?php echo $summaries['-']['Wkl']['totHours'] . ' h'; ?></td>
						<td class="wkl-tot-cell"><?php echo '$' . $summaries['-']['Wkl']['totWage']; ?></td>
						<td class="wkl-tot-cell"><?php echo '$' . $summaries['-']['Wkl']['totTips']; ?></td>
						<td class="wkl-tot-cell"><?php echo '$' . $summaries['-']['Wkl']['totTipout']; ?></td>
						<td class="wkl-tot-cell"><?php echo '$' . $summaries['-']['Wkl']['totEarned']; ?></td>
						<td class="wkl-tot-cell"><?php echo '$' . $summaries['-']['Wkl']['totSales']; ?></td>
						<td class="wkl-tot-cell"><?php echo $summaries['-']['Wkl']['totCovers'] . ' cov'; ?></td>
						<td class="wkl-tot-cell"><?php echo $summaries['-']['Wkl']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="mth-avg-cell" rowspan="2">Monthly</td>
						<td class="mth-avg-cell" rowspan="2"><?php echo $summaries['-']['Mth']['count']; ?></td>
						<td class="mth-avg-cell"><?php echo $summaries['-']['Mth']['avgHours'] . ' h'; ?></td>
						<td class="mth-avg-cell"><?php echo '$' . $summaries['-']['Mth']['avgWage']; ?></td>
						<td class="mth-avg-cell"><?php echo '$' . $summaries['-']['Mth']['avgTips']; ?></td>
						<td class="mth-avg-cell"><?php echo '$' . $summaries['-']['Mth']['avgTipout']; ?></td>
						<td class="mth-avg-cell"><?php echo '$' . $summaries['-']['Mth']['avgEarned']; ?></td>
						<td class="mth-avg-cell"><?php echo '$' . $summaries['-']['Mth']['avgSales']; ?></td>
						<td class="mth-avg-cell"><?php echo $summaries['-']['Mth']['avgCovers'] . ' cov'; ?></td>
						<td class="mth-avg-cell"><?php echo $summaries['-']['Mth']['avgCampHours'] . ' h'; ?></td>
						<td class="mth-avg-cell" rowspan="2"><?php echo '$' . $summaries['-']['Mth']['salesPerHour'] . '/h'; ?></td>
						<td class="mth-avg-cell" rowspan="2"><?php echo '$' . $summaries['-']['Mth']['salesPerCover'] . '/cov'; ?></td>
						<td class="mth-avg-cell" rowspan="2"><?php echo $summaries['-']['Mth']['tipsPercent'] . '%'; ?></td>
						<td class="mth-avg-cell" rowspan="2"><?php echo $summaries['-']['Mth']['tipoutPercent'] . '%'; ?></td>
						<td class="mth-avg-cell" rowspan="2"><?php echo $summaries['-']['Mth']['tipsVsWage'] . '%'; ?></td>
						<td class="mth-avg-cell" rowspan="2"><?php echo '$' . $summaries['-']['Mth']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="mth-tot-cell"><?php echo $summaries['-']['Mth']['totHours'] . ' h'; ?></td>
						<td class="mth-tot-cell"><?php echo '$' . $summaries['-']['Mth']['totWage']; ?></td>
						<td class="mth-tot-cell"><?php echo '$' . $summaries['-']['Mth']['totTips']; ?></td>
						<td class="mth-tot-cell"><?php echo '$' . $summaries['-']['Mth']['totTipout']; ?></td>
						<td class="mth-tot-cell"><?php echo '$' . $summaries['-']['Mth']['totEarned']; ?></td>
						<td class="mth-tot-cell"><?php echo '$' . $summaries['-']['Mth']['totSales']; ?></td>
						<td class="mth-tot-cell"><?php echo $summaries['-']['Mth']['totCovers'] . ' cov'; ?></td>
						<td class="mth-tot-cell"><?php echo $summaries['-']['Mth']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<th class="hdr-avg-cell">Type</th>
						<th class="hdr-avg-cell">#</th>
						<th class="hdr-avg-cell">Hours</th>
						<th class="hdr-avg-cell">Wage</th>
						<th class="hdr-avg-cell">Tips</th>
						<th class="hdr-avg-cell">Tipout</th>
						<th class="hdr-avg-cell">Earned</th>
						<th class="hdr-avg-cell">Sales</th>
						<th class="hdr-avg-cell">Covers</th>
						<th class="hdr-avg-cell">Camp</th>
						<th class="hdr-avg-cell">Sales/h</th>
						<th class="hdr-avg-cell">Sales/cov</th>
						<th class="hdr-avg-cell">%Tips</th>
						<th class="hdr-avg-cell">%T/O</th>
						<th class="hdr-avg-cell">%TvsW</th>
						<th class="hdr-avg-cell">Earn/h</th>
					</tr>
					<tr>
						<td class="din-avg-cell" rowspan="2">Mon PM</td>
						<td class="mon-avg-cell" rowspan="2"><?php echo $summaries['D']['Mon']['count']; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['avgHours'] . ' h'; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgWage']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgTips']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgTipout']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgEarned']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['D']['Mon']['avgSales']; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['avgCovers'] . ' cov'; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['D']['Mon']['avgCampHours'] . ' h'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Mon']['salesPerHour'] . '/h'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Mon']['salesPerCover'] . '/cov'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo $summaries['D']['Mon']['tipsPercent'] . '%'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo $summaries['D']['Mon']['tipoutPercent'] . '%'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo $summaries['D']['Mon']['tipsVsWage'] . '%'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Mon']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="mon-tot-cell"><?php echo $summaries['D']['Mon']['totHours'] . ' h'; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totWage']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totTips']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totTipout']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totEarned']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['D']['Mon']['totSales']; ?></td>
						<td class="mon-tot-cell"><?php echo $summaries['D']['Mon']['totCovers'] . ' cov'; ?></td>
						<td class="mon-tot-cell"><?php echo $summaries['D']['Mon']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="din-avg-cell" rowspan="2">Tue PM</td>
						<td class="tue-avg-cell" rowspan="2"><?php echo $summaries['D']['Tue']['count']; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['avgHours'] . ' h'; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgWage']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgTips']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgTipout']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgEarned']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['D']['Tue']['avgSales']; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['avgCovers'] . ' cov'; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['D']['Tue']['avgCampHours'] . ' h'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Tue']['salesPerHour'] . '/h'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Tue']['salesPerCover'] . '/cov'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo $summaries['D']['Tue']['tipsPercent'] . '%'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo $summaries['D']['Tue']['tipoutPercent'] . '%'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo $summaries['D']['Tue']['tipsVsWage'] . '%'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Tue']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="tue-tot-cell"><?php echo $summaries['D']['Tue']['totHours'] . ' h'; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totWage']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totTips']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totTipout']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totEarned']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['D']['Tue']['totSales']; ?></td>
						<td class="tue-tot-cell"><?php echo $summaries['D']['Tue']['totCovers'] . ' cov'; ?></td>
						<td class="tue-tot-cell"><?php echo $summaries['D']['Tue']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="din-avg-cell" rowspan="2">Wed PM</td>
						<td class="wed-avg-cell" rowspan="2"><?php echo $summaries['D']['Wed']['count']; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['avgHours'] . ' h'; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgWage']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgTips']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgTipout']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgEarned']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['D']['Wed']['avgSales']; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['avgCovers'] . ' cov'; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['D']['Wed']['avgCampHours'] . ' h'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Wed']['salesPerHour'] . '/h'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Wed']['salesPerCover'] . '/cov'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo $summaries['D']['Wed']['tipsPercent'] . '%'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo $summaries['D']['Wed']['tipoutPercent'] . '%'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo $summaries['D']['Wed']['tipsVsWage'] . '%'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Wed']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="wed-tot-cell"><?php echo $summaries['D']['Wed']['totHours'] . ' h'; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totWage']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totTips']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totTipout']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totEarned']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['D']['Wed']['totSales']; ?></td>
						<td class="wed-tot-cell"><?php echo $summaries['D']['Wed']['totCovers'] . ' cov'; ?></td>
						<td class="wed-tot-cell"><?php echo $summaries['D']['Wed']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="din-avg-cell" rowspan="2">Thu PM</td>
						<td class="thu-avg-cell" rowspan="2"><?php echo $summaries['D']['Thu']['count']; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['avgHours'] . ' h'; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgWage']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgTips']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgTipout']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgEarned']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['D']['Thu']['avgSales']; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['avgCovers'] . ' cov'; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['D']['Thu']['avgCampHours'] . ' h'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Thu']['salesPerHour'] . '/h'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Thu']['salesPerCover'] . '/cov'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo $summaries['D']['Thu']['tipsPercent'] . '%'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo $summaries['D']['Thu']['tipoutPercent'] . '%'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo $summaries['D']['Thu']['tipsVsWage'] . '%'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Thu']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="thu-tot-cell"><?php echo $summaries['D']['Thu']['totHours'] . ' h'; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totWage']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totTips']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totTipout']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totEarned']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['D']['Thu']['totSales']; ?></td>
						<td class="thu-tot-cell"><?php echo $summaries['D']['Thu']['totCovers'] . ' cov'; ?></td>
						<td class="thu-tot-cell"><?php echo $summaries['D']['Thu']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="din-avg-cell" rowspan="2">Fri PM</td>
						<td class="fri-avg-cell" rowspan="2"><?php echo $summaries['D']['Fri']['count']; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['avgHours'] . ' h'; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgWage']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgTips']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgTipout']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgEarned']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['D']['Fri']['avgSales']; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['avgCovers'] . ' cov'; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['D']['Fri']['avgCampHours'] . ' h'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Fri']['salesPerHour'] . '/h'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Fri']['salesPerCover'] . '/cov'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo $summaries['D']['Fri']['tipsPercent'] . '%'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo $summaries['D']['Fri']['tipoutPercent'] . '%'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo $summaries['D']['Fri']['tipsVsWage'] . '%'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Fri']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="fri-tot-cell"><?php echo $summaries['D']['Fri']['totHours'] . ' h'; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totWage']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totTips']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totTipout']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totEarned']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['D']['Fri']['totSales']; ?></td>
						<td class="fri-tot-cell"><?php echo $summaries['D']['Fri']['totCovers'] . ' cov'; ?></td>
						<td class="fri-tot-cell"><?php echo $summaries['D']['Fri']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="din-avg-cell" rowspan="2">Sat PM</td>
						<td class="sat-avg-cell" rowspan="2"><?php echo $summaries['D']['Sat']['count']; ?></td>
						<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['avgHours'] . ' h'; ?></td>
						<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgWage']; ?></td>
						<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgTips']; ?></td>
						<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgTipout']; ?></td>
						<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgEarned']; ?></td>
						<td class="sat-avg-cell"><?php echo '$' . $summaries['D']['Sat']['avgSales']; ?></td>
						<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['avgCovers'] . ' cov'; ?></td>
						<td class="sat-avg-cell"><?php echo $summaries['D']['Sat']['avgCampHours'] . ' h'; ?></td>
						<td class="sat-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Sat']['salesPerHour'] . '/h'; ?></td>
						<td class="sat-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Sat']['salesPerCover'] . '/cov'; ?></td>
						<td class="sat-avg-cell" rowspan="2"><?php echo $summaries['D']['Sat']['tipsPercent'] . '%'; ?></td>
						<td class="sat-avg-cell" rowspan="2"><?php echo $summaries['D']['Sat']['tipoutPercent'] . '%'; ?></td>
						<td class="sat-avg-cell" rowspan="2"><?php echo $summaries['D']['Sat']['tipsVsWage'] . '%'; ?></td>
						<td class="sat-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Sat']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="sat-tot-cell"><?php echo $summaries['D']['Sat']['totHours'] . ' h'; ?></td>
						<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totWage']; ?></td>
						<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totTips']; ?></td>
						<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totTipout']; ?></td>
						<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totEarned']; ?></td>
						<td class="sat-tot-cell"><?php echo '$' . $summaries['D']['Sat']['totSales']; ?></td>
						<td class="sat-tot-cell"><?php echo $summaries['D']['Sat']['totCovers'] . ' cov'; ?></td>
						<td class="sat-tot-cell"><?php echo $summaries['D']['Sat']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="din-avg-cell" rowspan="2">Sun PM</td>
						<td class="sun-avg-cell" rowspan="2"><?php echo $summaries['D']['Sun']['count']; ?></td>
						<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['avgHours'] . ' h'; ?></td>
						<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgWage']; ?></td>
						<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgTips']; ?></td>
						<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgTipout']; ?></td>
						<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgEarned']; ?></td>
						<td class="sun-avg-cell"><?php echo '$' . $summaries['D']['Sun']['avgSales']; ?></td>
						<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['avgCovers'] . ' cov'; ?></td>
						<td class="sun-avg-cell"><?php echo $summaries['D']['Sun']['avgCampHours'] . ' h'; ?></td>
						<td class="sun-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Sun']['salesPerHour'] . '/h'; ?></td>
						<td class="sun-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Sun']['salesPerCover'] . '/cov'; ?></td>
						<td class="sun-avg-cell" rowspan="2"><?php echo $summaries['D']['Sun']['tipsPercent'] . '%'; ?></td>
						<td class="sun-avg-cell" rowspan="2"><?php echo $summaries['D']['Sun']['tipoutPercent'] . '%'; ?></td>
						<td class="sun-avg-cell" rowspan="2"><?php echo $summaries['D']['Sun']['tipsVsWage'] . '%'; ?></td>
						<td class="sun-avg-cell" rowspan="2"><?php echo '$' . $summaries['D']['Sun']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="sun-tot-cell"><?php echo $summaries['D']['Sun']['totHours'] . ' h'; ?></td>
						<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totWage']; ?></td>
						<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totTips']; ?></td>
						<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totTipout']; ?></td>
						<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totEarned']; ?></td>
						<td class="sun-tot-cell"><?php echo '$' . $summaries['D']['Sun']['totSales']; ?></td>
						<td class="sun-tot-cell"><?php echo $summaries['D']['Sun']['totCovers'] . ' cov'; ?></td>
						<td class="sun-tot-cell"><?php echo $summaries['D']['Sun']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<th class="hdr-avg-cell">Type</th>
						<th class="hdr-avg-cell">#</th>
						<th class="hdr-avg-cell">Hours</th>
						<th class="hdr-avg-cell">Wage</th>
						<th class="hdr-avg-cell">Tips</th>
						<th class="hdr-avg-cell">Tipout</th>
						<th class="hdr-avg-cell">Earned</th>
						<th class="hdr-avg-cell">Sales</th>
						<th class="hdr-avg-cell">Covers</th>
						<th class="hdr-avg-cell">Camp</th>
						<th class="hdr-avg-cell">Sales/h</th>
						<th class="hdr-avg-cell">Sales/cov</th>
						<th class="hdr-avg-cell">%Tips</th>
						<th class="hdr-avg-cell">%T/O</th>
						<th class="hdr-avg-cell">%TvsW</th>
						<th class="hdr-avg-cell">Earn/h</th>
					</tr>
					<tr>
						<td class="lun-avg-cell" rowspan="2">Mon AM</td>
						<td class="mon-avg-cell" rowspan="2"><?php echo $summaries['L']['Mon']['count']; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['avgHours'] . ' h'; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgWage']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgTips']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgTipout']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgEarned']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['L']['Mon']['avgSales']; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['avgCovers'] . ' cov'; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['L']['Mon']['avgCampHours'] . ' h'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Mon']['salesPerHour'] . '/h'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Mon']['salesPerCover'] . '/cov'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo $summaries['L']['Mon']['tipsPercent'] . '%'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo $summaries['L']['Mon']['tipoutPercent'] . '%'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo $summaries['L']['Mon']['tipsVsWage'] . '%'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Mon']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="mon-tot-cell"><?php echo $summaries['L']['Mon']['totHours'] . ' h'; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totWage']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totTips']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totTipout']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totEarned']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['L']['Mon']['totSales']; ?></td>
						<td class="mon-tot-cell"><?php echo $summaries['L']['Mon']['totCovers'] . ' cov'; ?></td>
						<td class="mon-tot-cell"><?php echo $summaries['L']['Mon']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="lun-avg-cell" rowspan="2">Tue AM</td>
						<td class="tue-avg-cell" rowspan="2"><?php echo $summaries['L']['Tue']['count']; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['avgHours'] . ' h'; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgWage']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgTips']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgTipout']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgEarned']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['L']['Tue']['avgSales']; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['avgCovers'] . ' cov'; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['L']['Tue']['avgCampHours'] . ' h'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Tue']['salesPerHour'] . '/h'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Tue']['salesPerCover'] . '/cov'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo $summaries['L']['Tue']['tipsPercent'] . '%'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo $summaries['L']['Tue']['tipoutPercent'] . '%'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo $summaries['L']['Tue']['tipsVsWage'] . '%'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Tue']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="tue-tot-cell"><?php echo $summaries['L']['Tue']['totHours'] . ' h'; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totWage']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totTips']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totTipout']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totEarned']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['L']['Tue']['totSales']; ?></td>
						<td class="tue-tot-cell"><?php echo $summaries['L']['Tue']['totCovers'] . ' cov'; ?></td>
						<td class="tue-tot-cell"><?php echo $summaries['L']['Tue']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="lun-avg-cell" rowspan="2">Wed AM</td>
						<td class="wed-avg-cell" rowspan="2"><?php echo $summaries['L']['Wed']['count']; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['avgHours'] . ' h'; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgWage']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgTips']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgTipout']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgEarned']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['L']['Wed']['avgSales']; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['avgCovers'] . ' cov'; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['L']['Wed']['avgCampHours'] . ' h'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Wed']['salesPerHour'] . '/h'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Wed']['salesPerCover'] . '/cov'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo $summaries['L']['Wed']['tipsPercent'] . '%'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo $summaries['L']['Wed']['tipoutPercent'] . '%'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo $summaries['L']['Wed']['tipsVsWage'] . '%'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Wed']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="wed-tot-cell"><?php echo $summaries['L']['Wed']['totHours'] . ' h'; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totWage']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totTips']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totTipout']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totEarned']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['L']['Wed']['totSales']; ?></td>
						<td class="wed-tot-cell"><?php echo $summaries['L']['Wed']['totCovers'] . ' cov'; ?></td>
						<td class="wed-tot-cell"><?php echo $summaries['L']['Wed']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="lun-avg-cell" rowspan="2">Thu AM</td>
						<td class="thu-avg-cell" rowspan="2"><?php echo $summaries['L']['Thu']['count']; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['avgHours'] . ' h'; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgWage']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgTips']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgTipout']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgEarned']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['L']['Thu']['avgSales']; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['avgCovers'] . ' cov'; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['L']['Thu']['avgCampHours'] . ' h'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Thu']['salesPerHour'] . '/h'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Thu']['salesPerCover'] . '/cov'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo $summaries['L']['Thu']['tipsPercent'] . '%'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo $summaries['L']['Thu']['tipoutPercent'] . '%'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo $summaries['L']['Thu']['tipsVsWage'] . '%'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Thu']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="thu-tot-cell"><?php echo $summaries['L']['Thu']['totHours'] . ' h'; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totWage']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totTips']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totTipout']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totEarned']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['L']['Thu']['totSales']; ?></td>
						<td class="thu-tot-cell"><?php echo $summaries['L']['Thu']['totCovers'] . ' cov'; ?></td>
						<td class="thu-tot-cell"><?php echo $summaries['L']['Thu']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="lun-avg-cell" rowspan="2">Fri AM</td>
						<td class="fri-avg-cell" rowspan="2"><?php echo $summaries['L']['Fri']['count']; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['avgHours'] . ' h'; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgWage']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgTips']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgTipout']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgEarned']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['L']['Fri']['avgSales']; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['avgCovers'] . ' cov'; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['L']['Fri']['avgCampHours'] . ' h'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Fri']['salesPerHour'] . '/h'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Fri']['salesPerCover'] . '/cov'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo $summaries['L']['Fri']['tipsPercent'] . '%'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo $summaries['L']['Fri']['tipoutPercent'] . '%'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo $summaries['L']['Fri']['tipsVsWage'] . '%'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo '$' . $summaries['L']['Fri']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="fri-tot-cell"><?php echo $summaries['L']['Fri']['totHours'] . ' h'; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totWage']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totTips']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totTipout']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totEarned']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['L']['Fri']['totSales']; ?></td>
						<td class="fri-tot-cell"><?php echo $summaries['L']['Fri']['totCovers'] . ' cov'; ?></td>
						<td class="fri-tot-cell"><?php echo $summaries['L']['Fri']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<th class="hdr-avg-cell">Type</th>
						<th class="hdr-avg-cell">#</th>
						<th class="hdr-avg-cell">Hours</th>
						<th class="hdr-avg-cell">Wage</th>
						<th class="hdr-avg-cell">Tips</th>
						<th class="hdr-avg-cell">Tipout</th>
						<th class="hdr-avg-cell">Earned</th>
						<th class="hdr-avg-cell">Sales</th>
						<th class="hdr-avg-cell">Covers</th>
						<th class="hdr-avg-cell">Camp</th>
						<th class="hdr-avg-cell">Sales/h</th>
						<th class="hdr-avg-cell">Sales/cov</th>
						<th class="hdr-avg-cell">%Tips</th>
						<th class="hdr-avg-cell">%T/O</th>
						<th class="hdr-avg-cell">%TvsW</th>
						<th class="hdr-avg-cell">Earn/h</th>
					</tr>
					<tr>
						<td class="spl-avg-cell" rowspan="2">Mon /</td>
						<td class="mon-avg-cell" rowspan="2"><?php echo $summaries['S']['Mon']['count']; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['S']['Mon']['avgHours'] . ' h'; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['S']['Mon']['avgWage']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['S']['Mon']['avgTips']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['S']['Mon']['avgTipout']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['S']['Mon']['avgEarned']; ?></td>
						<td class="mon-avg-cell"><?php echo '$' . $summaries['S']['Mon']['avgSales']; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['S']['Mon']['avgCovers'] . ' cov'; ?></td>
						<td class="mon-avg-cell"><?php echo $summaries['S']['Mon']['avgCampHours'] . ' h'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Mon']['salesPerHour'] . '/h'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Mon']['salesPerCover'] . '/cov'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo $summaries['S']['Mon']['tipsPercent'] . '%'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo $summaries['S']['Mon']['tipoutPercent'] . '%'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo $summaries['S']['Mon']['tipsVsWage'] . '%'; ?></td>
						<td class="mon-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Mon']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="mon-tot-cell"><?php echo $summaries['S']['Mon']['totHours'] . ' h'; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['S']['Mon']['totWage']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['S']['Mon']['totTips']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['S']['Mon']['totTipout']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['S']['Mon']['totEarned']; ?></td>
						<td class="mon-tot-cell"><?php echo '$' . $summaries['S']['Mon']['totSales']; ?></td>
						<td class="mon-tot-cell"><?php echo $summaries['S']['Mon']['totCovers'] . ' cov'; ?></td>
						<td class="mon-tot-cell"><?php echo $summaries['S']['Mon']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="spl-avg-cell" rowspan="2">Tue /</td>
						<td class="tue-avg-cell" rowspan="2"><?php echo $summaries['S']['Tue']['count']; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['S']['Tue']['avgHours'] . ' h'; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['S']['Tue']['avgWage']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['S']['Tue']['avgTips']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['S']['Tue']['avgTipout']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['S']['Tue']['avgEarned']; ?></td>
						<td class="tue-avg-cell"><?php echo '$' . $summaries['S']['Tue']['avgSales']; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['S']['Tue']['avgCovers'] . ' cov'; ?></td>
						<td class="tue-avg-cell"><?php echo $summaries['S']['Tue']['avgCampHours'] . ' h'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Tue']['salesPerHour'] . '/h'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Tue']['salesPerCover'] . '/cov'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo $summaries['S']['Tue']['tipsPercent'] . '%'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo $summaries['S']['Tue']['tipoutPercent'] . '%'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo $summaries['S']['Tue']['tipsVsWage'] . '%'; ?></td>
						<td class="tue-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Tue']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="tue-tot-cell"><?php echo $summaries['S']['Tue']['totHours'] . ' h'; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['S']['Tue']['totWage']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['S']['Tue']['totTips']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['S']['Tue']['totTipout']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['S']['Tue']['totEarned']; ?></td>
						<td class="tue-tot-cell"><?php echo '$' . $summaries['S']['Tue']['totSales']; ?></td>
						<td class="tue-tot-cell"><?php echo $summaries['S']['Tue']['totCovers'] . ' cov'; ?></td>
						<td class="tue-tot-cell"><?php echo $summaries['S']['Tue']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="spl-avg-cell" rowspan="2">Wed /</td>
						<td class="wed-avg-cell" rowspan="2"><?php echo $summaries['S']['Wed']['count']; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['S']['Wed']['avgHours'] . ' h'; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['S']['Wed']['avgWage']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['S']['Wed']['avgTips']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['S']['Wed']['avgTipout']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['S']['Wed']['avgEarned']; ?></td>
						<td class="wed-avg-cell"><?php echo '$' . $summaries['S']['Wed']['avgSales']; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['S']['Wed']['avgCovers'] . ' cov'; ?></td>
						<td class="wed-avg-cell"><?php echo $summaries['S']['Wed']['avgCampHours'] . ' h'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Wed']['salesPerHour'] . '/h'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Wed']['salesPerCover'] . '/cov'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo $summaries['S']['Wed']['tipsPercent'] . '%'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo $summaries['S']['Wed']['tipoutPercent'] . '%'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo $summaries['S']['Wed']['tipsVsWage'] . '%'; ?></td>
						<td class="wed-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Wed']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="wed-tot-cell"><?php echo $summaries['S']['Wed']['totHours'] . ' h'; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['S']['Wed']['totWage']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['S']['Wed']['totTips']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['S']['Wed']['totTipout']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['S']['Wed']['totEarned']; ?></td>
						<td class="wed-tot-cell"><?php echo '$' . $summaries['S']['Wed']['totSales']; ?></td>
						<td class="wed-tot-cell"><?php echo $summaries['S']['Wed']['totCovers'] . ' cov'; ?></td>
						<td class="wed-tot-cell"><?php echo $summaries['S']['Wed']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="spl-avg-cell" rowspan="2">Thu /</td>
						<td class="thu-avg-cell" rowspan="2"><?php echo $summaries['S']['Thu']['count']; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['S']['Thu']['avgHours'] . ' h'; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['S']['Thu']['avgWage']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['S']['Thu']['avgTips']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['S']['Thu']['avgTipout']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['S']['Thu']['avgEarned']; ?></td>
						<td class="thu-avg-cell"><?php echo '$' . $summaries['S']['Thu']['avgSales']; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['S']['Thu']['avgCovers'] . ' cov'; ?></td>
						<td class="thu-avg-cell"><?php echo $summaries['S']['Thu']['avgCampHours'] . ' h'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Thu']['salesPerHour'] . '/h'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Thu']['salesPerCover'] . '/cov'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo $summaries['S']['Thu']['tipsPercent'] . '%'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo $summaries['S']['Thu']['tipoutPercent'] . '%'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo $summaries['S']['Thu']['tipsVsWage'] . '%'; ?></td>
						<td class="thu-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Thu']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="thu-tot-cell"><?php echo $summaries['S']['Thu']['totHours'] . ' h'; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['S']['Thu']['totWage']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['S']['Thu']['totTips']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['S']['Thu']['totTipout']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['S']['Thu']['totEarned']; ?></td>
						<td class="thu-tot-cell"><?php echo '$' . $summaries['S']['Thu']['totSales']; ?></td>
						<td class="thu-tot-cell"><?php echo $summaries['S']['Thu']['totCovers'] . ' cov'; ?></td>
						<td class="thu-tot-cell"><?php echo $summaries['S']['Thu']['totCampHours'] . ' h'; ?></td>
					</tr>
					<tr>
						<td class="spl-avg-cell" rowspan="2">Fri /</td>
						<td class="fri-avg-cell" rowspan="2"><?php echo $summaries['S']['Fri']['count']; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['S']['Fri']['avgHours'] . ' h'; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['S']['Fri']['avgWage']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['S']['Fri']['avgTips']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['S']['Fri']['avgTipout']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['S']['Fri']['avgEarned']; ?></td>
						<td class="fri-avg-cell"><?php echo '$' . $summaries['S']['Fri']['avgSales']; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['S']['Fri']['avgCovers'] . ' cov'; ?></td>
						<td class="fri-avg-cell"><?php echo $summaries['S']['Fri']['avgCampHours'] . ' h'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Fri']['salesPerHour'] . '/h'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Fri']['salesPerCover'] . '/cov'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo $summaries['S']['Fri']['tipsPercent'] . '%'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo $summaries['S']['Fri']['tipoutPercent'] . '%'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo $summaries['S']['Fri']['tipsVsWage'] . '%'; ?></td>
						<td class="fri-avg-cell" rowspan="2"><?php echo '$' . $summaries['S']['Fri']['hourlyWage'] . '/h'; ?></td>
					</tr>
					<tr>
						<td class="fri-tot-cell"><?php echo $summaries['S']['Fri']['totHours'] . ' h'; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['S']['Fri']['totWage']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['S']['Fri']['totTips']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['S']['Fri']['totTipout']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['S']['Fri']['totEarned']; ?></td>
						<td class="fri-tot-cell"><?php echo '$' . $summaries['S']['Fri']['totSales']; ?></td>
						<td class="fri-tot-cell"><?php echo $summaries['S']['Fri']['totCovers'] . ' cov'; ?></td>
						<td class="fri-tot-cell"><?php echo $summaries['S']['Fri']['totCampHours'] . ' h'; ?></td>
					</tr>
				</table>
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