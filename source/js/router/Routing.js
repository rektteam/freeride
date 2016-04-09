var Backbone = require('backbone');

var Router = Backbone.Router.extend({

	routes: {
		"modal":                 "onModal"
	},

	onModal: function() {
		$('#myModal').modal();
		console.info('onMyModal');
	},

	search: function(query, page) {

	}

});

module.exports = Router;