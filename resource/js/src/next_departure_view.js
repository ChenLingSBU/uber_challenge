/**
 * View for Next Departure.
 * For simplicity we only can query bus stop for sf-muni in San Francisco
 *
 * @author Chen Ling <chling.sbu@gmail.com>
 * @copyright Chen Ling 2015
 * Released under the MIT License
 *
 */

define([
  'jquery',
  'underscore',
  'backbone',
  'next_departure_model'
], function($, _, Backbone, NextDepartureModel) {

  var NextDepartureView = Backbone.View.extend({
    // initialize view's memebers below.
    el:'#map-canvas',
    map: null,

    // search radius is 500 meters
    searchRadius: 500,
    // This is used for testing purpose.
    // When developing and testing I will set this to false,
    // and use default geolocation, since i'm not in San Francisco.

    geolocation: true,
    walkMan: null,
    circle: null,

    // store markers instances for nearby stops
    stopMarkers: null,

    // cache visited bus stop to improve the performance, reduce unnecessary ajax calls
    cachedDepartures: {},

    infoWindow: new google.maps.InfoWindow(),

    model: new NextDepartureModel(),

    // default center of the map is Uber Headquarter in SF.
    defaultCenter: {lat: 37.775487, lng: -122.417530},

    initialize: function() {
      this.listenTo(this.model, 'updateStops', this.updateStops);
      this.listenTo(this.model, 'renderDepartures', this.renderDepartures);
      this.initMapWithPosition();
    },

    // initMap will be called by initMapWithPosition
    initMap: function(pos) {
      var self = this;
      var mapOptions = {
      center: pos,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      zoom: 15
      };
      this.map = new google.maps.Map(this.el,mapOptions);
      this.initWalkMan();
      this.initCircle();
      this.findStops(this.walkMan.getPosition());
      google.maps.event.addListener(this.walkMan, 'dragend', function() {
        self.updateCircle();
        self.findStops(self.walkMan.getPosition());
      });
    },

    // init the start posistion, then call initMap to init google map
  	initMapWithPosition: function() {
      var self = this;
      // HTML5 geolocation
      // get user's position if the user's device supports geolocation and user allows geolocation
      // otherwise use the default center as the center of the map.
      if (self.geolocation && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          var pos = ({lat: position.coords.latitude, lng: position.coords.longitude});
          // use google api to reverse geocode, and check whether the user is in San Francisco
          // model.getAddress is to get the real human friendly address.
          var ajaxDefer = self.model.getAddress(pos);
          $.when(ajaxDefer)
           .done(function(data) {
             var address = data.results;
             if (self.isSanFrancisco(address) == true) {
               self.initMap(pos);
             } else {
              window.alert('You are not in San Francisco, map will redirect to Uber Headquarter@San Francisco')
              self.initMap(self.defaultCenter);
             }
           })
           .fail(function(jqXHR) {
             window.alert('Error occured when getting current address: ' + jqXHR.status + ' ' + jqXHR.statusText);
           });
        });
      } else {
        self.initMap(self.defaultCenter);
      }
    },

    initWalkMan: function() {
      this.walkMan = new google.maps.Marker({
        position: this.map.getCenter(),
        title: 'drag me!',
        map: this.map,
        icon:'./resource/img/person.png',
        draggable: true
      });
    },

    initCircle: function() {
      this.circle = new google.maps.Circle({
        center: this.walkMan.getPosition(),
        clickable: false,
        fillcolor: '#ffffff',
        fillOpacity: 0.3,
        map: this.map,
        radius: this.searchRadius,
        strokeColor: '#000000',
        strokeOpacity: 0,
        zIndex: -10
      });
    },

    updateCircle: function() {
      var pos = this.walkMan.getPosition();
      this.circle.setCenter(pos);
      this.map.panTo(pos);
    },

    // when model finishs findStops, it will trigger updateStops function
    findStops: function(pos) {
      this.model.findStops(pos);
    },

    updateStops: function() {
      var self = this;
      self.cleanStopMarkers();
      var stops = this.model.get('stops');
      _.each(stops, function(stop) {
        var stopMarker = new google.maps.Marker({
          position: {lat: parseFloat(stop.lat, 10), lng: parseFloat(stop.long, 10)},
          title: 'Agency:' + stop.agency + ', ' + 'Bus Stop:' + stop.title + ', ' + 'Bus Stop ID' + stop.id,
          map: self.map,
          icon: './resource/img/bus.png'
        });
        self.stopMarkers.push(stopMarker);
        google.maps.event.addListener(stopMarker, 'click', function() {
          self.getDepartures(stop, stopMarker);
        });
      });
    },

    // when update stops, clean previous nearby stops
    cleanStopMarkers: function() {
      var self = this;
      _.each(self.stopMarkers, function(stopMarker) {
      stopMarker.setMap(null);
      });
      self.stopMarkers = [];
    },

    // for visited stops, we get departures info from cache
    // otherwise we will do a ajax call to get the departure info.
    getDepartures: function(stop, stopMarker) {
      // check whether cached and timestamp expired
      if (stop.id in this.cachedDepartures && this.cachedDepartures[stop.id].timeStamp >= (new Date).getTime()) {
        this.renderDepartures(stop, stopMarker, this.cachedDepartures[stop.id].departures);
      } else {
        this.model.getDepartures(stop, stopMarker);
      }
    },

    // set info window which contains departures info for the stop.
    renderDepartures: function(stop, stopMarker, departures) {
      var self = this;
      this.infoWindow.close();
      var timeStamp = departures[0].no_prediction == true ? 0 : departures[0].epoch_time;

      // set info window for the stop
      var content = '<b>' + stop.title + '</b></br>';
      _.each(departures, function(departure) {
      if ((departure.epoch_time != null) && (timeStamp > departure.epoch_time || timeStamp == 0 )) {
        timeStamp = departure.epoch_time;
      }
      if (departure.no_prediction == false) {
        content += '<b>Route:' + departure.route_tag + '</b>'
                 + ' ' + new Date(parseInt(departure.epoch_time, 10)).toLocaleTimeString()
                 + ' → ' + departure.direction + '</br>';
      } else {
        content += '<b>Route:' + departure.route_tag + '</b>'
                 + ' No Prediction ' + ' → ' + departure.direction + '</br>';
      }
      });

      // if no prediction for all the departures,we set the timeStamp to 5 mins later.
      // thus we can update cache for this after 5 mins.
      if (timeStamp == 0) {
        timeStamp = (new Date).getTime() + 5 * 60 * 1000;
      }

      var cacheKey = stop.id;
      // cache the departure info.
      self.cachedDepartures[cacheKey] = {departures: departures, timeStamp: timeStamp};
      this.infoWindow.setContent(content);
      this.infoWindow.open(self.map, stopMarker);
    },

    // check whether the given address is in San Francisco.
    // address is object array returned by google geocode api
    isSanFrancisco: function(address) {
      var len = address.length;
      if (address[len - 3].formatted_address == 'San Francisco County, CA, USA') {
        return true;
      } else {
        return false;
      }
    }

  });
  new NextDepartureView();
});
