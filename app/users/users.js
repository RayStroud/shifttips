angular.module('shiftTips')
.service('userService', ['$http', 'backend', 'localStorageService', function($http, backend, localStorageService) {
	var ctrl = this;
	ctrl.isSilentLoggedIn = false;	//flag for knowing when it's okay to start showing user the app
	ctrl.getNullUser = function() {
		return {"name":null,"email":null,"uid":-1};
	};
	ctrl.getUser = function() {
		return ctrl.user;
	};
	ctrl.addUser = function(user) {
		return $http.post(backend.domain + 'users.php', user);
	};
	ctrl.editUser = function(user) {
		return $http.put(backend.domain + 'users.php', user);
	};
	ctrl.removeUser = function(uid, id) {
		return $http.delete(backend.domain + 'users.php?id=' + id);
	};
	ctrl.login = function(name, email) {
		var response = $http.get(backend.domain + 'users.php?name=' + name + '&email=' + email)
		.success(function (data, status, headers, config) {
			ctrl.user = {"name":name,"email":email,"uid":data};
			localStorageService.set('user', ctrl.user);
		})
		.error(function (data, status, headers, config) {
			ctrl.user = ctrl.getNullUser();
		});
		return response;
	};
	ctrl.logout = function() {
		ctrl.user = ctrl.getNullUser();
		localStorageService.set('user', ctrl.getNullUser());
	};
	ctrl.silentLogin = function() {
		var storedUser = localStorageService.get('user') || ctrl.getNullUser();	//get stored user

		var response = $http.get(backend.domain + 'users.php?name=' + storedUser.name + '&email=' + storedUser.email)
		.success(function (data, status, headers, config) {
			if(data > 0) {	//if the uid is VALID
				ctrl.user = {"name":storedUser.name,"email":storedUser.email,"uid":data};
				localStorageService.set('user', ctrl.user);
				ctrl.isSilentLoggedIn = true;
			} else {		//if the uid is INVALID
				ctrl.user = ctrl.getNullUser();
				localStorageService.set('user', ctrl.getNullUser());
				ctrl.isSilentLoggedIn = true;
			}
		})
		.error(function (data, status, headers, config) {
			ctrl.user = ctrl.getNullUser();
			localStorageService.set('user', ctrl.getNullUser());
			ctrl.isSilentLoggedIn = true;
		});
		return response;
	};
	ctrl.user = ctrl.getNullUser();	//initialize user var
}])

.controller('UserController', ['userService', '$location', function(userService, $location) {
	var ctrl = this;
	ctrl.user = userService.getUser();
	ctrl.showAbout = false;

	ctrl.silentLogin = function() {
		userService.silentLogin()
		.success(function (data, status, headers, config) {
			ctrl.silentLoginResponse = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.user = userService.getUser();
			if(ctrl.user.uid == 0) {
				ctrl.silentLoginError = "Sorry, that name and email is not registered. Confirm the information is correct and try again, or register a new account."
			}
			else {
				ctrl.silentLoginError = "";
			}
		})
		.error(function (data, status, headers, config) {
			ctrl.silentLoginResponse = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Login failed.';
		});
	};

	ctrl.login = function(name, email) {
		userService.login(name, email)
		.success(function (data, status, headers, config) {
			ctrl.loginResponse = {result: 'success', data: data, status: status, headers: headers, config: config};
			ctrl.user = userService.getUser();
			if(ctrl.user.uid == 0) {
				ctrl.loginError = "Sorry, that name and email is not registered. Confirm the information is correct and try again, or register a new account."
			}
			else {
				ctrl.loginError = "";
			}
		})
		.error(function (data, status, headers, config) {
			ctrl.loginResponse = {result: 'error', data: data, status: status, headers: headers, config: config};
			ctrl.error = 'Oops! Something bad happened. Login failed.';
		});
	};

	ctrl.addUser = function() {
		userService.addUser(ctrl.newUser)
		.success(function (data, status, headers, config) {
			ctrl.addUserResponse = {result: 'success', data: data, status: status, headers: headers, config: config};
			if(data == 0) {
				ctrl.registerError = "Sorry, that email is already registered, or it cannot be registered at this time."
			}
			else {
				ctrl.registerError = "";
				ctrl.login(ctrl.newUser.name, ctrl.newUser.email);
			}
		})
		.error(function (data, status, headers, config) {
			ctrl.addUserResponse = {result: 'error', data: data, status: status, headers: headers, config: config};
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
	ctrl.isSilentLoggedIn = function() {
		return userService.isSilentLoggedIn;
	};
	ctrl.silentLogin();	//try logging in stored user on first load
}]);