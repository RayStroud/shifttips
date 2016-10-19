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
			COUNT(id) AS count,
			ROUND(AVG(hours)		,2) AS avgHours,
			ROUND(SUM(hours)		,0) AS totHours,
			ROUND(AVG(earnedWage)	,2) AS avgWage,
			ROUND(SUM(earnedWage)	,0) AS totWage,
			ROUND(AVG(earnedTips)	,2) AS avgTips,
			ROUND(SUM(earnedTips)	,0) AS totTips,
			ROUND(AVG(earnedTotal)	,2) AS avgEarned,
			ROUND(SUM(earnedTotal)	,0) AS totEarned,
			ROUND(AVG(tipout)		,2) AS avgTipout,
			ROUND(SUM(tipout)		,0) AS totTipout,
			ROUND(AVG(transfers)	,2) AS avgTransfers,
			ROUND(SUM(transfers)	,0) AS totTransfers,
			ROUND(AVG(sales)		,0) AS avgSales,
			ROUND(SUM(sales)		,0) AS totSales,
			ROUND(AVG(covers)		,1) AS avgCovers,
			ROUND(SUM(covers)		,0) AS totCovers,
			ROUND(AVG(campHours)	,2) AS avgCampHours,
			ROUND(SUM(campHours)	,2) AS totCampHours,
			ROUND(SUM(sales) / SUM(hours)	,2)	AS salesPerHour,
			ROUND(SUM(sales) / SUM(covers)	,2)	AS salesPerCover,
			ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	AS tipsPercent,
			ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	AS tipoutPercent,
			ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	AS tipsVsWage,
			ROUND((SUM(earnedTotal) / SUM(hours) 	,2)	AS hourly,
			ROUND((SUM(earnedTotal) / COUNT(id) 	,2)	AS earnedPerShift
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
			COUNT(id) AS count,
			ROUND(AVG(hours)		,2) AS avgHours,
			ROUND(SUM(hours)		,0) AS totHours,
			ROUND(AVG(earnedWage)	,2) AS avgWage,
			ROUND(SUM(earnedWage)	,0) AS totWage,
			ROUND(AVG(earnedTips)	,2) AS avgTips,
			ROUND(SUM(earnedTips)	,0) AS totTips,
			ROUND(AVG(earnedTotal)	,2) AS avgEarned,
			ROUND(SUM(earnedTotal)	,0) AS totEarned,
			ROUND(AVG(tipout)		,2) AS avgTipout,
			ROUND(SUM(tipout)		,0) AS totTipout,
			ROUND(AVG(transfers)	,2) AS avgTransfers,
			ROUND(SUM(transfers)	,0) AS totTransfers,
			ROUND(AVG(sales)		,0) AS avgSales,
			ROUND(SUM(sales)		,0) AS totSales,
			ROUND(AVG(covers)		,1) AS avgCovers,
			ROUND(SUM(covers)		,0) AS totCovers,
			ROUND(AVG(campHours)	,2) AS avgCampHours,
			ROUND(SUM(campHours)	,2) AS totCampHours,
			ROUND(SUM(sales) / SUM(hours)	,2)	AS salesPerHour,
			ROUND(SUM(sales) / SUM(covers)	,2)	AS salesPerCover,
			ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	AS tipsPercent,
			ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	AS tipoutPercent,
			ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	AS tipsVsWage,
			ROUND((SUM(earnedTotal) / SUM(hours) 	,2)	AS hourly,
			ROUND((SUM(earnedTotal) / COUNT(id) 	,2)	AS earnedPerShift
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
			COUNT(id) AS count,
			ROUND(AVG(hours)		,2) AS avgHours,
			ROUND(SUM(hours)		,0) AS totHours,
			ROUND(AVG(earnedWage)	,2) AS avgWage,
			ROUND(SUM(earnedWage)	,0) AS totWage,
			ROUND(AVG(earnedTips)	,2) AS avgTips,
			ROUND(SUM(earnedTips)	,0) AS totTips,
			ROUND(AVG(earnedTotal)	,2) AS avgEarned,
			ROUND(SUM(earnedTotal)	,0) AS totEarned,
			ROUND(AVG(tipout)		,2) AS avgTipout,
			ROUND(SUM(tipout)		,0) AS totTipout,
			ROUND(AVG(transfers)	,2) AS avgTransfers,
			ROUND(SUM(transfers)	,0) AS totTransfers,
			ROUND(AVG(sales)		,0) AS avgSales,
			ROUND(SUM(sales)		,0) AS totSales,
			ROUND(AVG(covers)		,1) AS avgCovers,
			ROUND(SUM(covers)		,0) AS totCovers,
			ROUND(AVG(campHours)	,2) AS avgCampHours,
			ROUND(SUM(campHours)	,2) AS totCampHours,
			ROUND(SUM(sales) / SUM(hours)	,2)	AS salesPerHour,
			ROUND(SUM(sales) / SUM(covers)	,2)	AS salesPerCover,
			ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	AS tipsPercent,
			ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	AS tipoutPercent,
			ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	AS tipsVsWage,
			ROUND((SUM(earnedTotal) / SUM(hours) 	,2)	AS hourly,
			ROUND((SUM(earnedTotal) / COUNT(id) 	,2)	AS earnedPerShift
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
			WEEKDAY(date) AS weekday,
			dayOfWeek,
			lunchDinner,
			COUNT(id) AS count,
			ROUND(AVG(hours)		,2) AS avgHours,
			ROUND(SUM(hours)		,0) AS totHours,
			ROUND(AVG(earnedWage)	,2) AS avgWage,
			ROUND(SUM(earnedWage)	,0) AS totWage,
			ROUND(AVG(earnedTips)	,2) AS avgTips,
			ROUND(SUM(earnedTips)	,0) AS totTips,
			ROUND(AVG(earnedTotal)	,2) AS avgEarned,
			ROUND(SUM(earnedTotal)	,0) AS totEarned,
			ROUND(AVG(tipout)		,2) AS avgTipout,
			ROUND(SUM(tipout)		,0) AS totTipout,
			ROUND(AVG(transfers)	,2) AS avgTransfers,
			ROUND(SUM(transfers)	,0) AS totTransfers,
			ROUND(AVG(sales)		,0) AS avgSales,
			ROUND(SUM(sales)		,0) AS totSales,
			ROUND(AVG(covers)		,1) AS avgCovers,
			ROUND(SUM(covers)		,0) AS totCovers,
			ROUND(AVG(campHours)	,2) AS avgCampHours,
			ROUND(SUM(campHours)	,2) AS totCampHours,
			ROUND(SUM(sales) / SUM(hours)	,2)	AS salesPerHour,
			ROUND(SUM(sales) / SUM(covers)	,2)	AS salesPerCover,
			ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	AS tipsPercent,
			ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	AS tipoutPercent,
			ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	AS tipsVsWage,
			ROUND((SUM(earnedTotal) / SUM(hours) 	,2)	AS hourly,
			ROUND((SUM(earnedTotal) / COUNT(id) 	,2)	AS earnedPerShift
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
			COUNT(id) AS count,
			ROUND(AVG(hours)		,2) AS avgHours,
			ROUND(SUM(hours)		,0) AS totHours,
			ROUND(AVG(earnedWage)	,2) AS avgWage,
			ROUND(SUM(earnedWage)	,0) AS totWage,
			ROUND(AVG(earnedTips)	,2) AS avgTips,
			ROUND(SUM(earnedTips)	,0) AS totTips,
			ROUND(AVG(earnedTotal)	,2) AS avgEarned,
			ROUND(SUM(earnedTotal)	,0) AS totEarned,
			ROUND(AVG(tipout)		,2) AS avgTipout,
			ROUND(SUM(tipout)		,0) AS totTipout,
			ROUND(AVG(transfers)	,2) AS avgTransfers,
			ROUND(SUM(transfers)	,0) AS totTransfers,
			ROUND(AVG(sales)		,0) AS avgSales,
			ROUND(SUM(sales)		,0) AS totSales,
			ROUND(AVG(covers)		,1) AS avgCovers,
			ROUND(SUM(covers)		,0) AS totCovers,
			ROUND(AVG(campHours)	,2) AS avgCampHours,
			ROUND(SUM(campHours)	,2) AS totCampHours,
			ROUND(SUM(sales) / SUM(hours)	,2)	AS salesPerHour,
			ROUND(SUM(sales) / SUM(covers)	,2)	AS salesPerCover,
			ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	AS tipsPercent,
			ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	AS tipoutPercent,
			ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	AS tipsVsWage,
			ROUND((SUM(earnedTotal) / SUM(hours) 	,2)	AS hourly,
			ROUND((SUM(earnedTotal) / COUNT(id) 	,2)	AS earnedPerShift
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
			COUNT(id) AS count,
			ROUND(AVG(hours)		,2) AS avgHours,
			ROUND(SUM(hours)		,0) AS totHours,
			ROUND(AVG(earnedWage)	,2) AS avgWage,
			ROUND(SUM(earnedWage)	,0) AS totWage,
			ROUND(AVG(earnedTips)	,2) AS avgTips,
			ROUND(SUM(earnedTips)	,0) AS totTips,
			ROUND(AVG(earnedTotal)	,2) AS avgEarned,
			ROUND(SUM(earnedTotal)	,0) AS totEarned,
			ROUND(AVG(tipout)		,2) AS avgTipout,
			ROUND(SUM(tipout)		,0) AS totTipout,
			ROUND(AVG(transfers)	,2) AS avgTransfers,
			ROUND(SUM(transfers)	,0) AS totTransfers,
			ROUND(AVG(sales)		,0) AS avgSales,
			ROUND(SUM(sales)		,0) AS totSales,
			ROUND(AVG(covers)		,1) AS avgCovers,
			ROUND(SUM(covers)		,0) AS totCovers,
			ROUND(AVG(campHours)	,2) AS avgCampHours,
			ROUND(SUM(campHours)	,2) AS totCampHours,
			ROUND(SUM(sales) / SUM(hours)	,2)	AS salesPerHour,
			ROUND(SUM(sales) / SUM(covers)	,2)	AS salesPerCover,
			ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	AS tipsPercent,
			ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	AS tipoutPercent,
			ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	AS tipsVsWage,
			ROUND((SUM(earnedTotal) / SUM(hours) 	,2)	AS hourly,
			ROUND((SUM(earnedTotal) / COUNT(id) 	,2)	AS earnedPerShift
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
			COUNT(id) AS count,
			ROUND(AVG(hours)		,2) AS avgHours,
			ROUND(SUM(hours)		,0) AS totHours,
			ROUND(AVG(earnedWage)	,2) AS avgWage,
			ROUND(SUM(earnedWage)	,0) AS totWage,
			ROUND(AVG(earnedTips)	,2) AS avgTips,
			ROUND(SUM(earnedTips)	,0) AS totTips,
			ROUND(AVG(earnedTotal)	,2) AS avgEarned,
			ROUND(SUM(earnedTotal)	,0) AS totEarned,
			ROUND(AVG(tipout)		,2) AS avgTipout,
			ROUND(SUM(tipout)		,0) AS totTipout,
			ROUND(AVG(transfers)	,2) AS avgTransfers,
			ROUND(SUM(transfers)	,0) AS totTransfers,
			ROUND(AVG(sales)		,0) AS avgSales,
			ROUND(SUM(sales)		,0) AS totSales,
			ROUND(AVG(covers)		,1) AS avgCovers,
			ROUND(SUM(covers)		,0) AS totCovers,
			ROUND(AVG(campHours)	,2) AS avgCampHours,
			ROUND(SUM(campHours)	,2) AS totCampHours,
			ROUND(SUM(sales) / SUM(hours)	,2)	AS salesPerHour,
			ROUND(SUM(sales) / SUM(covers)	,2)	AS salesPerCover,
			ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	AS tipsPercent,
			ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	AS tipoutPercent,
			ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	AS tipsVsWage,
			ROUND((SUM(earnedTotal) / SUM(hours) 	,2)	AS hourly,
			ROUND((SUM(earnedTotal) / COUNT(id) 	,2)	AS earnedPerShift
		FROM shift
		WHERE date BETWEEN v_dateFrom AND v_dateTo
			AND user_id LIKE v_user_id
		GROUP BY cut, lunchDinner;
	END //
	DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryByHalfhours;
	DELIMITER //
	CREATE PROCEDURE getSummaryByHalfhours (p_user_id INT, p_dateFrom DATE, p_dateTo DATE)
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
			ROUND(hours*2,0)/2 AS halfhours,
			lunchDinner,
			COUNT(id) AS count,
			ROUND(AVG(hours)		,2) AS avgHours,
			ROUND(SUM(hours)		,0) AS totHours,
			ROUND(AVG(earnedWage)	,2) AS avgWage,
			ROUND(SUM(earnedWage)	,0) AS totWage,
			ROUND(AVG(earnedTips)	,2) AS avgTips,
			ROUND(SUM(earnedTips)	,0) AS totTips,
			ROUND(AVG(earnedTotal)	,2) AS avgEarned,
			ROUND(SUM(earnedTotal)	,0) AS totEarned,
			ROUND(AVG(tipout)		,2) AS avgTipout,
			ROUND(SUM(tipout)		,0) AS totTipout,
			ROUND(AVG(transfers)	,2) AS avgTransfers,
			ROUND(SUM(transfers)	,0) AS totTransfers,
			ROUND(AVG(sales)		,0) AS avgSales,
			ROUND(SUM(sales)		,0) AS totSales,
			ROUND(AVG(covers)		,1) AS avgCovers,
			ROUND(SUM(covers)		,0) AS totCovers,
			ROUND(AVG(campHours)	,2) AS avgCampHours,
			ROUND(SUM(campHours)	,2) AS totCampHours,
			ROUND(SUM(sales) / SUM(hours)	,2)	AS salesPerHour,
			ROUND(SUM(sales) / SUM(covers)	,2)	AS salesPerCover,
			ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	AS tipsPercent,
			ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	AS tipoutPercent,
			ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	AS tipsVsWage,
			ROUND((SUM(earnedTotal) / SUM(hours) 	,2)	AS hourly,
			ROUND((SUM(earnedTotal) / COUNT(id) 	,2)	AS earnedPerShift
		FROM shift
		WHERE date BETWEEN v_dateFrom AND v_dateTo
			AND user_id LIKE v_user_id
		GROUP BY halfhours, lunchDinner;
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
			COUNT(id) AS count,
			ROUND(AVG(shifts)		,1) AS avgShifts,
			ROUND(SUM(shifts)		,0) AS totShifts,
			ROUND(AVG(hours)		,2) AS avgHours,
			ROUND(SUM(hours)		,0) AS totHours,
			ROUND(AVG(earnedWage)	,2) AS avgWage,
			ROUND(SUM(earnedWage)	,0) AS totWage,
			ROUND(AVG(earnedTips)	,2) AS avgTips,
			ROUND(SUM(earnedTips)	,0) AS totTips,
			ROUND(AVG(earnedTotal)	,2) AS avgEarned,
			ROUND(SUM(earnedTotal)	,0) AS totEarned,
			ROUND(AVG(tipout)		,2) AS avgTipout,
			ROUND(SUM(tipout)		,0) AS totTipout,
			ROUND(AVG(transfers)	,2) AS avgTransfers,
			ROUND(SUM(transfers)	,0) AS totTransfers,
			ROUND(AVG(sales)		,0) AS avgSales,
			ROUND(SUM(sales)		,0) AS totSales,
			ROUND(AVG(covers)		,1) AS avgCovers,
			ROUND(SUM(covers)		,0) AS totCovers,
			ROUND(AVG(campHours)	,2) AS avgCampHours,
			ROUND(SUM(campHours)	,2) AS totCampHours,
			ROUND(SUM(sales) / SUM(hours)	,2)	AS salesPerHour,
			ROUND(SUM(sales) / SUM(covers)	,2)	AS salesPerCover,
			ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	AS tipsPercent,
			ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	AS tipoutPercent,
			ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	AS tipsVsWage,
			ROUND((SUM(earnedTotal) / SUM(hours) 	,2)	AS hourly,
			ROUND((SUM(earnedTotal) / COUNT(id)) AS earnedPerShift
		FROM week;
	END //
	DELIMITER ;

DROP PROCEDURE IF EXISTS getSummaryMonthly;
	DELIMITER //
	CREATE PROCEDURE getSummaryMonthly (p_user_id INT, p_dateFrom DATE, p_dateTo DATE, p_lunchDinner CHAR(1))
	BEGIN
		CALL calculateMonths(p_user_id, p_dateFrom, p_dateTo, p_lunchDinner);
		SELECT
			COUNT(id) AS count,
			ROUND(AVG(shifts)		,1) AS avgShifts,
			ROUND(SUM(shifts)		,0) AS totShifts,
			ROUND(AVG(hours)		,2) AS avgHours,
			ROUND(SUM(hours)		,0) AS totHours,
			ROUND(AVG(earnedWage)	,2) AS avgWage,
			ROUND(SUM(earnedWage)	,0) AS totWage,
			ROUND(AVG(earnedTips)	,2) AS avgTips,
			ROUND(SUM(earnedTips)	,0) AS totTips,
			ROUND(AVG(earnedTotal)	,2) AS avgEarned,
			ROUND(SUM(earnedTotal)	,0) AS totEarned,
			ROUND(AVG(tipout)		,2) AS avgTipout,
			ROUND(SUM(tipout)		,0) AS totTipout,
			ROUND(AVG(transfers)	,2) AS avgTransfers,
			ROUND(SUM(transfers)	,0) AS totTransfers,
			ROUND(AVG(sales)		,0) AS avgSales,
			ROUND(SUM(sales)		,0) AS totSales,
			ROUND(AVG(covers)		,1) AS avgCovers,
			ROUND(SUM(covers)		,0) AS totCovers,
			ROUND(AVG(campHours)	,2) AS avgCampHours,
			ROUND(SUM(campHours)	,2) AS totCampHours,
			ROUND(SUM(sales) / SUM(hours)	,2)	AS salesPerHour,
			ROUND(SUM(sales) / SUM(covers)	,2)	AS salesPerCover,
			ROUND(SUM(earnedTips) * 100 / SUM(sales) 	,1)	AS tipsPercent,
			ROUND(SUM(tipout) * 100 / SUM(sales) 		,1)	AS tipoutPercent,
			ROUND(SUM(earnedTips) * 100 / SUM(earnedWage) 			,0)	AS tipsVsWage,
			ROUND((SUM(earnedTotal) / SUM(hours) 	,2)	AS hourly,
			ROUND((SUM(earnedTotal) / COUNT(id)) AS earnedPerShift
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
			SUM(earnedTotal) / SUM(hours) AS hourly,
			SUM(earnedTotal) / COUNT(id) AS earnedPerShift
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
			SUM(earnedTotal) / SUM(hours) AS hourly,
			SUM(earnedTotal) / COUNT(id) AS earnedPerShift
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
			SUM(earnedTotal) / SUM(hours) AS hourly,
			SUM(earnedTotal) / COUNT(id) AS earnedPerShift
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
			SUM(earnedTotal) / SUM(hours) AS hourly,
			SUM(earnedTotal) / COUNT(id) AS earnedPerShift
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
CREATE PROCEDURE saveUserPrefs 
(
	p_user_id INT, l_lunchDinner BIT, l_dayOfWeek BIT, l_startTime BIT, l_endTime BIT, l_hours BIT, l_earnedWage BIT, l_earnedTips BIT, l_earnedTotal BIT, l_firstTable BIT, l_sales BIT, l_tipout BIT, l_transfers BIT, l_covers BIT, l_campHours BIT, l_salesPerHour BIT, l_salesPerCover BIT, l_tipsPercent BIT, l_tipoutPercent BIT, l_tipsVsWage BIT, l_hourly BIT, l_cash BIT, l_due BIT, l_dueCheck BIT, l_cut BIT, l_section BIT, l_notes BIT, l_noCampHourly BIT, g_startTime BIT, g_endTime BIT, g_sales BIT, g_earnedTips BIT, g_tipsPercent BIT, g_hourly BIT, g_hours BIT, g_wage BIT, g_earnedWage BIT, g_earnedTotal BIT, g_tipout BIT, g_transfers BIT, g_covers BIT, g_campHours BIT, g_salesPerHour BIT, g_salesPerCover BIT, g_tipoutPercent BIT, g_tipsVsWage BIT, g_cash BIT, g_due BIT, g_dueCheck BIT, g_cut BIT, g_section BIT, g_noCampHourly BIT, s_hours BIT, s_earnedWage BIT, s_earnedTips BIT, s_earnedTotal BIT, s_sales BIT, s_tipout BIT, s_covers BIT, s_campHours BIT, s_salesPerHour BIT, s_salesPerCover BIT, s_tipsPercent BIT, s_tipoutPercent BIT, s_tipsVsWage BIT, s_hourly BIT, s_transfers BIT, s_noCampHourly BIT, p_shifts BIT, p_hours BIT, p_earnedWage BIT, p_earnedTips BIT, p_earnedTotal BIT, p_sales BIT, p_tipout BIT, p_covers BIT, p_campHours BIT, p_salesPerHour BIT, p_salesPerCover BIT, p_tipsPercent BIT, p_tipoutPercent BIT, p_tipsVsWage BIT, p_hourly BIT, p_transfers BIT, p_noCampHourly BIT, a_wage BIT, a_startTime BIT, a_endTime BIT, a_firstTable BIT, a_campHours BIT, a_sales BIT, a_covers BIT, a_tipout BIT, a_transfers BIT, a_cash BIT, a_due BIT, a_section BIT, a_cut BIT, a_notes BIT, e_wage BIT, e_startTime BIT, e_endTime BIT, e_firstTable BIT, e_campHours BIT, e_sales BIT, e_covers BIT, e_tipout BIT, e_transfers BIT, e_cash BIT, e_due BIT, e_section BIT, e_cut BIT, e_notes BIT, v_startTime BIT, v_endTime BIT, v_hours BIT, v_wage BIT, v_sales BIT, v_covers BIT, v_salesPerHour BIT, v_salesPerCover BIT, v_tipout BIT, v_tipoutPercent BIT, v_transfers BIT, v_cash BIT, v_due BIT, v_earnedWage BIT, v_earnedTips BIT, v_tipsPercent BIT, v_earnedTotal BIT, v_tipsVsWage BIT, v_hourly BIT, v_firstTable BIT, v_campHours BIT, v_section BIT, v_cut BIT, v_notes BIT, v_noCampHourly BIT
)
BEGIN
	INSERT INTO prefs (id, l_lunchDinner, l_dayOfWeek, l_startTime, l_endTime, l_hours, l_earnedWage, l_earnedTips, l_earnedTotal, l_firstTable, l_sales, l_tipout, l_transfers, l_covers, l_campHours, l_salesPerHour, l_salesPerCover, l_tipsPercent, l_tipoutPercent, l_tipsVsWage, l_hourly, l_cash, l_due, l_dueCheck, l_cut, l_section, l_notes, l_noCampHourly, g_startTime, g_endTime, g_sales, g_earnedTips, g_tipsPercent, g_hourly, g_hours, g_wage, g_earnedWage, g_earnedTotal, g_tipout, g_transfers, g_covers, g_campHours, g_salesPerHour, g_salesPerCover, g_tipoutPercent, g_tipsVsWage, g_cash, g_due, g_dueCheck, g_cut, g_section, g_noCampHourly, s_hours, s_earnedWage, s_earnedTips, s_earnedTotal, s_sales, s_tipout, s_covers, s_campHours, s_salesPerHour, s_salesPerCover, s_tipsPercent, s_tipoutPercent, s_tipsVsWage, s_hourly, s_transfers, s_noCampHourly, p_shifts, p_hours, p_earnedWage, p_earnedTips, p_earnedTotal, p_sales, p_tipout, p_covers, p_campHours, p_salesPerHour, p_salesPerCover, p_tipsPercent, p_tipoutPercent, p_tipsVsWage, p_hourly, p_transfers, p_noCampHourly, a_wage, a_startTime, a_endTime, a_firstTable, a_campHours, a_sales, a_covers, a_tipout, a_transfers, a_cash, a_due, a_section, a_cut, a_notes, e_wage, e_startTime, e_endTime, e_firstTable, e_campHours, e_sales, e_covers, e_tipout, e_transfers, e_cash, e_due, e_section, e_cut, e_notes, v_startTime, v_endTime, v_hours, v_wage, v_sales, v_covers, v_salesPerHour, v_salesPerCover, v_tipout, v_tipoutPercent, v_transfers, v_cash, v_due, v_earnedWage, v_earnedTips, v_tipsPercent, v_earnedTotal, v_tipsVsWage, v_hourly, v_firstTable, v_campHours, v_section, v_cut, v_notes, v_noCampHourly)
		VALUES(p_user_id, l_lunchDinner, l_dayOfWeek, l_startTime, l_endTime, l_hours, l_earnedWage, l_earnedTips, l_earnedTotal, l_firstTable, l_sales, l_tipout, l_transfers, l_covers, l_campHours, l_salesPerHour, l_salesPerCover, l_tipsPercent, l_tipoutPercent, l_tipsVsWage, l_hourly, l_cash, l_due, l_dueCheck, l_cut, l_section, l_notes, l_noCampHourly, g_startTime, g_endTime, g_sales, g_earnedTips, g_tipsPercent, g_hourly, g_hours, g_wage, g_earnedWage, g_earnedTotal, g_tipout, g_transfers, g_covers, g_campHours, g_salesPerHour, g_salesPerCover, g_tipoutPercent, g_tipsVsWage, g_cash, g_due, g_dueCheck, g_cut, g_section, g_noCampHourly, s_hours, s_earnedWage, s_earnedTips, s_earnedTotal, s_sales, s_tipout, s_covers, s_campHours, s_salesPerHour, s_salesPerCover, s_tipsPercent, s_tipoutPercent, s_tipsVsWage, s_hourly, s_transfers, s_noCampHourly, p_shifts, p_hours, p_earnedWage, p_earnedTips, p_earnedTotal, p_sales, p_tipout, p_covers, p_campHours, p_salesPerHour, p_salesPerCover, p_tipsPercent, p_tipoutPercent, p_tipsVsWage, p_hourly, p_transfers, p_noCampHourly, a_wage, a_startTime, a_endTime, a_firstTable, a_campHours, a_sales, a_covers, a_tipout, a_transfers, a_cash, a_due, a_section, a_cut, a_notes, e_wage, e_startTime, e_endTime, e_firstTable, e_campHours, e_sales, e_covers, e_tipout, e_transfers, e_cash, e_due, e_section, e_cut, e_notes, v_startTime, v_endTime, v_hours, v_wage, v_sales, v_covers, v_salesPerHour, v_salesPerCover, v_tipout, v_tipoutPercent, v_transfers, v_cash, v_due, v_earnedWage, v_earnedTips, v_tipsPercent, v_earnedTotal, v_tipsVsWage, v_hourly, v_firstTable, v_campHours, v_section, v_cut, v_notes, v_noCampHourly)
		ON DUPLICATE KEY UPDATE 
			l_lunchDinner = l_lunchDinner,
			l_dayOfWeek = l_dayOfWeek,
			l_startTime = l_startTime,
			l_endTime = l_endTime,
			l_hours = l_hours,
			l_earnedWage = l_earnedWage,
			l_earnedTips = l_earnedTips,
			l_earnedTotal = l_earnedTotal,
			l_firstTable = l_firstTable,
			l_sales = l_sales,
			l_tipout = l_tipout,
			l_transfers = l_transfers,
			l_covers = l_covers,
			l_campHours = l_campHours,
			l_salesPerHour = l_salesPerHour,
			l_salesPerCover = l_salesPerCover,
			l_tipsPercent = l_tipsPercent,
			l_tipoutPercent = l_tipoutPercent,
			l_tipsVsWage = l_tipsVsWage,
			l_hourly = l_hourly,
			l_cash = l_cash,
			l_due = l_due,
			l_dueCheck = l_dueCheck,
			l_cut = l_cut,
			l_section = l_section,
			l_notes = l_notes,
			l_noCampHourly = l_noCampHourly,
			g_startTime = g_startTime,
			g_endTime = g_endTime,
			g_sales = g_sales,
			g_earnedTips = g_earnedTips,
			g_tipsPercent = g_tipsPercent,
			g_hourly = g_hourly,
			g_hours = g_hours,
			g_wage = g_wage,
			g_earnedWage = g_earnedWage,
			g_earnedTotal = g_earnedTotal,
			g_tipout = g_tipout,
			g_transfers = g_transfers,
			g_covers = g_covers,
			g_campHours = g_campHours,
			g_salesPerHour = g_salesPerHour,
			g_salesPerCover = g_salesPerCover,
			g_tipoutPercent = g_tipoutPercent,
			g_tipsVsWage = g_tipsVsWage,
			g_cash = g_cash,
			g_due = g_due,
			g_dueCheck = g_dueCheck,
			g_cut = g_cut,
			g_section = g_section,
			g_noCampHourly = g_noCampHourly,
			s_hours = s_hours,
			s_earnedWage = s_earnedWage,
			s_earnedTips = s_earnedTips,
			s_earnedTotal = s_earnedTotal,
			s_sales = s_sales,
			s_tipout = s_tipout,
			s_covers = s_covers,
			s_campHours = s_campHours,
			s_salesPerHour = s_salesPerHour,
			s_salesPerCover = s_salesPerCover,
			s_tipsPercent = s_tipsPercent,
			s_tipoutPercent = s_tipoutPercent,
			s_tipsVsWage = s_tipsVsWage,
			s_hourly = s_hourly,
			s_transfers = s_transfers,
			s_noCampHourly = s_noCampHourly,
			p_shifts = p_shifts,
			p_hours = p_hours,
			p_earnedWage = p_earnedWage,
			p_earnedTips = p_earnedTips,
			p_earnedTotal = p_earnedTotal,
			p_sales = p_sales,
			p_tipout = p_tipout,
			p_covers = p_covers,
			p_campHours = p_campHours,
			p_salesPerHour = p_salesPerHour,
			p_salesPerCover = p_salesPerCover,
			p_tipsPercent = p_tipsPercent,
			p_tipoutPercent = p_tipoutPercent,
			p_tipsVsWage = p_tipsVsWage,
			p_hourly = p_hourly,
			p_transfers = p_transfers,
			p_noCampHourly = p_noCampHourly,
			a_wage = a_wage,
			a_startTime = a_startTime,
			a_endTime = a_endTime,
			a_firstTable = a_firstTable,
			a_campHours = a_campHours,
			a_sales = a_sales,
			a_covers = a_covers,
			a_tipout = a_tipout,
			a_transfers = a_transfers,
			a_cash = a_cash,
			a_due = a_due,
			a_section = a_section,
			a_cut = a_cut,
			a_notes = a_notes,
			e_wage = e_wage,
			e_startTime = e_startTime,
			e_endTime = e_endTime,
			e_firstTable = e_firstTable,
			e_campHours = e_campHours,
			e_sales = e_sales,
			e_covers = e_covers,
			e_tipout = e_tipout,
			e_transfers = e_transfers,
			e_cash = e_cash,
			e_due = e_due,
			e_section = e_section,
			e_cut = e_cut,
			e_notes = e_notes,
			v_startTime = v_startTime,
			v_endTime = v_endTime,
			v_hours = v_hours,
			v_wage = v_wage,
			v_sales = v_sales,
			v_covers = v_covers,
			v_salesPerHour = v_salesPerHour,
			v_salesPerCover = v_salesPerCover,
			v_tipout = v_tipout,
			v_tipoutPercent = v_tipoutPercent,
			v_transfers = v_transfers,
			v_cash = v_cash,
			v_due = v_due,
			v_earnedWage = v_earnedWage,
			v_earnedTips = v_earnedTips,
			v_tipsPercent = v_tipsPercent,
			v_earnedTotal = v_earnedTotal,
			v_tipsVsWage = v_tipsVsWage,
			v_hourly = v_hourly,
			v_firstTable = v_firstTable,
			v_campHours = v_campHours,
			v_section = v_section,
			v_cut = v_cut,
			v_notes = v_notes,
			v_noCampHourly = v_noCampHourly;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS getUserPrefs;
DELIMITER //
CREATE PROCEDURE getUserPrefs(p_user_id INT)
BEGIN
	SELECT id, l_lunchDinner, l_dayOfWeek, l_startTime, l_endTime, l_hours, l_earnedWage, l_earnedTips, l_earnedTotal, l_firstTable, l_sales, l_tipout, l_transfers, l_covers, l_campHours, l_salesPerHour, l_salesPerCover, l_tipsPercent, l_tipoutPercent, l_tipsVsWage, l_hourly, l_cash, l_due, l_dueCheck, l_cut, l_section, l_notes, l_noCampHourly, g_startTime, g_endTime, g_sales, g_earnedTips, g_tipsPercent, g_hourly, g_hours, g_wage, g_earnedWage, g_earnedTotal, g_tipout, g_transfers, g_covers, g_campHours, g_salesPerHour, g_salesPerCover, g_tipoutPercent, g_tipsVsWage, g_cash, g_due, g_dueCheck, g_cut, g_section, g_noCampHourly, s_hours, s_earnedWage, s_earnedTips, s_earnedTotal, s_sales, s_tipout, s_covers, s_campHours, s_salesPerHour, s_salesPerCover, s_tipsPercent, s_tipoutPercent, s_tipsVsWage, s_hourly, s_transfers, s_noCampHourly, p_shifts, p_hours, p_earnedWage, p_earnedTips, p_earnedTotal, p_sales, p_tipout, p_covers, p_campHours, p_salesPerHour, p_salesPerCover, p_tipsPercent, p_tipoutPercent, p_tipsVsWage, p_hourly, p_transfers, p_noCampHourly, a_wage, a_startTime, a_endTime, a_firstTable, a_campHours, a_sales, a_covers, a_tipout, a_transfers, a_cash, a_due, a_section, a_cut, a_notes, e_wage, e_startTime, e_endTime, e_firstTable, e_campHours, e_sales, e_covers, e_tipout, e_transfers, e_cash, e_due, e_section, e_cut, e_notes, v_startTime, v_endTime, v_hours, v_wage, v_sales, v_covers, v_salesPerHour, v_salesPerCover, v_tipout, v_tipoutPercent, v_transfers, v_cash, v_due, v_earnedWage, v_earnedTips, v_tipsPercent, v_earnedTotal, v_tipsVsWage, v_hourly, v_firstTable, v_campHours, v_section, v_cut, v_notes, v_noCampHourly
	FROM prefs
	WHERE id = p_user_id;
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
