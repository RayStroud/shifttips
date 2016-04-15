angular.module('shiftTips')
.service('shiftsService', ['$http', 'userService', function($http, userService) {
	var ctrl = this;
	ctrl.uid = -1;		//record uid to compare against current logged in user
	ctrl.shiftId = -1;	//record shift id to compare against stored shift
	ctrl.resetIds = function() {
		ctrl.uid = -1;
		ctrl.shiftId = -1;
		ctrl.shifts = null;
		ctrl.shift = null;
	}
	ctrl.getShifts = function(uid) {
		if (!ctrl.shifts || ctrl.uid != uid) {	//if empty, or if uid changed
			ctrl.uid = uid;
			ctrl.shifts = $http.get('./data/shifts.php?uid=' + uid);
		}
		return ctrl.shifts;
	};
	ctrl.getShift = function(uid, id) {
		return $http.get('./data/shifts.php?uid=' + uid + '&id=' + id);
	};
	ctrl.addShift = function(shift) {
		ctrl.resetIds();
		return $http.post('./data/shifts.php', shift);
	};
	ctrl.editShift = function(shift) {
		ctrl.resetIds();
		return $http.put('./data/shifts.php', shift);
	};
	ctrl.removeShift = function(uid, id) {
		return $http.delete('./data/shifts.php?uid=' + uid + '&id=' + id);
	};
	ctrl.setDueCheck = function(uid, id, dueCheck) {
		ctrl.resetIds();
		return $http.get('./data/shifts.php?uid=' + uid + '&id=' + id + '&dueCheck=' + dueCheck);
	};
}])

.controller('ShiftViewController', [ 'shiftsService', 'userService', '$routeParams', function(shiftsService, userService, $routeParams) {
	var ctrl = this;

	ctrl.loadShift = function() {
		shiftsService.getShift(userService.getUser().uid, $routeParams.id)
		.success(function (data, status, headers, config) {
			ctrl.getResponse = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.shift = data;
		})
		.error(function (data, status, headers, config) {
			ctrl.getResponse = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find shift.';
		});
	};

	this.deleteClick = function(shiftId) {
		shiftsService.removeShift(userService.getUser().uid, shiftId)
		.success(function (data, status, headers, config) {
			ctrl.deleteResponse = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Shift deleted.';
		})
		.error(function (data, status, headers, config) {
			ctrl.deleteResponse = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot delete shift.';
		});
	};

	ctrl.setDueCheck = function(id, dueCheck) {
		shiftsService.setDueCheck(userService.getUser().uid, id, dueCheck);
		ctrl.loadShift();
	};

	ctrl.loadShift();
}])

.controller('ShiftGridController', ['shiftsService', 'userService', function(shiftsService, userService) {
	var ctrl = this;

	ctrl.getShifts = function() {
		shiftsService.getShifts(userService.getUser().uid)
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.shifts = data;
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find shifts.';
		});
	};
	ctrl.getShifts();
}])

.controller('ShiftListController', ['$location', 'shiftsService', 'summaryService', 'userService', 'filterService', function($location, shiftsService, summaryService, userService, filterService) {
	var ctrl = this;
	ctrl.uid = userService.getUser().uid;
	ctrl.prefs = filterService.getUserPrefs(ctrl.uid).list;

	ctrl.getShifts = function() {
		shiftsService.getShifts(userService.getUser().uid)
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.shifts = data;
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find shifts.';
		});
	};

	ctrl.updateSummary = function() {
		var filters = filterService.getUserFilters(ctrl.uid);
		var p_dateFrom = moment(filters.from).isValid() ? moment(filters.from).format('YYYY-MM-DD') : null;
		var p_dateTo = moment(filters.to).isValid() ? moment(filters.to).format('YYYY-MM-DD') : null;

		//* DEBUG */ console.log("getSummaryFiltered(" + ctrl.uid + ", " + p_dateFrom + ", " + p_dateTo + ", " + filters.lunchDinner + ", " + filters.mon + ", " + filters.tue + ", " + filters.wed + ", " + filters.thu + ", " + filters.fri + ", " + filters.sat + ", " + filters.sun + ")");

		summaryService.getSummaryFiltered(ctrl.uid, p_dateFrom, p_dateTo, filters.lunchDinner, filters.mon, filters.tue, filters.wed, filters.thu, filters.fri, filters.sat, filters.sun)
		.success(function (data, status, headers, config) {
			/* DEBUG */ctrl.summaryResponse = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.summary = data;
		})
		.error(function (data, status, headers, config) {
			/* DEBUG */ctrl.summaryResponse = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot get summary.';
		});

	};

	ctrl.viewShift = function(id) {
		$location.path('/shift/' + id);
	};

	ctrl.getShifts();
	ctrl.updateSummary();
}])

.controller('ShiftDueController', ['shiftsService', 'userService', function(shiftsService, userService) {
	var ctrl = this;

	ctrl.loadShifts = function() {
		shiftsService.getShifts(userService.getUser().uid)
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.shifts = data;
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Cannot find shifts.';
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

	ctrl.isFilterType = function(filterType) {
		return ctrl.filterType == filterType;
	};

	ctrl.changeFilterType = function(filterType) {
		ctrl.filterType = filterType;
		switch (filterType) {
			case 'all':
				ctrl.dueCheckFilter = undefined;
				break;
			case 'dueback':
				ctrl.dueCheckFilter = '';
				break;
			default:
				ctrl.dueCheckFilter = 'N';
				break;
		}
	};

	ctrl.setDueCheck = function(id, dueCheck) {
		shiftsService.setDueCheck(userService.getUser().uid, id, dueCheck);
		ctrl.loadShifts();
	};

	ctrl.loadShifts();
	ctrl.filterType = 'unreceived';
	ctrl.dueCheckFilter = 'N';
	ctrl.sortDate = ['date','startTime'];
	ctrl.sortReverse = true;
	ctrl.changeSortField(ctrl.sortDate);
	ctrl.changeSortField(ctrl.sortDate);
}])

.controller('ShiftFilterController', function() {
	this.visible = false;

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
})

.controller('ShiftAddController', ['shiftsService', 'userService', 'filterService', function(shiftsService, userService, filterService) {
	var ctrl = this;
	ctrl.uid = userService.getUser().uid;
	ctrl.prefs = filterService.getUserPrefs(ctrl.uid).add;
	ctrl.shift = {user_id: ctrl.uid, wage: filterService.getUserWage(ctrl.uid)};

	ctrl.addShift = function() {
		//remove the timezone information that angular adds during its validation
		var postShift = JSON.parse(JSON.stringify(ctrl.shift));
		postShift.date = postShift.date ? moment(postShift.date).format('YYYY-MM-DD') : null;
		postShift.startTime = postShift.startTime ? moment(postShift.startTime).format('HH:mm:ss') : null;
		postShift.endTime = postShift.endTime ? moment(postShift.endTime).format('HH:mm:ss') : null;
		postShift.firstTable = postShift.firstTable ? moment(postShift.firstTable).format('HH:mm:ss') : null;
		ctrl.postShift = postShift;
		//insert shift
		shiftsService.addShift(postShift)
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			window.location.replace('#/shift/' + data);
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. The shift cannot be added.';
		});
	};
}])

.controller('ShiftEditController', [ 'shiftsService', 'userService', 'filterService', '$routeParams', function(shiftsService, userService, filterService, $routeParams) {
	var ctrl = this;
	ctrl.uid = userService.getUser().uid;
	ctrl.prefs = filterService.getUserPrefs(ctrl.uid).edit;

	shiftsService.getShift(ctrl.uid, $routeParams.id)
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
		//* DEBUG */ postShift.user_id = ctrl.uid;
		postShift.date = postShift.date ? moment(postShift.date).format('YYYY-MM-DD') : null;
		postShift.startTime = postShift.startTime ? moment(postShift.startTime).format('HH:mm:ss') : null;
		postShift.endTime = postShift.endTime ? moment(postShift.endTime).format('HH:mm:ss') : null;
		postShift.firstTable = postShift.firstTable ? moment(postShift.firstTable).format('HH:mm:ss') : null;
		ctrl.postShift = postShift;
		//update shift
		shiftsService.editShift(postShift)
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			window.location.replace('#/shift/' + postShift.id);
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. The shift cannot be edited.';
		});
	};
}])

.filter('timeToDate', function() {
	return function(time) {
		var returnDate = time == null ? null : new Date('January 1, 1970 ' + time);
		return returnDate;
	};
})

.filter('isValidShift', function() {
	return function(shifts, filters) {
		var filteredShifts = [];
		angular.forEach(shifts, function(shift) {
			//check dates, if filters are empty, set it to true aka pass it
			var isAfterFromDate = filters.from 
				? moment(shift.date).isAfter(filters.from) || moment(shift.date).isSame(filters.from) 
				: true;
			var isBeforeToDate = filters.to 
				? moment(shift.date).isBefore(filters.to) || moment(shift.date).isSame(filters.to) 
				: true;

			//check lunch/dinner
			var isLunchDinner = filters.lunchDinner ? shift.lunchDinner == filters.lunchDinner : true;	//if lunchDinner is defined, check if it's the right one, otherwise pass it

			var isDayOfWeek = false;
			if(filters.days) {		//if there is a day selected
									//check all days
				isDayOfWeek = filters.mon
					? shift.dayOfWeek == "Mon" ? true : isDayOfWeek		//if monday is selected, check if dayOfWeek is 'mon', if so true, if not keep going
					: isDayOfWeek;										//if monday is not selected, keep going	
				isDayOfWeek = filters.tue
					? shift.dayOfWeek == "Tue" ? true : isDayOfWeek
					: isDayOfWeek;
				isDayOfWeek = filters.wed
					? shift.dayOfWeek == "Wed" ? true : isDayOfWeek
					: isDayOfWeek;
				isDayOfWeek = filters.thu
					? shift.dayOfWeek == "Thu" ? true : isDayOfWeek
					: isDayOfWeek;
				isDayOfWeek = filters.fri
					? shift.dayOfWeek == "Fri" ? true : isDayOfWeek
					: isDayOfWeek;
				isDayOfWeek = filters.sat
					? shift.dayOfWeek == "Sat" ? true : isDayOfWeek
					: isDayOfWeek;
				isDayOfWeek = filters.sun
					? shift.dayOfWeek == "Sun" ? true : isDayOfWeek
					: isDayOfWeek;
			} else {				//if there is no day selected
				isDayOfWeek = true	//pass the day of week test
			}

			// /* DEBUG */ console.log(shift)
			// /* DEBUG */ console.log(filters)
			// /* DEBUG */ console.log(new moment(shift.date)) 
			// /* DEBUG */ console.log(new moment(filters.from))
			// /* DEBUG */ console.log(new moment(filters.to))
			// /* DEBUG */ console.log(isAfterFromDate, isBeforeToDate, isLunchDinner, isDayOfWeek, isNullArray);

			//check if all conditions match
			if (isAfterFromDate && isBeforeToDate && isLunchDinner && isDayOfWeek) {
				filteredShifts.push(shift);
			}				
		});
		return filteredShifts;
	};
})

.filter('isValidDueCheck', function() {
	return function(shifts, filterType) {
		var filteredShifts = [];
		angular.forEach(shifts, function(shift) {
			switch(filterType) {
				case 'all':
					filteredShifts.push(shift);
					break;	
				case 'defined':
					if (shift.dueCheck != null) {
						filteredShifts.push(shift);
					}
					break;	
				case 'unreceived':
					if (shift.dueCheck == 'N') {
						filteredShifts.push(shift);
					}
					break;	
			}			
		});
		return filteredShifts;
	};
})

.filter('placeholder', function() {
	return function(input, placeholder, prefix, suffix) {
		if (input == undefined || input == null) {
			return placeholder;
		} else {
			var output = '';
			if(prefix != undefined && prefix != null) { output += prefix; } 
			output += input;
			if(suffix != undefined && suffix != null) { output += suffix; }
			return output;
		}
	};
})

.filter('yearweek', function() {
	return function(yearweek) {
		var weekStart = moment().year(yearweek.substr(0,4)).week(yearweek.substr(4,2)).day("Monday").format('ddd MMM Do, YYYY');
		var weekEnd = moment().year(yearweek.substr(0,4)).week(parseInt(yearweek.substr(4,2))+1).day("Sunday").format('ddd MMM Do, YYYY');
		return weekStart + " to " + weekEnd;
	};
});
