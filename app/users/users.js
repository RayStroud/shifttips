angular.module('shiftTips')
.service('userService', ['$http', 'backend', 'localStorageService', function($http, backend, localStorageService) {
	var ctrl = this;
	ctrl.user = localStorageService.get('user') || {"name":null,"uid":-1};
	this.getUser = function() {
		return ctrl.user;
	};
	this.addUser = function(user) {
		return $http.post(backend.domain + 'users.php', user);
	};
	this.editUser = function(user) {
		return $http.put(backend.domain + 'users.php', user);
	};
	this.removeUser = function(uid, id) {
		return $http.delete(backend.domain + 'users.php?id=' + id);
	};
	this.login = function(name, email) {
		var response = $http.get(backend.domain + 'users.php?name=' + name + '&email=' + email)
		.success(function (data, status, headers, config) {
			var loggedUser = {"name":name,"uid":data};
			ctrl.user = loggedUser;
			localStorageService.set('user', loggedUser);
		})
		.error(function (data, status, headers, config) {
			ctrl.user = {"name":null,"uid":-1};
		});
		return response;
	};
	this.logout = function() {
		var nullUser = {"name":null,"uid":-1};
		localStorageService.set('user', nullUser);
		ctrl.user = nullUser;
	};
}])

.controller('UserController', ['userService', '$location', function(userService, $location) {
	var ctrl = this;
	ctrl.user = userService.getUser();

	ctrl.login = function(name, email) {
		userService.login(name, email)
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
			ctrl.error = 'Oops! Something bad happened. Login failed.';
		});
	};

	ctrl.addUser = function() {
		userService.addUser(ctrl.newUser)
		.success(function (data, status, headers, config) {
			ctrl.response = {result: 'success', data: data, status: status, headers: headers, config: config};
			if(data == 0) {
				ctrl.registerError = "Sorry, that email is already registered, or it cannot be registered at this time."
			}
			else {
				ctrl.registerError = "";
				ctrl.login(ctrl.newUser.name, ctrl.newUser.email);
			}
		})
		.error(function (data, status, headers, config) {
			ctrl.response = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. The email cannot be registered.';
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