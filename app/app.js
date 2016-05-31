(function() {
	angular.module('shiftTips', ['ngRoute', 'angular.filter', 'LocalStorageModule'])

	.constant('backend', {domain:'http://207.6.77.17:7290/shifttips/data/'})

	.config(function ($routeProvider) {
		$routeProvider
		.when('/shifts', 			{templateUrl: 'app/shifts/shift-list.html'})
		.when('/shifts/list', 		{templateUrl: 'app/shifts/shift-list.html'})
		.when('/shifts/grid', 		{templateUrl: 'app/shifts/shift-grid.html'})
		.when('/shift/:id', 		{templateUrl: 'app/shifts/shift-view.html'})
		.when('/shift/:id/edit', 	{templateUrl: 'app/shifts/shift-edit.html'})
		.when('/shifts/add', 		{templateUrl: 'app/shifts/shift-add.html'})
		.when('/shifts/due', 		{templateUrl: 'app/shifts/shift-due.html'})
		.when('/summary', 			{templateUrl: 'app/summary/summary.html'})
		.when('/summary/period', 	{templateUrl: 'app/summary/summary-period.html'})
		.when('/account', 			{templateUrl: 'app/users/account.html'})
		.when('/', 					{templateUrl: 'app/home/home.html'})
		.otherwise(					{redirectTo: '/'});
	})
})();