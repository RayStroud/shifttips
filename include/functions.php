ƒ<?php
	// #######################################################################
	// stored calculated fields
	// #######################################################################

	function calculateHours($startTime, $endTime)
	{
		if(isset($startTime) && isset($endTime))
		{
			$startTimeDateTime = new DateTime($startTime);
			$endTimeDateTime = new DateTime($endTime);
			$hoursDateTime = $startTimeDateTime->diff($endTimeDateTime);
			$hours = $hoursDateTime->format('%h') + $hoursDateTime->format('%i') / 24;
		}
		else
		{
			$hours = null;
		}
		return $hours;
	}

	function calculateEarnedWage($hours, $wage)
	{
		if(isset($hours) && isset($wage))
		{
			$earnedWage = $hours * $wage;
		}
		else
		{
			$earnedWage = null;
		}
		return $earnedWage;
	}

	function calculateEarnedTips($cash, $due)
	{
		if(isset($cash) || isset($due))
		{
			$earnedTips = $cash + $due;
		}
		else
		{
			$earnedTips = null;
		}
		return $earnedTips;
	}

	function calculateEarnedTotal($earnedWage, $earnedTips)
	{
		if(isset($earnedWage) && isset($earnedTips))
		{
			$earnedTotal = $earnedTips + $earnedWage;
		}
		else
		{
			$earnedTotal = null;
		}
		return $earnedTotal;
	}

	function calculateTipsVsWage($earnedWage, $earnedTips)
	{
		if(isset($earnedWage) && isset($earnedTips))
		{
			$tipsVsWage = ($earnedTips / $earnedWage) * 100;
		}
		else
		{
			$tipsVsWage = null;
		}
		return $tipsVsWage;
	}

	function calculateSalesPerHour($sales, $hours)
	{
		if(isset($sales) && isset($hours))
		{
			$salesPerHour = $sales / $hours;
		}
		else
		{
			$salesPerHour = null;
		}
		return $salesPerHour;
	}

	function calculateSalesPerCover($sales, $covers)
	{
		if(isset($sales) && isset($covers))
		{
			$salesPerCover = $sales / $covers;
		}
		else
		{
			$salesPerCover = null;
		}
		return $salesPerCover;
	}

	function calculateTipsPercent($sales, $earnedTips)
	{
		if(isset($sales) && isset($earnedTips))
		{
			$tipsPercent = ($earnedTips / $sales) * 100;
		}
		else
		{
			$tipsPercent = null;
		}
		return $tipsPercent;
	}

	function calculateTipoutPercent($sales, $tipout)
	{
		if(isset($sales) && isset($tipout))
		{
			$tipoutPercent = ($tipout / $sales) * 100;
		}
		else
		{
			$tipoutPercent = null;
		}
		return $tipoutPercent;
	}

	function calculateEarnedHourly($earnedTotal, $hours)
	{
		if(isset($earnedTotal) && isset($hours))
		{
			$earnedHourly = $earnedTotal / $hours;
		}
		else
		{
			$earnedHourly = null;
		}
		return $earnedHourly;
	}

	function calculateNoCampHourly($earnedTotal, $hours, $campHours)
	{
		if(isset($earnedTotal) && isset($hours) && isset($campHours))
		{
			$noCampHourly = $earnedTotal / ($hours - $campHours);
		}
		else
		{
			$noCampHourly = null;
		}
		return $noCampHourly;
	}

	function calculateLunchDinner($startTime)
	{
		if(isset($startTime))
		{
			//pull hour from startTime
			$startTimeDateTime = new DateTime($startTime);
			$startTimeHour = $startTimeDateTime->format('H');
			//* DEBUG */ echo '<p>Shift Hour is: ' . $startTimeHour . '</p>';

			//if startTime is before 2 oclock, it's lunch
			if($startTimeHour < 14)
			{
				$lunchDinner = 'L';
			}
			else
			{
				$lunchDinner = 'D';
			}  
		}
		else
		{
			$lunchDinner = null;
		}
		return $lunchDinner;
	}

	function calculateDayOfWeek($startTime)
	{
		if(isset($startTime))
		{
			//pull day from startTime
			$startTimeDateTime = new DateTime($startTime);
			$dayOfWeek = $startTimeDateTime->format('D');
		}
		else
		{
			$dayOfWeek = null;
		}
		return $dayOfWeek;
	}


	// #######################################################################
	// summary calculated fields
	// #######################################################################
?>