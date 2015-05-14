DROP TABLE IF EXISTS shift;
CREATE TABLE shift
(
	sid				INT 			NOT NULL AUTO_INCREMENT,
	wage 			DECIMAL(5,2) 	NULL,
	startTime		TIMESTAMP		NULL,
	endTime			TIMESTAMP		NULL,
	firstTable 		TIMESTAMP		NULL,
	campHours		DECIMAL(5,2)	NULL,
	sales			DECIMAL(7,2)	NULL,
	tipout			INT				NULL,
	transfers		INT 			NULL,
	cash			INT				NULL,
	due 			INT 			NULL,
	covers			INT				NULL,
	cut 			CHAR(1) 		NULL,
	section			VARCHAR(25)		NULL,
	notes 			VARCHAR(250) 	NULL,
	
	hours			DECIMAL(5,2)	NULL,
	earnedWage		INT				NULL,
	earnedTips		INT 			NULL,
	earnedTotal		INT				NULL,
	tipsVsWage		INT				NULL,
	salesPerHour	DECIMAL(6,2)	NULL,
	salesPerCover	DECIMAL(6,2)	NULL,
	tipsPercent		DECIMAL(4,1)	NULL,
	tipoutPercent	DECIMAL(4,1)	NULL,
	earnedHourly	DECIMAL(5,2)	NULL,
	noCampHourly	DECIMAL(5,2)	NULL,
	lunchDinner	 	CHAR(1) 		NULL,
	dayOfWeek		CHAR(3) 		NULL,

	primary key (sid)
);

DROP TABLE IF EXISTS summaries;
CREATE TABLE summaries
(
	id				INT 			NOT NULL AUTO_INCREMENT,

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
	lunchDinner		CHAR(1),
	dayOfWeek		CHAR(3),
	timedate		TIMESTAMP,

	PRIMARY KEY (id)
);