angular.module('shiftTips')

.controller('DayforceViewController', ['$scope', 'filterService', function($scope, filterService) {
	var ctrl = this;
	// ctrl.prefs = filterService.getUserPrefs(ctrl.uid).dayforce;	//for future custom view

	ctrl.parseFile = function(textData) {
		alert("parse called!");
	}
}])

.directive("textFile", [function() {
	return {
		scope: {
			textFile: "="
		},
		restrict: "A",
		link: function(scope, elem, attrs) {
			elem.bind("change", function(changeEvent) {
				var reader = new FileReader();
				reader.onload = function(loadEvent) {
					scope.$apply(function() {
						scope.textFile = loadEvent.target.result;
					});
				}
				reader.readAsText(changeEvent.target.files[0]);
			});
		}
	}
}]);