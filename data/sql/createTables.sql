DROP TABLE IF EXISTS month;
DROP TABLE IF EXISTS week;
DROP TABLE IF EXISTS summary;
DROP TABLE IF EXISTS shift;
DROP TABLE IF EXISTS prefs;
DROP TABLE IF EXISTS user;

CREATE TABLE user
(
	name			VARCHAR(35)		NOT NULL,
	email			VARCHAR(254)	NOT NULL,

	created			TIMESTAMP,
	active			TIMESTAMP,

	-- UNIQUE(email),

	id INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (id)	
);

CREATE TABLE prefs
(
	-- -----------------------
	-- list
	-- -----------------------
	list_lunchDinner 	BIT		,
	list_dayOfWeek 		BIT		,
	list_startTime 		BIT		,
	list_endTime 		BIT		,
	list_hours 			BIT		,
	list_earnedWage 	BIT		,
	list_earnedTips 	BIT		,
	list_earnedTotal 	BIT		,
	list_firstTable 	BIT		,
	list_sales 			BIT		,
	list_tipout 		BIT		,
	list_transfers 		BIT		,
	list_covers 		BIT		,
	list_campHours 		BIT		,
	list_salesPerHour 	BIT		,
	list_salesPerCover 	BIT		,
	list_tipsPercent 	BIT		,
	list_tipoutPercent 	BIT		,
	list_tipsVsWage 	BIT		,
	list_hourly 		BIT		,

	list_cash 			BIT		,
	list_due 			BIT		,
	list_dueCheck 		BIT		,
	list_cut 			BIT		,
	list_section 		BIT		,
	list_notes 			BIT		,
	list_noCampHourly 	BIT		,
	-- -----------------------
	-- grid" 
	-- -----------------------
	grid_startTime 		BIT		,
	grid_endTime 		BIT		,
	grid_sales 			BIT		,
	grid_earnedTips 	BIT		,
	grid_tipsPercent 	BIT		,
	grid_hourly 		BIT		,

	grid_hours 			BIT		,
	grid_wage 			BIT		,
	grid_earnedWage 	BIT		,
	grid_earnedTotal 	BIT		,
	grid_tipout 		BIT		,
	grid_transfers 		BIT		,
	grid_covers 		BIT		,
	grid_campHours 		BIT		,
	grid_salesPerHour 	BIT		,
	grid_salesPerCover 	BIT		,
	grid_tipoutPercent 	BIT		,
	grid_tipsVsWage 	BIT		,
	grid_cash 			BIT		,
	grid_due 			BIT		,
	grid_dueCheck 		BIT		,
	grid_cut 			BIT		,
	grid_section 		BIT		,
	grid_noCampHourly 	BIT		,
	-- -----------------------
	-- summary 
	-- -----------------------
	summ_hours 			BIT		,
	summ_earnedWage 	BIT		,
	summ_earnedTips 	BIT		,
	summ_earnedTotal 	BIT		,
	summ_sales 			BIT		,
	summ_tipout 		BIT		,
	summ_covers 		BIT		,
	summ_campHours 		BIT		,
	summ_salesPerHour 	BIT		,
	summ_salesPerCover 	BIT		,
	summ_tipsPercent 	BIT		,
	summ_tipoutPercent 	BIT		,
	summ_tipsVsWage 	BIT		,
	summ_hourly 		BIT		,

	summ_transfers 		BIT		,
	summ_noCampHourly 	BIT		,
	-- -----------------------
	-- period 
	-- -----------------------
	prod_shifts 		BIT		,
	prod_hours 			BIT		,
	prod_earnedWage 	BIT		,
	prod_earnedTips 	BIT		,
	prod_earnedTotal 	BIT		,
	prod_sales 			BIT		,
	prod_tipout 		BIT		,
	prod_covers 		BIT		,
	prod_campHours 		BIT		,
	prod_salesPerHour 	BIT		,
	prod_salesPerCover 	BIT		,
	prod_tipsPercent 	BIT		,
	prod_tipoutPercent 	BIT		,
	prod_tipsVsWage 	BIT		,
	prod_hourly 		BIT		,

	prod_transfers 		BIT		,
	prod_noCampHourly 	BIT		,
	-- -----------------------
	-- add
	-- -----------------------
	adds_wage 			BIT		,
	adds_startTime 		BIT		,
	adds_endTime 		BIT		,
	adds_firstTable 	BIT		,
	adds_campHours 		BIT		,
	adds_sales 			BIT		,
	adds_covers 		BIT		,
	adds_tipout 		BIT		,
	adds_transfers 		BIT		,
	adds_cash 			BIT		,
	adds_due 			BIT		,
	adds_section 		BIT		,
	adds_cut 			BIT		,
	adds_notes 			BIT		,
	-- -----------------------
	-- edit
	-- -----------------------
	edit_wage 			BIT		,
	edit_startTime 		BIT		,
	edit_endTime 		BIT		,
	edit_firstTable 	BIT		,
	edit_campHours 		BIT		,
	edit_sales 			BIT		,
	edit_covers 		BIT		,
	edit_tipout 		BIT		,
	edit_transfers 		BIT		,
	edit_cash 			BIT		,
	edit_due 			BIT		,
	edit_section 		BIT		,
	edit_cut 			BIT		,
	edit_notes 			BIT		,
	-- -----------------------
	-- view
	-- -----------------------
	view_startTime 		BIT		,
	view_endTime 		BIT		,
	view_hours 			BIT		,
	view_wage 			BIT		,
	view_sales 			BIT		,
	view_covers 		BIT		,
	view_salesPerHour 	BIT		,
	view_salesPerCover 	BIT		,
	view_tipout 		BIT		,
	view_tipoutPercent 	BIT		,
	view_transfers 		BIT		,
	view_cash 			BIT		,
	view_due 			BIT		,
	view_earnedWage 	BIT		,
	view_earnedTips 	BIT		,
	view_tipsPercent 	BIT		,
	view_earnedTotal 	BIT		,
	view_tipsVsWage 	BIT		,
	view_hourly 		BIT		,

	view_firstTable 	BIT		,
	view_campHours 		BIT		,
	view_section 		BIT		,
	view_cut 			BIT		,
	view_notes 			BIT		,

	view_noCampHourly 	BIT		,

	id INT NOT NULL,
	PRIMARY KEY (id) REFERENCES user(id)
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

	created			TIMESTAMP,
	updated			TIMESTAMP,

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