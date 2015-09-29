DROP PROCEDURE IF EXISTS getShifts;
DELIMITER //
CREATE PROCEDURE getShifts (p_dateFrom DATE, p_dateTo DATE)
BEGIN
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	SELECT id, wage, YEARWEEK(date, 3) as 'yearweek', date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek, WEEKDAY(date) as weekday
	FROM shift
	WHERE date BETWEEN v_dateFrom AND v_dateTo
	ORDER BY date, startTime ASC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getShiftsFiltered;
DELIMITER //
CREATE PROCEDURE getShiftsFiltered (
	p_dateFrom		DATE,
	p_dateTo		DATE,
	p_lunchDinner	CHAR(1),
	p_mon			BIT,
	p_tue			BIT,
	p_wed			BIT,
	p_thu			BIT,
	p_fri			BIT,
	p_sat			BIT,
	p_sun			BIT
)
BEGIN
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;
	DECLARE v_lunchDinner	CHAR(1);
	DECLARE v_anyDay		CHAR(1);
	DECLARE v_mon			CHAR(3);
	DECLARE v_tue			CHAR(3);
	DECLARE v_wed			CHAR(3);
	DECLARE v_thu			CHAR(3);
	DECLARE v_fri			CHAR(3);
	DECLARE v_sat			CHAR(3);
	DECLARE v_sun			CHAR(3);

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	IF (p_lunchDinner = 'L') 
		THEN SET v_lunchDinner = 'L';
		ELSEIF (p_lunchDinner = 'D') 
			THEN SET v_lunchDinner = 'D';
		ELSE 
			SET v_lunchDinner = '%';
	END IF;

	IF (p_mon = 0 && p_tue = 0 && p_wed = 0 && p_thu = 0 && p_fri = 0 && p_sat = 0 && p_sun = 0)
		THEN SET v_anyDay := '%';
		ELSE SET v_anyDay := '';
	END IF;

	IF (p_mon = 1)
		THEN SET v_mon := 'MON';
		ELSE SET v_mon := '';
	END IF;

	IF (p_tue = 1)
		THEN SET v_tue := 'TUE';
		ELSE SET v_tue := '';
	END IF;

	IF (p_wed = 1)
		THEN SET v_wed := 'WED';
		ELSE SET v_wed := '';
	END IF;

	IF (p_thu = 1)
		THEN SET v_thu := 'THU';
		ELSE SET v_thu := '';
	END IF;

	IF (p_fri = 1)
		THEN SET v_fri := 'FRI';
		ELSE SET v_fri := '';
	END IF;

	IF (p_sat = 1)
		THEN SET v_sat := 'SAT';
		ELSE SET v_sat := '';
	END IF;

	IF (p_sun = 1)
		THEN SET v_sun := 'SUN';
		ELSE SET v_sun := '';
	END IF;

	SELECT id, wage, YEARWEEK(date, 3) as 'yearWeek', date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek
	FROM shift
	WHERE date BETWEEN v_dateFrom AND v_dateTo
		AND UPPER(lunchDinner) LIKE v_lunchDinner
		AND (UPPER(dayOfWeek) IN (v_mon, v_tue, v_wed, v_thu, v_fri, v_sat, v_sun) 
			OR dayOfWeek LIKE v_anyDay)
	ORDER BY date, startTime ASC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS saveShift;
DELIMITER //
CREATE PROCEDURE saveShift (
	# p_id: # to update, NULL to insert with id return, 0 to insert without return
	p_id			INT,
	p_wage 			DECIMAL(5,2),
	p_date			DATE,
	p_startTime		TIME,
	p_endTime		TIME,
	p_firstTable 	TIME,
	p_campHours		DECIMAL(5,2),
	p_sales			DECIMAL(7,2),
	p_tipout		INT,
	p_transfers		INT,
	p_cash			INT,
	p_due 			INT,
	p_covers		INT,
	p_cut 			CHAR(1),
	p_section		VARCHAR(25),
	p_notes 		VARCHAR(1000)
)
BEGIN	
	DECLARE v_hours			DECIMAL(5,2);
	DECLARE v_earnedWage	INT;
	DECLARE v_earnedTips	INT;
	DECLARE v_earnedTotal	INT;
	DECLARE v_tipsVsWage	INT;
	DECLARE v_salesPerHour	DECIMAL(6,2);
	DECLARE v_salesPerCover	DECIMAL(6,2);
	DECLARE v_tipsPercent	DECIMAL(4,1);
	DECLARE v_tipoutPercent	DECIMAL(4,1);
	DECLARE v_hourly		DECIMAL(5,2);
	DECLARE v_noCampHourly	DECIMAL(5,2);
	DECLARE v_lunchDinner	CHAR(1);
	DECLARE v_dayOfWeek		CHAR(3);
	# endTime variable for calculating hours
	DECLARE v_endTime		TIME;

	IF (p_endTime BETWEEN '00:00' AND p_startTime)
		THEN SET v_endTime := ADDTIME(p_endTime, '24:00');
		ELSE SET v_endTime := p_endTime; 
	END IF;

	SET v_hours := HOUR(TIMEDIFF(v_endTime, p_startTime)) + (MINUTE(TIMEDIFF(v_endTime, p_startTime))/60);
	SET v_earnedWage := p_wage * v_hours;
	IF (p_cash IS NULL && p_due IS NULL)
		THEN SET v_earnedTips := NULL;
		ELSE SET v_earnedTips := IFNULL(p_cash,0) + IFNULL(p_due,0);
	END IF;
	IF (v_earnedWage IS NULL && v_earnedTips IS NULL)
		THEN SET v_earnedTotal := NULL;
		ELSE SET v_earnedTotal := IFNULL(v_earnedWage,0) + IFNULL(v_earnedTips,0);
	END IF;
	SET v_tipsVsWage := v_earnedTips * 100 / v_earnedWage; 
	SET v_salesPerHour := p_sales / v_hours;
	SET v_salesPerCover := p_sales / p_covers;
	SET v_tipsPercent := v_earnedTips * 100 / p_sales;
	IF (p_tipout IS NULL && p_transfers IS NULL)
		THEN SET v_tipoutPercent := NULL;
		ELSE SET v_tipoutPercent := (IFNULL(p_tipout,0) + IFNULL(p_transfers,0)) * 100 / p_sales ;
	END IF;
	SET v_hourly := v_earnedTotal / v_hours;
	SET v_noCampHourly := v_earnedTotal / (v_hours - p_campHours);
	IF (p_startTime BETWEEN '10:00' AND '13:00')
		THEN SET v_lunchDinner := 'L';
		ELSE SET v_lunchDinner := 'D';
	END IF;
	SET v_dayOfWeek := LEFT(DAYNAME(p_date),3);

	IF (p_id IS NULL)
		THEN INSERT INTO shift (wage, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek) 
				VALUES (p_wage, p_date, p_startTime, p_endTime, p_firstTable, p_campHours, p_sales, p_tipout, p_transfers, p_cash, p_due, p_covers, p_cut, p_section, p_notes, v_hours, v_earnedWage, v_earnedTips, v_earnedTotal, v_tipsVsWage, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_hourly, v_noCampHourly, v_lunchDinner, v_dayOfWeek);
			SELECT LAST_INSERT_ID() as id;
		ELSEIF (p_id = 0)
			THEN INSERT INTO shift (wage, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek) 
					VALUES (p_wage, p_date, p_startTime, p_endTime, p_firstTable, p_campHours, p_sales, p_tipout, p_transfers, p_cash, p_due, p_covers, p_cut, p_section, p_notes, v_hours, v_earnedWage, v_earnedTips, v_earnedTotal, v_tipsVsWage, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_hourly, v_noCampHourly, v_lunchDinner, v_dayOfWeek);
		ELSE UPDATE shift SET
			wage = p_wage,
			date = p_date,
			startTime = p_startTime,
			endTime = p_endTime,
			firstTable = p_firstTable,
			campHours = p_campHours,
			sales = p_sales,
			tipout = p_tipout,
			transfers = p_transfers,
			cash = p_cash,
			due = p_due,
			covers = p_covers,
			cut = p_cut,
			section = p_section,
			notes = p_notes,
			hours = v_hours,
			earnedWage = v_earnedWage,
			earnedTips = v_earnedTips,
			earnedTotal = v_earnedTotal,
			tipsVsWage = v_tipsVsWage,
			salesPerHour = v_salesPerHour,
			salesPerCover = v_salesPerCover,
			tipsPercent = v_tipsPercent,
			tipoutPercent = v_tipoutPercent,
			hourly = v_hourly,
			noCampHourly = v_noCampHourly,
			lunchDinner = v_lunchDinner,
			dayOfWeek = v_dayOfWeek
			WHERE id = p_id;
	END IF;
	CALL calculateSplits();
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS deleteShift;
DELIMITER //
CREATE PROCEDURE deleteShift (p_id INT)
BEGIN
	DELETE FROM shift WHERE id = p_id LIMIT 1;
	CALL calculateSplits();
	CALL calculateWeeks();
	CALL calculateMonths();
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS calculateSummaries;
DELIMITER //
CREATE PROCEDURE calculateSummaries (p_dateFrom DATE, p_dateTo DATE)
BEGIN
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	TRUNCATE TABLE summary;

	#Both/Dinner/Lunch/Split
	CALL calculateSummary(v_dateFrom, v_dateTo, "%", "%");
	CALL calculateSummary(v_dateFrom, v_dateTo, "D", "%");
	CALL calculateSummary(v_dateFrom, v_dateTo, "L", "%");
	CALL calculateSplitSummary(v_dateFrom, v_dateTo, "%");

	#Day of the Week - Dinner
	CALL calculateSummary(v_dateFrom, v_dateTo, "D", "Mon");
	CALL calculateSummary(v_dateFrom, v_dateTo, "D", "Tue");
	CALL calculateSummary(v_dateFrom, v_dateTo, "D", "Wed");
	CALL calculateSummary(v_dateFrom, v_dateTo, "D", "Thu");
	CALL calculateSummary(v_dateFrom, v_dateTo, "D", "Fri");
	CALL calculateSummary(v_dateFrom, v_dateTo, "D", "Sat");
	CALL calculateSummary(v_dateFrom, v_dateTo, "D", "Sun");

	#Day of the Week - Lunch
	CALL calculateSummary(v_dateFrom, v_dateTo, "L", "Mon");
	CALL calculateSummary(v_dateFrom, v_dateTo, "L", "Tue");
	CALL calculateSummary(v_dateFrom, v_dateTo, "L", "Wed");
	CALL calculateSummary(v_dateFrom, v_dateTo, "L", "Thu");
	CALL calculateSummary(v_dateFrom, v_dateTo, "L", "Fri");

	#Day of the Week - Split
	CALL calculateSplitSummary(v_dateFrom, v_dateTo, "Mon");
	CALL calculateSplitSummary(v_dateFrom, v_dateTo, "Tue");
	CALL calculateSplitSummary(v_dateFrom, v_dateTo, "Wed");
	CALL calculateSplitSummary(v_dateFrom, v_dateTo, "Thu");
	CALL calculateSplitSummary(v_dateFrom, v_dateTo, "Fri");

	#Weekly
	CALL calculateWeeklySummary(v_dateFrom, v_dateTo, 1);

	#Monthly
	CALL calculateMonthlySummary(v_dateFrom, v_dateTo, 1);

	SELECT * FROM summary;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummary;
DELIMITER //
CREATE PROCEDURE getSummary (p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;
	DECLARE v_lunchDinner	CHAR(1);

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	IF (p_lunchDinner = 'L') 
		THEN SET v_lunchDinner = 'L';
		ELSEIF (p_lunchDinner = 'D') 
			THEN SET v_lunchDinner = 'D';
		ELSE 
			SET v_lunchDinner = '%';
	END IF;

	SELECT
		COUNT(id) as count,
		ROUND(AVG(hours)		,2) as avgHours,
		ROUND(SUM(hours)		,0) as totHours,
		ROUND(AVG(earnedWage)	,2) as avgWage,
		ROUND(SUM(earnedWage)	,0) as totWage,
		ROUND(AVG(earnedTips)	,2) as avgTips,
		ROUND(SUM(earnedTips)	,0) as totTips,
		ROUND(AVG(earnedWage + earnedTips)	,2) as avgEarned,
		ROUND(SUM(earnedWage + earnedTips)	,0) as totEarned,
		ROUND(AVG(tipout)		,2) as avgTipout,
		ROUND(SUM(tipout)		,0) as totTipout,
		ROUND(AVG(sales)		,0) as avgSales,
		ROUND(SUM(sales)		,0) as totSales,
		ROUND(AVG(covers)		,1) as avgCovers,
		ROUND(SUM(covers)		,0) as totCovers,
		ROUND(AVG(campHours)	,2) as avgCampHours,
		ROUND(SUM(campHours)	,2) as totCampHours,
		ROUND(SUM(sales) / SUM(hours)	,2)	as salesPerHour,
		ROUND(SUM(sales) / SUM(covers)	,2)	as salesPerCover,
		ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	as tipsPercent,
		ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	as tipoutPercent,
		ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	as tipsVsWage,
		ROUND((SUM(earnedWage) + SUM(earnedTips)) / SUM(hours) 	,2)	as hourly
	FROM shift
	WHERE date BETWEEN v_dateFrom AND v_dateTo
		AND UPPER(lunchDinner) LIKE UPPER(v_lunchDinner);
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryByLunchDinner;
DELIMITER //
CREATE PROCEDURE getSummaryByLunchDinner (p_dateFrom DATE, p_dateTo DATE)
BEGIN
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	SELECT
		lunchDinner,
		COUNT(id) as count,
		ROUND(AVG(hours)		,2) as avgHours,
		ROUND(SUM(hours)		,0) as totHours,
		ROUND(AVG(earnedWage)	,2) as avgWage,
		ROUND(SUM(earnedWage)	,0) as totWage,
		ROUND(AVG(earnedTips)	,2) as avgTips,
		ROUND(SUM(earnedTips)	,0) as totTips,
		ROUND(AVG(earnedWage + earnedTips)	,2) as avgEarned,
		ROUND(SUM(earnedWage + earnedTips)	,0) as totEarned,
		ROUND(AVG(tipout)		,2) as avgTipout,
		ROUND(SUM(tipout)		,0) as totTipout,
		ROUND(AVG(sales)		,0) as avgSales,
		ROUND(SUM(sales)		,0) as totSales,
		ROUND(AVG(covers)		,1) as avgCovers,
		ROUND(SUM(covers)		,0) as totCovers,
		ROUND(AVG(campHours)	,2) as avgCampHours,
		ROUND(SUM(campHours)	,2) as totCampHours,
		ROUND(SUM(sales) / SUM(hours)	,2)	as salesPerHour,
		ROUND(SUM(sales) / SUM(covers)	,2)	as salesPerCover,
		ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	as tipsPercent,
		ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	as tipoutPercent,
		ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	as tipsVsWage,
		ROUND((SUM(earnedWage) + SUM(earnedTips)) / SUM(hours) 	,2)	as hourly
	FROM shift
	WHERE date BETWEEN v_dateFrom AND v_dateTo
	GROUP BY lunchDinner;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryByDayOfWeek;
DELIMITER //
CREATE PROCEDURE getSummaryByDayOfWeek (p_dateFrom DATE, p_dateTo DATE)
BEGIN
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	SELECT
		WEEKDAY(date) as weekday,
		dayOfWeek,
		lunchDinner,
		COUNT(id) as count,
		ROUND(AVG(hours)		,2) as avgHours,
		ROUND(SUM(hours)		,0) as totHours,
		ROUND(AVG(earnedWage)	,2) as avgWage,
		ROUND(SUM(earnedWage)	,0) as totWage,
		ROUND(AVG(earnedTips)	,2) as avgTips,
		ROUND(SUM(earnedTips)	,0) as totTips,
		ROUND(AVG(earnedWage + earnedTips)	,2) as avgEarned,
		ROUND(SUM(earnedWage + earnedTips)	,0) as totEarned,
		ROUND(AVG(tipout)		,2) as avgTipout,
		ROUND(SUM(tipout)		,0) as totTipout,
		ROUND(AVG(sales)		,0) as avgSales,
		ROUND(SUM(sales)		,0) as totSales,
		ROUND(AVG(covers)		,1) as avgCovers,
		ROUND(SUM(covers)		,0) as totCovers,
		ROUND(AVG(campHours)	,2) as avgCampHours,
		ROUND(SUM(campHours)	,2) as totCampHours,
		ROUND(SUM(sales) / SUM(hours)	,2)	as salesPerHour,
		ROUND(SUM(sales) / SUM(covers)	,2)	as salesPerCover,
		ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	as tipsPercent,
		ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	as tipoutPercent,
		ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	as tipsVsWage,
		ROUND((SUM(earnedWage) + SUM(earnedTips)) / SUM(hours) 	,2)	as hourly
	FROM shift
	WHERE date BETWEEN v_dateFrom AND v_dateTo
	GROUP BY dayOfWeek, lunchDinner
	ORDER BY WEEKDAY(date), lunchDinner DESC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryBySection;
DELIMITER //
CREATE PROCEDURE getSummaryBySection (p_dateFrom DATE, p_dateTo DATE)
BEGIN
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	SELECT
		section,
		lunchDinner,
		COUNT(id) as count,
		ROUND(AVG(hours)		,2) as avgHours,
		ROUND(SUM(hours)		,0) as totHours,
		ROUND(AVG(earnedWage)	,2) as avgWage,
		ROUND(SUM(earnedWage)	,0) as totWage,
		ROUND(AVG(earnedTips)	,2) as avgTips,
		ROUND(SUM(earnedTips)	,0) as totTips,
		ROUND(AVG(earnedWage + earnedTips)	,2) as avgEarned,
		ROUND(SUM(earnedWage + earnedTips)	,0) as totEarned,
		ROUND(AVG(tipout)		,2) as avgTipout,
		ROUND(SUM(tipout)		,0) as totTipout,
		ROUND(AVG(sales)		,0) as avgSales,
		ROUND(SUM(sales)		,0) as totSales,
		ROUND(AVG(covers)		,1) as avgCovers,
		ROUND(SUM(covers)		,0) as totCovers,
		ROUND(AVG(campHours)	,2) as avgCampHours,
		ROUND(SUM(campHours)	,2) as totCampHours,
		ROUND(SUM(sales) / SUM(hours)	,2)	as salesPerHour,
		ROUND(SUM(sales) / SUM(covers)	,2)	as salesPerCover,
		ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	as tipsPercent,
		ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	as tipoutPercent,
		ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	as tipsVsWage,
		ROUND((SUM(earnedWage) + SUM(earnedTips)) / SUM(hours) 	,2)	as hourly
	FROM shift
	WHERE date BETWEEN v_dateFrom AND v_dateTo
	GROUP BY section, lunchDinner;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryByStartTime;
DELIMITER //
CREATE PROCEDURE getSummaryByStartTime (p_dateFrom DATE, p_dateTo DATE)
BEGIN
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	SELECT
		startTime,
		lunchDinner,
		COUNT(id) as count,
		ROUND(AVG(hours)		,2) as avgHours,
		ROUND(SUM(hours)		,0) as totHours,
		ROUND(AVG(earnedWage)	,2) as avgWage,
		ROUND(SUM(earnedWage)	,0) as totWage,
		ROUND(AVG(earnedTips)	,2) as avgTips,
		ROUND(SUM(earnedTips)	,0) as totTips,
		ROUND(AVG(earnedWage + earnedTips)	,2) as avgEarned,
		ROUND(SUM(earnedWage + earnedTips)	,0) as totEarned,
		ROUND(AVG(tipout)		,2) as avgTipout,
		ROUND(SUM(tipout)		,0) as totTipout,
		ROUND(AVG(sales)		,0) as avgSales,
		ROUND(SUM(sales)		,0) as totSales,
		ROUND(AVG(covers)		,1) as avgCovers,
		ROUND(SUM(covers)		,0) as totCovers,
		ROUND(AVG(campHours)	,2) as avgCampHours,
		ROUND(SUM(campHours)	,2) as totCampHours,
		ROUND(SUM(sales) / SUM(hours)	,2)	as salesPerHour,
		ROUND(SUM(sales) / SUM(covers)	,2)	as salesPerCover,
		ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	as tipsPercent,
		ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	as tipoutPercent,
		ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	as tipsVsWage,
		ROUND((SUM(earnedWage) + SUM(earnedTips)) / SUM(hours) 	,2)	as hourly
	FROM shift
	WHERE date BETWEEN v_dateFrom AND v_dateTo
	GROUP BY startTime, lunchDinner;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryByCut;
DELIMITER //
CREATE PROCEDURE getSummaryByCut (p_dateFrom DATE, p_dateTo DATE)
BEGIN
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	SELECT
		cut,
		lunchDinner,
		COUNT(id) as count,
		ROUND(AVG(hours)		,2) as avgHours,
		ROUND(SUM(hours)		,0) as totHours,
		ROUND(AVG(earnedWage)	,2) as avgWage,
		ROUND(SUM(earnedWage)	,0) as totWage,
		ROUND(AVG(earnedTips)	,2) as avgTips,
		ROUND(SUM(earnedTips)	,0) as totTips,
		ROUND(AVG(earnedWage + earnedTips)	,2) as avgEarned,
		ROUND(SUM(earnedWage + earnedTips)	,0) as totEarned,
		ROUND(AVG(tipout)		,2) as avgTipout,
		ROUND(SUM(tipout)		,0) as totTipout,
		ROUND(AVG(sales)		,0) as avgSales,
		ROUND(SUM(sales)		,0) as totSales,
		ROUND(AVG(covers)		,1) as avgCovers,
		ROUND(SUM(covers)		,0) as totCovers,
		ROUND(AVG(campHours)	,2) as avgCampHours,
		ROUND(SUM(campHours)	,2) as totCampHours,
		ROUND(SUM(sales) / SUM(hours)	,2)	as salesPerHour,
		ROUND(SUM(sales) / SUM(covers)	,2)	as salesPerCover,
		ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	as tipsPercent,
		ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	as tipoutPercent,
		ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	as tipsVsWage,
		ROUND((SUM(earnedWage) + SUM(earnedTips)) / SUM(hours) 	,2)	as hourly
	FROM shift
	WHERE date BETWEEN v_dateFrom AND v_dateTo
	GROUP BY cut, lunchDinner;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryWeekly;
DELIMITER //
CREATE PROCEDURE getSummaryWeekly (p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	CALL calculateWeeks(p_dateFrom, p_dateTo, p_lunchDinner);
	SELECT
		COUNT(id) as count,
		ROUND(AVG(shifts)		,1) as avgShifts,
		ROUND(SUM(shifts)		,0) as totShifts,
		ROUND(AVG(hours)		,2) as avgHours,
		ROUND(SUM(hours)		,0) as totHours,
		ROUND(AVG(earnedWage)	,2) as avgWage,
		ROUND(SUM(earnedWage)	,0) as totWage,
		ROUND(AVG(earnedTips)	,2) as avgTips,
		ROUND(SUM(earnedTips)	,0) as totTips,
		ROUND(AVG(earnedWage + earnedTips)	,2) as avgEarned,
		ROUND(SUM(earnedWage + earnedTips)	,0) as totEarned,
		ROUND(AVG(tipout)		,2) as avgTipout,
		ROUND(SUM(tipout)		,0) as totTipout,
		ROUND(AVG(sales)		,0) as avgSales,
		ROUND(SUM(sales)		,0) as totSales,
		ROUND(AVG(covers)		,1) as avgCovers,
		ROUND(SUM(covers)		,0) as totCovers,
		ROUND(AVG(campHours)	,2) as avgCampHours,
		ROUND(SUM(campHours)	,2) as totCampHours,
		ROUND(SUM(sales) / SUM(hours)	,2)	as salesPerHour,
		ROUND(SUM(sales) / SUM(covers)	,2)	as salesPerCover,
		ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	as tipsPercent,
		ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	as tipoutPercent,
		ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	as tipsVsWage,
		ROUND((SUM(earnedWage) + SUM(earnedTips)) / SUM(hours) 	,2)	as hourly
	FROM week;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryMonthly;
DELIMITER //
CREATE PROCEDURE getSummaryMonthly (p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	CALL calculateMonths(p_dateFrom, p_dateTo, p_lunchDinner);
	SELECT
		COUNT(id) as count,
		ROUND(AVG(shifts)		,1) as avgShifts,
		ROUND(SUM(shifts)		,0) as totShifts,
		ROUND(AVG(hours)		,2) as avgHours,
		ROUND(SUM(hours)		,0) as totHours,
		ROUND(AVG(earnedWage)	,2) as avgWage,
		ROUND(SUM(earnedWage)	,0) as totWage,
		ROUND(AVG(earnedTips)	,2) as avgTips,
		ROUND(SUM(earnedTips)	,0) as totTips,
		ROUND(AVG(earnedWage + earnedTips)	,2) as avgEarned,
		ROUND(SUM(earnedWage + earnedTips)	,0) as totEarned,
		ROUND(AVG(tipout)		,2) as avgTipout,
		ROUND(SUM(tipout)		,0) as totTipout,
		ROUND(AVG(sales)		,0) as avgSales,
		ROUND(SUM(sales)		,0) as totSales,
		ROUND(AVG(covers)		,1) as avgCovers,
		ROUND(SUM(covers)		,0) as totCovers,
		ROUND(AVG(campHours)	,2) as avgCampHours,
		ROUND(SUM(campHours)	,2) as totCampHours,
		ROUND(SUM(sales) / SUM(hours)	,2)	as salesPerHour,
		ROUND(SUM(sales) / SUM(covers)	,2)	as salesPerCover,
		ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	as tipsPercent,
		ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	as tipoutPercent,
		ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	as tipsVsWage,
		ROUND((SUM(earnedWage) + SUM(earnedTips)) / SUM(hours) 	,2)	as hourly
	FROM month;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS calculateSummary;
DELIMITER //
CREATE PROCEDURE calculateSummary (p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1), p_dayOfWeek CHAR(3))
BEGIN
	DECLARE v_count 			INT;
	DECLARE v_avgHours 			DECIMAL(5,2);
	DECLARE v_totHours 			DECIMAL(7,2);
	DECLARE v_avgWage 			DECIMAL(5,2);
	DECLARE v_totWage 			DECIMAL(7,2);
	DECLARE v_avgTips 			DECIMAL(5,2);
	DECLARE v_totTips 			INT;
	DECLARE v_avgTipout 		DECIMAL(5,2);
	DECLARE v_totTipout 		INT;
	DECLARE v_avgSales 			DECIMAL(7,2);
	DECLARE v_totSales 			DECIMAL(10,2);
	DECLARE v_avgCovers 		DECIMAL(5,2);
	DECLARE v_totCovers 		INT;
	DECLARE v_avgCampHours 		DECIMAL(4,2);
	DECLARE v_totCampHours 		DECIMAL(8,2);
	DECLARE v_salesPerHour 		DECIMAL(7,2);
	DECLARE v_salesPerCover 	DECIMAL(7,2);
	DECLARE v_tipsPercent 		DECIMAL(4,1);
	DECLARE v_tipoutPercent 	DECIMAL(4,1);
	DECLARE v_tipsVsWage 		INT;
	DECLARE v_hourly 			DECIMAL(4,2);

	SELECT COUNT(id) 
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_count;
	SELECT AVG(hours)
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgHours;
	SELECT SUM(hours) 
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totHours;
	SELECT AVG(earnedWage)
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgWage;
	SELECT SUM(earnedWage) 
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totWage;
	SELECT AVG(earnedTips)
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgTips;
	SELECT SUM(earnedTips) 
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totTips;
	SELECT AVG(tipout)
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgTipout;
	SELECT SUM(tipout) 
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totTipout;
	SELECT AVG(sales)
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgSales;
	SELECT SUM(sales) 
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totSales;
	SELECT AVG(covers)
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgCovers;
	SELECT SUM(covers) 
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totCovers;
	SELECT AVG(campHours)
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgCampHours;
	SELECT SUM(campHours) 
		FROM shift 
		WHERE date BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totCampHours;
	SET v_salesPerHour = v_totSales / v_totHours;
	SET v_salesPerCover = v_totSales / v_totCovers;
	SET v_tipsPercent = v_totTips * 100 / v_totSales;
	SET v_tipoutPercent = v_totTipout * 100 / v_totSales;
	SET v_tipsVsWage = v_totTips * 100 / v_totWage;
	SET v_hourly = (v_totWage + v_totTips) / v_totHours;

	INSERT INTO summary (count, avgHours, totHours, avgWage, totWage, avgTips, totTips, avgTipout, totTipout, avgSales, totSales, avgCovers, totCovers, avgCampHours, totCampHours, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, tipsVsWage, hourly, lunchDinner, dayOfWeek, timestamp)
		VALUES (v_count, v_avgHours, v_totHours, v_avgWage, v_totWage, v_avgTips, v_totTips, v_avgTipout, v_totTipout, v_avgSales, v_totSales, v_avgCovers, v_totCovers, v_avgCampHours, v_totCampHours, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_tipsVsWage, v_hourly, p_lunchDinner, p_dayOfWeek, CURRENT_TIMESTAMP);
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS calculateSplitSummary;
DELIMITER //
CREATE PROCEDURE calculateSplitSummary (p_dateFrom DATE, p_dateTo DATE, p_dayOfWeek CHAR(3))
BEGIN
	DECLARE v_count 			INT;
	DECLARE v_avgHours 			DECIMAL(5,2);
	DECLARE v_totHours 			DECIMAL(7,2);
	DECLARE v_avgWage 			DECIMAL(5,2);
	DECLARE v_totWage 			DECIMAL(7,2);
	DECLARE v_avgTips 			DECIMAL(5,2);
	DECLARE v_totTips 			INT;
	DECLARE v_avgTipout 		DECIMAL(5,2);
	DECLARE v_totTipout 		INT;
	DECLARE v_avgSales 			DECIMAL(7,2);
	DECLARE v_totSales 			DECIMAL(10,2);
	DECLARE v_avgCovers 		DECIMAL(5,2);
	DECLARE v_totCovers 		INT;
	DECLARE v_avgCampHours 		DECIMAL(4,2);
	DECLARE v_totCampHours 		DECIMAL(8,2);
	DECLARE v_salesPerHour 		DECIMAL(7,2);
	DECLARE v_salesPerCover 	DECIMAL(7,2);
	DECLARE v_tipsPercent 		DECIMAL(4,1);
	DECLARE v_tipoutPercent 	DECIMAL(4,1);
	DECLARE v_tipsVsWage 		INT;
	DECLARE v_hourly 			DECIMAL(4,2);

	SELECT COUNT(id) 
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_count;
	SELECT AVG(hours)
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgHours;
	SELECT SUM(hours) 
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totHours;
	SELECT AVG(earnedWage)
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgWage;
	SELECT SUM(earnedWage) 
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totWage;
	SELECT AVG(earnedTips)
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgTips;
	SELECT SUM(earnedTips) 
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totTips;
	SELECT AVG(tipout)
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgTipout;
	SELECT SUM(tipout) 
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totTipout;
	SELECT AVG(sales)
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgSales;
	SELECT SUM(sales) 
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totSales;
	SELECT AVG(covers)
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgCovers;
	SELECT SUM(covers) 
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totCovers;
	SELECT AVG(campHours)
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgCampHours;
	SELECT SUM(campHours) 
		FROM split 
		WHERE splitDate BETWEEN p_dateFrom AND p_dateTo
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totCampHours;
	SET v_salesPerHour = v_totSales / v_totHours;
	SET v_salesPerCover = v_totSales / v_totCovers;
	SET v_tipsPercent = v_totTips * 100 / v_totSales;
	SET v_tipoutPercent = v_totTipout * 100 / v_totSales;
	SET v_tipsVsWage = v_totTips * 100 / v_totWage;
	SET v_hourly = (v_totWage + v_totTips) / v_totHours;

	INSERT INTO summary (count, avgHours, totHours, avgWage, totWage, avgTips, totTips, avgTipout, totTipout, avgSales, totSales, avgCovers, totCovers, avgCampHours, totCampHours, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, tipsVsWage, hourly, lunchDinner, dayOfWeek, timestamp)
		VALUES (v_count, v_avgHours, v_totHours, v_avgWage, v_totWage, v_avgTips, v_totTips, v_avgTipout, v_totTipout, v_avgSales, v_totSales, v_avgCovers, v_totCovers, v_avgCampHours, v_totCampHours, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_tipsVsWage, v_hourly, 'S', p_dayOfWeek, CURRENT_TIMESTAMP);
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS calculateWeeklySummary;
DELIMITER //
CREATE PROCEDURE calculateWeeklySummary (p_dateFrom DATE, p_dateTo DATE, p_insert BIT)
# p_insert: NULL or 0 will return, 1 will insert row into summary table
BEGIN
	DECLARE v_dateFrom			DATE;
	DECLARE v_dateTo			DATE;
	DECLARE v_count 			INT;
	DECLARE v_avgShifts 		DECIMAL(4,2);
	DECLARE v_totShifts 		INT;
	DECLARE v_avgHours 			DECIMAL(5,2);
	DECLARE v_totHours 			DECIMAL(7,2);
	DECLARE v_avgWage 			DECIMAL(5,2);
	DECLARE v_totWage 			DECIMAL(7,2);
	DECLARE v_avgTips 			DECIMAL(5,2);
	DECLARE v_totTips 			INT;
	DECLARE v_avgEarned			DECIMAL(5,2);
	DECLARE v_totEarned			DECIMAL(7,2);
	DECLARE v_avgTipout 		DECIMAL(5,2);
	DECLARE v_totTipout 		INT;
	DECLARE v_avgSales 			DECIMAL(7,2);
	DECLARE v_totSales 			DECIMAL(10,2);
	DECLARE v_avgCovers 		DECIMAL(5,2);
	DECLARE v_totCovers 		INT;
	DECLARE v_avgCampHours 		DECIMAL(4,2);
	DECLARE v_totCampHours 		DECIMAL(8,2);
	DECLARE v_salesPerHour 		DECIMAL(7,2);
	DECLARE v_salesPerCover 	DECIMAL(7,2);
	DECLARE v_tipsPercent 		DECIMAL(4,1);
	DECLARE v_tipoutPercent 	DECIMAL(4,1);
	DECLARE v_tipsVsWage 		INT;
	DECLARE v_hourly 			DECIMAL(4,2);

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	SELECT COUNT(id) 
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_count;
	SELECT AVG(shifts)
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_avgShifts;
	SELECT SUM(shifts) 
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_totShifts;
	SELECT AVG(hours)
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_avgHours;
	SELECT SUM(hours) 
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_totHours;
	SELECT AVG(earnedWage)
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_avgWage;
	SELECT SUM(earnedWage) 
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_totWage;
	SELECT AVG(earnedTips)
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_avgTips;
	SELECT SUM(earnedTips) 
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_totTips;
	SELECT AVG(tipout + transfers)
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_avgTipout;
	SELECT SUM(tipout + transfers) 
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_totTipout;
	SELECT AVG(sales)
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_avgSales;
	SELECT SUM(sales) 
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_totSales;
	SELECT AVG(covers)
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_avgCovers;
	SELECT SUM(covers) 
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_totCovers;
	SELECT AVG(campHours)
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_avgCampHours;
	SELECT SUM(campHours) 
		FROM week 
		WHERE startWeek BETWEEN v_dateFrom AND v_dateTo
			OR endWeek BETWEEN v_dateFrom AND v_dateTo
		INTO v_totCampHours;
	SET v_avgEarned = v_avgWage + v_avgTips;
	SET v_totEarned = v_totWage + v_totTips;
	SET v_salesPerHour = v_totSales / v_totHours;
	SET v_salesPerCover = v_totSales / v_totCovers;
	SET v_tipsPercent = v_totTips * 100 / v_totSales;
	SET v_tipoutPercent = v_totTipout * 100 / v_totSales;
	SET v_tipsVsWage = v_totTips * 100 / v_totWage;
	SET v_hourly = v_totEarned / v_totHours;

	IF (p_insert = 1)
		THEN INSERT INTO summary (count, avgHours, totHours, avgWage, totWage, avgTips, totTips, avgTipout, totTipout, avgSales, totSales, avgCovers, totCovers, avgCampHours, totCampHours, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, tipsVsWage, hourly, lunchDinner, dayOfWeek, timestamp)
			VALUES (v_count, v_avgHours, v_totHours, v_avgWage, v_totWage, v_avgTips, v_totTips, v_avgTipout, v_totTipout, v_avgSales, v_totSales, v_avgCovers, v_totCovers, v_avgCampHours, v_totCampHours, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_tipsVsWage, v_hourly, '-', 'Wkl', CURRENT_TIMESTAMP);
		ELSE SELECT v_count as count,
			v_avgShifts as avgShifts,
			v_totShifts as totShifts,
			v_avgHours as avgHours,
			v_totHours as totHours,
			v_avgWage as avgWage,
			v_totWage as totWage,
			v_avgTips as avgTips,
			v_totTips as totTips,
			v_avgEarned as avgEarned,
			v_totEarned as totEarned,
			v_avgTipout as avgTipout,
			v_totTipout as totTipout,
			v_avgSales as avgSales,
			v_totSales as totSales,
			v_avgCovers as avgCovers,
			v_totCovers as totCovers,
			v_avgCampHours as avgCampHours,
			v_totCampHours as totCampHours,
			v_salesPerHour as salesPerHour,
			v_salesPerCover as salesPerCover,
			v_tipsPercent as tipsPercent,
			v_tipoutPercent as tipoutPercent,
			v_tipsVsWage as tipsVsWage,
			v_hourly as hourly;
	END IF;	
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS calculateMonthlySummary;
DELIMITER //
CREATE PROCEDURE calculateMonthlySummary (p_dateFrom DATE, p_dateTo DATE, p_insert BIT)
# p_insert: NULL or 0 will return, 1 will insert row into summary table
BEGIN
	DECLARE v_dateFrom			DATE;
	DECLARE v_dateTo			DATE;
	DECLARE v_count 			INT;
	DECLARE v_avgShifts 		DECIMAL(4,2);
	DECLARE v_totShifts 		INT;
	DECLARE v_avgHours 			DECIMAL(5,2);
	DECLARE v_totHours 			DECIMAL(7,2);
	DECLARE v_avgWage 			DECIMAL(5,2);
	DECLARE v_totWage 			DECIMAL(7,2);
	DECLARE v_avgTips 			DECIMAL(5,2);
	DECLARE v_totTips 			INT;
	DECLARE v_avgEarned			DECIMAL(5,2);
	DECLARE v_totEarned			DECIMAL(7,2);
	DECLARE v_avgTipout 		DECIMAL(5,2);
	DECLARE v_totTipout 		INT;
	DECLARE v_avgSales 			DECIMAL(7,2);
	DECLARE v_totSales 			DECIMAL(10,2);
	DECLARE v_avgCovers 		DECIMAL(5,2);
	DECLARE v_totCovers 		INT;
	DECLARE v_avgCampHours 		DECIMAL(4,2);
	DECLARE v_totCampHours 		DECIMAL(8,2);
	DECLARE v_salesPerHour 		DECIMAL(7,2);
	DECLARE v_salesPerCover 	DECIMAL(7,2);
	DECLARE v_tipsPercent 		DECIMAL(4,1);
	DECLARE v_tipoutPercent 	DECIMAL(4,1);
	DECLARE v_tipsVsWage 		INT;
	DECLARE v_hourly 			DECIMAL(4,2);

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	SELECT COUNT(id) 
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_count;
	SELECT AVG(shifts) 
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_avgShifts;
	SELECT SUM(shifts) 
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_totShifts;
	SELECT AVG(hours)
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_avgHours;
	SELECT SUM(hours) 
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_totHours;
	SELECT AVG(earnedWage)
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_avgWage;
	SELECT SUM(earnedWage) 
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_totWage;
	SELECT AVG(earnedTips)
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_avgTips;
	SELECT SUM(earnedTips) 
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_totTips;
	SELECT AVG(tipout)
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_avgTipout;
	SELECT SUM(tipout) 
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_totTipout;
	SELECT AVG(sales)
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_avgSales;
	SELECT SUM(sales) 
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_totSales;
	SELECT AVG(covers)
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_avgCovers;
	SELECT SUM(covers) 
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_totCovers;
	SELECT AVG(campHours)
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_avgCampHours;
	SELECT SUM(campHours) 
		FROM month 
		WHERE year BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
			AND month BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		INTO v_totCampHours;
	SET v_avgEarned = v_avgWage + v_avgTips;
	SET v_totEarned = v_totWage + v_totTips;
	SET v_salesPerHour = v_totSales / v_totHours;
	SET v_salesPerCover = v_totSales / v_totCovers;
	SET v_tipsPercent = v_totTips * 100 / v_totSales;
	SET v_tipoutPercent = v_totTipout * 100 / v_totSales;
	SET v_tipsVsWage = v_totTips * 100 / v_totWage;
	SET v_hourly = (v_totWage + v_totTips) / v_totHours;

	IF (p_insert = 1)
		THEN INSERT INTO summary (count, avgHours, totHours, avgWage, totWage, avgTips, totTips, avgTipout, totTipout, avgSales, totSales, avgCovers, totCovers, avgCampHours, totCampHours, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, tipsVsWage, hourly, lunchDinner, dayOfWeek, timestamp)
			VALUES (v_count, v_avgHours, v_totHours, v_avgWage, v_totWage, v_avgTips, v_totTips, v_avgTipout, v_totTipout, v_avgSales, v_totSales, v_avgCovers, v_totCovers, v_avgCampHours, v_totCampHours, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_tipsVsWage, v_hourly, '-', 'Mth', CURRENT_TIMESTAMP);
		ELSE SELECT v_count as count,
			v_avgShifts as avgShifts,
			v_totShifts as totShifts,
			v_avgHours as avgHours,
			v_totHours as totHours,
			v_avgWage as avgWage,
			v_totWage as totWage,
			v_avgTips as avgTips,
			v_totTips as totTips,
			v_avgEarned as avgEarned,
			v_totEarned as totEarned,
			v_avgTipout as avgTipout,
			v_totTipout as totTipout,
			v_avgSales as avgSales,
			v_totSales as totSales,
			v_avgCovers as avgCovers,
			v_totCovers as totCovers,
			v_avgCampHours as avgCampHours,
			v_totCampHours as totCampHours,
			v_salesPerHour as salesPerHour,
			v_salesPerCover as salesPerCover,
			v_tipsPercent as tipsPercent,
			v_tipoutPercent as tipoutPercent,
			v_tipsVsWage as tipsVsWage,
			v_hourly as hourly;
	END IF;	
END //
DELIMITER ;

/*
############################################################################
	Somehow calculate summaries by combining lunch and dinner shifts on the same day to make split shifts

	SELECT 
		DATE(`date`) as `date`, 
	    COUNT(`id`) as `#shifts`, 
	    SUM(`hours`) as `s_hours`, 
	    SUM(`sales`) as `s_sales`, 
	    SUM(`earnedTips`) as `s_tips` 
	FROM `shift` 
	GROUP BY `date`

	maybe do this by making a new table called "splits" where every row from "shift" gets analysed, and shifts on the same date get combined into one row, with "S" as the lunchDinner letter
*/

DROP PROCEDURE IF EXISTS calculateSplits;
DELIMITER //
CREATE PROCEDURE calculateSplits()
BEGIN
	TRUNCATE TABLE split;
	INSERT INTO split (splitDate, count, campHours, sales, tipout, transfers, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, dayOfWeek)
	SELECT 
		date AS splitDate,
		COUNT(id) AS count,

		SUM(campHours) AS campHours,
		SUM(sales) AS sales,
		SUM(tipout) AS tipout,
		SUM(transfers) AS transfers,
		SUM(covers) AS covers,
		GROUP_CONCAT(cut SEPARATOR ' | ') AS cut,
		GROUP_CONCAT(section SEPARATOR ' | ') AS section,
		GROUP_CONCAT(notes SEPARATOR ' | ') AS notes,

		SUM(hours) AS hours,
		SUM(earnedWage) AS earnedWage,
		SUM(earnedTips) AS earnedTips,
		SUM(earnedTotal) AS earnedTotal,

		(SUM(earnedTips) / earnedWage) * 100 AS tipsVsWage,
		SUM(sales) / SUM(hours)  AS salesPerHour,
		SUM(sales) / SUM(covers) AS salesPerCover,
		(SUM(earnedTips) / SUM(sales)) * 100 AS tipsPercent,
		(SUM(tipout) / SUM(sales)) * 100 AS tipoutPercent,
		SUM(earnedTotal) / SUM(hours) AS hourly,
		LEFT(DAYNAME(DATE(date)), 3) AS dayOfWeek
	FROM shift
	GROUP BY date
	HAVING COUNT(id) > 1;
END //
DELIMITER ;
CALL calculateSplits;

/*
############################################################################
	Group shifts by week
	SELECT YEARWEEK(`startTime`) as `Week`, SUM(hours) FROM `shift` GROUP BY `Week`


	SET @date = '2015-05-11';
	SET @yearweek = YEARWEEK(@date,7);								#201519, for the 19th week in 2015 (pick mode 7 for monday as the start)
	SET @year = LEFT(@yearweek,4);									#2015, first four chars of @yearweek
	SET @week = RIGHT(@yearweek,2);									#19, last two chars of @yearweek
	SET @jan01 = MAKEDATE(@year, 1);								#2015-01-01, the first day of the year
	SET @addweeks = DATE_ADD(@jan01, INTERVAL @week WEEK);			#2014-05-14, adds @week to @jan01
	SET @weekday = WEEKDAY(@addweeks);								#3, because 2014-05-14 is a Thursday (0 Mon, 1 Tue, 2 Wed, 3 Thu, 4 Fri, 5 Sat, 6 Sun)
	SET @adjustday = @weekday - 0;									#3, subtract 0 to get Monday as start of week (-0 Mon, -1 Tue, -2 Wed, -3 Thu, -4 Fri, -5 Sat, -6 Sun)
	SET @startweek = DATE_SUB(@addweeks, INTERVAL @adjustday DAY);	#2015-05-11, because it's the Monday of that week

	SELECT @date, @yearweek, @year, @week, @jan01, @addweeks, @weekday, @adjustday, @startweek


	# YEARWEEK(startTime,3) - mode 3 means Monday is the start of a week (1-53), week 1 is the first week with 4 or more days
	# for the STR_TO_DATE, %x is year (mode 3), %v is the week (mode 3), %W is the Weekday Name. 
	SELECT
		YEARWEEK(startTime,3) as yearweek,
		STR_TO_DATE(CONCAT(YEARWEEK(startTime,3), ' Monday'), '%x%v %W') as weekstart,
		STR_TO_DATE(CONCAT(YEARWEEK(startTime,3), ' Sunday'), '%x%v %W') as weekend
	FROM shift
	GROUP BY YEARWEEK(startTime)
*/

DROP PROCEDURE IF EXISTS calculateWeeks;
DELIMITER //
CREATE PROCEDURE calculateWeeks(p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;
	DECLARE v_lunchDinner	CHAR(1);

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	IF (p_lunchDinner = 'L') 
		THEN SET v_lunchDinner = 'L';
		ELSEIF (p_lunchDinner = 'D') 
			THEN SET v_lunchDinner = 'D';
		ELSE 
			SET v_lunchDinner = '%';
	END IF;

	TRUNCATE TABLE week;
	INSERT INTO week (yearweek, startWeek, endWeek, shifts, campHours, sales, tipout, transfers, covers, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly)
	SELECT 
		YEARWEEK(date, 3) as yearweek, 
		STR_TO_DATE(CONCAT(YEARWEEK(date,3), ' Monday'), '%x%v %W') as startWeek,
		STR_TO_DATE(CONCAT(YEARWEEK(date,3), ' Sunday'), '%x%v %W') as endWeek,
		COUNT(id) AS shifts,

		SUM(campHours) AS campHours,
		SUM(sales) AS sales,
		SUM(tipout) AS tipout,
		SUM(transfers) AS transfers,
		SUM(covers) AS covers,

		SUM(hours) AS hours,
		SUM(earnedWage) AS earnedWage,
		SUM(earnedTips) AS earnedTips,
		SUM(earnedTotal) AS earnedTotal,

		SUM(earnedTips) * 100 / SUM(earnedWage) AS tipsVsWage,
		SUM(sales) / SUM(hours)  AS salesPerHour,
		SUM(sales) / SUM(covers) AS salesPerCover,
		SUM(earnedTips) * 100 / SUM(sales) AS tipsPercent,
		SUM(tipout) * 100 / SUM(sales) AS tipoutPercent,
		SUM(earnedTotal) / SUM(hours) AS hourly
	FROM shift
	WHERE YEARWEEK(date, 3) BETWEEN YEARWEEK(v_dateFrom, 3) AND YEARWEEK(v_dateTo, 3)
		AND UPPER(lunchDinner) LIKE UPPER(v_lunchDinner)
	GROUP BY YEARWEEK(date, 3);
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getWeeks;
DELIMITER //
CREATE PROCEDURE getWeeks(p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;
	DECLARE v_lunchDinner	CHAR(1);

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	IF (p_lunchDinner = 'L') 
		THEN SET v_lunchDinner = 'L';
		ELSEIF (p_lunchDinner = 'D') 
			THEN SET v_lunchDinner = 'D';
		ELSE 
			SET v_lunchDinner = '%';
	END IF;

	SELECT 
		YEARWEEK(date, 3) as yearweek, 
		STR_TO_DATE(CONCAT(YEARWEEK(date,3), ' Monday'), '%x%v %W') as startWeek,
		STR_TO_DATE(CONCAT(YEARWEEK(date,3), ' Sunday'), '%x%v %W') as endWeek,
		COUNT(id) AS shifts,

		SUM(campHours) AS campHours,
		SUM(sales) AS sales,
		SUM(tipout) AS tipout,
		SUM(transfers) AS transfers,
		SUM(covers) AS covers,

		SUM(hours) AS hours,
		SUM(earnedWage) AS earnedWage,
		SUM(earnedTips) AS earnedTips,
		SUM(earnedTotal) AS earnedTotal,

		SUM(earnedTips) * 100 / SUM(earnedWage) AS tipsVsWage,
		SUM(sales) / SUM(hours)  AS salesPerHour,
		SUM(sales) / SUM(covers) AS salesPerCover,
		SUM(earnedTips) * 100 / SUM(sales) AS tipsPercent,
		SUM(tipout) * 100 / SUM(sales) AS tipoutPercent,
		SUM(earnedTotal) / SUM(hours) AS hourly
	FROM shift
	WHERE YEARWEEK(date, 3) BETWEEN YEARWEEK(v_dateFrom, 3) AND YEARWEEK(v_dateTo, 3)
		AND UPPER(lunchDinner) LIKE UPPER(v_lunchDinner)
	GROUP BY YEARWEEK(date, 3);
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS calculateMonths;
DELIMITER //
CREATE PROCEDURE calculateMonths(p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;
	DECLARE v_lunchDinner	CHAR(1);

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	IF (p_lunchDinner = 'L') 
		THEN SET v_lunchDinner = 'L';
		ELSEIF (p_lunchDinner = 'D') 
			THEN SET v_lunchDinner = 'D';
		ELSE 
			SET v_lunchDinner = '%';
	END IF;

	TRUNCATE TABLE month;
	INSERT INTO month (year, month, monthname, shifts, campHours, sales, tipout, transfers, covers, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly)
	SELECT 
		YEAR(DATE) AS year,
		MONTH(date) as month,
		MONTHNAME(date) as monthname,
		COUNT(id) AS shifts,

		SUM(campHours) AS campHours,
		SUM(sales) AS sales,
		SUM(tipout) AS tipout,
		SUM(transfers) AS transfers,
		SUM(covers) AS covers,

		SUM(hours) AS hours,
		SUM(earnedWage) AS earnedWage,
		SUM(earnedTips) AS earnedTips,
		SUM(earnedTotal) AS earnedTotal,

		SUM(earnedTips) * 100 / SUM(earnedWage) AS tipsVsWage,
		SUM(sales) / SUM(hours)  AS salesPerHour,
		SUM(sales) / SUM(covers) AS salesPerCover,
		SUM(earnedTips) * 100 / SUM(sales) AS tipsPercent,
		SUM(tipout) * 100 / SUM(sales) AS tipoutPercent,
		SUM(earnedTotal) / SUM(hours) AS hourly
	FROM shift
	WHERE YEAR(date) BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
		AND MONTH(date) BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		AND UPPER(lunchDinner) LIKE UPPER(v_lunchDinner)
	GROUP BY YEAR(date), MONTH(date);
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getMonths;
DELIMITER //
CREATE PROCEDURE getMonths(p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;
	DECLARE v_lunchDinner	CHAR(1);

	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := p_dateFrom;
	END IF;

	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := p_dateTo;
	END IF;

	IF (p_lunchDinner = 'L') 
		THEN SET v_lunchDinner = 'L';
		ELSEIF (p_lunchDinner = 'D') 
			THEN SET v_lunchDinner = 'D';
		ELSE 
			SET v_lunchDinner = '%';
	END IF;

	SELECT 
		YEAR(date) as year,
		MONTH(date) as month,
		MONTHNAME(date) as monthname,
		COUNT(id) AS shifts,

		SUM(campHours) AS campHours,
		SUM(sales) AS sales,
		SUM(tipout) AS tipout,
		SUM(transfers) AS transfers,
		SUM(covers) AS covers,

		SUM(hours) AS hours,
		SUM(earnedWage) AS earnedWage,
		SUM(earnedTips) AS earnedTips,
		SUM(earnedTotal) AS earnedTotal,

		SUM(earnedTips) * 100 / SUM(earnedWage) AS tipsVsWage,
		SUM(sales) / SUM(hours)  AS salesPerHour,
		SUM(sales) / SUM(covers) AS salesPerCover,
		SUM(earnedTips) * 100 / SUM(sales) AS tipsPercent,
		SUM(tipout) * 100 / SUM(sales) AS tipoutPercent,
		SUM(earnedTotal) / SUM(hours) AS hourly
	FROM shift
	WHERE YEAR(date) BETWEEN YEAR(v_dateFrom) AND YEAR(v_dateTo)
		AND MONTH(date) BETWEEN MONTH(v_dateFrom) AND MONTH(v_dateTo)
		AND UPPER(lunchDinner) LIKE UPPER(v_lunchDinner)
	GROUP BY YEAR(date), MONTH(date);
END //
DELIMITER ;


/*
###############################################################################################

	#calculate avg hours based on start time

	SELECT 
		TIME(startTime) as startTime,

	    COUNT(id) as count,
	    AVG(hours) as avgHours,
	    AVG(sales) as avgSales,
	    AVG(earnedTotal) as avgEarned,
	    AVG(covers) as avgCovers,
	    SUM(sales) / SUM(hours) as salesPerHour,
	    SUM(sales) / SUM(covers) as salesPerCover,
		(SUM(earnedTips) / SUM(sales)) * 100 AS tipsPercent,
		(SUM(tipout) / SUM(sales)) * 100 AS tipoutPercent,
		SUM(earnedTotal) / SUM(hours) AS hourly
	FROM shift
	GROUP BY TIME(startTime)

	SELECT 
		cut,

	    COUNT(id) as count,
	    AVG(hours) as avgHours,
	    AVG(sales) as avgSales,
	    AVG(earnedTotal) as avgEarned,
	    AVG(covers) as avgCovers,
	    SUM(sales) / SUM(hours) as salesPerHour,
	    SUM(sales) / SUM(covers) as salesPerCover,
		(SUM(earnedTips) / SUM(sales)) * 100 AS tipsPercent,
		(SUM(tipout) / SUM(sales)) * 100 AS tipoutPercent,
		SUM(earnedTotal) / SUM(hours) AS hourly
	FROM shift
	GROUP BY cut


	SELECT 
		section,

	    COUNT(id) as count,
	    AVG(hours) as avgHours,
	    AVG(sales) as avgSales,
	    AVG(earnedTotal) as avgEarned,
	    AVG(covers) as avgCovers,
	    SUM(sales) / SUM(hours) as salesPerHour,
	    SUM(sales) / SUM(covers) as salesPerCover,
		(SUM(earnedTips) / SUM(sales)) * 100 AS tipsPercent,
		(SUM(tipout) / SUM(sales)) * 100 AS tipoutPercent,
		SUM(earnedTotal) / SUM(hours) AS hourly
	FROM shift
	WHERE lunchDinner = 'D'
	GROUP BY section
*/