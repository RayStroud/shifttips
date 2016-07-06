angular.module('shiftTips')
.service('filterService', ['$http', 'backend', 'localStorageService', function($http, backend, localStorageService) {
	var ctrl = this;
	ctrl.dataVersion = '2016-06-28';

	//constants for list sort and summary type
	ctrl.listSortValues = { 
		"date" : ['date','startTime'],
		"dayOfWeek" : ['weekday','date','startTime'],
		"lunchDinner" : ['-lunchDinner','date','startTime']
	};
	ctrl.dueSortValues = { 
		"date" : ['date','startTime'],
		"dueCheck" : ['dueCheck']
	};
	ctrl.getSummaryTypeValues = function(name) {
		switch(name) {
			case "lunchDinner": 
				return {
					"name": "Lunch/Dinner",
					"sort": "-lunchDinner"
				}
				break;
			case "dayOfWeek": 
				return {
					"name": "Day of Week",
					"sort": ["weekday", "-lunchDinner"]
				}
				break;
			case "section": 
				return {
					"name": "Section",
					"sort": ["-lunchDinner", "section"]
				}
				break;
			case "startTime": 
				return {
					"name": "Start Time",
					"sort": ["-lunchDinner", "startTime"]
				}
				break;
			case "cut": 
				return {
					"name": "Cut Order",
					"sort": ["-lunchDinner", "cut"]
				}
				break;
		}
	};
	ctrl.getPeriodTypeValues = function(name) {
		switch(name) {
			case "weekly": 
				return {
					"name": "Weekly",
					"sort": "yearweek"
				};
				break;				
			case "monthly": 
				return {
					"name": "Monthly",
					"sort": ["year", "month"]
				}
				break;
		}
	};

	ctrl.getDefaultFilters = function() {
		return {
			"visible" 		: false	,
			
			"from" 			: ""	,
			"to"			: ""	,

			"lunchDinner" 	: ""	,
			"lun" 			: false	,
			"din" 			: false	,

			"days"			: false	,
			"mon" 			: false	,
			"tue" 			: false	,
			"wed" 			: false	,
			"thu" 			: false	,
			"fri" 			: false	,
			"sat" 			: false	,
			"sun" 			: false	,

			"sort" : {
				"list" 		: ctrl.listSortValues.date 							,
				"summary" 	: ctrl.getSummaryTypeValues('lunchDinner').sort 	,
				"period" 	: ctrl.getPeriodTypeValues('weekly').sort 			,
				"due"		: ctrl.dueSortValues.date
			},
			"reverse" : {
				"list" 		: true	,
				"summary" 	: false	,
				"period" 	: true	,
				"due"		: true
			},
			"summaryType" 	: ctrl.getSummaryTypeValues('lunchDinner')	,
			"periodType" 	: ctrl.getPeriodTypeValues('weekly')	,
			"dueType" 		: "unretrieved"	,
			"gridReverse"	: true	,
			"fontSize"		: 2
		};
	};
	ctrl.getDefaultPrefs = function() {
		return {
			"list" : {
				"lunchDinner"	: true	,
				"dayOfWeek"		: true	,
				"startTime"		: true	,
				"endTime"		: true	,
				"hours"			: true	,
				"earnedWage"	: true	,
				"earnedTips"	: true	,
				"earnedTotal"	: true	,
				"firstTable"	: true	,
				"sales"			: true	,
				"tipout"		: true	,
				"transfers"		: true	,
				"covers"		: true	,
				"campHours"		: true	,
				"salesPerHour"	: true	,
				"salesPerCover"	: true	,
				"tipsPercent"	: true	,
				"tipoutPercent"	: true	,
				"tipsVsWage"	: true	,
				"hourly"		: true	,

				"cash"			: false	,
				"due"			: false	,
				"dueCheck"		: false	,
				"cut"			: false	,
				"section"		: false	,
				"notes"			: false	,
				"noCampHourly"	: false	
			},
			"grid" : {
				"startTime"		: true	,
				"endTime"		: true	,
				"sales"			: true	,
				"earnedTips"	: true	,
				"tipsPercent"	: true	,
				"hourly"		: true	,

				"hours"			: false	,
				"wage"			: false	,
				"earnedWage"	: false	,
				"earnedTotal"	: false	,
				"tipout"		: false	,
				"transfers"		: false	,
				"covers"		: false	,
				"campHours"		: false	,
				"salesPerHour"	: false	,
				"salesPerCover"	: false	,
				"tipoutPercent"	: false	,
				"tipsVsWage"	: false	,
				"cash"			: false	,
				"due"			: false	,
				"dueCheck"		: false	,
				"cut"			: false	,
				"section"		: false	,
				"noCampHourly"	: false	
			},
			"summary" : {
				"hours"			: true	,
				"earnedWage"	: true	,
				"earnedTips"	: true	,
				"earnedTotal"	: true	,
				"sales"			: true	,
				"tipout"		: true	,
				"covers"		: true	,
				"campHours"		: true	,
				"salesPerHour"	: true	,
				"salesPerCover"	: true	,
				"tipsPercent"	: true	,
				"tipoutPercent"	: true	,
				"tipsVsWage"	: true	,
				"hourly"		: true	,

				"transfers"		: false	,
				"noCampHourly"	: false	
			},
			"period" : {
				"shifts"		: true	,
				"hours"			: true	,
				"earnedWage"	: true	,
				"earnedTips"	: true	,
				"earnedTotal"	: true	,
				"sales"			: true	,
				"tipout"		: true	,
				"covers"		: true	,
				"campHours"		: true	,
				"salesPerHour"	: true	,
				"salesPerCover"	: true	,
				"tipsPercent"	: true	,
				"tipoutPercent"	: true	,
				"tipsVsWage"	: true	,
				"hourly"		: true	,

				"transfers"		: false	,
				"noCampHourly"	: false
			},
			"add" : {
				"wage"			: true	,
				"startTime"		: true	,
				"endTime"		: true	,
				"firstTable"	: true	,
				"campHours"		: true	,
				"sales"			: true	,
				"covers"		: true	,
				"tipout"		: true	,
				"transfers"		: true	,
				"cash"			: true	,
				"due"			: true	,
				"section"		: true	,
				"cut"			: true	,
				"notes"			: true		
			},
			"edit" : {
				"wage"			: true	,
				"startTime"		: true	,
				"endTime"		: true	,
				"firstTable"	: true	,
				"campHours"		: true	,
				"sales"			: true	,
				"covers"		: true	,
				"tipout"		: true	,
				"transfers"		: true	,
				"cash"			: true	,
				"due"			: true	,
				"section"		: true	,
				"cut"			: true	,
				"notes"			: true		
			},
			"view" : {
				"startTime"		: true	,
				"endTime"		: true	,
				"hours"			: true	,
				"wage"			: true	,
				"sales"			: true	,
				"covers"		: true	,
				"salesPerHour"	: true	,
				"salesPerCover"	: true	,
				"tipout"		: true	,
				"tipoutPercent"	: true	,
				"transfers"		: true	,
				"cash"			: true	,
				"due"			: true	,
				"earnedWage"	: true	,
				"earnedTips"	: true	,
				"tipsPercent"	: true	,
				"earnedTotal"	: true	,
				"tipsVsWage"	: true	,
				"hourly"		: true	,

				"firstTable"	: true	,
				"campHours"		: true	,
				"section"		: true	,
				"cut"			: true	,
				"notes"			: true	,

				"noCampHourly"	: false
			}
		}
	};
	ctrl.getFullPrefs = function() {
		return {
			"list" : {
				"lunchDinner"	: true	,
				"dayOfWeek"		: true	,
				"startTime"		: true	,
				"endTime"		: true	,
				"hours"			: true	,
				"earnedWage"	: true	,
				"earnedTips"	: true	,
				"earnedTotal"	: true	,
				"firstTable"	: true	,
				"sales"			: true	,
				"tipout"		: true	,
				"transfers"		: true	,
				"covers"		: true	,
				"campHours"		: true	,
				"salesPerHour"	: true	,
				"salesPerCover"	: true	,
				"tipsPercent"	: true	,
				"tipoutPercent"	: true	,
				"tipsVsWage"	: true	,
				"hourly"		: true	,

				"cash"			: true	,
				"due"			: true	,
				"dueCheck"		: true	,
				"cut"			: true	,
				"section"		: true	,
				"notes"			: true	,
				"noCampHourly"	: true	
			},
			"grid" : {
				"startTime"		: true	,
				"endTime"		: true	,
				"sales"			: true	,
				"earnedTips"	: true	,
				"tipsPercent"	: true	,
				"hourly"		: true	,

				"hours"			: true	,
				"wage"			: true	,
				"earnedWage"	: true	,
				"earnedTotal"	: true	,
				"tipout"		: true	,
				"transfers"		: true	,
				"covers"		: true	,
				"campHours"		: true	,
				"salesPerHour"	: true	,
				"salesPerCover"	: true	,
				"tipoutPercent"	: true	,
				"tipsVsWage"	: true	,
				"cash"			: true	,
				"due"			: true	,
				"dueCheck"		: true	,
				"cut"			: true	,
				"section"		: true	,
				"noCampHourly"	: true	
			},
			"summary" : {
				"hours"			: true	,
				"earnedWage"	: true	,
				"earnedTips"	: true	,
				"earnedTotal"	: true	,
				"sales"			: true	,
				"tipout"		: true	,
				"covers"		: true	,
				"campHours"		: true	,
				"salesPerHour"	: true	,
				"salesPerCover"	: true	,
				"tipsPercent"	: true	,
				"tipoutPercent"	: true	,
				"tipsVsWage"	: true	,
				"hourly"		: true	,

				"transfers"		: true	,
				"noCampHourly"	: true	
			},
			"period" : {
				"shifts"		: true	,
				"hours"			: true	,
				"earnedWage"	: true	,
				"earnedTips"	: true	,
				"earnedTotal"	: true	,
				"sales"			: true	,
				"tipout"		: true	,
				"covers"		: true	,
				"campHours"		: true	,
				"salesPerHour"	: true	,
				"salesPerCover"	: true	,
				"tipsPercent"	: true	,
				"tipoutPercent"	: true	,
				"tipsVsWage"	: true	,
				"hourly"		: true	,

				"transfers"		: true	,
				"noCampHourly"	: true
			},
			"add" : {
				"wage"			: true	,
				"startTime"		: true	,
				"endTime"		: true	,
				"firstTable"	: true	,
				"campHours"		: true	,
				"sales"			: true	,
				"covers"		: true	,
				"tipout"		: true	,
				"transfers"		: true	,
				"cash"			: true	,
				"due"			: true	,
				"section"		: true	,
				"cut"			: true	,
				"notes"			: true		
			},
			"edit" : {
				"wage"			: true	,
				"startTime"		: true	,
				"endTime"		: true	,
				"firstTable"	: true	,
				"campHours"		: true	,
				"sales"			: true	,
				"covers"		: true	,
				"tipout"		: true	,
				"transfers"		: true	,
				"cash"			: true	,
				"due"			: true	,
				"section"		: true	,
				"cut"			: true	,
				"notes"			: true		
			},
			"view" : {
				"startTime"		: true	,
				"endTime"		: true	,
				"hours"			: true	,
				"wage"			: true	,
				"sales"			: true	,
				"covers"		: true	,
				"salesPerHour"	: true	,
				"salesPerCover"	: true	,
				"tipout"		: true	,
				"tipoutPercent"	: true	,
				"transfers"		: true	,
				"cash"			: true	,
				"due"			: true	,
				"earnedWage"	: true	,
				"earnedTips"	: true	,
				"tipsPercent"	: true	,
				"earnedTotal"	: true	,
				"tipsVsWage"	: true	,
				"hourly"		: true	,

				"firstTable"	: true	,
				"campHours"		: true	,
				"section"		: true	,
				"cut"			: true	,
				"notes"			: true	,

				"noCampHourly"	: true
			}
		}
	};
	ctrl.getMinimalPrefs = function() {
		return {
			"list" : {
				"lunchDinner"	: true	,
				"dayOfWeek"		: true	,
				"startTime"		: true	,
				"endTime"		: true	,
				"hours"			: true	,
				"earnedWage"	: false	,
				"earnedTips"	: true	,
				"earnedTotal"	: false	,
				"firstTable"	: false	,
				"sales"			: true	,
				"tipout"		: false	,
				"transfers"		: false	,
				"covers"		: false	,
				"campHours"		: false	,
				"salesPerHour"	: false	,
				"salesPerCover"	: false	,
				"tipsPercent"	: true	,
				"tipoutPercent"	: false	,
				"tipsVsWage"	: false	,
				"hourly"		: true	,

				"cash"			: false	,
				"due"			: false	,
				"dueCheck"		: false	,
				"cut"			: false	,
				"section"		: false	,
				"notes"			: false	,
				"noCampHourly"	: false	
			},
			"grid" : {
				"startTime"		: true	,
				"endTime"		: true	,
				"sales"			: true	,
				"earnedTips"	: true	,
				"tipsPercent"	: true	,
				"hourly"		: true	,

				"hours"			: false	,
				"wage"			: false	,
				"earnedWage"	: false	,
				"earnedTotal"	: false	,
				"tipout"		: false	,
				"transfers"		: false	,
				"covers"		: false	,
				"campHours"		: false	,
				"salesPerHour"	: false	,
				"salesPerCover"	: false	,
				"tipoutPercent"	: false	,
				"tipsVsWage"	: false	,
				"cash"			: false	,
				"due"			: false	,
				"dueCheck"		: false	,
				"cut"			: false	,
				"section"		: false	,
				"noCampHourly"	: false	
			},
			"summary" : {
				"hours"			: true	,
				"earnedWage"	: true	,
				"earnedTips"	: true	,
				"earnedTotal"	: true	,
				"sales"			: true	,
				"tipout"		: false	,
				"covers"		: false	,
				"campHours"		: false	,
				"salesPerHour"	: false	,
				"salesPerCover"	: false	,
				"tipsPercent"	: true	,
				"tipoutPercent"	: false	,
				"tipsVsWage"	: false	,
				"hourly"		: true	,

				"transfers"		: false	,
				"noCampHourly"	: false	
			},
			"period" : {
				"shifts"		: true	,
				"hours"			: true	,
				"earnedWage"	: true	,
				"earnedTips"	: true	,
				"earnedTotal"	: true	,
				"sales"			: true	,
				"tipout"		: false	,
				"covers"		: false	,
				"campHours"		: false	,
				"salesPerHour"	: false	,
				"salesPerCover"	: false	,
				"tipsPercent"	: true	,
				"tipoutPercent"	: false	,
				"tipsVsWage"	: false	,
				"hourly"		: true	,

				"transfers"		: false	,
				"noCampHourly"	: false
			},
			"add" : {
				"wage"			: false	,
				"startTime"		: true	,
				"endTime"		: true	,
				"firstTable"	: false	,
				"campHours"		: false	,
				"sales"			: true	,
				"covers"		: false	,
				"tipout"		: false	,
				"transfers"		: false	,
				"cash"			: true	,
				"due"			: true	,
				"section"		: false	,
				"cut"			: false	,
				"notes"			: true		
			},
			"edit" : {
				"wage"			: false	,
				"startTime"		: true	,
				"endTime"		: true	,
				"firstTable"	: false	,
				"campHours"		: false	,
				"sales"			: true	,
				"covers"		: false	,
				"tipout"		: false	,
				"transfers"		: false	,
				"cash"			: true	,
				"due"			: true	,
				"section"		: false	,
				"cut"			: false	,
				"notes"			: true		
			},
			"view" : {
				"startTime"		: true	,
				"endTime"		: true	,
				"hours"			: true	,
				"wage"			: false	,
				"sales"			: true	,
				"covers"		: false	,
				"salesPerHour"	: false	,
				"salesPerCover"	: false	,
				"tipout"		: false	,
				"tipoutPercent"	: false	,
				"transfers"		: false	,
				"cash"			: true	,
				"due"			: true	,
				"earnedWage"	: true	,
				"earnedTips"	: true	,
				"tipsPercent"	: true	,
				"earnedTotal"	: true	,
				"tipsVsWage"	: false	,
				"hourly"		: true	,

				"firstTable"	: false	,
				"campHours"		: false	,
				"section"		: false	,
				"cut"			: false	,
				"notes"			: true	,

				"noCampHourly"	: false
			}
		}
	};

	ctrl.getUserFilters = function(uid) {
		var storedFilters = localStorageService.get('filters');
		return (storedFilters !== undefined && storedFilters.hasOwnProperty(uid) ) ? storedFilters[uid] : ctrl.getDefaultFilters();
	};
	ctrl.getUserPrefs = function(uid) {
		var storedPrefs = localStorageService.get('prefs');
		return (storedPrefs !== undefined && storedPrefs.hasOwnProperty(uid) ) ? storedPrefs[uid] : ctrl.getDefaultPrefs();
	};
	ctrl.getUserWage = function(uid) {
		var storedWages = localStorageService.get('wages');
		return (storedWages !== undefined && storedWages.hasOwnProperty(uid) ) ? storedWages[uid] : {};
	};

	ctrl.updateUserFilters = function(uid, userFilters) {
		var storedFilters = localStorageService.get('filters');
		storedFilters[uid] = userFilters;
		localStorageService.set('filters', storedFilters);
	};
	ctrl.updateUserPrefs = function(uid, userPrefs) {
		var storedPrefs = localStorageService.get('prefs');
		storedPrefs[uid] = userPrefs;
		localStorageService.set('prefs', storedPrefs);
	};
	ctrl.updateUserWage = function(uid, userWage) {
		var storedWages = localStorageService.get('wages');
		storedWages[uid] = userWage;
		localStorageService.set('wages', storedWages);
	};

	ctrl.resetUserFilters = function(uid) {
		ctrl.updateUserFilters(uid, ctrl.getDefaultFilters());
	};
	ctrl.resetUserPrefs = function(uid, type) {
		switch(type) {
			case "full":
				ctrl.updateUserPrefs(uid, ctrl.getFullPrefs());
				break;
			case "minimal":
				ctrl.updateUserPrefs(uid, ctrl.getMinimalPrefs());
				break;
			case "default":
			default:
				ctrl.updateUserPrefs(uid, ctrl.getDefaultPrefs());
				break;
		}
	};
	ctrl.resetUserWage = function(uid) {
		ctrl.updateUserWage(uid, {});
	};

	ctrl.getCustomPrefs = function(uid) {
		return $http.get(backend.domain + 'prefs.php?id=' + uid + '&getPrefs');
	};
	ctrl.saveCustomPrefs = function(uid, prefs) {
		var prefsString = '';
		if(prefs.list.lunchDinner) {prefsString += '&l_lunchDinner';} 
		if(prefs.list.dayOfWeek) {prefsString += '&l_dayOfWeek';} 
		if(prefs.list.startTime) {prefsString += '&l_startTime';} 
		if(prefs.list.endTime) {prefsString += '&l_endTime';} 
		if(prefs.list.hours) {prefsString += '&l_hours';} 
		if(prefs.list.earnedWage) {prefsString += '&l_earnedWage';} 
		if(prefs.list.earnedTips) {prefsString += '&l_earnedTips';} 
		if(prefs.list.earnedTotal) {prefsString += '&l_earnedTotal';} 
		if(prefs.list.firstTable) {prefsString += '&l_firstTable';} 
		if(prefs.list.sales) {prefsString += '&l_sales';} 
		if(prefs.list.tipout) {prefsString += '&l_tipout';} 
		if(prefs.list.transfers) {prefsString += '&l_transfers';} 
		if(prefs.list.covers) {prefsString += '&l_covers';} 
		if(prefs.list.campHours) {prefsString += '&l_campHours';} 
		if(prefs.list.salesPerHour) {prefsString += '&l_salesPerHour';} 
		if(prefs.list.salesPerCover) {prefsString += '&l_salesPerCover';} 
		if(prefs.list.tipsPercent) {prefsString += '&l_tipsPercent';} 
		if(prefs.list.tipoutPercent) {prefsString += '&l_tipoutPercent';} 
		if(prefs.list.tipsVsWage) {prefsString += '&l_tipsVsWage';} 
		if(prefs.list.hourly) {prefsString += '&l_hourly';} 
		if(prefs.list.cash) {prefsString += '&l_cash';} 
		if(prefs.list.due) {prefsString += '&l_due';} 
		if(prefs.list.dueCheck) {prefsString += '&l_dueCheck';} 
		if(prefs.list.cut) {prefsString += '&l_cut';} 
		if(prefs.list.section) {prefsString += '&l_section';} 
		if(prefs.list.notes) {prefsString += '&l_notes';} 
		if(prefs.list.noCampHourly) {prefsString += '&l_noCampHourly';} 
		if(prefs.grid.startTime) {prefsString += '&g_startTime';} 
		if(prefs.grid.endTime) {prefsString += '&g_endTime';} 
		if(prefs.grid.sales) {prefsString += '&g_sales';} 
		if(prefs.grid.earnedTips) {prefsString += '&g_earnedTips';} 
		if(prefs.grid.tipsPercent) {prefsString += '&g_tipsPercent';} 
		if(prefs.grid.hourly) {prefsString += '&g_hourly';} 
		if(prefs.grid.hours) {prefsString += '&g_hours';} 
		if(prefs.grid.wage) {prefsString += '&g_wage';} 
		if(prefs.grid.earnedWage) {prefsString += '&g_earnedWage';} 
		if(prefs.grid.earnedTotal) {prefsString += '&g_earnedTotal';} 
		if(prefs.grid.tipout) {prefsString += '&g_tipout';} 
		if(prefs.grid.transfers) {prefsString += '&g_transfers';} 
		if(prefs.grid.covers) {prefsString += '&g_covers';} 
		if(prefs.grid.campHours) {prefsString += '&g_campHours';} 
		if(prefs.grid.salesPerHour) {prefsString += '&g_salesPerHour';} 
		if(prefs.grid.salesPerCover) {prefsString += '&g_salesPerCover';} 
		if(prefs.grid.tipoutPercent) {prefsString += '&g_tipoutPercent';} 
		if(prefs.grid.tipsVsWage) {prefsString += '&g_tipsVsWage';} 
		if(prefs.grid.cash) {prefsString += '&g_cash';} 
		if(prefs.grid.due) {prefsString += '&g_due';} 
		if(prefs.grid.dueCheck) {prefsString += '&g_dueCheck';} 
		if(prefs.grid.cut) {prefsString += '&g_cut';} 
		if(prefs.grid.section) {prefsString += '&g_section';} 
		if(prefs.grid.noCampHourly) {prefsString += '&g_noCampHourly';} 
		if(prefs.summary.hours) {prefsString += '&s_hours';} 
		if(prefs.summary.earnedWage) {prefsString += '&s_earnedWage';} 
		if(prefs.summary.earnedTips) {prefsString += '&s_earnedTips';} 
		if(prefs.summary.earnedTotal) {prefsString += '&s_earnedTotal';} 
		if(prefs.summary.sales) {prefsString += '&s_sales';} 
		if(prefs.summary.tipout) {prefsString += '&s_tipout';} 
		if(prefs.summary.covers) {prefsString += '&s_covers';} 
		if(prefs.summary.campHours) {prefsString += '&s_campHours';} 
		if(prefs.summary.salesPerHour) {prefsString += '&s_salesPerHour';} 
		if(prefs.summary.salesPerCover) {prefsString += '&s_salesPerCover';} 
		if(prefs.summary.tipsPercent) {prefsString += '&s_tipsPercent';} 
		if(prefs.summary.tipoutPercent) {prefsString += '&s_tipoutPercent';} 
		if(prefs.summary.tipsVsWage) {prefsString += '&s_tipsVsWage';} 
		if(prefs.summary.hourly) {prefsString += '&s_hourly';} 
		if(prefs.summary.transfers) {prefsString += '&s_transfers';} 
		if(prefs.summary.noCampHourly) {prefsString += '&s_noCampHourly';} 
		if(prefs.period.shifts) {prefsString += '&p_shifts';} 
		if(prefs.period.hours) {prefsString += '&p_hours';} 
		if(prefs.period.earnedWage) {prefsString += '&p_earnedWage';} 
		if(prefs.period.earnedTips) {prefsString += '&p_earnedTips';} 
		if(prefs.period.earnedTotal) {prefsString += '&p_earnedTotal';} 
		if(prefs.period.sales) {prefsString += '&p_sales';} 
		if(prefs.period.tipout) {prefsString += '&p_tipout';} 
		if(prefs.period.covers) {prefsString += '&p_covers';} 
		if(prefs.period.campHours) {prefsString += '&p_campHours';} 
		if(prefs.period.salesPerHour) {prefsString += '&p_salesPerHour';} 
		if(prefs.period.salesPerCover) {prefsString += '&p_salesPerCover';} 
		if(prefs.period.tipsPercent) {prefsString += '&p_tipsPercent';} 
		if(prefs.period.tipoutPercent) {prefsString += '&p_tipoutPercent';} 
		if(prefs.period.tipsVsWage) {prefsString += '&p_tipsVsWage';} 
		if(prefs.period.hourly) {prefsString += '&p_hourly';} 
		if(prefs.period.transfers) {prefsString += '&p_transfers';} 
		if(prefs.period.noCampHourly) {prefsString += '&p_noCampHourly';} 
		if(prefs.add.wage) {prefsString += '&a_wage';} 
		if(prefs.add.startTime) {prefsString += '&a_startTime';} 
		if(prefs.add.endTime) {prefsString += '&a_endTime';} 
		if(prefs.add.firstTable) {prefsString += '&a_firstTable';} 
		if(prefs.add.campHours) {prefsString += '&a_campHours';} 
		if(prefs.add.sales) {prefsString += '&a_sales';} 
		if(prefs.add.covers) {prefsString += '&a_covers';} 
		if(prefs.add.tipout) {prefsString += '&a_tipout';} 
		if(prefs.add.transfers) {prefsString += '&a_transfers';} 
		if(prefs.add.cash) {prefsString += '&a_cash';} 
		if(prefs.add.due) {prefsString += '&a_due';} 
		if(prefs.add.section) {prefsString += '&a_section';} 
		if(prefs.add.cut) {prefsString += '&a_cut';} 
		if(prefs.add.notes) {prefsString += '&a_notes';} 
		if(prefs.edit.wage) {prefsString += '&e_wage';} 
		if(prefs.edit.startTime) {prefsString += '&e_startTime';} 
		if(prefs.edit.endTime) {prefsString += '&e_endTime';} 
		if(prefs.edit.firstTable) {prefsString += '&e_firstTable';} 
		if(prefs.edit.campHours) {prefsString += '&e_campHours';} 
		if(prefs.edit.sales) {prefsString += '&e_sales';} 
		if(prefs.edit.covers) {prefsString += '&e_covers';} 
		if(prefs.edit.tipout) {prefsString += '&e_tipout';} 
		if(prefs.edit.transfers) {prefsString += '&e_transfers';} 
		if(prefs.edit.cash) {prefsString += '&e_cash';} 
		if(prefs.edit.due) {prefsString += '&e_due';} 
		if(prefs.edit.section) {prefsString += '&e_section';} 
		if(prefs.edit.cut) {prefsString += '&e_cut';} 
		if(prefs.edit.notes) {prefsString += '&e_notes';} 
		if(prefs.view.startTime) {prefsString += '&v_startTime';} 
		if(prefs.view.endTime) {prefsString += '&v_endTime';} 
		if(prefs.view.hours) {prefsString += '&v_hours';} 
		if(prefs.view.wage) {prefsString += '&v_wage';} 
		if(prefs.view.sales) {prefsString += '&v_sales';} 
		if(prefs.view.covers) {prefsString += '&v_covers';} 
		if(prefs.view.salesPerHour) {prefsString += '&v_salesPerHour';} 
		if(prefs.view.salesPerCover) {prefsString += '&v_salesPerCover';} 
		if(prefs.view.tipout) {prefsString += '&v_tipout';} 
		if(prefs.view.tipoutPercent) {prefsString += '&v_tipoutPercent';} 
		if(prefs.view.transfers) {prefsString += '&v_transfers';} 
		if(prefs.view.cash) {prefsString += '&v_cash';} 
		if(prefs.view.due) {prefsString += '&v_due';} 
		if(prefs.view.earnedWage) {prefsString += '&v_earnedWage';} 
		if(prefs.view.earnedTips) {prefsString += '&v_earnedTips';} 
		if(prefs.view.tipsPercent) {prefsString += '&v_tipsPercent';} 
		if(prefs.view.earnedTotal) {prefsString += '&v_earnedTotal';} 
		if(prefs.view.tipsVsWage) {prefsString += '&v_tipsVsWage';} 
		if(prefs.view.hourly) {prefsString += '&v_hourly';} 
		if(prefs.view.firstTable) {prefsString += '&v_firstTable';} 
		if(prefs.view.campHours) {prefsString += '&v_campHours';} 
		if(prefs.view.section) {prefsString += '&v_section';} 
		if(prefs.view.cut) {prefsString += '&v_cut';} 
		if(prefs.view.notes) {prefsString += '&v_notes';} 
		if(prefs.view.noCampHourly) {prefsString += '&v_noCampHourly';} 
		return $http.get(backend.domain + 'prefs.php?id= ' + uid + '&savePrefs&' + prefsString);
	};
	
	ctrl.checkStoredDataVersion = function() {
		var storedFilters = localStorageService.get('filters');
		var storedPrefs = localStorageService.get('prefs');
		//check if data versions match, if not, reset
		if ( !(storedFilters !== "undefined" && storedFilters.hasOwnProperty("version") && storedFilters.version == ctrl.dataVersion) ) {
			localStorageService.set('filters', {"version":ctrl.dataVersion});
		}
		if ( !(storedPrefs !== "undefined" && storedPrefs.hasOwnProperty("version") && storedPrefs.version == ctrl.dataVersion) ) {
			localStorageService.set('prefs', {"version":ctrl.dataVersion});
		}
	};

	ctrl.checkStoredDataVersion();
}])

.controller('PrefsController', ['filterService', 'userService', function(filterService, userService){
	var ctrl = this;
	ctrl.uid = userService.getUser().uid;
	ctrl.prefs = filterService.getUserPrefs(ctrl.uid);
	ctrl.wage = filterService.getUserWage(ctrl.uid);

	ctrl.setPref = function(page, field, value) {
		ctrl.prefs[page][field] = value;
		filterService.updateUserPrefs(ctrl.uid, ctrl.prefs);
		ctrl.saveMessage = '';
		ctrl.saveError = '';
	};
	ctrl.setWage = function(value) {
		ctrl.wage = value;
		filterService.updateUserWage(ctrl.uid, ctrl.wage);
	};

	ctrl.resetPrefs = function(type) {
		filterService.resetUserPrefs(ctrl.uid, type);
		ctrl.prefs = filterService.getUserPrefs(ctrl.uid);
		ctrl.saveMessage = '';
		ctrl.saveError = '';
	};
	ctrl.resetWage = function() {
		filterService.resetUserWage(ctrl.uid);
		ctrl.wage = filterService.getUseWage(ctrl.uid);
	};

	ctrl.getCustomPrefs = function() {
		filterService.getCustomPrefs(ctrl.uid)
		.success(function (data, status, headers, config) {
			/* DEBUG */ ctrl.getSavedResponse = {result: 'success', data: data, status: status, headers: headers, config: config};
			filterService.updateUserPrefs(ctrl.uid, data);
			ctrl.prefs = filterService.getUserPrefs(ctrl.uid);
		})
		.error(function (data, status, headers, config) {
			/* DEBUG */ ctrl.getSavedResponse = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.getError = 'Oops! Something bad happened. Cannot get saved settings.';
		});
	};
	ctrl.saveCustomPrefs = function() {
		filterService.saveCustomPrefs(ctrl.uid, ctrl.prefs)
		.success(function (data, status, headers, config) {
			/* DEBUG */ ctrl.saveResponse = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.saveMessage = 'View Settings saved.'
		})
		.error(function (data, status, headers, config) {
			/* DEBUG */ ctrl.saveResponse = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.saveError = 'Oops! Something bad happened. Cannot save current settings.';
		});
	};
}])

.controller('FiltersController', ['filterService', 'userService', function(filterService, userService){
	var ctrl = this;
	ctrl.uid = userService.getUser().uid;
	ctrl.filters = filterService.getUserFilters(ctrl.uid);

	//convert dates to JS format
	ctrl.filters.from = ctrl.filters.from ? new Date(ctrl.filters.from) : null;		
	ctrl.filters.to = ctrl.filters.to ? new Date(ctrl.filters.to) : null;

	ctrl.listSortValues = { 
		"date" : ['date','startTime'],
		"dayOfWeek" : ['weekday','date','startTime'],
		"lunchDinner" : ['-lunchDinner','date','startTime']
	};
	ctrl.dueSortValues = { 
		"date" : ['date','startTime'],
		"dueCheck" : 'dueCheck'
	};

	ctrl.resetFilters = function() {
		filterService.resetUserFilters(ctrl.uid);
		ctrl.filters = filterService.getUserFilters(ctrl.uid);
	};

	ctrl.updateFilters = function() {
		filterService.updateUserFilters(ctrl.uid, ctrl.filters);
	};

	ctrl.setFilter = function(name, value) {
		ctrl.filters[name] = value;
		ctrl.updateFilters();
	};

	ctrl.toggleLD = function(ld) {
		ctrl.filters[ld] = !ctrl.filters[ld];	//toggle
		var dl = (ld == "lun") ? "din" : "lun";	//determine opposite
		ctrl.filters[dl] = ctrl.filters[ld] ? false : ctrl.filters[dl];	//if was set to true, set opposite to false, otherwise value remains
		ctrl.filters.lunchDinner = ctrl.filters.lun
			? (ctrl.filters.din ? "" : "L")
			: (ctrl.filters.din ? "D" : "");
		ctrl.updateFilters();
	};

	ctrl.toggleDay = function(day) {
		ctrl.filters[day] = !ctrl.filters[day];
		ctrl.filters.days = (ctrl.filters.mon || ctrl.filters.tue || ctrl.filters.wed || ctrl.filters.thu || ctrl.filters.fri || ctrl.filters.sat || ctrl.filters.sun)	//if any days
			? (ctrl.filters.mon && ctrl.filters.tue && ctrl.filters.wed && ctrl.filters.thu && ctrl.filters.fri && ctrl.filters.sat && ctrl.filters.sun) //if all days
				? false //if all days, then false
				: true	//if only some days, then true
			: false; 	//if no days, then false
		ctrl.updateFilters();
	};

	ctrl.changeSort = function(type, value) {
		// if field is already selected, toggle the sort direction
		if(ctrl.filters.sort[type] == value) {
			ctrl.filters.reverse[type] = !ctrl.filters.reverse[type];
		} else {
			ctrl.filters.sort[type] = value;
			ctrl.filters.reverse[type] = false;
		}
		ctrl.updateFilters();
	};
	ctrl.isSort = function(type, value) {
		//return ctrl.filters.sort[type] == value;
		return JSON.stringify(ctrl.filters.sort[type]) == JSON.stringify(value);
	};

	ctrl.changeSummaryType = function(type) {
		//check to see if the sort is by summary type
		var isSortBySummary = ctrl.isSort('summary', ctrl.filters.summaryType.sort);

		//change summary type
		ctrl.filters.summaryType = filterService.getSummaryTypeValues(type);

		//adjust sort.summary if necessary
		if(isSortBySummary) {
			ctrl.filters.sort.summary = ctrl.filters.summaryType.sort;
		}

		//update filters
		ctrl.updateFilters();
	};

	ctrl.changePeriodType = function(type) {
		//check to see if the sort is by period
		var isSortByPeriod = ctrl.isSort('period', ctrl.filters.periodType.sort);

		//change period type
		ctrl.filters.periodType = filterService.getPeriodTypeValues(type);

		//adjust sort.period if necessary
		if(isSortByPeriod) {
			ctrl.filters.sort.period = ctrl.filters.periodType.sort;
		}

		//update filters
		ctrl.updateFilters();
	};

	ctrl.switchFontSize = function() {
		ctrl.filters.fontSize = (ctrl.filters.fontSize + 1 ) % 4;
		ctrl.updateFilters();
	};
}]);