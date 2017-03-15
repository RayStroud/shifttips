angular.module('shiftTips')
.service('summaryService', ['$http', 'backend', 'userService', function($http, backend, userService) {
	this.getSummary = function(uid, from, to, lunchDinner) {
		return $http.get(backend.domain + 'summary.php?uid=' + uid + '&from=' + from + '&to=' + to + '&ld=' + lunchDinner);
	};
	this.getSummaryFiltered = function(uid, from, to, lunchDinner, mon, tue, wed, thu, fri, sat, sun) {
		var dayString = mon ? '&mon' : '';
		dayString += tue ? '&tue' : '';
		dayString += wed ? '&wed' : '';
		dayString += thu ? '&thu' : '';
		dayString += fri ? '&fri' : '';
		dayString += sat ? '&sat' : '';
		dayString += sun ? '&sun' : '';
		return $http.get(backend.domain + 'summary.php?uid=' + uid + '&from=' + from + '&to=' + to + '&ld=' + lunchDinner + dayString);
	};
	this.getSummaryByLunchDinner = function(uid, from, to) {
		return $http.get(backend.domain + 'summary.php?uid=' + uid + '&lunchDinner&from=' + from + '&to=' + to);
	};
	this.getSummaryByDayOfWeek = function(uid, from, to) {
		return $http.get(backend.domain + 'summary.php?uid=' + uid + '&day&from=' + from + '&to=' + to);
	};
	this.getSummaryBySection = function(uid, from, to) {
		return $http.get(backend.domain + 'summary.php?uid=' + uid + '&section&from=' + from + '&to=' + to);
	};
	this.getSummaryByStartTime = function(uid, from, to) {
		return $http.get(backend.domain + 'summary.php?uid=' + uid + '&startTime&from=' + from + '&to=' + to);
	};
	this.getSummaryByCut = function(uid, from, to) {
		return $http.get(backend.domain + 'summary.php?uid=' + uid + '&cut&from=' + from + '&to=' + to);
	};
	this.getSummaryByHalfhours = function(uid, from, to) {
		return $http.get(backend.domain + 'summary.php?uid=' + uid + '&halfhours&from=' + from + '&to=' + to);
	};
	this.getSummaryByLocation = function(uid, from, to) {
		return $http.get(backend.domain + 'summary.php?uid=' + uid + '&location&from=' + from + '&to=' + to);
	};
	this.getSummaryWeekly = function(uid, from, to, lunchDinner) {
		return $http.get(backend.domain + 'summary.php?uid=' + uid + '&week&from=' + from + '&to=' + to + '&ld=' + lunchDinner);
	};
	this.getSummaryMonthly = function(uid, from, to, lunchDinner) {
		return $http.get(backend.domain + 'summary.php?uid=' + uid + '&month&from=' + from + '&to=' + to + '&ld=' + lunchDinner);
	};
}])

.controller('SummaryController', [ 'summaryService', 'userService', 'filterService', function(summaryService, userService, filterService) {
	var ctrl = this;
	ctrl.uid = userService.getUser().uid;
	ctrl.prefs = filterService.getUserPrefs(ctrl.uid).summary;

	ctrl.updateSummaryData = function() {
		var filters = filterService.getUserFilters(ctrl.uid);
		var p_dateFrom = moment(filters.from).isValid() ? moment(filters.from).format('YYYY-MM-DD') : null;
		var p_dateTo = moment(filters.to).isValid() ? moment(filters.to).format('YYYY-MM-DD') : null;
		var promise = null;
		switch(filters.summaryType.name) {
			case 'Lunch/Dinner':
				promise = summaryService.getSummaryByLunchDinner(ctrl.uid, p_dateFrom, p_dateTo);
				break;
			case 'Day of Week':
				promise = summaryService.getSummaryByDayOfWeek(ctrl.uid, p_dateFrom, p_dateTo);
				break;
			case 'Section':
				promise = summaryService.getSummaryBySection(ctrl.uid, p_dateFrom, p_dateTo);
				break;
			case 'Start Time':
				promise = summaryService.getSummaryByStartTime(ctrl.uid, p_dateFrom, p_dateTo);
				break;
			case 'Cut Order':
				promise = summaryService.getSummaryByCut(ctrl.uid, p_dateFrom, p_dateTo);
				break;
			case 'Shift Length':
				promise = summaryService.getSummaryByHalfhours(ctrl.uid, p_dateFrom, p_dateTo);
				break;
			case 'Location':
				promise = summaryService.getSummaryByLocation(ctrl.uid, p_dateFrom, p_dateTo);
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
		summaryService.getSummary(ctrl.uid, p_dateFrom, p_dateTo, filters.lunchDinner)
		.success(function (data, status, headers, config) {
			//* DEBUG */ ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaryTotal = data;
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot get summary.';
		});
	};
	ctrl.updateSummaryData();
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