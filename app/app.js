(function() {
	angular.module('shiftTips', ['ngRoute'])

	.config(function ($routeProvider) {
		$routeProvider
		.when('/shifts', 			{templateUrl: 'app/shifts/shift-list.html'})
		.when('/shifts/list', 		{templateUrl: 'app/shifts/shift-list.html'})
		.when('/shifts/grid', 		{templateUrl: 'app/shifts/shift-grid.html'})
		.when('/shift/:id', 		{templateUrl: 'app/shifts/shift-view.html'})
		.when('/summary', 			{templateUrl: 'app/summary/summary.html'})
		.when('/summary/period', 	{templateUrl: 'app/summary/summary-period.html'})
		.when('/shifts/add', 		{templateUrl: 'app/shifts/shift-add.html'})
		.when('/shift/:id/edit', 	{templateUrl: 'app/shifts/shift-edit.html'})
		.when('/', 					{templateUrl: 'app/home/home.html'})
		.otherwise(					{redirectTo: '/'});
	})
})();