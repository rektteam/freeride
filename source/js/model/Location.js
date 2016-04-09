var Backbone = require('backbone');
var $ = require('jquery');

var Location = Backbone.Model.extend({

	initialize: function() {
		this.attributes = {a: 1};
	},

	getLocation: function() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition($.proxy(this.savePosition, this));
		}
		else {
			alert('Your browser does not support geolocation');
		}
	},

	savePosition: function(locationData) {
		this.attributes = locationData;
		this.trigger('change');
	},

	getLongitude: function() {
		return this.attributes.coords.longitude;
	},

	getLatitude: function() {
		return this.attributes.coords.latitude;
	},

	getAccuracy: function() {
		return this.attributes.coords.accuracy;
	}

});

module.exports = Location;