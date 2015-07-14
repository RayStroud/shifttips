(function() {
	var app = angular.module('shiftTips', ['ngRoute']);

	app.config(function ($routeProvider) {
		$routeProvider.when('/shifts', {
			templateUrl: 'pages/list.html',
			controller: 'ShiftListController',
			contrllerAs: 'shiftListCtrl'
		})
		// .when('/shifts/weekly', {
		// 	templateUrl: 'pages/list-weekly.html',
		// 	controller: 'ShiftListWeeklyController',
		// 	contrllerAs: 'shiftListWeeklyCtrl'
		// })
		.when('/shift/:id', {
			templateUrl: 'pages/view.html',
			controller: 'ShiftViewController',
			contrllerAs: 'shiftViewCtrl'
		})
		// .when('/summary', {
		// 	templateUrl: 'pages/summary.html',
		// 	controller: 'SummaryController',
		// 	contrllerAs: 'summaryCtrl'
		// })
		// .when('/summary/weekly', {
		// 	templateUrl: 'pages/summary-weekly.html',
		// 	controller: 'SummaryWeeklyController',
		// 	contrllerAs: 'summaryWeeklyCtrl'
		// })
		// .when('/summary/monthly', {
		// 	templateUrl: 'pages/summary-monthly.html',
		// 	controller: 'SummaryMonthlyController',
		// 	contrllerAs: 'summaryMonthlyCtrl'
		// })
		.when('/shifts/add', {
			templateUrl: 'pages/add.html',
			controller: 'ShiftAddController',
			contrllerAs: 'shiftAddCtrl'
		})
		// .when('/shifts/edit/:id', {
		// 	templateUrl: 'pages/edit.html',
		// 	controller: 'ShiftEditController',
		// 	contrllerAs: 'shiftEditCtrl'
		// })
		.when('/', {
			templateUrl: 'pages/home.html'
		})
		.otherwise({
			redirectTo: '/'
		});
	});

	app.controller('ShiftViewController', [ '$http', '$routeParams', function($http, $routeParams) {
		var ctrl = this;

		var getShiftInfo = function(id) {
			$http.get('./json/shift.php?id=' + id)
			.success(function(data) {
				ctrl.shift = data;
			})
			.error(function (data, status, headers, config) {
				ctrl.response = 'DATA: ' + data + '|STATUS: ' + status + '|HEADERS: ' + headers + '|CONFIG: ' + config;
				ctrl.error = 'Oops! Something bad happened. Cannot find shift.';
			});
		};

		$http.get('./json/shift.php?id=' + $routeParams.id)
		.success(function(data) {
			ctrl.shift = data;
		})
		.error(function (data, status, headers, config) {
			ctrl.response = 'DATA: ' + data + '|STATUS: ' + status + '|HEADERS: ' + headers + '|CONFIG: ' + config;
			ctrl.error = 'Oops! Something bad happened. Cannot find shift.';
		});
	}]);

	app.controller('ShiftListController', [ '$http', function($http) {
		var ctrl = this;

		$http.get('./json/shifts.php')
		.success(function(data) {
			ctrl.shifts = data;
		})
		.error(function (data, status, headers, config) {
			ctrl.response = 'DATA: ' + data + '|STATUS: ' + status + '|HEADERS: ' + headers + '|CONFIG: ' + config;
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
		};
		this.toggleTue = function() {
			this.tueCheck = !this.tueCheck;
			this.aDays[1] = this.tueCheck ? 'Tue' : null;
		};
		this.toggleWed = function() {
			this.wedCheck = !this.wedCheck;
			this.aDays[2] = this.wedCheck ? 'Wed' : null;
		};
		this.toggleThu = function() {
			this.thuCheck = !this.thuCheck;
			this.aDays[3] = this.thuCheck ? 'Thu' : null;
		};
		this.toggleFri = function() {
			this.friCheck = !this.friCheck;
			this.aDays[4] = this.friCheck ? 'Fri' : null;
		};
		this.toggleSat = function() {
			this.satCheck = !this.satCheck;
			this.aDays[5] = this.satCheck ? 'Sat' : null;
		};
		this.toggleSun = function() {
			this.sunCheck = !this.sunCheck;
			this.aDays[6] = this.sunCheck ? 'Sun' : null;
		};
	});

	app.controller('ShiftAddController', ['$http', function($http) {
		var ctrl = this;
		this.shift = {wage: 9};

		this.addShift = function(shift) {
			//remove the timezone information that angular adds during its validation
			postShift = JSON.parse(JSON.stringify(ctrl.shift));
			postShift.date = postShift.date ? moment(postShift.date).format('YYYY-MM-DD') : null;
			postShift.startTime = postShift.startTime ? moment(postShift.startTime).format('HH:mm:ss') : null;
			postShift.endTime = postShift.endTime ? moment(postShift.endTime).format('HH:mm:ss') : null;
			postShift.firstTable = postShift.firstTable ? moment(postShift.firstTable).format('HH:mm:ss') : null;
			ctrl.postShift = postShift;
			$http({
				method: "POST",
				url: 'json/shift-add.php',
				data: postShift,
				headers: {'Content-Type': 'application/c-www-form-urlencoded'}
			})
			.success(function (data, status, headers, config) {
				ctrl.response = 'DATA: ' + data + '|STATUS: ' + status + '|HEADERS: ' + headers + '|CONFIG: ' + config;
				window.location.replace('#/shift/' + data);
			})
			.error(function (data, status, headers, config) {
				ctrl.response = 'DATA: ' + data + '|STATUS: ' + status + '|HEADERS: ' + headers + '|CONFIG: ' + config;
				ctrl.error = 'Oops! Something bad happened. The shift cannot be added.';
			});
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