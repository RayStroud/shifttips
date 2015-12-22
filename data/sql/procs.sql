DROP PROCEDURE IF EXISTS getShiftById;
DELIMITER //
CREATE PROCEDURE getShiftById (p_id INT)
BEGIN
	SELECT id, wage, YEARWEEK(date, 3) as 'yearweek', date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, dueCheck, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek, WEEKDAY(date) as weekday
	FROM shift
	WHERE id = p_id
	LIMIT 1;
END //
DELIMITER ;

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

	SELECT id, wage, YEARWEEK(date, 3) as 'yearweek', date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, dueCheck, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek, WEEKDAY(date) as weekday
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

	SELECT id, wage, YEARWEEK(date, 3) as 'yearWeek', date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, dueCheck, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek
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
	p_dueCheck		BIT,
	p_covers		INT,
	p_cut 			CHAR(1),
	p_section		VARCHAR(25),
	p_notes 		VARCHAR(1000)
)
BEGIN
	DECLARE v_dueCheck		BIT;
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

	IF p_dueCheck = 1
		THEN SET v_dueCheck := 1;
		ELSEIF (p_due IS NULL || p_due < 1)
			THEN SET v_dueCheck := NULL;
		ELSE SET v_dueCheck := 0;
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
	IF (p_startTime BETWEEN '10:00' AND '13:00')
		THEN SET v_lunchDinner := 'L';
		ELSE SET v_lunchDinner := 'D';
	END IF;
	SET v_dayOfWeek := LEFT(DAYNAME(p_date),3);

	IF (p_id IS NULL)
		THEN INSERT INTO shift (wage, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, dueCheck, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek) 
				VALUES (p_wage, p_date, p_startTime, p_endTime, p_firstTable, p_campHours, p_sales, p_tipout, p_transfers, p_cash, p_due, v_dueCheck, p_covers, p_cut, p_section, p_notes, v_hours, v_earnedWage, v_earnedTips, v_earnedTotal, v_tipsVsWage, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_hourly, v_noCampHourly, v_lunchDinner, v_dayOfWeek);
			SELECT LAST_INSERT_ID() as id;
		ELSEIF (p_id = 0)
			THEN INSERT INTO shift (wage, date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, dueCheck, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, hourly, noCampHourly, lunchDinner, dayOfWeek) 
					VALUES (p_wage, p_date, p_startTime, p_endTime, p_firstTable, p_campHours, p_sales, p_tipout, p_transfers, p_cash, p_due, v_dueCheck, p_covers, p_cut, p_section, p_notes, v_hours, v_earnedWage, v_earnedTips, v_earnedTotal, v_tipsVsWage, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_hourly, v_noCampHourly, v_lunchDinner, v_dayOfWeek);
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
			dayOfWeek = v_dayOfWeek
			WHERE id = p_id LIMIT 1;
	END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS deleteShift;
DELIMITER //
CREATE PROCEDURE deleteShift (p_id INT)
BEGIN
	DELETE FROM shift WHERE id = p_id LIMIT 1;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS setDueCheck;
DELIMITER //
CREATE PROCEDURE setDueCheck (p_id INT, p_dueCheck BIT)
BEGIN
	UPDATE shift SET dueCheck = p_dueCheck WHERE id = p_id LIMIT 1;
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
		AND UPPER(lunchDinner) LIKE UPPER(v_lunchDinner);
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryFiltered;
DELIMITER //
CREATE PROCEDURE getSummaryFiltered (
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
			OR dayOfWeek LIKE v_anyDay);
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
