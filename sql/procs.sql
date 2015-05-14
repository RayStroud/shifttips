DROP PROCEDURE IF EXISTS calculateSummaries;

DELIMITER //
CREATE PROCEDURE calculateSummaries (p_startDate TIMESTAMP, p_endDate TIMESTAMP)
BEGIN
	TRUNCATE TABLE summaries;

	CALL calculateSummary(p_startDate, p_endDate, "L", "%");
	CALL calculateSummary(p_startDate, p_endDate, "D", "%");
	CALL calculateSummary(p_startDate, p_endDate, "%", "%");

	CALL calculateSummary(p_startDate, p_endDate, "L", "Mon");
	CALL calculateSummary(p_startDate, p_endDate, "L", "Tue");
	CALL calculateSummary(p_startDate, p_endDate, "L", "Wed");
	CALL calculateSummary(p_startDate, p_endDate, "L", "Thu");
	CALL calculateSummary(p_startDate, p_endDate, "L", "Fri");
	CALL calculateSummary(p_startDate, p_endDate, "L", "Sat");
	CALL calculateSummary(p_startDate, p_endDate, "L", "Sun");

	CALL calculateSummary(p_startDate, p_endDate, "D", "Mon");
	CALL calculateSummary(p_startDate, p_endDate, "D", "Tue");
	CALL calculateSummary(p_startDate, p_endDate, "D", "Wed");
	CALL calculateSummary(p_startDate, p_endDate, "D", "Thu");
	CALL calculateSummary(p_startDate, p_endDate, "D", "Fri");
	CALL calculateSummary(p_startDate, p_endDate, "D", "Sat");
	CALL calculateSummary(p_startDate, p_endDate, "D", "Sun");

	CALL calculateSummary(p_startDate, p_endDate, "%", "Mon");
	CALL calculateSummary(p_startDate, p_endDate, "%", "Tue");
	CALL calculateSummary(p_startDate, p_endDate, "%", "Wed");
	CALL calculateSummary(p_startDate, p_endDate, "%", "Thu");
	CALL calculateSummary(p_startDate, p_endDate, "%", "Fri");
	CALL calculateSummary(p_startDate, p_endDate, "%", "Sat");
	CALL calculateSummary(p_startDate, p_endDate, "%", "Sun");
END //
DELIMITER ;


DROP PROCEDURE IF EXISTS calculateSummary;

DELIMITER //
CREATE PROCEDURE calculateSummary (p_startDate TIMESTAMP, p_endDate TIMESTAMP, p_lunchDinner CHAR(1), p_dayOfWeek CHAR(3))
BEGIN
	DECLARE v_avgHours 			DECIMAL(5,2);
	DECLARE v_totalHours 		DECIMAL(7,2);
	DECLARE v_avgWage 			DECIMAL(5,2);
	DECLARE v_totalWage 		DECIMAL(7,2);
	DECLARE v_avgTips 			INT;
	DECLARE v_totalTips 		INT;
	DECLARE v_avgTipout 		INT;
	DECLARE v_totalTipout 		INT;
	DECLARE v_avgSales 			INT;
	DECLARE v_totalSales 		INT;
	DECLARE v_avgCovers 		INT;
	DECLARE v_totalCovers 		INT;
	DECLARE v_avgCampHours 		DECIMAL(4,2);
	DECLARE v_totalCampHours 	DECIMAL(5,2);
	DECLARE v_salesPerHour 		INT;
	DECLARE v_salesPerCover 	INT;
	DECLARE v_tipsPercent 		DECIMAL(4,1);
	DECLARE v_tipoutPercent 	DECIMAL(4,1);
	DECLARE v_tipsVsWage 		INT;
	DECLARE v_hourlyWage 		DECIMAL(4,2);

	SELECT SUM(hours) / COUNT(sid) 
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
		INTO v_totalHours;
	SELECT SUM(hours * wage) / COUNT(sid) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_avgWage;
	SELECT SUM(hours * wage) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND UPPER(lunchDinner) LIKE UPPER(p_lunchDinner)
			AND UPPER(dayOfWeek) LIKE UPPER(p_dayOfWeek)
		INTO v_totalWage;
	SELECT SUM(earnedTips) / COUNT(sid) 
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
		INTO v_totalTips;
	SELECT SUM(tipout) / COUNT(sid) 
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
		INTO v_totalTipout;
	SELECT SUM(sales) / COUNT(sid) 
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
		INTO v_totalSales;
	SELECT SUM(covers) / COUNT(sid) 
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
		INTO v_totalCovers;
	SELECT SUM(campHours) / COUNT(sid)
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
		INTO v_totalCampHours;
	SET v_salesPerHour = v_totalSales / v_totalHours;
	SET v_salesPerCover = v_totalSales / v_totalCovers;
	SET v_tipsPercent = v_totalTips * 100 / v_totalSales;
	SET v_tipoutPercent = v_totalTipout * 100 / v_totalSales;
	SET v_tipsVsWage = v_totalTips * 100 / v_totalWage;
	SET v_hourlyWage = (v_totalWage + v_totalTips) / v_totalHours;

	INSERT INTO summaries (avgHours, totalHours, avgWage, totalWage, avgTips, totalTips, avgTipout, totalTipout, avgSales, totalSales, avgCovers, totalCovers, avgCampHours, totalCampHours, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, tipsVsWage, hourlyWage, lunchDinner, dayOfWeek, timedate)
		VALUES (v_avgHours, v_totalHours, v_avgWage, v_totalWage, v_avgTips, v_totalTips, v_avgTipout, v_totalTipout, v_avgSales, v_totalSales, v_avgCovers, v_totalCovers, v_avgCampHours, v_totalCampHours, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_tipsVsWage, v_hourlyWage, p_lunchDinner, p_dayOfWeek, CURRENT_TIMESTAMP);

	SELECT * FROM summaries;

END //
DELIMITER ;

/*
	Somehow calculate summaries by combining lunch and dinner shifts on the same day to make split shifts

	maybe do this by making a new table called "splits" where every row from "shift" gets analysed, and shifts on the same date get combined into one row, with "S" as the lunchDinner letter

*/



/*
############################################################################
-- find the average earnings per week

-- find the sum of the earnedTotal
SELECT SUM(earnedTotal)

	FROM shift;

-- find the count of how many weeks
*/