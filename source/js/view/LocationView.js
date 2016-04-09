var Backbone = require('backbone');
var $ = require('jquery');

var LocationView = Backbone.View.extend({

	// Main element of the view
	el: 'body',
	// Google maps id
	googleMapsId: '#google-maps',
	// This array will contain the markers
	markers: [],
	// Waypoints between start and end direction
	waypoints: [],

	events: {
		'click .get-location' : 'onGetLocationClick'
	},

	initialize: function () {
		this.googleMapsEl = $(this.googleMapsId);

		this.model.on('change', $.proxy(this.locationChanged, this), this);
		this.onGetLocationClick();
	},

	initGoogleMaps: function() {
		this.directionsDisplay = new google.maps.DirectionsRenderer;
		this.directionsService = new google.maps.DirectionsService;
		this.stepDisplay = new google.maps.InfoWindow;

		this.map = new google.maps.Map(this.googleMapsEl.get(0), {
			center: {lat: this.model.getLatitude(), lng: this.model.getLongitude()},
			scrollwheel: false,
			draggable: true,
			zoom: 17
		});

		this.markers.push({lat: this.model.getLatitude(), lng: this.model.getLongitude()});

		this.directionsDisplay.setMap(this.map);
		this.me = new google.maps.Marker({
			map: this.map,
			position: {
				lat: this.model.getLatitude(),
				lng: this.model.getLongitude()
			},
			title: 'Hello World!'
		});

		window.maps = this.map;
		this.addMapEvents();
	},

	calcRoute: function() {
		var map = this.map;
		var self = this;
		var start = new google.maps.LatLng(this.markers[0].lat, this.markers[0].lng);
		//var end = new google.maps.LatLng(38.334818, -181.884886);
		var end = new google.maps.LatLng(this.markers[1].lat, this.markers[1].lng);

		var bounds = new google.maps.LatLngBounds();
		bounds.extend(start);
		bounds.extend(end);
		map.fitBounds(bounds);
		console.info(start.lat() + ' ' + start.lng());
		console.info(end.lat() + ' ' + end.lng());
		var request = {
			origin: start,
			destination: end,
			travelMode: google.maps.TravelMode.BICYCLING,
			waypoints: this.waypoints
		};
		this.planRoute(request);
	},

	/**
	 *
	 */
	planRoute: function(request) {
		var self = this;
		this.directionsService.route(request, function (response, status) {
			switch(status)  {
				case google.maps.DirectionsStatus.OK:
					console.info('Route ok');
					self.directionsDisplay.setDirections(response);
					self.directionsDisplay.setMap(self.map);
					break;
				case google.maps.DirectionsStatus.ZERO_RESULTS:
					console.info('Zero result, skipping waypoints and redraw without it');
					request.waypoints = [];
					$.proxy(self.planRoute(request), self);
					break;
				default:
					alert("Directions Request from " + start.toUrlValue(6) + " to " + end.toUrlValue(6) + " failed: " + status);
			}
		});
	},

	onGetLocationClick: function () {
		this.model.getLocation();
	},

	locationChanged: function () {
		this.initGoogleMaps();
	},

	addMapEvents: function() {
		var self = this;
		google.maps.event.addListener(this.map, 'click', function(event) {
			$.proxy(self.addMarker(event.latLng), self);
		});
	},

	addMarker: function (locationInfo) {
		console.info('Adding marker');
		var marker = new google.maps.Marker({
			position: locationInfo,
			map: this.map
		});

		if (this.markers.length > 1 ) {
			console.info('Adding Waypoint');
			this.addWayPoint(locationInfo);
		}
		else {
			console.info('Adding end position');
			this.markers.push({
				lat: locationInfo.lat(),
				lng: locationInfo.lng()
			});
		}

		this.calcRoute();
	},

	addWayPoint: function(locationInfo) {
		this.waypoints.push({
			location: new google.maps.LatLng(locationInfo.lat(), locationInfo.lng())
		})
	}

});

module.exports = LocationView;