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
	vehicleType: 'WALKING',
	// Get closest veloh anchor selector
	getClosestVelohSel: '.get-closest',
	// Loading class
	loadingCls: 'loading',

	events: {
		'click .get-closest' : 'onGetClosestVelohClick'
	},

	initialize: function () {
		this.googleMapsEl = $(this.googleMapsId);

		this.model.on('init-google-maps', $.proxy(this.initGoogleMaps, this), this);
		this.model.on('show-waypoints', $.proxy(this.showWayPoints, this), this);
		this.model.on('show-progress', $.proxy(this.showProgress, this), this);
	},

	/**
	 * Shows a loader animation in the header by adding
	 * a class to the Dom element
	 *
	 * @method showProgress
	 *
	 * @return void;
	 */
	showProgress: function() {
		this.$el.addClass(this.loadingCls);
	},

	/**
	 * Hides loading animation in the header by removing
	 * a class from a DOM element
	 *
	 * @method showProgress
	 *
	 * @return void;
	 */
	hideProgress: function() {
		this.$el.removeClass(this.loadingCls);
	},

	/**
	 * Finds the nearest veloh bike around you
	 *
	 * @method onGetClosestVelohClick
	 *
	 * @return void;
	 */
	onGetClosestVelohClick: function() {
		this.model.set('justClosest', true);
		this.model.getClosestVeloh();
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
		this.directionsDisplay = new google.maps.DirectionsRenderer();
		this.directionsService = new google.maps.DirectionsService;

		this.map = new google.maps.Map(this.googleMapsEl.get(0), {
			center: this.model.getStartingPoint(),
			scrollwheel: true,
			draggable: true,
			zoom: 15
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
					self.directionsDisplay.setDirections(response);
					self.directionsDisplay.setMap(self.map);
					break;
				case google.maps.DirectionsStatus.ZERO_RESULTS:
					routeParams.waypoints = [];
					$.proxy(self.planRoute(routeParams), self);
					break;
				default:
					alert("Directions Request from " + start.toUrlValue(6) + " to " + end.toUrlValue(6) + " failed: " + status);
			}
		});
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
			map: this.map,
			draggable: true,
			animation: google.maps.Animation.DROP
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
			this.markers[i].setMap(null);
		}
		this.me.setMap(null);
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
		this.cleanMap();
		this.hideProgress();
	}

});

module.exports = LocationView;