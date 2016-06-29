-- ############################################################################################
-- SHIFTS
-- ############################################################################################

DROP PROCEDURE IF EXISTS getShiftById;
DELIMITER //
CREATE PROCEDURE getShiftById (p_user_id INT, p_id INT)
BEGIN
	SELECT user_id, id, wage, YEARWEEK(date, 3) as 'yearweek', date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, dueCheck, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek, WEEKDAY(date) as weekday
	FROM shift
	WHERE id = p_id
		AND user_id = p_user_id
	LIMIT 1;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getShifts;
DELIMITER //
CREATE PROCEDURE getShifts (p_user_id INT, p_dateFrom DATE, p_dateTo DATE)
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

	SELECT user_id, id, wage, YEARWEEK(date, 3) as 'yearweek', date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, dueCheck, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek, WEEKDAY(date) as weekday
	FROM shift
	WHERE date BETWEEN v_dateFrom AND v_dateTo
		AND user_id = p_user_id
	ORDER BY date, startTime ASC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getShiftsFiltered;
DELIMITER //
CREATE PROCEDURE getShiftsFiltered (
	p_user_id		INT,
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

	SELECT user_id, id, wage, YEARWEEK(date, 3) as 'yearWeek', date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, dueCheck, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek
	FROM shift
	WHERE date BETWEEN v_dateFrom AND v_dateTo
		AND UPPER(lunchDinner) LIKE v_lunchDinner
		AND (UPPER(dayOfWeek) IN (v_mon, v_tue, v_wed, v_thu, v_fri, v_sat, v_sun) 
			OR dayOfWeek LIKE v_anyDay)
		AND user_id = p_user_id
	ORDER BY date, startTime ASC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS saveShift;
DELIMITER //
CREATE PROCEDURE saveShift (
	# p_id: # to update, NULL to insert with id return, 0 to insert without return
	p_user_id		INT,
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
	p_dueCheck		CHAR(1),
	p_covers		INT,
	p_cut 			CHAR(1),
	p_section		VARCHAR(25),
	p_notes 		VARCHAR(1000)
)
BEGIN
	DECLARE v_dueCheck		CHAR(1);
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

	IF p_due < 1
		THEN SET v_dueCheck = NULL;
		ELSEIF p_dueCheck IS NULL AND p_due > 0
			THEN SET v_dueCheck := '!';
		ELSE SET v_dueCheck := p_dueCheck; 
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
	SET v_tipoutPercent := p_tipout * 100 / p_sales;
	SET v_hourly := v_earnedTotal / v_hours;
	SET v_noCampHourly := v_earnedTotal / (v_hours - p_campHours);
	IF (p_startTime BETWEEN '10:00' AND '14:00')
		THEN SET v_lunchDinner := 'L';
		ELSE SET v_lunchDinner := 'D';
	END IF;
	SET v_dayOfWeek := LEFT(DAYNAME(p_date),3);

	IF (p_id IS NULL)
		THEN INSERT INTO shift (user_id, wage, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, dueCheck, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek, created, updated) 
				VALUES (p_user_id, p_wage, p_date, p_startTime, p_endTime, p_firstTable, p_campHours, p_sales, p_tipout, p_transfers, p_cash, p_due, v_dueCheck, p_covers, p_cut, p_section, p_notes, v_hours, v_earnedWage, v_earnedTips, v_earnedTotal, v_tipsVsWage, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_hourly, v_noCampHourly, v_lunchDinner, v_dayOfWeek, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
			SELECT LAST_INSERT_ID() as id;
		ELSEIF (p_id = 0)
			THEN INSERT INTO shift (user_id, wage, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, dueCheck, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek, created, updated) 
					VALUES (p_user_id, p_wage, p_date, p_startTime, p_endTime, p_firstTable, p_campHours, p_sales, p_tipout, p_transfers, p_cash, p_due, v_dueCheck, p_covers, p_cut, p_section, p_notes, v_hours, v_earnedWage, v_earnedTips, v_earnedTotal, v_tipsVsWage, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_hourly, v_noCampHourly, v_lunchDinner, v_dayOfWeek, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
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
			dueCheck = v_dueCheck,
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
			dayOfWeek = v_dayOfWeek,
			updated = CURRENT_TIMESTAMP
			WHERE id = p_id 
				AND user_id = p_user_id 
				LIMIT 1;
	END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS deleteShift;
DELIMITER //
CREATE PROCEDURE deleteShift (p_user_id INT, p_id INT)
BEGIN
	DELETE FROM shift WHERE id = p_id AND user_id = p_user_id LIMIT 1;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS setDueCheck;
DELIMITER //
CREATE PROCEDURE setDueCheck (p_user_id INT, p_id INT, p_dueCheck CHAR(1))
BEGIN
	UPDATE shift SET dueCheck = p_dueCheck WHERE id = p_id AND user_id = p_user_id LIMIT 1;
END //
DELIMITER ;

-- ############################################################################################
-- SUMMARY
-- ############################################################################################

DROP PROCEDURE IF EXISTS getSummary;
DELIMITER //
CREATE PROCEDURE getSummary (p_user_id INT, p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	-- VARCHAR(11) is highest int value, which is what a user_id can be
	DECLARE v_user_id		VARCHAR(11);
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;
	DECLARE v_lunchDinner	CHAR(1);

	IF (p_user_id IS NULL)
		THEN SET v_user_id := '%';
		ELSE SET v_user_id := p_user_id;
	END IF;

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
		ROUND(AVG(transfers)	,2) as avgTransfers,
		ROUND(SUM(transfers)	,0) as totTransfers,
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
		AND UPPER(lunchDinner) LIKE UPPER(v_lunchDinner)
		AND user_id LIKE v_user_id;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryFiltered;
DELIMITER //
CREATE PROCEDURE getSummaryFiltered (
	p_user_id		INT,
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
	-- VARCHAR(11) is highest int value, which is what a user_id can be
	DECLARE v_user_id		VARCHAR(11);
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

	IF (p_user_id IS NULL)
		THEN SET v_user_id := '%';
		ELSE SET v_user_id := p_user_id;
	END IF;

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
		ROUND(AVG(transfers)	,2) as avgTransfers,
		ROUND(SUM(transfers)	,0) as totTransfers,
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
		AND UPPER(lunchDinner) LIKE v_lunchDinner
		AND (UPPER(dayOfWeek) IN (v_mon, v_tue, v_wed, v_thu, v_fri, v_sat, v_sun) 
			OR dayOfWeek LIKE v_anyDay)
		AND user_id LIKE v_user_id;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryByLunchDinner;
DELIMITER //
CREATE PROCEDURE getSummaryByLunchDinner (p_user_id INT, p_dateFrom DATE, p_dateTo DATE)
BEGIN
	-- VARCHAR(11) is highest int value, which is what a user_id can be
	DECLARE v_user_id		VARCHAR(11);
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;

	IF (p_user_id IS NULL)
		THEN SET v_user_id := '%';
		ELSE SET v_user_id := p_user_id;
	END IF;

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
		ROUND(AVG(transfers)	,2) as avgTransfers,
		ROUND(SUM(transfers)	,0) as totTransfers,
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
		AND user_id LIKE v_user_id
	GROUP BY lunchDinner;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryByDayOfWeek;
DELIMITER //
CREATE PROCEDURE getSummaryByDayOfWeek (p_user_id INT, p_dateFrom DATE, p_dateTo DATE)
BEGIN
	-- VARCHAR(11) is highest int value, which is what a user_id can be
	DECLARE v_user_id		VARCHAR(11);
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

	IF (p_user_id IS NULL)
		THEN SET v_user_id := '%';
		ELSE SET v_user_id := p_user_id;
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
		ROUND(AVG(transfers)	,2) as avgTransfers,
		ROUND(SUM(transfers)	,0) as totTransfers,
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
		AND user_id LIKE v_user_id
	GROUP BY dayOfWeek, lunchDinner
	ORDER BY WEEKDAY(date), lunchDinner DESC;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryBySection;
DELIMITER //
CREATE PROCEDURE getSummaryBySection (p_user_id INT, p_dateFrom DATE, p_dateTo DATE)
BEGIN
	-- VARCHAR(11) is highest int value, which is what a user_id can be
	DECLARE v_user_id		VARCHAR(11);
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;

	IF (p_user_id IS NULL)
		THEN SET v_user_id := '%';
		ELSE SET v_user_id := p_user_id;
	END IF;

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
		ROUND(AVG(transfers)	,2) as avgTransfers,
		ROUND(SUM(transfers)	,0) as totTransfers,
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
		AND user_id LIKE v_user_id
	GROUP BY section, lunchDinner;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryByStartTime;
DELIMITER //
CREATE PROCEDURE getSummaryByStartTime (p_user_id INT, p_dateFrom DATE, p_dateTo DATE)
BEGIN
	-- VARCHAR(11) is highest int value, which is what a user_id can be
	DECLARE v_user_id		VARCHAR(11);
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;

	IF (p_user_id IS NULL)
		THEN SET v_user_id := '%';
		ELSE SET v_user_id := p_user_id;
	END IF;

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
		ROUND(AVG(transfers)	,2) as avgTransfers,
		ROUND(SUM(transfers)	,0) as totTransfers,
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
		AND user_id LIKE v_user_id
	GROUP BY startTime, lunchDinner;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryByCut;
DELIMITER //
CREATE PROCEDURE getSummaryByCut (p_user_id INT, p_dateFrom DATE, p_dateTo DATE)
BEGIN
	-- VARCHAR(11) is highest int value, which is what a user_id can be
	DECLARE v_user_id		VARCHAR(11);
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;

	IF (p_user_id IS NULL)
		THEN SET v_user_id := '%';
		ELSE SET v_user_id := p_user_id;
	END IF;

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
		ROUND(AVG(transfers)	,2) as avgTransfers,
		ROUND(SUM(transfers)	,0) as totTransfers,
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
		AND user_id LIKE v_user_id
	GROUP BY cut, lunchDinner;
END //
DELIMITER ;

-- ############################################################################################
-- PERIOD
-- ############################################################################################

DROP PROCEDURE IF EXISTS getSummaryWeekly;
DELIMITER //
CREATE PROCEDURE getSummaryWeekly (p_user_id INT, p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	CALL calculateWeeks(p_user_id, p_dateFrom, p_dateTo, p_lunchDinner);
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
		ROUND(AVG(transfers)	,2) as avgTransfers,
		ROUND(SUM(transfers)	,0) as totTransfers,
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
CREATE PROCEDURE getSummaryMonthly (p_user_id INT, p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	CALL calculateMonths(p_user_id, p_dateFrom, p_dateTo, p_lunchDinner);
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
		ROUND(AVG(transfers)	,2) as avgTransfers,
		ROUND(SUM(transfers)	,0) as totTransfers,
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

DROP PROCEDURE IF EXISTS calculateWeeks;
DELIMITER //
CREATE PROCEDURE calculateWeeks(p_user_id INT, p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	-- VARCHAR(11) is highest int value, which is what a user_id can be
	DECLARE v_user_id		VARCHAR(11);
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;
	DECLARE v_lunchDinner	CHAR(1);

	IF (p_user_id IS NULL)
		THEN SET v_user_id := '%';
		ELSE SET v_user_id := p_user_id;
	END IF;

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
		AND user_id LIKE v_user_id
	GROUP BY YEARWEEK(date, 3);
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getWeeks;
DELIMITER //
CREATE PROCEDURE getWeeks(p_user_id INT, p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	-- VARCHAR(11) is highest int value, which is what a user_id can be
	DECLARE v_user_id		VARCHAR(11);
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;
	DECLARE v_lunchDinner	CHAR(1);

	IF (p_user_id IS NULL)
		THEN SET v_user_id := '%';
		ELSE SET v_user_id := p_user_id;
	END IF;

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
		AND user_id LIKE v_user_id
	GROUP BY YEARWEEK(date, 3);
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS calculateMonths;
DELIMITER //
CREATE PROCEDURE calculateMonths(p_user_id INT, p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	-- VARCHAR(11) is highest int value, which is what a user_id can be
	DECLARE v_user_id		VARCHAR(11);
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;
	DECLARE v_lunchDinner	CHAR(1);

	IF (p_user_id IS NULL)
		THEN SET v_user_id := '%';
		ELSE SET v_user_id := p_user_id;
	END IF;

	-- get first day of the month
	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := SUBDATE(p_dateFrom, (DAY(p_dateFrom)-1));
	END IF;

	-- get last day of the month
	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := LAST_DAY(p_dateTo);
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
	WHERE date BETWEEN v_dateFrom AND v_dateTo
		AND UPPER(lunchDinner) LIKE UPPER(v_lunchDinner)
		AND user_id LIKE v_user_id
	GROUP BY YEAR(date), MONTH(date);
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getMonths;
DELIMITER //
CREATE PROCEDURE getMonths(p_user_id INT, p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
BEGIN
	-- VARCHAR(11) is highest int value, which is what a user_id can be
	DECLARE v_user_id		VARCHAR(11);
	DECLARE v_dateFrom		DATE;
	DECLARE v_dateTo		DATE;
	DECLARE v_lunchDinner	CHAR(1);

	IF (p_user_id IS NULL)
		THEN SET v_user_id := '%';
		ELSE SET v_user_id := p_user_id;
	END IF;

	-- get first day of the month
	IF (p_dateFrom IS NULL)
		THEN SET v_dateFrom := '1000-01-01';
		ELSE SET v_dateFrom := SUBDATE(p_dateFrom, (DAY(p_dateFrom)-1));
	END IF;

	-- get last day of the month
	IF (p_dateTo IS NULL)
		THEN SET v_dateTo := '9999-12-31';
		ELSE SET v_dateTo := LAST_DAY(p_dateTo);
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
	WHERE date BETWEEN v_dateFrom AND v_dateTo
		AND UPPER(lunchDinner) LIKE UPPER(v_lunchDinner)
		AND user_id LIKE v_user_id
	GROUP BY YEAR(date), MONTH(date);
END //
DELIMITER ;

-- ############################################################################################
-- USER
-- ############################################################################################


DROP PROCEDURE IF EXISTS createUser;
DELIMITER //
CREATE PROCEDURE createUser(p_name VARCHAR(35), p_email VARCHAR(254))
BEGIN
	IF NOT EXISTS (SELECT 1 FROM user WHERE email = p_email)
		AND p_email LIKE '%@%.%'
		THEN INSERT INTO user SET
				name = p_name,
				email = p_email,
				created = CURRENT_TIMESTAMP;
			SELECT LAST_INSERT_ID() as id;
		ELSE SELECT 0 as id;
	END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS login;
DELIMITER //
CREATE PROCEDURE login(p_name VARCHAR(35), p_email VARCHAR(254))
BEGIN
	DECLARE v_user_id		VARCHAR(11);

	SELECT id FROM user WHERE email = p_email AND name = p_name INTO v_user_id;

	IF v_user_id > 0
		THEN SELECT v_user_id AS id; UPDATE user SET active = CURRENT_TIMESTAMP WHERE id = v_user_id;
		ELSE SELECT 0 as id;
	END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS saveUserPrefs;
DELIMITER //
CREATE PROCEDURE saveUserPrefs (p_user_id INT, list_lunchDinner BIT, list_dayOfWeek BIT, list_startTime BIT, list_endTime BIT, list_hours BIT, list_earnedWage BIT, list_earnedTips BIT, list_earnedTotal BIT, list_firstTable BIT, list_sales BIT, list_tipout BIT, list_transfers BIT, list_covers BIT, list_campHours BIT, list_salesPerHour BIT, list_salesPerCover BIT, list_tipsPercent BIT, list_tipoutPercent BIT, list_tipsVsWage BIT, list_hourly BIT, list_cash BIT, list_due BIT, list_dueCheck BIT, list_cut BIT, list_section BIT, list_notes BIT, list_noCampHourly BIT, grid_startTime BIT, grid_endTime BIT, grid_sales BIT, grid_earnedTips BIT, grid_tipsPercent BIT, grid_hourly BIT, grid_hours BIT, grid_wage BIT, grid_earnedWage BIT, grid_earnedTotal BIT, grid_tipout BIT, grid_transfers BIT, grid_covers BIT, grid_campHours BIT, grid_salesPerHour BIT, grid_salesPerCover BIT, grid_tipoutPercent BIT, grid_tipsVsWage BIT, grid_cash BIT, grid_due BIT, grid_dueCheck BIT, grid_cut BIT, grid_section BIT, grid_noCampHourly BIT, summ_hours BIT, summ_earnedWage BIT, summ_earnedTips BIT, summ_earnedTotal BIT, summ_sales BIT, summ_tipout BIT, summ_covers BIT, summ_campHours BIT, summ_salesPerHour BIT, summ_salesPerCover BIT, summ_tipsPercent BIT, summ_tipoutPercent BIT, summ_tipsVsWage BIT, summ_hourly BIT, summ_transfers BIT, summ_noCampHourly BIT, prod_shifts BIT, prod_hours BIT, prod_earnedWage BIT, prod_earnedTips BIT, prod_earnedTotal BIT, prod_sales BIT, prod_tipout BIT, prod_covers BIT, prod_campHours BIT, prod_salesPerHour BIT, prod_salesPerCover BIT, prod_tipsPercent BIT, prod_tipoutPercent BIT, prod_tipsVsWage BIT, prod_hourly BIT, prod_transfers BIT, prod_noCampHourly BIT, adds_wage BIT, adds_startTime BIT, adds_endTime BIT, adds_firstTable BIT, adds_campHours BIT, adds_sales BIT, adds_covers BIT, adds_tipout BIT, adds_transfers BIT, adds_cash BIT, adds_due BIT, adds_section BIT, adds_cut BIT, adds_notes BIT, edit_wage BIT, edit_startTime BIT, edit_endTime BIT, edit_firstTable BIT, edit_campHours BIT, edit_sales BIT, edit_covers BIT, edit_tipout BIT, edit_transfers BIT, edit_cash BIT, edit_due BIT, edit_section BIT, edit_cut BIT, edit_notes BIT, view_startTime BIT, view_endTime BIT, view_hours BIT, view_wage BIT, view_sales BIT, view_covers BIT, view_salesPerHour BIT, view_salesPerCover BIT, view_tipout BIT, view_tipoutPercent BIT, view_transfers BIT, view_cash BIT, view_due BIT, view_earnedWage BIT, view_earnedTips BIT, view_tipsPercent BIT, view_earnedTotal BIT, view_tipsVsWage BIT, view_hourly BIT, view_firstTable BIT, view_campHours BIT, view_section BIT, view_cut BIT, view_notes BIT, view_noCampHourly BIT)
BEGIN
	INSERT INTO prefs (id, list_lunchDinner, list_dayOfWeek, list_startTime, list_endTime, list_hours, list_earnedWage, list_earnedTips, list_earnedTotal, list_firstTable, list_sales, list_tipout, list_transfers, list_covers, list_campHours, list_salesPerHour, list_salesPerCover, list_tipsPercent, list_tipoutPercent, list_tipsVsWage, list_hourly, list_cash, list_due, list_dueCheck, list_cut, list_section, list_notes, list_noCampHourly, grid_startTime, grid_endTime, grid_sales, grid_earnedTips, grid_tipsPercent, grid_hourly, grid_hours, grid_wage, grid_earnedWage, grid_earnedTotal, grid_tipout, grid_transfers, grid_covers, grid_campHours, grid_salesPerHour, grid_salesPerCover, grid_tipoutPercent, grid_tipsVsWage, grid_cash, grid_due, grid_dueCheck, grid_cut, grid_section, grid_noCampHourly, summ_hours, summ_earnedWage, summ_earnedTips, summ_earnedTotal, summ_sales, summ_tipout, summ_covers, summ_campHours, summ_salesPerHour, summ_salesPerCover, summ_tipsPercent, summ_tipoutPercent, summ_tipsVsWage, summ_hourly, summ_transfers, summ_noCampHourly, prod_shifts, prod_hours, prod_earnedWage, prod_earnedTips, prod_earnedTotal, prod_sales, prod_tipout, prod_covers, prod_campHours, prod_salesPerHour, prod_salesPerCover, prod_tipsPercent, prod_tipoutPercent, prod_tipsVsWage, prod_hourly, prod_transfers, prod_noCampHourly, adds_wage, adds_startTime, adds_endTime, adds_firstTable, adds_campHours, adds_sales, adds_covers, adds_tipout, adds_transfers, adds_cash, adds_due, adds_section, adds_cut, adds_notes, edit_wage, edit_startTime, edit_endTime, edit_firstTable, edit_campHours, edit_sales, edit_covers, edit_tipout, edit_transfers, edit_cash, edit_due, edit_section, edit_cut, edit_notes, view_startTime, view_endTime, view_hours, view_wage, view_sales, view_covers, view_salesPerHour, view_salesPerCover, view_tipout, view_tipoutPercent, view_transfers, view_cash, view_due, view_earnedWage, view_earnedTips, view_tipsPercent, view_earnedTotal, view_tipsVsWage, view_hourly, view_firstTable, view_campHours, view_section, view_cut, view_notes, view_noCampHourly)
		VALUES(p_user_id, list_lunchDinner, list_dayOfWeek, list_startTime, list_endTime, list_hours, list_earnedWage, list_earnedTips, list_earnedTotal, list_firstTable, list_sales, list_tipout, list_transfers, list_covers, list_campHours, list_salesPerHour, list_salesPerCover, list_tipsPercent, list_tipoutPercent, list_tipsVsWage, list_hourly, list_cash, list_due, list_dueCheck, list_cut, list_section, list_notes, list_noCampHourly, grid_startTime, grid_endTime, grid_sales, grid_earnedTips, grid_tipsPercent, grid_hourly, grid_hours, grid_wage, grid_earnedWage, grid_earnedTotal, grid_tipout, grid_transfers, grid_covers, grid_campHours, grid_salesPerHour, grid_salesPerCover, grid_tipoutPercent, grid_tipsVsWage, grid_cash, grid_due, grid_dueCheck, grid_cut, grid_section, grid_noCampHourly, summ_hours, summ_earnedWage, summ_earnedTips, summ_earnedTotal, summ_sales, summ_tipout, summ_covers, summ_campHours, summ_salesPerHour, summ_salesPerCover, summ_tipsPercent, summ_tipoutPercent, summ_tipsVsWage, summ_hourly, summ_transfers, summ_noCampHourly, prod_shifts, prod_hours, prod_earnedWage, prod_earnedTips, prod_earnedTotal, prod_sales, prod_tipout, prod_covers, prod_campHours, prod_salesPerHour, prod_salesPerCover, prod_tipsPercent, prod_tipoutPercent, prod_tipsVsWage, prod_hourly, prod_transfers, prod_noCampHourly, adds_wage, adds_startTime, adds_endTime, adds_firstTable, adds_campHours, adds_sales, adds_covers, adds_tipout, adds_transfers, adds_cash, adds_due, adds_section, adds_cut, adds_notes, edit_wage, edit_startTime, edit_endTime, edit_firstTable, edit_campHours, edit_sales, edit_covers, edit_tipout, edit_transfers, edit_cash, edit_due, edit_section, edit_cut, edit_notes, view_startTime, view_endTime, view_hours, view_wage, view_sales, view_covers, view_salesPerHour, view_salesPerCover, view_tipout, view_tipoutPercent, view_transfers, view_cash, view_due, view_earnedWage, view_earnedTips, view_tipsPercent, view_earnedTotal, view_tipsVsWage, view_hourly, view_firstTable, view_campHours, view_section, view_cut, view_notes, view_noCampHourly)
		ON DUPLICATE KEY UPDATE 
			list_lunchDinner = list_lunchDinner,
			list_dayOfWeek = list_dayOfWeek,
			list_startTime = list_startTime,
			list_endTime = list_endTime,
			list_hours = list_hours,
			list_earnedWage = list_earnedWage,
			list_earnedTips = list_earnedTips,
			list_earnedTotal = list_earnedTotal,
			list_firstTable = list_firstTable,
			list_sales = list_sales,
			list_tipout = list_tipout,
			list_transfers = list_transfers,
			list_covers = list_covers,
			list_campHours = list_campHours,
			list_salesPerHour = list_salesPerHour,
			list_salesPerCover = list_salesPerCover,
			list_tipsPercent = list_tipsPercent,
			list_tipoutPercent = list_tipoutPercent,
			list_tipsVsWage = list_tipsVsWage,
			list_hourly = list_hourly,
			list_cash = list_cash,
			list_due = list_due,
			list_dueCheck = list_dueCheck,
			list_cut = list_cut,
			list_section = list_section,
			list_notes = list_notes,
			list_noCampHourly = list_noCampHourly,
			grid_startTime = grid_startTime,
			grid_endTime = grid_endTime,
			grid_sales = grid_sales,
			grid_earnedTips = grid_earnedTips,
			grid_tipsPercent = grid_tipsPercent,
			grid_hourly = grid_hourly,
			grid_hours = grid_hours,
			grid_wage = grid_wage,
			grid_earnedWage = grid_earnedWage,
			grid_earnedTotal = grid_earnedTotal,
			grid_tipout = grid_tipout,
			grid_transfers = grid_transfers,
			grid_covers = grid_covers,
			grid_campHours = grid_campHours,
			grid_salesPerHour = grid_salesPerHour,
			grid_salesPerCover = grid_salesPerCover,
			grid_tipoutPercent = grid_tipoutPercent,
			grid_tipsVsWage = grid_tipsVsWage,
			grid_cash = grid_cash,
			grid_due = grid_due,
			grid_dueCheck = grid_dueCheck,
			grid_cut = grid_cut,
			grid_section = grid_section,
			grid_noCampHourly = grid_noCampHourly,
			summ_hours = summ_hours,
			summ_earnedWage = summ_earnedWage,
			summ_earnedTips = summ_earnedTips,
			summ_earnedTotal = summ_earnedTotal,
			summ_sales = summ_sales,
			summ_tipout = summ_tipout,
			summ_covers = summ_covers,
			summ_campHours = summ_campHours,
			summ_salesPerHour = summ_salesPerHour,
			summ_salesPerCover = summ_salesPerCover,
			summ_tipsPercent = summ_tipsPercent,
			summ_tipoutPercent = summ_tipoutPercent,
			summ_tipsVsWage = summ_tipsVsWage,
			summ_hourly = summ_hourly,
			summ_transfers = summ_transfers,
			summ_noCampHourly = summ_noCampHourly,
			prod_shifts = prod_shifts,
			prod_hours = prod_hours,
			prod_earnedWage = prod_earnedWage,
			prod_earnedTips = prod_earnedTips,
			prod_earnedTotal = prod_earnedTotal,
			prod_sales = prod_sales,
			prod_tipout = prod_tipout,
			prod_covers = prod_covers,
			prod_campHours = prod_campHours,
			prod_salesPerHour = prod_salesPerHour,
			prod_salesPerCover = prod_salesPerCover,
			prod_tipsPercent = prod_tipsPercent,
			prod_tipoutPercent = prod_tipoutPercent,
			prod_tipsVsWage = prod_tipsVsWage,
			prod_hourly = prod_hourly,
			prod_transfers = prod_transfers,
			prod_noCampHourly = prod_noCampHourly,
			adds_wage = adds_wage,
			adds_startTime = adds_startTime,
			adds_endTime = adds_endTime,
			adds_firstTable = adds_firstTable,
			adds_campHours = adds_campHours,
			adds_sales = adds_sales,
			adds_covers = adds_covers,
			adds_tipout = adds_tipout,
			adds_transfers = adds_transfers,
			adds_cash = adds_cash,
			adds_due = adds_due,
			adds_section = adds_section,
			adds_cut = adds_cut,
			adds_notes = adds_notes,
			edit_wage = edit_wage,
			edit_startTime = edit_startTime,
			edit_endTime = edit_endTime,
			edit_firstTable = edit_firstTable,
			edit_campHours = edit_campHours,
			edit_sales = edit_sales,
			edit_covers = edit_covers,
			edit_tipout = edit_tipout,
			edit_transfers = edit_transfers,
			edit_cash = edit_cash,
			edit_due = edit_due,
			edit_section = edit_section,
			edit_cut = edit_cut,
			edit_notes = edit_notes,
			view_startTime = view_startTime,
			view_endTime = view_endTime,
			view_hours = view_hours,
			view_wage = view_wage,
			view_sales = view_sales,
			view_covers = view_covers,
			view_salesPerHour = view_salesPerHour,
			view_salesPerCover = view_salesPerCover,
			view_tipout = view_tipout,
			view_tipoutPercent = view_tipoutPercent,
			view_transfers = view_transfers,
			view_cash = view_cash,
			view_due = view_due,
			view_earnedWage = view_earnedWage,
			view_earnedTips = view_earnedTips,
			view_tipsPercent = view_tipsPercent,
			view_earnedTotal = view_earnedTotal,
			view_tipsVsWage = view_tipsVsWage,
			view_hourly = view_hourly,
			view_firstTable = view_firstTable,
			view_campHours = view_campHours,
			view_section = view_section,
			view_cut = view_cut,
			view_notes = view_notes,
			view_noCampHourly = view_noCampHourly
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS checkUsers;
DELIMITER //
CREATE PROCEDURE checkUsers()
BEGIN
	SELECT user.id, user.name, user.email, user.created, user.active, COUNT(shift.id) as 'shifts', MAX(shift.updated) as 'lastUpdate'
	FROM shift
		JOIN user ON shift.user_id = user.id
	GROUP BY user.id;
END //
DELIMITER ;
