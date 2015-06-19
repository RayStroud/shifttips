DROP PROCEDURE IF EXISTS getShifts;
DELIMITER //
CREATE PROCEDURE getShifts 
(
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

	SELECT id, wage, YEARWEEK(date, 3) as 'yearWeek', date, startTime, endTime, firstTable, campHours, sales, tipout, transfers, cash, due, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, noCampHourly, lunchDinner, dayOfWeek
	FROM shift
	WHERE date BETWEEN v_dateFrom AND v_dateTo
		AND UPPER(lunchDinner) LIKE v_lunchDinner
		AND (UPPER(dayOfWeek) IN (v_mon, v_tue, v_wed, v_thu, v_fri, v_sat, v_sun) 
			OR dayOfWeek LIKE v_anyDay)
	ORDER BY date, startTime ASC;
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
	CALL calculateSplits();
	CALL calculateSplitSummary(v_dateFrom, v_dateTo, "Mon");
	CALL calculateSplitSummary(v_dateFrom, v_dateTo, "Tue");
	CALL calculateSplitSummary(v_dateFrom, v_dateTo, "Wed");
	CALL calculateSplitSummary(v_dateFrom, v_dateTo, "Thu");
	CALL calculateSplitSummary(v_dateFrom, v_dateTo, "Fri");

	#Weekly
	CALL calculateWeeks();
	CALL calculateWeeklySummary(v_dateFrom, v_dateTo);

	SELECT * FROM summary;
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
	DECLARE v_hourlyWage 		DECIMAL(4,2);

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
	SET v_hourlyWage = (v_totWage + v_totTips) / v_totHours;

	INSERT INTO summary (count, avgHours, totHours, avgWage, totWage, avgTips, totTips, avgTipout, totTipout, avgSales, totSales, avgCovers, totCovers, avgCampHours, totCampHours, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, tipsVsWage, hourlyWage, lunchDinner, dayOfWeek, timestamp)
		VALUES (v_count, v_avgHours, v_totHours, v_avgWage, v_totWage, v_avgTips, v_totTips, v_avgTipout, v_totTipout, v_avgSales, v_totSales, v_avgCovers, v_totCovers, v_avgCampHours, v_totCampHours, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_tipsVsWage, v_hourlyWage, p_lunchDinner, p_dayOfWeek, CURRENT_TIMESTAMP);
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
	DECLARE v_hourlyWage 		DECIMAL(4,2);

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
	SET v_hourlyWage = (v_totWage + v_totTips) / v_totHours;

	INSERT INTO summary (count, avgHours, totHours, avgWage, totWage, avgTips, totTips, avgTipout, totTipout, avgSales, totSales, avgCovers, totCovers, avgCampHours, totCampHours, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, tipsVsWage, hourlyWage, lunchDinner, dayOfWeek, timestamp)
		VALUES (v_count, v_avgHours, v_totHours, v_avgWage, v_totWage, v_avgTips, v_totTips, v_avgTipout, v_totTipout, v_avgSales, v_totSales, v_avgCovers, v_totCovers, v_avgCampHours, v_totCampHours, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_tipsVsWage, v_hourlyWage, 'S', p_dayOfWeek, CURRENT_TIMESTAMP);
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS calculateWeeklySummary;
DELIMITER //
CREATE PROCEDURE calculateWeeklySummary (p_dateFrom DATE, p_dateTo DATE)
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
	DECLARE v_hourlyWage 		DECIMAL(4,2);

	SELECT COUNT(id) 
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_count;
	SELECT AVG(hours)
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_avgHours;
	SELECT SUM(hours) 
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_totHours;
	SELECT AVG(earnedWage)
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_avgWage;
	SELECT SUM(earnedWage) 
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_totWage;
	SELECT AVG(earnedTips)
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_avgTips;
	SELECT SUM(earnedTips) 
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_totTips;
	SELECT AVG(tipout)
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_avgTipout;
	SELECT SUM(tipout) 
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_totTipout;
	SELECT AVG(sales)
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_avgSales;
	SELECT SUM(sales) 
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_totSales;
	SELECT AVG(covers)
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_avgCovers;
	SELECT SUM(covers) 
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_totCovers;
	SELECT AVG(campHours)
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_avgCampHours;
	SELECT SUM(campHours) 
		FROM week 
		WHERE startWeek >= p_dateFrom
			AND endWeek <= p_dateTo
		INTO v_totCampHours;
	SET v_salesPerHour = v_totSales / v_totHours;
	SET v_salesPerCover = v_totSales / v_totCovers;
	SET v_tipsPercent = v_totTips * 100 / v_totSales;
	SET v_tipoutPercent = v_totTipout * 100 / v_totSales;
	SET v_tipsVsWage = v_totTips * 100 / v_totWage;
	SET v_hourlyWage = (v_totWage + v_totTips) / v_totHours;

	INSERT INTO summary (count, avgHours, totHours, avgWage, totWage, avgTips, totTips, avgTipout, totTipout, avgSales, totSales, avgCovers, totCovers, avgCampHours, totCampHours, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, tipsVsWage, hourlyWage, lunchDinner, dayOfWeek, timestamp)
		VALUES (v_count, v_avgHours, v_totHours, v_avgWage, v_totWage, v_avgTips, v_totTips, v_avgTipout, v_totTipout, v_avgSales, v_totSales, v_avgCovers, v_totCovers, v_avgCampHours, v_totCampHours, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_tipsVsWage, v_hourlyWage, '-', '---', CURRENT_TIMESTAMP);
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
	INSERT INTO split (splitDate, count, campHours, sales, tipout, transfers, covers, cut, section, notes, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly, dayOfWeek)
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
		SUM(sales) / SUM(hours)  AS salesPerHours,
		SUM(sales) / SUM(covers) AS salesPerCover,
		(SUM(earnedTips) / SUM(sales)) * 100 AS tipsPercent,
		(SUM(tipout) / SUM(sales)) * 100 AS tipoutPercent,
		SUM(earnedTotal) / SUM(hours) AS earnedHourly,
		LEFT(DAYNAME(DATE(date)), 3) AS dayOfWeek
	FROM shift
	GROUP BY date
	HAVING COUNT(id) > 1;
END //
DELIMITER ;
CALL calculateSplits;


/*
############################################################################
	find which section makes more tips

*/


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
CREATE PROCEDURE calculateWeeks()
BEGIN
	TRUNCATE TABLE week;
	INSERT INTO week (yearweek, startWeek, endWeek, count, campHours, sales, tipout, transfers, covers, hours, earnedWage, earnedTips, earnedTotal, tipsVsWage, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, earnedHourly)
	SELECT 
		YEARWEEK(date, 3) as yearweek, 
		STR_TO_DATE(CONCAT(YEARWEEK(date,3), ' Monday'), '%x%v %W') as startWeek,
		STR_TO_DATE(CONCAT(YEARWEEK(date,3), ' Sunday'), '%x%v %W') as endWeek,
		COUNT(id) AS count,

		SUM(campHours) AS campHours,
		SUM(sales) AS sales,
		SUM(tipout) AS tipout,
		SUM(transfers) AS transfers,
		SUM(covers) AS covers,

		SUM(hours) AS hours,
		SUM(earnedWage) AS earnedWage,
		SUM(earnedTips) AS earnedTips,
		SUM(earnedTotal) AS earnedTotal,

		(SUM(earnedTips) / earnedWage) * 100 AS tipsVsWage,
		SUM(sales) / SUM(hours)  AS salesPerHours,
		SUM(sales) / SUM(covers) AS salesPerCover,
		(SUM(earnedTips) / SUM(sales)) * 100 AS tipsPercent,
		(SUM(tipout) / SUM(sales)) * 100 AS tipoutPercent,
		SUM(earnedTotal) / SUM(hours) AS earnedHourly
	FROM shift
	GROUP BY yearweek;
END //
DELIMITER ;
CALL calculateWeeks;


/*
###############################################################################################

#calculate avg hours based on start time

*/
/*
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
	SUM(earnedTotal) / SUM(hours) AS earnedHourly
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
	SUM(earnedTotal) / SUM(hours) AS earnedHourly
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
	SUM(earnedTotal) / SUM(hours) AS earnedHourly
FROM shift
WHERE lunchDinner = 'D'
GROUP BY section
*/