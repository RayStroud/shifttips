angular.module('shiftTips')
.service('filterService', ['localStorageService', function(localStorageService) {
	var ctrl = this;

	//constants for list sort and summary type
	ctrl.listSortValues = { 
		"date" : ['date','startTime'],
		"dayOfWeek" : ['weekday','date','startTime'],
		"lunchDinner" : ['-lunchDinner','date','startTime']
	};
	ctrl.summaryTypeValues = {
		"lunchDinner": {
			"name": "Lunch/Dinner",
			"sort": "-lunchDinner"
		},
		"dayOfWeek": {
			"name": "Day of Week",
			"sort": ["weekday", "-lunchDinner"]
		},
		"section": {
			"name": "Section",
			"sort": ["-lunchDinner", "section"]
		},
		"startTime": {
			"name": "Start Time",
			"sort": ["-lunchDinner", "startTime"]
		},
		"cut": {
			"name": "Cut Order",
			"sort": ["-lunchDinner", "cut"]
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

			"listSort"		: ctrl.listSortValues.date	,
			"listReverse"	: true	,
			"gridReverse"	: true	,
			"summaryType" 	: ctrl.summaryTypeValues.lunchDinner	,
			"periodType" 	: null
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
				"count"			: true	,
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
				"wageValue"		: null	,
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
			}
		}
	};

	ctrl.getFilters = function() {
		return ctrl.filters;
	};
	ctrl.getPrefs = function() {
		return ctrl.prefs;
	};

	ctrl.updateFilters = function(filters) {
		ctrl.filters = filters;
		localStorageService.set('filters', filters);
	};
	ctrl.updatePrefs = function(prefs) {
		ctrl.prefs = prefs;
		localStorageService.set('prefs', prefs);		
	};

	ctrl.clearFilters = function() {
		ctrl.filters = ctrl.getDefaultFilters();
		localStorageService.set('filters', ctrl.filters);
	};

	ctrl.filters = localStorageService.get('filters') || ctrl.getDefaultFilters();
	ctrl.filters.from = ctrl.filters.from ? new Date(ctrl.filters.from) : null;
	ctrl.filters.to = ctrl.filters.to ? new Date(ctrl.filters.to) : null;
	ctrl.prefs = localStorageService.get('prefs') || ctrl.getDefaultPrefs();
}])

.controller('PrefsController', ['filterService', function(filterService){
	var ctrl = this;
	ctrl.prefs = filterService.getPrefs();

	ctrl.setPref = function(page, field, value) {
		ctrl.prefs[page][field] = value;
		filterService.updatePrefs(ctrl.prefs);
	};
}])

.controller('FiltersController', ['filterService', function(filterService){
	var ctrl = this;
	ctrl.prefs = filterService.getPrefs();
	ctrl.filters = filterService.getFilters();
	ctrl.listSortValues = { 
		"date" : ['date','startTime'],
		"dayOfWeek" : ['weekday','date','startTime'],
		"lunchDinner" : ['-lunchDinner','date','startTime']
	};

	ctrl.clear = function() {
		filterService.clearFilters();
		ctrl.filters = filterService.getFilters();
	};

	ctrl.updateFilters = function() {
		filterService.updateFilters(ctrl.filters);
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

	ctrl.changeListSort = function(name) {
		// if field is already selected, toggle the sort direction
		if(ctrl.filters.listSort == name) {
			ctrl.filters.listReverse = !ctrl.filters.listReverse;
		} else {
			ctrl.filters.listSort = name;
			ctrl.filters.listReverse = false;
		}
		ctrl.updateFilters();
	};
	ctrl.isListSort = function(name) {
		//return ctrl.filters.listSort == name;
		return JSON.stringify(ctrl.filters.listSort) == JSON.stringify(name);
	};
}]);