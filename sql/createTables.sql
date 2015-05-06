drop table shift;
create table shift
(
	sid			int 			not null auto_increment,
	wage 		decimal(5,2) 	null,
	startTime	timestamp		null,
	endTime		timestamp		null,
	firstTable 	timestamp		null,
	campHours	decimal(5,2)	null,
	sales		decimal(7,2)	null,
	tipout		int				null,
	transfers	int 			null,
	cash		int				null,
	due 		int 			null,
	covers		int				null,
	cut 		char(1) 		null,
	section		varchar(25)		null,
	notes 		varchar(250) 	null,
	
	hours			decimal(5,2)	null,
	earnedWage		int				null,
	earnedTips		int 			null,
	earnedTotal		int				null,
	tipsVsWage		int				null,
	salesPerHour	decimal(6,2)	null,
	salesPerCover	decimal(6,2)	null,
	tipsPercent		decimal(4,1)	null,
	tipoutPercent	decimal(4,1)	null,
	earnedHourly	decimal(5,2)	null,
	noCampHourly	decimal(5,2)	null,
	lunchDinner	 	char(1) 		null,
	dayOfWeek		char(3) 		null,

	primary key (sid)
);