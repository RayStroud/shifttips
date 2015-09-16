angular.module('shiftTips')
.service('summaryService', ['$http', function($http) {
	this.getSummary = function() {
		return $http.get('./data/summary.php');
	};
	this.getSummaryWeekly = function() {
		return $http.get('./data/summary.php?week');
	};
	this.getSummaryMonthly = function() {
		return $http.get('./data/summary.php?month');
	};
	this.getSummaryByTime = function(id) {
		return $http.get('./data/summary.php?time');
	};
	this.getSummaryByDayOfWeek = function(id) {
		return $http.get('./data/summary.php?day');
	};
}])

.controller('SummaryWeeklyController', [ 'summaryService', function(summaryService) {
	var ctrl = this;
	ctrl.sortField = 'startWeek';
	ctrl.sortReverse = false;

	summaryService.getSummaryWeekly()
	.success(function (data, status, headers, config) {
		//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
		ctrl.weeks = data.weeks;
		ctrl.summary = data.summary;
	})
	.error(function (data, status, headers, config) {
		//* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
		ctrl.error = 'Oops! Something bad happened. Cannot find summary.';
	});

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
}])

.controller('SummaryController', [ 'summaryService', function(summaryService) {
	var ctrl = this;

	summaryService.getSummary()
	.success(function (data, status, headers, config) {
		//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
		ctrl.summaryTotal = data;
	})
	.error(function (data, status, headers, config) {
		//* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
		ctrl.error = 'Oops! Something bad happened. Cannot find summary.';
	});

	ctrl.getSummaryByTime = function() {
		summaryService.getSummaryByTime()
		.success(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaries = data;
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find summary.';
		});
	};
	ctrl.getSummaryByDayOfWeek = function() {
		summaryService.getSummaryByDayOfWeek()
		.success(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaries = data;
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find summary.';
		});
	};
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

	ctrl.sortField = '[weekday,-lunchDinner]';
	ctrl.sortReverse = false;
	ctrl.getSummaryByTime();
}]);
