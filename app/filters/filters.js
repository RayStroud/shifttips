angular.module('shiftTips')
.service('filterService', ['localStorageService', function(localStorageService) {
	var ctrl = this;

	//constants for list sort and summary type
	ctrl.listSortValues = { 
		"date" : ['date','startTime'],
		"dayOfWeek" : ['weekday','date','startTime'],
		"lunchDinner" : ['-lunchDinner','date','startTime']
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
				"list" 		: ctrl.listSortValues.date 	,
				"summary" 	: ctrl.getSummaryTypeValues('lunchDinner').sort 	,
				"period" 	: ctrl.getPeriodTypeValues('weekly').sort
			},
			"reverse" : {
				"list" 		: true	,
				"summary" 	: false	,
				"period" 	: false
			},
			"summaryType" 	: ctrl.getSummaryTypeValues('lunchDinner')	,
			"periodType" 	: ctrl.getPeriodTypeValues('weekly')	,
			"gridReverse"	: true
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
		return (ctrl.filters !== undefined && ctrl.filters.hasOwnProperty(uid) ) ? ctrl.filters[uid] : ctrl.getDefaultFilters();
	};
	ctrl.getUserPrefs = function(uid) {
		return (ctrl.prefs !== undefined && ctrl.prefs.hasOwnProperty(uid) ) ? ctrl.prefs[uid] : ctrl.getDefaultPrefs();
	};
	ctrl.getUserWage = function(uid) {
		return (ctrl.wages !== undefined && ctrl.wages.hasOwnProperty(uid) ) ? ctrl.wages[uid] : null;
	};

	ctrl.updateUserFilters = function(uid, userFilters) {
		ctrl.filters[uid] = userFilters;
		localStorageService.set('filters', ctrl.filters);
	};
	ctrl.updateUserPrefs = function(uid, userPrefs) {
		ctrl.prefs[uid] = userPrefs;
		localStorageService.set('prefs', ctrl.prefs);
	};
	ctrl.updateUserWage = function(uid, userWage) {
		ctrl.wages[uid] = userWage;
		localStorageService.set('wages', ctrl.wages);
	};

	ctrl.resetUserFilters = function(uid) {
		ctrl.filters[uid] = ctrl.getDefaultFilters();
		localStorageService.set('filters', ctrl.filters);
	};
	ctrl.resetUserPrefs = function(uid, type) {
		switch(type) {
			case "full":
				ctrl.prefs[uid] = ctrl.getFullPrefs();
				break;
			case "minimal":
				ctrl.prefs[uid] = ctrl.getMinimalPrefs();
				break;
			case "default":
			default:
				ctrl.prefs[uid] = ctrl.getDefaultPrefs();
				break;
		}
		localStorageService.set('prefs', ctrl.prefs);
	};
	ctrl.resetUserWage = function(uid) {
		ctrl.wages[uid] = null;
		localStorageService.set('wages', ctrl.wages);
	};

	ctrl.clearAllData = function() {
		ctrl.filters = null;
		ctrl.prefs = null;
		ctrl.wages = null;
		localStorageService.set('filters', null);
		localStorageService.set('prefs', null);
		localStorageService.set('wages', null);
	};

	//retrieve filters, prefs, wages from local storage
	//* DEBUG */ ctrl.clearAllData();
	ctrl.filters = localStorageService.get('filters') || ctrl.getDefaultFilters();
	ctrl.prefs = localStorageService.get('prefs') || ctrl.getDefaultPrefs();
	ctrl.wages = localStorageService.get('wages') || {};
}])

.controller('PrefsController', ['filterService', 'userService', function(filterService, userService){
	var ctrl = this;
	ctrl.uid = userService.getUser().uid;
	ctrl.prefs = filterService.getUserPrefs(ctrl.uid);
	ctrl.wage = filterService.getUserWage(ctrl.uid);

	ctrl.setPref = function(page, field, value) {
		ctrl.prefs[page][field] = value;
		filterService.updateUserPrefs(ctrl.uid, ctrl.prefs);
	};
	ctrl.setWage = function(value) {
		ctrl.wage = value;
		filterService.updateUserWage(ctrl.uid, ctrl.wage);
	};

	ctrl.resetPrefs = function(type) {
		filterService.resetUserPrefs(ctrl.uid, type);
		ctrl.prefs = filterService.getUserPrefs(ctrl.uid);
	};
	ctrl.resetWage = function() {
		filterService.resetUserWage(ctrl.uid);
		ctrl.wage = filterService.getUseWage(ctrl.uid);
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
}]);