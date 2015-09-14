angular.module('shiftTips')
.service('ShiftsService', ['$http', function($http) {
	this.getShifts = function() {
		return $http.get('../data/shifts.php');
	};
	this.getShift = function(id) {
		return $http.get('../data/shifts.php', id);
	};
	this.saveShift = function(shift) {
		return $http.post('../data/shifts.php', shift);
	};
	this.removeShift = function(id) {
		return $http.delete('../data/shifts.php', id);
	};
}]);