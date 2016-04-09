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
	// Vehicle type
	vehicleType: 'BICYCLING',

	initialize: function () {
		this.googleMapsEl = $(this.googleMapsId);

		this.model.on('init-google-maps', $.proxy(this.locationChanged, this), this);
		this.model.on('show-waypoints', $.proxy(this.showWayPoints, this), this);
	},

	/**
	 * Instantiate google maps nad paste it to the DOM,
	 * then calls event listener binding.
	 *
	 * @method initGoogleMaps
	 *
	 * @return {void};
	 */
	initGoogleMaps: function() {
		this.directionsDisplay = new google.maps.DirectionsRenderer;
		this.directionsService = new google.maps.DirectionsService;

		this.map = new google.maps.Map(this.googleMapsEl.get(0), {
			center: this.model.getStartingPoint(),
			scrollwheel: false,
			draggable: true,
			zoom: 17
		});

		this.directionsDisplay.setMap(this.map);
		this.me = new google.maps.Marker({
			map: this.map,
			position: this.model.getStartingPoint(),
			title: 'Hello World!'
		});

		window.maps = this.map;

		this.addMapEvents();
	},

	/**
	 * Creating necessary data for route calculation
	 *
	 * @method calcRoute
	 *
	 * @return void;
	 */
	calcRoute: function() {
		var map = this.map;

		var start = new google.maps.LatLng(this.model.getStartingPoint());
		var end = new google.maps.LatLng(this.model.getEndPoint());

		var bounds = new google.maps.LatLngBounds();
		bounds.extend(start);
		bounds.extend(end);
		map.fitBounds(bounds);

		var request = {
			origin: start,
			destination: end,
			travelMode: google.maps.TravelMode[this.vehicleType],
			waypoints: this.model.get('waypoints')
		};

		this.planRoute(request);
	},

	/**
	 * Draws the route to the google map.
	 *
	 * @method planRoute
	 * @param {Object} routeParams      Contains starting point, end point and waypoints
	 *
	 * @return void;
	 */
	planRoute: function(routeParams) {
		var self = this;
		this.directionsService.route(routeParams, function (response, status) {
			switch(status)  {
				case google.maps.DirectionsStatus.OK:
					console.info('Route ok');
					self.directionsDisplay.setDirections(response);
					self.directionsDisplay.setMap(self.map);
					break;
				case google.maps.DirectionsStatus.ZERO_RESULTS:
					console.info('Zero result, skipping waypoints and redraw without it');
					routeParams.waypoints = [];
					$.proxy(self.planRoute(routeParams), self);
					break;
				default:
					alert("Directions Request from " + start.toUrlValue(6) + " to " + end.toUrlValue(6) + " failed: " + status);
			}
		});
	},

	locationChanged: function () {
		this.initGoogleMaps();
	},

	/**
	 * Starts to listen to google maps events
	 *
	 * @method addMapEvents
	 *
	 * @return void;
	 */
	addMapEvents: function() {
		var self = this;
		google.maps.event.addListener(this.map, 'click', function(event) {
			$.proxy(self.addMarker(event.latLng), self);
		});
	},

	/**
	 * Places a marker to the map according to the location params.
	 * It will define the ending point of our journey
	 *
	 * @param {Object} locationInfo
	 *
	 * @return void;
	 */
	addMarker: function (locationInfo) {
		this.cleanMap();

		var marker = new google.maps.Marker({
			position: locationInfo,
			map: this.map
		});

		this.markers.push(marker);


		this.model.set('endPoint', {
			lat: locationInfo.lat(),
			lng: locationInfo.lng()
		});
		this.model.getWayPoints();
	},

	/**
	 * Will clean up the marks on the map form the markers
	 *
	 * @method cleanMap
	 *
	 * @return void;
	 */
	cleanMap: function() {
		for (var i = 0; i < this.markers.length; i++) {
			this.markers[i].setMap(this.map);
		}
	},

	/**
	 * Starts the process for calculation and drawing the route
	 *
	 * @method showWayPoints
	 *
	 * @return void;
	 */
	showWayPoints: function() {
		this.calcRoute();
	}

});

module.exports = LocationView;