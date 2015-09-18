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

	ctrl.changeSummaryType = function(type) {
		var promise = null;
		switch(type) {
			case 'lunchDinner':
				ctrl.changeSummaryTypeSort('-lunchDinner');
				promise = summaryService.getSummaryByLunchDinner();
				break;
			case 'dayOfWeek':
				ctrl.changeSummaryTypeSort(['weekday','-lunchDinner']);
				promise = summaryService.getSummaryByDayOfWeek();
				break;
			case 'section':
				ctrl.changeSummaryTypeSort(['-lunchDinner','section']);
				promise = summaryService.getSummaryBySection();
				break;
			case 'startTime':
				ctrl.changeSummaryTypeSort(['-lunchDinner','startTime']);
				promise = summaryService.getSummaryByStartTime();
				break;
			case 'cut':
				ctrl.changeSummaryTypeSort(['-lunchDinner','cut']);
				promise = summaryService.getSummaryByCut();
				break;
		}
		promise.success(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaries = data;
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find summary.';
		});
	};
	ctrl.changeSummaryTypeSort = function(typeSort) {
		//if sort field was set to previous typeSort, change it
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

	ctrl.typeSort = '-lunchDinner';
	ctrl.sortField = ctrl.typeSort;
	ctrl.sortReverse = false;
	ctrl.changeSummaryType('lunchDinner');
}])

.controller('SummaryFilterController', function() {
	this.from = '';
	this.to = '';

	this.lunCheck = false;
	this.dinCheck = false;
	this.lunchDinner = '';	//value to filter by lunchDinner

	this.toggleLun = function() {
		if (this.lunCheck) {
			this.lunCheck = false;
			this.lunchDinner = '';
		} else {
			this.lunCheck = true;
			this.dinCheck = false;
			this.lunchDinner = 'L';
		}
	};
	this.toggleDin = function() {
		if (this.dinCheck) {
			this.dinCheck = false;
			this.lunchDinner = '';
		} else {
			this.dinCheck = true;
			this.lunCheck = false;
			this.lunchDinner = 'D';
		}
	};
});
