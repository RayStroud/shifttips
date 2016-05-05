DROP TABLE IF EXISTS month;
DROP TABLE IF EXISTS week;
DROP TABLE IF EXISTS summary;
DROP TABLE IF EXISTS shift;
DROP TABLE IF EXISTS device;
DROP TABLE IF EXISTS user;

CREATE TABLE user
(
	name			VARCHAR(35)		NOT NULL,
	email			VARCHAR(254)	NOT NULL,

	-- UNIQUE(email),

	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (id)	
);

CREATE TABLE shift
(
	wage 			DECIMAL(5,2),
	date			DATE 			NOT NULL,
	startTime		TIME,
	endTime			TIME,
	firstTable 		TIME,
	campHours		DECIMAL(5,2),
	sales			DECIMAL(7,2),
	tipout			INT,
	transfers		INT,
	cash			INT,
	due 			INT,
	dueCheck		CHAR(1),
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

	user_id	INT NOT NULL,
	INDEX shift_user_ix (user_id),
	FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,

	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (id)
);

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
	avgTransfers 	DECIMAL(5,2),
	totTransfers 	INT,
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