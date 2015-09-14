(function() {
	angular.module('shiftTips', ['ngRoute'])

	.config(function ($routeProvider) {
		$routeProvider.when('/shifts', {
			templateUrl: 'app/shifts/shift-grid.html',
			controller: 'ShiftGridController',
			controllerAs: 'shiftGridCtrl'
		})
		// .when('/weekly/grid', {
		// 	templateUrl: 'shifts/weekly-grid.html',
		// 	controller: 'ShiftListWeeklyController',
		// 	controllerAs: 'shiftListWeeklyCtrl'
		// })
		.when('/shift/:id', {
			templateUrl: 'app/shifts/shift-view.html',
			controller: 'ShiftViewController',
			controllerAs: 'shiftViewCtrl'
		})
		.when('/summary', {
			templateUrl: 'app/summary/summary.html',
			controller: 'SummaryController',
			controllerAs: 'summaryCtrl'
		})
		.when('/summary/weekly', {
			templateUrl: 'app/summary/summary-weekly.html',
			controller: 'SummaryWeeklyController',
			controllerAs: 'summaryWeeklyCtrl'
		})
		// .when('/summary/monthly', {
		// 	templateUrl: 'summary/summary-monthly.html',
		// 	controller: 'SummaryMonthlyController',
		// 	controllerAs: 'summaryMonthlyCtrl'
		// })
		.when('/shifts/add', {
			templateUrl: 'app/shifts/shift-add.html',
			controller: 'ShiftAddController',
			controllerAs: 'shiftAddCtrl'
		})
		.when('/shift/:id/edit', {
			templateUrl: 'app/shifts/shift-edit.html',
			controller: 'ShiftEditController',
			controllerAs: 'shiftEditCtrl'
		})
		.when('/', {
			templateUrl: 'app/home/home.html'
		})
		.otherwise({
			redirectTo: '/'
		});
	})
})();