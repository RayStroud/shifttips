DROP TABLE IF EXISTS shift;
CREATE TABLE shift
(
	wage 			DECIMAL(5,2),
	date			DATE,
	startTime		TIME,
	endTime			TIME,
	firstTable 		TIME,
	campHours		DECIMAL(5,2),
	sales			DECIMAL(7,2),
	tipout			INT,
	transfers		INT,
	cash			INT,
	due 			INT,
	covers			INT,
	cut 			CHAR(1),
	section			VARCHAR(25),
	notes 			VARCHAR(1000),
	
	hours			DECIMAL(5,2),
	earnedWage		INT,
	earnedTips		INT,
	earnedTotal		INT,
	tipsVsWage		INT,
	salesPerHour	DECIMAL(6,2),
	salesPerCover	DECIMAL(6,2),
	tipsPercent		DECIMAL(4,1),
	tipoutPercent	DECIMAL(4,1),
	hourly			DECIMAL(5,2),
	noCampHourly	DECIMAL(5,2),
	lunchDinner		CHAR(1),
	dayOfWeek		CHAR(3),

	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (id)
);

DROP TABLE IF EXISTS summary;
CREATE TABLE summary
(
	count	 		INT,
	avgHours 		DECIMAL(5,2),
	totHours 		DECIMAL(7,2),
	avgWage 		DECIMAL(5,2),
	totWage 		DECIMAL(7,2),
	avgTips 		DECIMAL(5,2),
	totTips 		INT,
	avgTipout 		DECIMAL(5,2),
	totTipout 		INT,
	avgSales 		DECIMAL(7,2),
	totSales 		DECIMAL(10,2),
	avgCovers 		DECIMAL(5,2),
	totCovers 		INT,
	avgCampHours 	DECIMAL(4,2),
	totCampHours 	DECIMAL(8,2),
	salesPerHour 	DECIMAL(7,2),
	salesPerCover 	DECIMAL(7,2),
	tipsPercent 	DECIMAL(4,1),
	tipoutPercent 	DECIMAL(4,1),
	tipsVsWage 		INT,
	hourly 		DECIMAL(4,2),
	lunchDinner		CHAR(1),
	dayOfWeek		CHAR(3),
	timestamp		TIMESTAMP,

	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (id)
);

DROP TABLE IF EXISTS split;
CREATE TABLE split
(
	splitDate		DATE,
	count			INT,

	campHours		DECIMAL(5,2),
	sales			DECIMAL(7,2),
	tipout			INT,
	transfers		INT,
	covers			INT,
	cut 			VARCHAR(10),
	section			VARCHAR(50),
	notes 			VARCHAR(500),
	
	hours			DECIMAL(5,2),
	earnedWage		INT,
	earnedTips		INT,
	earnedTotal		INT,
	tipsVsWage		INT,
	salesPerHour	DECIMAL(6,2),
	salesPerCover	DECIMAL(6,2),
	tipsPercent		DECIMAL(4,1),
	tipoutPercent	DECIMAL(4,1),
	hourly			DECIMAL(5,2),
	dayOfWeek		CHAR(3),

	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (id)
);

DROP TABLE IF EXISTS week;
CREATE TABLE week
(
	yearweek		CHAR(6),
	startWeek		DATE,
	endWeek			DATE,
	shifts			INT,

	campHours		DECIMAL(5,2),
	sales			DECIMAL(7,2),
	tipout			INT,
	transfers		INT,
	covers			INT,
	
	hours			DECIMAL(5,2),
	earnedWage		INT,
	earnedTips		INT,
	earnedTotal		INT,
	tipsVsWage		INT,
	salesPerHour	DECIMAL(6,2),
	salesPerCover	DECIMAL(6,2),
	tipsPercent		DECIMAL(4,1),
	tipoutPercent	DECIMAL(4,1),
	hourly			DECIMAL(5,2),

	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (id)
);

DROP TABLE IF EXISTS month;
CREATE TABLE month
(
	year			CHAR(4),
	month			CHAR(2),
	monthname		CHAR(3),
	shifts			INT,

	campHours		DECIMAL(5,2),
	sales			DECIMAL(7,2),
	tipout			INT,
	transfers		INT,
	covers			INT,
	
	hours			DECIMAL(5,2),
	earnedWage		INT,
	earnedTips		INT,
	earnedTotal		INT,
	tipsVsWage		INT,
	salesPerHour	DECIMAL(6,2),
	salesPerCover	DECIMAL(6,2),
	tipsPercent		DECIMAL(4,1),
	tipoutPercent	DECIMAL(4,1),
	hourly			DECIMAL(5,2),

	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (id)
);