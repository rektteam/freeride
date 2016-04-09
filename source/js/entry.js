var Backbone = require('backbone');
var _ = require('underscore');
var Router = require('./router/Routing');
var LocationView = require('./view/LocationView');
var Location = require('./model/Location');
global.jQuery = require('jquery');
require('bootstrap');

new LocationView({
	model: new Location()
});

app = {
	events: _.extend({}, Backbone.Events)
};

new Router();
Backbone.history.start({pushState: true});

