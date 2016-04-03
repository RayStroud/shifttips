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

	ctrl.defaultFilters = function() {
		return {
			"visible" 		: false	,
			
			"from" 			: ""	,
			"to"			: ""	,

			"lunCheck"		: false	,
			"dinCheck"		: false	,
			"lunchDinner" 	: ""	,

			"monCheck"		: false	,
			"tueCheck"		: false	,
			"wedCheck"		: false	,
			"thuCheck"		: false	,
			"friCheck"		: false	,
			"satCheck"		: false	,
			"sunCheck"		: false	,
			"aDays"			: [null,null,null,null,null,null,null] ,

			"listSort"		: ctrl.listSortDate	,
			"listReverse"	: true	,
			"gridReverse"	: true	,
			"summaryType" 	: ctrl.summaryTypeValues.lunchDinner	,
			"periodType" 	: null
		};
	};
	ctrl.defaultPrefs = function() {
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

	ctrl.filters = localStorageService.get('filters') || ctrl.defaultFilters();
	ctrl.prefs = localStorageService.get('prefs') || ctrl.defaultPrefs();
}]);