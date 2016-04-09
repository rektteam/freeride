var Backbone = require('backbone');
var $ = require('jquery');

var LocationView = Backbone.View.extend({

	// Main element of the view
	el: 'body',
	// Google maps id
	googleMapsId: '#google-maps',

	events: {
		'click .get-location' : 'onGetLocationClick'
	},

	initialize: function () {
		this.googleMapsEl = $(this.googleMapsId);

		this.model.on('change', $.proxy(this.locationChanged, this), this);
		this.onGetLocationClick();
	},

	initGoogleMaps: function() {
		var directionsDisplay = new google.maps.DirectionsRenderer;

		this.map = new google.maps.Map(this.googleMapsEl.get(0), {
			center: {lat: this.model.getLatitude(), lng: this.model.getLongitude()},
			scrollwheel: false,
			draggable: true,
			zoom: 17
		});

		directionsDisplay.setMap(this.map);
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
		console.info('#addMarker');
		console.info(locationInfo);
		var marker = new google.maps.Marker({
			position: locationInfo,
			map: this.map
		});
	}

});

module.exports = LocationView;