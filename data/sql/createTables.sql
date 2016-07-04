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
	l_lunchDinner 	BIT		,
	l_dayOfWeek 	BIT		,
	l_startTime 	BIT		,
	l_endTime 		BIT		,
	l_hours 		BIT		,
	l_earnedWage 	BIT		,
	l_earnedTips 	BIT		,
	l_earnedTotal 	BIT		,
	l_firstTable 	BIT		,
	l_sales 		BIT		,
	l_tipout 		BIT		,
	l_transfers 	BIT		,
	l_covers 		BIT		,
	l_campHours 	BIT		,
	l_salesPerHour 	BIT		,
	l_salesPerCover BIT		,
	l_tipsPercent 	BIT		,
	l_tipoutPercent BIT		,
	l_tipsVsWage 	BIT		,
	l_hourly 		BIT		,

	l_cash 			BIT		,
	l_due 			BIT		,
	l_dueCheck 		BIT		,
	l_cut 			BIT		,
	l_section 		BIT		,
	l_notes 		BIT		,
	l_noCampHourly 	BIT		,
	-- -----------------------
	-- grid" 
	-- -----------------------
	g_startTime 	BIT		,
	g_endTime 		BIT		,
	g_sales 		BIT		,
	g_earnedTips 	BIT		,
	g_tipsPercent 	BIT		,
	g_hourly 		BIT		,

	g_hours 		BIT		,
	g_wage 			BIT		,
	g_earnedWage 	BIT		,
	g_earnedTotal 	BIT		,
	g_tipout 		BIT		,
	g_transfers 	BIT		,
	g_covers 		BIT		,
	g_campHours 	BIT		,
	g_salesPerHour 	BIT		,
	g_salesPerCover BIT		,
	g_tipoutPercent BIT		,
	g_tipsVsWage 	BIT		,
	g_cash 			BIT		,
	g_due 			BIT		,
	g_dueCheck 		BIT		,
	g_cut 			BIT		,
	g_section 		BIT		,
	g_noCampHourly 	BIT		,
	-- -----------------------
	-- summary 
	-- -----------------------
	s_hours 		BIT		,
	s_earnedWage 	BIT		,
	s_earnedTips 	BIT		,
	s_earnedTotal 	BIT		,
	s_sales 		BIT		,
	s_tipout 		BIT		,
	s_covers 		BIT		,
	s_campHours 	BIT		,
	s_salesPerHour 	BIT		,
	s_salesPerCover BIT		,
	s_tipsPercent 	BIT		,
	s_tipoutPercent BIT		,
	s_tipsVsWage 	BIT		,
	s_hourly 		BIT		,

	s_transfers 	BIT		,
	s_noCampHourly 	BIT		,
	-- -----------------------
	-- period 
	-- -----------------------
	p_shifts 		BIT		,
	p_hours 		BIT		,
	p_earnedWage 	BIT		,
	p_earnedTips 	BIT		,
	p_earnedTotal 	BIT		,
	p_sales 		BIT		,
	p_tipout 		BIT		,
	p_covers 		BIT		,
	p_campHours 	BIT		,
	p_salesPerHour 	BIT		,
	p_salesPerCover BIT		,
	p_tipsPercent 	BIT		,
	p_tipoutPercent BIT		,
	p_tipsVsWage 	BIT		,
	p_hourly 		BIT		,

	p_transfers 	BIT		,
	p_noCampHourly 	BIT		,
	-- -----------------------
	-- add
	-- -----------------------
	a_wage 			BIT		,
	a_startTime 	BIT		,
	a_endTime 		BIT		,
	a_firstTable 	BIT		,
	a_campHours 	BIT		,
	a_sales 		BIT		,
	a_covers 		BIT		,
	a_tipout 		BIT		,
	a_transfers 	BIT		,
	a_cash 			BIT		,
	a_due 			BIT		,
	a_section 		BIT		,
	a_cut 			BIT		,
	a_notes 		BIT		,
	-- -----------------------
	-- edit
	-- -----------------------
	e_wage 			BIT		,
	e_startTime 	BIT		,
	e_endTime 		BIT		,
	e_firstTable 	BIT		,
	e_campHours 	BIT		,
	e_sales 		BIT		,
	e_covers 		BIT		,
	e_tipout 		BIT		,
	e_transfers 	BIT		,
	e_cash 			BIT		,
	e_due 			BIT		,
	e_section 		BIT		,
	e_cut 			BIT		,
	e_notes 		BIT		,
	-- -----------------------
	-- view
	-- -----------------------
	v_startTime 	BIT		,
	v_endTime 		BIT		,
	v_hours 		BIT		,
	v_wage 			BIT		,
	v_sales 		BIT		,
	v_covers 		BIT		,
	v_salesPerHour 	BIT		,
	v_salesPerCover BIT		,
	v_tipout 		BIT		,
	v_tipoutPercent BIT		,
	v_transfers 	BIT		,
	v_cash 			BIT		,
	v_due 			BIT		,
	v_earnedWage 	BIT		,
	v_earnedTips 	BIT		,
	v_tipsPercent 	BIT		,
	v_earnedTotal 	BIT		,
	v_tipsVsWage 	BIT		,
	v_hourly 		BIT		,

	v_firstTable 	BIT		,
	v_campHours 	BIT		,
	v_section 		BIT		,
	v_cut 			BIT		,
	v_notes 		BIT		,

	v_noCampHourly 	BIT		,

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