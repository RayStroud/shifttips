angular.module('shiftTips')
.service('summaryService', ['$http', 'userService', function($http, userService) {
	this.getSummary = function(uid, from, to, lunchDinner) {
		return $http.get('./data/summary.php?uid=' + uid + '&from=' + from + '&to=' + to + '&ld=' + lunchDinner);
	};
	this.getSummaryFiltered = function(uid, from, to, lunchDinner, mon, tue, wed, thu, fri, sat, sun) {
		var dayString = mon ? '&mon' : '';
		dayString += tue ? '&tue' : '';
		dayString += wed ? '&wed' : '';
		dayString += thu ? '&thu' : '';
		dayString += fri ? '&fri' : '';
		dayString += sat ? '&sat' : '';
		dayString += sun ? '&sun' : '';
		return $http.get('./data/summary.php?uid=' + uid + '&from=' + from + '&to=' + to + '&ld=' + lunchDinner + dayString);
	};
	this.getSummaryByLunchDinner = function(uid, from, to) {
		return $http.get('./data/summary.php?uid=' + uid + '&lunchDinner&from=' + from + '&to=' + to);
	};
	this.getSummaryByDayOfWeek = function(uid, from, to) {
		return $http.get('./data/summary.php?uid=' + uid + '&day&from=' + from + '&to=' + to);
	};
	this.getSummaryBySection = function(uid, from, to) {
		return $http.get('./data/summary.php?uid=' + uid + '&section&from=' + from + '&to=' + to);
	};
	this.getSummaryByStartTime = function(uid, from, to) {
		return $http.get('./data/summary.php?uid=' + uid + '&startTime&from=' + from + '&to=' + to);
	};
	this.getSummaryByCut = function(uid, from, to) {
		return $http.get('./data/summary.php?uid=' + uid + '&cut&from=' + from + '&to=' + to);
	};
	this.getSummaryWeekly = function(uid, from, to, lunchDinner) {
		return $http.get('./data/summary.php?uid=' + uid + '&week&from=' + from + '&to=' + to + '&ld=' + lunchDinner);
	};
	this.getSummaryMonthly = function(uid, from, to, lunchDinner) {
		return $http.get('./data/summary.php?uid=' + uid + '&month&from=' + from + '&to=' + to + '&ld=' + lunchDinner);
	};
}])

.controller('SummaryController', [ 'summaryService', 'userService', function(summaryService, userService) {
	var ctrl = this;

	ctrl.changeSummaryType = function(type, from, to, lunchDinner) {
		ctrl.type = type;
		var p_dateFrom = moment(from).format('YYYY-MM-DD') || null;
		var p_dateTo = moment(to).format('YYYY-MM-DD') || null;
		var promise = null;
		switch(type) {
			case 'lunchDinner':
				ctrl.typeName = 'Lunch/Dinner';
				ctrl.changeSummaryTypeSort('-lunchDinner');
				promise = summaryService.getSummaryByLunchDinner(userService.getUser().uid, p_dateFrom, p_dateTo);
				break;
			case 'dayOfWeek':
				ctrl.typeName = 'Day of Week';
				ctrl.changeSummaryTypeSort(['weekday','-lunchDinner']);
				promise = summaryService.getSummaryByDayOfWeek(userService.getUser().uid, p_dateFrom, p_dateTo);
				break;
			case 'section':
				ctrl.typeName = 'Section';
				ctrl.changeSummaryTypeSort(['-lunchDinner','section']);
				promise = summaryService.getSummaryBySection(userService.getUser().uid, p_dateFrom, p_dateTo);
				break;
			case 'startTime':
				ctrl.typeName = 'Start Time';
				ctrl.changeSummaryTypeSort(['-lunchDinner','startTime']);
				promise = summaryService.getSummaryByStartTime(userService.getUser().uid, p_dateFrom, p_dateTo);
				break;
			case 'cut':
				ctrl.typeName = 'Cut Order';
				ctrl.changeSummaryTypeSort(['-lunchDinner','cut']);
				promise = summaryService.getSummaryByCut(userService.getUser().uid, p_dateFrom, p_dateTo);
				break;
		}
		promise.success(function (data, status, headers, config) {
			//* DEBUG */ ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaries = data;
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot get summary.';
		});
		summaryService.getSummary(userService.getUser().uid, p_dateFrom, p_dateTo, lunchDinner)
		.success(function (data, status, headers, config) {
			//* DEBUG */ ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaryTotal = data;
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot get summary.';
		});
	};
	ctrl.changeSummaryTypeSort = function(typeSort) {
		//if sortField was set to previous typeSort, change it
		if(ctrl.sortField == ctrl.typeSort) {
			ctrl.sortField = typeSort;
		}
		ctrl.typeSort = typeSort;
	}
	ctrl.changeSortField = function(field) {
		// if field is already selected, toggle the sort direction
		if(ctrl.sortField == field) {
			ctrl.sortReverse = !ctrl.sortReverse;
		} else {
			ctrl.sortField = field;
			ctrl.sortReverse = false;
		}
	};
	ctrl.isSortField = function(field) {
		return ctrl.sortField == field;
	};
	ctrl.isSummaryType = function(type) {
		return ctrl.type == type;
	};

	ctrl.sortReverse = false;
	ctrl.changeSummaryType('lunchDinner', null, null, null);
}])

.controller('SummaryPeriodController', [ 'summaryService', 'userService', 'filterService', function(summaryService, userService, filterService) {
	var ctrl = this;
	ctrl.uid = userService.getUser().uid;
	ctrl.prefs = filterService.getUserPrefs(ctrl.uid).period;

	ctrl.updatePeriodData = function() {
		var filters = filterService.getUserFilters(ctrl.uid);
		var p_dateFrom = moment(filters.from).isValid() ? moment(filters.from).format('YYYY-MM-DD') : null;
		var p_dateTo = moment(filters.to).isValid() ? moment(filters.to).format('YYYY-MM-DD') : null;

		var promise = null;
		switch(filters.periodType.name) {
			case 'Weekly':
				promise = summaryService.getSummaryWeekly(ctrl.uid, p_dateFrom, p_dateTo, filters.lunchDinner);
				break;
			case 'Monthly':
				promise = summaryService.getSummaryMonthly(ctrl.uid, p_dateFrom, p_dateTo, filters.lunchDinner);
				break;
		}
		promise.success(function (data, status, headers, config) {
			//* DEBUG */ ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.list = data.list;
			ctrl.summary = data.summary;
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot get summary.';
		});

	};
	ctrl.updatePeriodData();
}]);