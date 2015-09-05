(function() {
	var app = angular.module('shiftTips', ['ngRoute']);

	app.config(function ($routeProvider) {
		$routeProvider.when('/shifts', {
			templateUrl: 'pages/shift-grid.html',
			controller: 'ShiftGridController',
			controllerAs: 'shiftGridCtrl'
		})
		// .when('/weekly/grid', {
		// 	templateUrl: 'pages/weekly-grid.html',
		// 	controller: 'ShiftListWeeklyController',
		// 	controllerAs: 'shiftListWeeklyCtrl'
		// })
		.when('/shift/:id', {
			templateUrl: 'pages/shift-view.html',
			controller: 'ShiftViewController',
			controllerAs: 'shiftViewCtrl'
		})
		.when('/summary', {
			templateUrl: 'pages/summary.html',
			controller: 'SummaryController',
			controllerAs: 'summaryCtrl'
		})
		.when('/summary/weekly', {
			templateUrl: 'pages/summary-weekly.html',
			controller: 'SummaryWeeklyController',
			controllerAs: 'summaryWeeklyCtrl'
		})
		// .when('/summary/monthly', {
		// 	templateUrl: 'pages/summary-monthly.html',
		// 	controller: 'SummaryMonthlyController',
		// 	controllerAs: 'summaryMonthlyCtrl'
		// })
		.when('/shifts/add', {
			templateUrl: 'pages/shift-add.html',
			controller: 'ShiftAddController',
			controllerAs: 'shiftAddCtrl'
		})
		.when('/shift/:id/edit', {
			templateUrl: 'pages/shift-edit.html',
			controller: 'ShiftEditController',
			controllerAs: 'shiftEditCtrl'
		})
		.when('/', {
			templateUrl: 'pages/home.html'
		})
		.otherwise({
			redirectTo: '/'
		});
	});

	app.controller('ShiftViewController', [ '$http', '$routeParams', function($http, $routeParams) {
		var ctrl = this;

		$http.get('./data/shifts.php?id=' + $routeParams.id)
		.success(function (data, status, headers, config) {
			ctrl.getResponse = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.shift = data;
		})
		.error(function (data, status, headers, config) {
			ctrl.getResponse = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find shift.';
		});

		this.deleteClick = function(shiftId) {
			var deleteObject = {id: shiftId};
			$http({
				method: 'DELETE',
				url: './data/shifts.php?id=' + shiftId,
				data: null,
				headers: {'Content-Type': 'application/c-www-form-urlencoded'}
			})
			.success(function (data, status, headers, config) {
				ctrl.deleteResponse = {result: 'success', data: data, status: status, headers: headers, config: config};
				ctrl.error = 'Shift deleted.';
			})
			.error(function (data, status, headers, config) {
				ctrl.deleteResponse = {result: 'success', data: data, status: status, headers: headers, config: config};
				ctrl.error = 'Oops! Something bad happened. Cannot delete shift.';
			});
		};
	}]);

	app.controller('ShiftGridController', [ '$http', function($http) {
		var ctrl = this;

		$http.get('./data/shifts.php')
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.shifts = data;
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find shifts.';
		});
	}]);

	app.controller('FilterController', function() {
		this.from = '';
		this.to = '';

		this.lunCheck = false;
		this.dinCheck = false;
		this.lunchDinner = '';	//value to filter by lunchDinner

		this.monCheck = false;
		this.tueCheck = false;
		this.wedCheck = false;
		this.thuCheck = false;
		this.friCheck = false;
		this.satCheck = false;
		this.sunCheck = false;
		this.aDays = [null,null,null,null,null,null,null];		//array to keep track of checked days

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

		this.toggleMon = function() {
			this.monCheck = !this.monCheck;
			this.aDays[0] = this.monCheck ? 'Mon' : null;
			this.refreshDaysString();
		};
		this.toggleTue = function() {
			this.tueCheck = !this.tueCheck;
			this.aDays[1] = this.tueCheck ? 'Tue' : null;
			this.refreshDaysString();
		};
		this.toggleWed = function() {
			this.wedCheck = !this.wedCheck;
			this.aDays[2] = this.wedCheck ? 'Wed' : null;
			this.refreshDaysString();
		};
		this.toggleThu = function() {
			this.thuCheck = !this.thuCheck;
			this.aDays[3] = this.thuCheck ? 'Thu' : null;
			this.refreshDaysString();
		};
		this.toggleFri = function() {
			this.friCheck = !this.friCheck;
			this.aDays[4] = this.friCheck ? 'Fri' : null;
			this.refreshDaysString();
		};
		this.toggleSat = function() {
			this.satCheck = !this.satCheck;
			this.aDays[5] = this.satCheck ? 'Sat' : null;
			this.refreshDaysString();
		};
		this.toggleSun = function() {
			this.sunCheck = !this.sunCheck;
			this.aDays[6] = this.sunCheck ? 'Sun' : null;
			this.refreshDaysString();
		};
		this.refreshDaysString = function() {
			var string = '';
			for(i = 0; i < 7; i++) {
				string += string === '' //if string is still empty, don't add comma
					? this.aDays[i] ? this.aDays[i] : ''
					: this.aDays[i] ? ", " + this.aDays[i] : '';
			}
			this.sDays = string;
		};
	});

	app.controller('ShiftAddController', ['$http', function($http) {
		var ctrl = this;
		this.shift = {wage: 9};

		this.addShift = function() {
			//remove the timezone information that angular adds during its validation
			var postShift = JSON.parse(JSON.stringify(ctrl.shift));
			postShift.date = postShift.date ? moment(postShift.date).format('YYYY-MM-DD') : null;
			postShift.startTime = postShift.startTime ? moment(postShift.startTime).format('HH:mm:ss') : null;
			postShift.endTime = postShift.endTime ? moment(postShift.endTime).format('HH:mm:ss') : null;
			postShift.firstTable = postShift.firstTable ? moment(postShift.firstTable).format('HH:mm:ss') : null;
			ctrl.postShift = postShift;
			$http({
				method: 'POST',
				url: './data/shifts.php',
				data: postShift,
				headers: {'Content-Type': 'application/c-www-form-urlencoded'}
			})
			.success(function (data, status, headers, config) {
				ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
				window.location.replace('#/shift/' + data);
			})
			.error(function (data, status, headers, config) {
				ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
				ctrl.error = 'Oops! Something bad happened. The shift cannot be added.';
			});
		};
	}]);

	app.controller('ShiftEditController', [ '$http', '$routeParams', function($http, $routeParams) {
		var ctrl = this;

		$http.get('./data/shifts.php?id=' + $routeParams.id)
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			var retrievedShift = data;
			//convert date fields
			retrievedShift.date = retrievedShift.date ? moment(retrievedShift.date, 'YYYY-MM-DD').toDate() : null;
			retrievedShift.startTime = retrievedShift.startTime ? moment(retrievedShift.startTime, 'HH:mm:ss').toDate() : null;
			retrievedShift.endTime = retrievedShift.endTime ? moment(retrievedShift.endTime, 'HH:mm:ss').toDate() : null;
			retrievedShift.firstTable = retrievedShift.firstTable ? moment(retrievedShift.firstTable, 'HH:mm:ss').toDate() : null;
			//convert number fields
			retrievedShift.wage = retrievedShift.wage || retrievedShift.wage == 0 ? Number(retrievedShift.wage) : null;
			retrievedShift.campHours = retrievedShift.campHours || retrievedShift.campHours == 0 ? Number(retrievedShift.campHours) : null;
			retrievedShift.sales = retrievedShift.sales || retrievedShift.sales == 0 ? Number(retrievedShift.sales) : null;
			retrievedShift.covers = retrievedShift.covers || retrievedShift.covers == 0 ? Number(retrievedShift.covers) : null;
			retrievedShift.tipout = retrievedShift.tipout || retrievedShift.tipout == 0 ? Number(retrievedShift.tipout) : null;
			retrievedShift.transfers = retrievedShift.transfers || retrievedShift.transfers == 0 ? Number(retrievedShift.transfers) : null;
			retrievedShift.cash = retrievedShift.cash || retrievedShift.cash == 0 ? Number(retrievedShift.cash) : null;
			retrievedShift.due = retrievedShift.due || retrievedShift.due == 0 ? Number(retrievedShift.due) : null;

			ctrl.shift = retrievedShift;
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find shift.';
		});

		this.editShift = function() {
			//remove the timezone information that angular adds during its validation
			var postShift = JSON.parse(JSON.stringify(ctrl.shift));
			postShift.date = postShift.date ? moment(postShift.date).format('YYYY-MM-DD') : null;
			postShift.startTime = postShift.startTime ? moment(postShift.startTime).format('HH:mm:ss') : null;
			postShift.endTime = postShift.endTime ? moment(postShift.endTime).format('HH:mm:ss') : null;
			postShift.firstTable = postShift.firstTable ? moment(postShift.firstTable).format('HH:mm:ss') : null;
			ctrl.postShift = postShift;
			$http({
				method: 'PUT',
				url: './data/shifts.php?id=' + postShift.id,
				data: postShift,
				headers: {'Content-Type': 'application/c-www-form-urlencoded'}
			})
			.success(function (data, status, headers, config) {
				ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
				window.location.replace('#/shift/' + postShift.id);
			})
			.error(function (data, status, headers, config) {
				ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
				ctrl.error = 'Oops! Something bad happened. The shift cannot be edited.';
			});
		};
	}]);

	app.controller('SummaryWeeklyController', [ '$http', function($http) {
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
	}]);

	app.controller('SummaryController', [ '$http', function($http) {
		var ctrl = this;

		$http.get('./data/summaries.php')
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summaries = data;
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
	}]);

	app.filter('timeToDate', function() {
		return function(time) {
			return new Date('January 1, 1970 ' + time);
		};
	})

	app.filter('isValidShift', function() {
		return function(shifts, filters) {
			var filteredShifts = [];
			angular.forEach(shifts, function(shift) {
				//check dates, if filters are empty, set it to true
				var isAfterFromDate = filters.fromDate 
					? moment(shift.date).isAfter(filters.fromDate) || moment(shift.date).isSame(filters.fromDate) 
					: true;
				var isBeforeToDate = filters.toDate 
					? moment(shift.date).isBefore(filters.toDate) || moment(shift.date).isSame(filters.toDate) 
					: true;

				//check lunch/dinner
				var isLunchDinner = filters.lunchDinner ? shift.lunchDinner == filters.lunchDinner : true;

				//check dayOfWeek
				var isDayOfWeek = false;
				var isNullArray = true;
				for(i = 0; i < 7; i++) {
					if (filters.aDays[i]) {
						isNullArray = false;
						if (shift.dayOfWeek == filters.aDays[i]) {
							isDayOfWeek = true;
						}
					}
				}
				if (isNullArray) {isDayOfWeek = true;}	//if array is all null, then should match all dayOfWeeks

				//check if all conditions match
				// console.log(shift)
				// console.log(filters)
				// console.log(new moment(shift.date)) 
				// console.log(new moment(filters.fromDate))
				// console.log(new moment(filters.toDate))
				// console.log(isAfterFromDate, isBeforeToDate, isLunchDinner, isDayOfWeek, isNullArray);
				if (isAfterFromDate && isBeforeToDate && isLunchDinner && isDayOfWeek) {
					filteredShifts.push(shift);
				}				
			});
			return filteredShifts;
		};
	});

	app.filter('placeholder', function() {
		return function(input, placeholder) {
			if (input == undefined || input == null) {
				return placeholder;
			} else {
				return input;
			}
		};
	});	
})();