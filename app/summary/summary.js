angular.module('shiftTips')
.service('summaryService', ['$http', function($http) {
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

.controller('SummaryController', [ 'summaryService', function(summaryService) {
	var ctrl = this;
	/* DEBUG */ var uid = 1;

	ctrl.changeSummaryType = function(type, from, to, lunchDinner) {
		ctrl.type = type;
		var p_dateFrom = moment(from).format('YYYY-MM-DD') || null;
		var p_dateTo = moment(to).format('YYYY-MM-DD') || null;
		var promise = null;
		switch(type) {
			case 'lunchDinner':
				ctrl.typeName = 'Lunch/Dinner';
				ctrl.changeSummaryTypeSort('-lunchDinner');
				promise = summaryService.getSummaryByLunchDinner(uid, p_dateFrom, p_dateTo);
				break;
			case 'dayOfWeek':
				ctrl.typeName = 'Day of Week';
				ctrl.changeSummaryTypeSort(['weekday','-lunchDinner']);
				promise = summaryService.getSummaryByDayOfWeek(uid, p_dateFrom, p_dateTo);
				break;
			case 'section':
				ctrl.typeName = 'Section';
				ctrl.changeSummaryTypeSort(['-lunchDinner','section']);
				promise = summaryService.getSummaryBySection(uid, p_dateFrom, p_dateTo);
				break;
			case 'startTime':
				ctrl.typeName = 'Start Time';
				ctrl.changeSummaryTypeSort(['-lunchDinner','startTime']);
				promise = summaryService.getSummaryByStartTime(uid, p_dateFrom, p_dateTo);
				break;
			case 'cut':
				ctrl.typeName = 'Cut Order';
				ctrl.changeSummaryTypeSort(['-lunchDinner','cut']);
				promise = summaryService.getSummaryByCut(uid, p_dateFrom, p_dateTo);
				break;
		}
		promise.success(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaries = data;
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot get summary.';
		});
		summaryService.getSummary(uid, p_dateFrom, p_dateTo, lunchDinner)
		.success(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaryTotal = data;
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
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

.controller('SummaryPeriodController', [ 'summaryService', function(summaryService) {
	var ctrl = this;
	/* DEBUG */ var uid = 1;

	ctrl.changePeriodType = function(type, from, to, lunchDinner) {
		ctrl.type = type;
		var p_dateFrom = moment(from).format('YYYY-MM-DD') || null;
		var p_dateTo = moment(to).format('YYYY-MM-DD') || null;
		var promise = null;
		switch(type) {
			case 'weekly':
				ctrl.typeName = 'Weekly';
				ctrl.changePeriodTypeSort('yearweek');
				promise = summaryService.getSummaryWeekly(uid, p_dateFrom, p_dateTo, lunchDinner);
				break;
			case 'monthly':
				ctrl.typeName = 'Monthly';
				ctrl.changePeriodTypeSort(['year','month']);
				promise = summaryService.getSummaryMonthly(uid, p_dateFrom, p_dateTo, lunchDinner);
				break;
		}
		promise.success(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.list = data.list;
			ctrl.summary = data.summary;
		})
		.error(function (data, status, headers, config) {
			//* DEBUG */ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot get summary.';
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
}])

.controller('SummaryFilterController', function() {
	this.visible = false;
	
	this.from = '';
	this.to = '';

	this.lunCheck = false;
	this.dinCheck = false;
	this.lunchDinner = '';	//value to filter by lunchDinner

	this.show = function() {this.visible = true;};
	this.hide = function() {this.visible = false;};

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