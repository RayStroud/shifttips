angular.module('shiftTips')
.service('userService', ['$http', function($http) {
	var ctrl = this;
	ctrl.user = {"name":null,"uid":-1};
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
		var response = $http.get('./data/users.php?name=' + name + '&email=' + email)
		.success(function (data, status, headers, config) {
			ctrl.user.uid = data;
			ctrl.user.name = name;
		})
		.error(function (data, status, headers, config) {
			ctrl.user = {"name":null,"uid":-1};
		});
		return response;
	};
	this.logout = function() {
		ctrl.user = {"name":null,"uid":-1};
	};
	this.getUser = function() {
		return ctrl.user;
	};
}])

.controller('UserController', ['userService', '$location', function(userService, $location) {
	var ctrl = this;
	ctrl.user = userService.getUser();

	ctrl.login = function() {
		userService.login(ctrl.loginUser.name, ctrl.loginUser.email)
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.user = userService.getUser();
			if(ctrl.user.uid == 0) {
				ctrl.loginError = "Sorry, that name and email is not registered. Confirm the information is correct and try again, or register a new account."
			}
			else {
				ctrl.loginError = "";
			}
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. The account cannot be created.';
		});
	};

	ctrl.addUser = function() {
		userService.addUser(ctrl.newUser)
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			if(data == 0) {
				ctrl.registerError = "Sorry, that email is already registered."
			}
			else {
				ctrl.registerError = "";
				userService.login(ctrl.newUser.name, ctrl.newUser.email);
			}
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. The account cannot be created.';
		});
	};

	ctrl.logout = function() {
		userService.logout();
		ctrl.user = userService.getUser();
		$location.path('/login');
	};

	ctrl.isLoggedIn = function() {
		return ctrl.user.uid > 0;
	};
}]);