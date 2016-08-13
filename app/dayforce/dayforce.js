angular.module('shiftTips')

.controller('DayforceViewController', ['$scope', 'filterService', function($scope, filterService) {
	var ctrl = this;
	// ctrl.prefs = filterService.getUserPrefs(ctrl.uid).dayforce;	//for future custom view

	ctrl.parseFile = function(textData) {
		ctrl.shifts = ctrl.parseVCalendar(textData);
	}

	ctrl.parseVCalendar = function(text) {
		var events = [];	//to store all events
		var event = {};		//to store current event

		//loop through each line
		text.split(/\r?\n/).forEach(function(line) {
			var match;	//to store regex match

			//if line is BEGIN:VEVENT
			if(match = line.match(/^BEGIN:VEVENT/)) {
				//make new event
				event = {};
			}

			//if line is DTSTART
			else if(match = line.match(/^DTSTART:(.*)/)) {
				//parse date, startTime, yearweek, dayOfWeek, lunchDinner
				var dtstart = match[1];
				var momentDate = moment(dtstart, 'YYYYMMDDTHHmmssZ');
				event.date = momentDate.format("YYYY-MM-DD");
				event.startTime = momentDate.format("HH:mm:ss");
				event.yearweek = momentDate.year() + '' + momentDate.isoWeek();
				event.dayOfWeek = momentDate.format("ddd");
				event.lunchDinner = momentDate.hour() < 14 ? "L" : "D";
			}

			//if line is DTEND
			else if(match = line.match(/^DTEND:(.*)/)) {
				//parse endTime
				var dtend = match[1];
				var momentDate = moment(dtend, 'YYYYMMDDTHHmmssZ');
				event.endTime = momentDate.format("HH:mm:ss");
			}

			//if line is SUMMARY
			else if(match = line.match(/^SUMMARY:(.*)/)) {
				//store summary
				event.summary = match[1];
			}

			//if line is LOCATION
			else if(match = line.match(/^LOCATION:(.*)/)) {
				//store location
				event.location = match[1];
			}

			//if line is END:VEVENT
			if(match = line.match(/^END:VEVENT/)) {
				//make new event
				events.push(event);
			}
		});
		return events;
	};
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