var Backbone = require('backbone');
var $ = require('jquery');

var LocationView = Backbone.View.extend({

	// Main element of the view
	el: 'body',
	// Google maps id
	googleMapsId: '#google-maps',
	// This array will contain the markers
	markers: [],

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
		var request = {
			origin: start,
			destination: end,
			travelMode: google.maps.TravelMode.DRIVING
		};
		this.directionsService.route(request, function (response, status) {
			if (status == google.maps.DirectionsStatus.OK) {
				self.directionsDisplay.setDirections(response);
				self.directionsDisplay.setMap(map);
			} else {
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
		console.info(locationInfo);
		var marker = new google.maps.Marker({
			position: locationInfo,
			map: this.map
		});
		this.markers.push({
			lat: locationInfo.lat(),
			lng: locationInfo.lng()
		});
		console.info(this.markers);
		this.calcRoute();
	}

});

module.exports = LocationView;