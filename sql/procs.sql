DROP PROCEDURE IF EXISTS calculateSummaries;
DELIMITER //
CREATE PROCEDURE calculateSummaries (p_startDate TIMESTAMP, p_endDate TIMESTAMP)
BEGIN
	TRUNCATE TABLE summaries;

	CALL calculateSummary(p_startDate, p_endDate, "%", "%");
	CALL calculateSummary(p_startDate, p_endDate, "D", "%");
	CALL calculateSummary(p_startDate, p_endDate, "L", "%");
	CALL calculateSplitSummary(p_startDate, p_endDate, "%");

	CALL calculateSummary(p_startDate, p_endDate, "D", "Mon");
	CALL calculateSummary(p_startDate, p_endDate, "D", "Tue");
	CALL calculateSummary(p_startDate, p_endDate, "D", "Wed");
	CALL calculateSummary(p_startDate, p_endDate, "D", "Thu");
	CALL calculateSummary(p_startDate, p_endDate, "D", "Fri");
	CALL calculateSummary(p_startDate, p_endDate, "D", "Sat");
	CALL calculateSummary(p_startDate, p_endDate, "D", "Sun");

	CALL calculateSummary(p_startDate, p_endDate, "L", "Mon");
	CALL calculateSummary(p_startDate, p_endDate, "L", "Tue");
	CALL calculateSummary(p_startDate, p_endDate, "L", "Wed");
	CALL calculateSummary(p_startDate, p_endDate, "L", "Thu");
	CALL calculateSummary(p_startDate, p_endDate, "L", "Fri");
	#CALL calculateSummary(p_startDate, p_endDate, "L", "Sat");
	#CALL calculateSummary(p_startDate, p_endDate, "L", "Sun");

	CALL calculateSplitSummary(p_startDate, p_endDate, "Mon");
	CALL calculateSplitSummary(p_startDate, p_endDate, "Tue");
	CALL calculateSplitSummary(p_startDate, p_endDate, "Wed");
	CALL calculateSplitSummary(p_startDate, p_endDate, "Thu");
	CALL calculateSplitSummary(p_startDate, p_endDate, "Fri");
	#CALL calculateSplitSummary(p_startDate, p_endDate, "Sat");
	#CALL calculateSplitSummary(p_startDate, p_endDate, "Sun");

	#CALL calculateSummary(p_startDate, p_endDate, "%", "Mon");
	#CALL calculateSummary(p_startDate, p_endDate, "%", "Tue");
	#CALL calculateSummary(p_startDate, p_endDate, "%", "Wed");
	#CALL calculateSummary(p_startDate, p_endDate, "%", "Thu");
	#CALL calculateSummary(p_startDate, p_endDate, "%", "Fri");
	#CALL calculateSummary(p_startDate, p_endDate, "%", "Sat");
	#CALL calculateSummary(p_startDate, p_endDate, "%", "Sun");

	SELECT * FROM summaries;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS calculateSummary;
DELIMITER //
CREATE PROCEDURE calculateSummary (p_startDate TIMESTAMP, p_endDate TIMESTAMP, p_lunchDinner CHAR(1), p_dayOfWeek CHAR(3))
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

	SELECT COUNT(sid) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_count;
	SELECT AVG(hours)
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgHours;
	SELECT SUM(hours) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totHours;
	SELECT AVG(earnedWage)
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgWage;
	SELECT SUM(earnedWage) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totWage;
	SELECT AVG(earnedTips)
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgTips;
	SELECT SUM(earnedTips) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totTips;
	SELECT AVG(tipout)
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgTipout;
	SELECT SUM(tipout) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totTipout;
	SELECT AVG(sales)
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgSales;
	SELECT SUM(sales) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totSales;
	SELECT AVG(covers)
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgCovers;
	SELECT SUM(covers) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totCovers;
	SELECT AVG(campHours)
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgCampHours;
	SELECT SUM(campHours) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totCampHours;
	SET v_salesPerHour = v_totSales / v_totHours;
	SET v_salesPerCover = v_totSales / v_totCovers;
	SET v_tipsPercent = v_totTips * 100 / v_totSales;
	SET v_tipoutPercent = v_totTipout * 100 / v_totSales;
	SET v_tipsVsWage = v_totTips * 100 / v_totWage;
	SET v_hourlyWage = (v_totWage + v_totTips) / v_totHours;

	INSERT INTO summaries (count, avgHours, totHours, avgWage, totWage, avgTips, totTips, avgTipout, totTipout, avgSales, totSales, avgCovers, totCovers, avgCampHours, totCampHours, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, tipsVsWage, hourlyWage, lunchDinner, dayOfWeek, timedate)
		VALUES (v_count, v_avgHours, v_totHours, v_avgWage, v_totWage, v_avgTips, v_totTips, v_avgTipout, v_totTipout, v_avgSales, v_totSales, v_avgCovers, v_totCovers, v_avgCampHours, v_totCampHours, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_tipsVsWage, v_hourlyWage, p_lunchDinner, p_dayOfWeek, CURRENT_TIMESTAMP);
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS calculateSplitSummary;
DELIMITER //
CREATE PROCEDURE calculateSplitSummary (p_startDate TIMESTAMP, p_endDate TIMESTAMP, p_dayOfWeek CHAR(3))
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
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_count;
	SELECT AVG(hours)
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgHours;
	SELECT SUM(hours) 
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totHours;
	SELECT AVG(earnedWage)
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgWage;
	SELECT SUM(earnedWage) 
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totWage;
	SELECT AVG(earnedTips)
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgTips;
	SELECT SUM(earnedTips) 
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totTips;
	SELECT AVG(tipout)
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgTipout;
	SELECT SUM(tipout) 
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totTipout;
	SELECT AVG(sales)
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgSales;
	SELECT SUM(sales) 
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totSales;
	SELECT AVG(covers)
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgCovers;
	SELECT SUM(covers) 
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totCovers;
	SELECT AVG(campHours)
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgCampHours;
	SELECT SUM(campHours) 
		FROM split 
		WHERE splitDate BETWEEN p_startDate AND p_endDate
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totCampHours;
	SET v_salesPerHour = v_totSales / v_totHours;
	SET v_salesPerCover = v_totSales / v_totCovers;
	SET v_tipsPercent = v_totTips * 100 / v_totSales;
	SET v_tipoutPercent = v_totTipout * 100 / v_totSales;
	SET v_tipsVsWage = v_totTips * 100 / v_totWage;
	SET v_hourlyWage = (v_totWage + v_totTips) / v_totHours;

	INSERT INTO summaries (count, avgHours, totHours, avgWage, totWage, avgTips, totTips, avgTipout, totTipout, avgSales, totSales, avgCovers, totCovers, avgCampHours, totCampHours, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, tipsVsWage, hourlyWage, lunchDinner, dayOfWeek, timedate)
		VALUES (v_count, v_avgHours, v_totHours, v_avgWage, v_totWage, v_avgTips, v_totTips, v_avgTipout, v_totTipout, v_avgSales, v_totSales, v_avgCovers, v_totCovers, v_avgCampHours, v_totCampHours, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_tipsVsWage, v_hourlyWage, 'S', p_dayOfWeek, CURRENT_TIMESTAMP);
END //
DELIMITER ;

/*
############################################################################
	Somehow calculate summaries by combining lunch and dinner shifts on the same day to make split shifts

	SELECT 
		DATE(`startTime`) as `date`, 
	    COUNT(`sid`) as `#shifts`, 
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
		DATE(startTime) AS splitDate,
		COUNT(sid) AS count,

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
		LEFT(DAYNAME(DATE(startTime)), 3) AS dayOfWeek
	FROM shift
	GROUP BY DATE(startTime)
	HAVING COUNT(sid) > 1;
END //
DELIMITER ;


/*
############################################################################
	find which section makes more tips

*/


/*
############################################################################
	Group shifts by week
	SELECT YEARWEEK(`startTime`) as `Week`, SUM(hours) FROM `shift` GROUP BY `Week`

8?


/*
############################################################################
-- find the average earnings per week

-- find the sum of the earnedtot
SELECT SUM(earnedtot)

	FROM shift;

-- find all the weeks with more than 3 shifts

--


*/