angular.module('shiftTips')
.service('summaryService', ['$http', function($http) {
	this.getSummary = function() {
		return $http.get('./data/summary.php');
	};
	this.getSummaryByLunchDinner = function(id) {
		return $http.get('./data/summary.php?lunchDinner');
	};
	this.getSummaryByDayOfWeek = function(id) {
		return $http.get('./data/summary.php?day');
	};
	this.getSummaryBySection = function(id) {
		return $http.get('./data/summary.php?section');
	};
	this.getSummaryByStartTime = function(id) {
		return $http.get('./data/summary.php?startTime');
	};
	this.getSummaryByCut = function(id) {
		return $http.get('./data/summary.php?cut');
	};
	this.getSummaryWeekly = function() {
		return $http.get('./data/summary.php?week');
	};
	this.getSummaryMonthly = function() {
		return $http.get('./data/summary.php?month');
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

	ctrl.getSummaryByLunchDinner = function() {
		summaryService.getSummaryByLunchDinner()
		.success(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaries = data;
			ctrl.changeSummaryType('-lunchDinner');
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
			ctrl.changeSummaryType('[-lunchDinner, dayOfWeek]');
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find summary.';
		});
	};
	ctrl.getSummaryBySection = function() {
		summaryService.getSummaryBySection()
		.success(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaries = data;
			ctrl.changeSummaryType('[-lunchDinner, section]');
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find summary.';
		});
	};
	ctrl.getSummaryByStartTime = function() {
		summaryService.getSummaryByStartTime()
		.success(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaries = data;
			ctrl.changeSummaryType('[-lunchDinner, startTime]');
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find summary.';
		});
	};
	ctrl.getSummaryByCut = function() {
		summaryService.getSummaryByCut()
		.success(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaries = data;
			ctrl.changeSummaryType('[-lunchDinner, cut]');
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find summary.';
		});
	};
	ctrl.changeSummaryType = function(type) {
		//if sort field was set to previous type, change it
		if(ctrl.sortField == ctrl.type) {
			ctrl.sortField = type;
		}
		ctrl.type = type;
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

	ctrl.type = '-lunchDinner';
	ctrl.sortField = ctrl.type;
	ctrl.sortReverse = false;
	ctrl.getSummaryByLunchDinner();
}]);
