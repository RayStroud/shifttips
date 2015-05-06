-- calculate summaries
-- pass startDate, endDate, lunchDinner
-- return avgHours, totalHours, avgWage, totalWage, avgTips, totalTips, avgTipout, totalTipout, avgSales, totalSales, avgCovers, totalCovers, avgCampHours, totalCampHours, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, tipsVsWage, hourlyWage

DROP PROCEDURE IF EXISTS calculateSummaries;

DELIMITER //
CREATE PROCEDURE calculateSummaries (p_startDate TIMESTAMP, p_endDate TIMESTAMP, p_lunchDinner CHAR(1))
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
	DECLARE v_lunchDinnerLetter	CHAR(1);

	DROP TABLE IF EXISTS summaries;
	CREATE TABLE summaries
	(
		id				int 			not null auto_increment,

		avgHours 		DECIMAL(5,2),
		totalHours 		DECIMAL(7,2),
		avgWage 		DECIMAL(5,2),
		totalWage 		DECIMAL(7,2),
		avgTips 		INT,
		totalTips 		INT,
		avgTipout 		INT,
		totalTipout 	INT,
		avgSales 		INT,
		totalSales 		INT,
		avgCovers 		INT,
		totalCovers 	INT,
		avgCampHours 	DECIMAL(4,2),
		totalCampHours 	DECIMAL(5,2),
		salesPerHour 	INT,
		salesPerCover 	INT,
		tipsPercent 	DECIMAL(4,1),
		tipoutPercent 	DECIMAL(4,1),
		tipsVsWage 		INT,
		hourlyWage 		DECIMAL(4,2),

		primary key (id)
	);

	CASE p_lunchDinner
		WHEN 'L' THEN
			SET v_lunchDinnerLetter = 'D';
		WHEN 'D' THEN
			SET v_lunchDinnerLetter = 'L';
		ELSE
			SET v_lunchDinnerLetter = 'B';
		END CASE;


	SELECT SUM(hours) / COUNT(sid) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_avgHours;
	SELECT SUM(hours) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_totalHours;
	SELECT SUM(hours * wage) / COUNT(sid) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_avgWage;
	SELECT SUM(hours * wage) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_totalWage;
	SELECT SUM(earnedTips) / COUNT(sid) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_avgTips;
	SELECT SUM(earnedTips) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_totalTips;
	SELECT SUM(tipout) / COUNT(sid) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_avgTipout;
	SELECT SUM(tipout) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_totalTipout;
	SELECT SUM(sales) / COUNT(sid) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_avgSales;
	SELECT SUM(sales) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_totalSales;
	SELECT SUM(covers) / COUNT(sid) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_avgCovers;
	SELECT SUM(covers) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_totalCovers;
	SELECT SUM(campHours) / COUNT(sid)
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_avgCampHours;
	SELECT SUM(campHours) 
		FROM shift 
		WHERE startTime BETWEEN p_startDate AND p_endDate
			AND lunchDinner != v_lunchDinnerLetter
		INTO v_totalCampHours;
	SET v_salesPerHour = v_totalSales / v_totalHours;
	SET v_salesPerCover = v_totalSales / v_totalCovers;
	SET v_tipsPercent = v_totalTips * 100 / v_totalSales;
	SET v_tipoutPercent = v_totalTipout * 100 / v_totalSales;
	SET v_tipsVsWage = v_totalTips * 100 / v_totalWage;
	SET v_hourlyWage = (v_totalWage + v_totalTips) / v_totalHours;

	INSERT INTO summaries (avgHours, totalHours, avgWage, totalWage, avgTips, totalTips, avgTipout, totalTipout, avgSales, totalSales, avgCovers, totalCovers, avgCampHours, totalCampHours, salesPerHour, salesPerCover, tipsPercent, tipoutPercent, tipsVsWage, hourlyWage)
		VALUES (v_avgHours, v_totalHours, v_avgWage, v_totalWage, v_avgTips, v_totalTips, v_avgTipout, v_totalTipout, v_avgSales, v_totalSales, v_avgCovers, v_totalCovers, v_avgCampHours, v_totalCampHours, v_salesPerHour, v_salesPerCover, v_tipsPercent, v_tipoutPercent, v_tipsVsWage, v_hourlyWage);

	SELECT * FROM summaries;

END //
DELIMITER ;



############################################################################
-- find the average earnings per week

-- find the sum of the earnedTotal
SELECT SUM(earnedTotal)

	FROM shift

-- find the count of how many weeks