(function() {
	var app = angular.module('shiftTips', []);

	app.controller('ShiftsController', [ '$http', function($http) {
		var ctrl = this;
		ctrl.shifts = [];

		$http.get('./json/shifts.php').
			success(function(data) {
				ctrl.shifts = data;
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
		this.sDays = '';		//string to use for filtering by dayOfWeek

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

	var db = [{
		id: "1",
		date: "1988-05-11",
		startTime: "4:00PM",
		endTime: "12:00AM",
		sales: "1234.56",
		tipout: "56",
		earnedTips: "154",
		earnedHourly: "28.56",

		dayOfWeek: "Wed",
		lunchDinner: "D"
	}, {
		id: "2",
		date: "1988-05-12",
		startTime: "11:30AM",
		endTime: "2:00PM",
		sales: "1234.56",
		tipout: "56",
		earnedTips: "154",
		earnedHourly: "28.56",

		dayOfWeek: "Thu",
		lunchDinner: "L"
	}, {
		id: "3",
		date: "1988-05-13",
		startTime: "4:00PM",
		endTime: "12:00AM",
		sales: "1234.56",
		tipout: "56",
		earnedTips: "154",
		earnedHourly: "28.56",

		dayOfWeek: "Fri",
		lunchDinner: "D"
	}, {
		id: "4",
		date: "1988-05-15",
		startTime: "4:00PM",
		endTime: "12:00AM",
		sales: "1234.56",
		tipout: "56",
		earnedTips: "154",
		earnedHourly: "28.56",

		dayOfWeek: "Sun",
		lunchDinner: "D"
	}];
})();