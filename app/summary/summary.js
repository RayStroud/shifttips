angular.module('shiftTips')
.controller('SummaryWeeklyController', [ '$http', function($http) {
	var ctrl = this;
	ctrl.sortField = 'startWeek';
	ctrl.sortReverse = false;

	$http.get('./data/summaries.php?weekly')
	.success(function (data, status, headers, config) {
		ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
		ctrl.weeks = data.weeks;
		ctrl.summary = data.summary;
	})
	.error(function (data, status, headers, config) {
		ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
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

.controller('SummaryController', [ '$http', function($http) {
	var ctrl = this;
	ctrl.sortField = '[weekday,-lunchDinner]';
	ctrl.sortReverse = false;

	$http.get('./data/summaries.php')
	.success(function (data, status, headers, config) {
		ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
		ctrl.summaryTotal = data;
	})
	.error(function (data, status, headers, config) {
		ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
		ctrl.error = 'Oops! Something bad happened. Cannot find summary.';
	});

	ctrl.getSummaryByTime = function() {
		$http.get('./data/summaries.php?time')
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaries = data;
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find summary.';
		});
	};
	ctrl.getSummaryByDayOfWeek = function() {
		$http.get('./data/summaries.php?day')
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaries = data;
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
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

	ctrl.getSummaryByTime();
}]);
