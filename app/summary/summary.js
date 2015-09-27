angular.module('shiftTips')
.service('summaryService', ['$http', function($http) {
	this.getSummary = function(from, to) {
		return $http.get('./data/summary.php?from=' + from + '&to=' + to);
	};
	this.getSummaryByLunchDinner = function(from, to) {
		return $http.get('./data/summary.php?lunchDinner&from=' + from + '&to=' + to);
	};
	this.getSummaryByDayOfWeek = function(from, to) {
		return $http.get('./data/summary.php?day&from=' + from + '&to=' + to);
	};
	this.getSummaryBySection = function(from, to) {
		return $http.get('./data/summary.php?section&from=' + from + '&to=' + to);
	};
	this.getSummaryByStartTime = function(from, to) {
		return $http.get('./data/summary.php?startTime&from=' + from + '&to=' + to);
	};
	this.getSummaryByCut = function(from, to) {
		return $http.get('./data/summary.php?cut&from=' + from + '&to=' + to);
	};
	this.getSummaryWeekly = function(from, to, lunchDinner) {
		return $http.get('./data/summary.php?week&from=' + from + '&to=' + to + '&ld=' + lunchDinner);
	};
	this.getSummaryMonthly = function(from, to, lunchDinner) {
		return $http.get('./data/summary.php?month&from=' + from + '&to=' + to + '&ld=' + lunchDinner);
	};
}])

.controller('SummaryPeriodController', [ 'summaryService', function(summaryService) {
	var ctrl = this;

	ctrl.changePeriodType = function(type, from, to, lunchDinner) {
		ctrl.type = type;
		var p_dateFrom = moment(from).format('YYYY-MM-DD') || null;
		var p_dateTo = moment(to).format('YYYY-MM-DD') || null;
		var promise = null;
		switch(type) {
			case 'weekly':
				ctrl.changePeriodTypeSort('yearweek');
				promise = summaryService.getSummaryWeekly(p_dateFrom, p_dateTo, lunchDinner);
				break;
			case 'monthly':
				ctrl.changePeriodTypeSort(['year','month']);
				promise = summaryService.getSummaryMonthly(p_dateFrom, p_dateTo, lunchDinner);
				break;
		}
		promise.success(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.list = data.list;
			ctrl.summary = data.summary;
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find summary.';
		});
	};
	ctrl.changePeriodTypeSort = function(typeSort) {
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

	ctrl.typeSort = 'yearweek';
	ctrl.sortField = ctrl.typeSort;
	ctrl.sortReverse = false;
	ctrl.changePeriodType('weekly', null, null, null);

	//###################################################
	// OLD STUFF
	//###################################################
	// ctrl.sortField = 'startWeek';
	// ctrl.sortReverse = false;

	// ctrl.getSummaryWeekly = function(from, to, lunchDinner) {
	// 	var p_dateFrom = moment(from).format('YYYY-MM-DD') || null;
	// 	var p_dateTo = moment(to).format('YYYY-MM-DD') || null;
	// 	summaryService.getSummaryWeekly(p_dateFrom, p_dateTo, lunchDinner)
	// 	.success(function (data, status, headers, config) {
	// 		/* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
	// 		ctrl.weeks = data.weeks;
	// 		ctrl.summary = data.summary;
	// 	})
	// 	.error(function (data, status, headers, config) {
	// 		/* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
	// 		ctrl.error = 'Oops! Something bad happened. Cannot find summary.';
	// 	});
	// }
	// ctrl.changeSortField = function(field) {
	// 	// if field is already selected, toggle the sort direction
	// 	if(ctrl.sortField == field) {
	// 		ctrl.sortReverse = !ctrl.sortReverse;
	// 	} else {
	// 		ctrl.sortField = field;
	// 		ctrl.sortReverse = false;
	// 	}
	// };
	// ctrl.isSortField = function(field) {
	// 	return ctrl.sortField == field;
	// };
	// ctrl.getSummaryWeekly(null, null, null);
}])

.controller('SummaryController', [ 'summaryService', function(summaryService) {
	var ctrl = this;

	ctrl.changeSummaryType = function(type, from, to) {
		ctrl.type = type;
		var p_dateFrom = moment(from).format('YYYY-MM-DD') || null;
		var p_dateTo = moment(to).format('YYYY-MM-DD') || null;
		var promise = null;
		switch(type) {
			case 'lunchDinner':
				ctrl.changeSummaryTypeSort('-lunchDinner');
				promise = summaryService.getSummaryByLunchDinner(p_dateFrom, p_dateTo);
				break;
			case 'dayOfWeek':
				ctrl.changeSummaryTypeSort(['weekday','-lunchDinner']);
				promise = summaryService.getSummaryByDayOfWeek(p_dateFrom, p_dateTo);
				break;
			case 'section':
				ctrl.changeSummaryTypeSort(['-lunchDinner','section']);
				promise = summaryService.getSummaryBySection(p_dateFrom, p_dateTo);
				break;
			case 'startTime':
				ctrl.changeSummaryTypeSort(['-lunchDinner','startTime']);
				promise = summaryService.getSummaryByStartTime(p_dateFrom, p_dateTo);
				break;
			case 'cut':
				ctrl.changeSummaryTypeSort(['-lunchDinner','cut']);
				promise = summaryService.getSummaryByCut(p_dateFrom, p_dateTo);
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
		summaryService.getSummary(p_dateFrom, p_dateTo)
		.success(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaryTotal = data;
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
	ctrl.changeSummaryType('lunchDinner', null, null);
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
})
;