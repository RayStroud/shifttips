angular.module('shiftTips')
.service('usersService', ['$http', function($http) {
	this.getUsers = function(uid) {
		return $http.get('./data/users.php');
	};
	this.getUser = function(id) {
		return $http.get('./data/users.php?id=' + id);
	};
	this.addUser = function(user) {
		return $http.post('./data/users.php', user);
	};
	this.editUser = function(user) {
		return $http.put('./data/users.php', user);
	};
	this.removeUser = function(uid, id) {
		return $http.delete('./data/users.php?id=' + id);
	};
	this.login = function(name, email) {
		return $http.get('./data/users.php?name=' + name + '&email=' + email);
	};
}])

.controller('LoginController', ['usersService', function(usersService) {
	var ctrl = this;

	ctrl.login = function() {
		usersService.login(ctrl.loginUser.name, ctrl.loginUser.email)
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. The account cannot be created.';
		});
	};

	ctrl.addUser = function() {
		usersService.addUser(ctrl.newUser)
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. The account cannot be created.';
		});
	};

}]);