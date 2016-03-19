var elixir  = require('laravel-elixir');
var Promise = require('es6-promise').Promise;

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
	mix.styles([
		'app.css',
	]);

	mix.scripts([
		'collect.js',
		'string-format.js',
		'manage-motd.js',
		'manage-items.js',
	]);

    mix.phpUnit();
});
