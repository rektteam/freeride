var Backbone = require('backbone');
var $ = require('jquery');

var Location = Backbone.Model.extend({

	// Communication url of the model
	url: 'routeplanner.php',

	attributes: {
		startingPoint: undefined,
		endPoint: undefined,
		waypoints: [],
		justClosest: false
	},

	initialize: function() {
		this.getLocation();
	},

	/**
	 * Retrieves the user's location and save it as an attribute
	 *
	 * @method getLocation
	 *
	 * @return void;
	 */
	getLocation: function() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition($.proxy(this.savePosition, this));
		}
		else {
			alert('Your browser does not support geolocation');
		}
	},

	/**
	 * Saves starting point and triggers an event
	 * about the attributes changes
	 *
	 * @param locationData
	 *
	 * @return void;
	 */
	savePosition: function(locationData) {
		this.attributes = locationData;
		this.attributes.startingPoint = {
			lat: locationData.coords.latitude,
			lng: locationData.coords.longitude
		};

		this.trigger('init-google-maps');
	},

	/**
	 * Returns with the destination point coordinates
	 *
	 * @method getEndPoint
	 *
	 * @returns {Object} endPoint       Latitude and Longitude
	 *
	 */
	getEndPoint: function() {
		return this.attributes.endPoint;
	},

	/**
	 * Returns with the starting point coordinates
	 *
	 * @method getStartingPoint
	 *
	 * @returns {Object} startingPoint       Latitude and Longitude
	 *
	 */
	getStartingPoint: function() {
		return this.attributes.startingPoint;
	},

	/**
	 * Sends an ajax call to the backend to get
	 * back the waypoints
	 *
	 * @method getWayPoints
	 *
	 * @return void;
	 */
	getWayPoints: function() {
		var self = this;
		var data = {
			currentPositionLat: this.attributes.startingPoint.lat,
			currentPositionLng: this.attributes.startingPoint.lng,
			destinationPositionLat: this.attributes.endPoint.lat,
			destinationPositionLng: this.attributes.endPoint.lng
		};
		this.trigger('show-progress');
		$.ajax({
			type: 'post',
			url: this.url,
			data: data,
			success: function(response) {
				self.attributes.waypoints = response.stations;
				self.convertWayPoints();
				self.trigger('show-waypoints');
			}
		})
	},

	/**
	 * Convert waypoints provided by the backend to a usable format
	 * for google maps, then set it to the model as waypoint attribute
	 *
	 * @method convertWaypoints
	 *
	 * @return void;
	 */
	convertWayPoints: function() {
		var convertedWayPoints = [];

		for (var i = 0; i < this.attributes.waypoints.length; i++) {
			var waypoint = this.attributes.waypoints[i];
			console.info(waypoint);
			convertedWayPoints.push({
				location: new google.maps.LatLng(waypoint.lat, waypoint.lng)
			});
		}
		this.attributes.waypoints = convertedWayPoints;
		console.info(convertedWayPoints);
	}


});

module.exports = Location;