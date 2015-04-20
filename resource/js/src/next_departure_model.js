/**
 * Model for Next Departure, contains ajax call and some data. 
 *  
 * @author Chen Ling <chling.sbu@gmail.com>
 * @copyright Chen Ling 2015
 * Released under the MIT License
 *
 */

define([
	'jquery',
	'underscore',
	'backbone'
], function($, _, Backbone) {
	 var NextDepartureModel = Backbone.Model.extend({

	 	findStops: function(pos) {
	 		var self = this;
	 		$.ajax({
	 			url: './php/src/ajax_handler.php',
	 			data: {command: 'get_nearby_stops', lat: pos.lat(), lng: pos.lng()},
	 			type: 'GET',
	 			dataType: 'json'
	 		}).done(function(data) {
	 				self.set('stops', data)
	 				    .trigger('updateStops');
	 		})
	 		.fail(function(jqXHR) {
	 			window.alert('Error occured when getting nearby stops. ' + jqXHR.status + ' ' + jqXHR.statusText);
	 		});
	 	},

	 	getDepartures: function(stop, stopMarker) {
	 		var self = this;
	 		$.ajax({
	 			url: './php/src/ajax_handler.php',
	 			data: {command: 'get_departures', stopId: stop.id, agency: stop.agency},
	 			type: 'GET',
	 			dataType: 'json'
	 		}).done(function(data) {
	 			self.set('departures', data)
	 			    .trigger('renderDepartures', stop, stopMarker, self.get('departures'));
	 		})
	 		.fail(function(jqXHR) {
	 			window.alert('Error occured when getting departures for stop id: ' + stop.id + '. ' + jqXHR.status + ' ' + jqXHR.statusText);
	 		});
	 	},

	 	getAddress: function(pos) {
	 		var self = this;
	 		var latlng = pos.lat + ',' + pos.lng;
	 		//var key = 'AIzaSyAGEbLl5kjH9GbwOEZPMun5femCAGkju-o';
	 		return $.ajax({
	 		       	url: 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' + latlng,
	 		       	type: 'GET',
	 		       	dataType: 'json'
	 		       });
	 	}

	 });
	 return NextDepartureModel;
});