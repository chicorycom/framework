const CopyPlugin = require("copy-webpack-plugin");
const mix = require('laravel-mix')
const path =require('path')


/**
 *  Mix Asset Manager
 * ------------------
 * Laravel Mix is a wrapper around webpack for easy hook int into
 * the webpack build steps/life cycle
 */
mix.setPublicPath('public');
mix.js('src/resources/js/app.js', 'public/js')
	.sass('resources/sass/app.scss', 'public/css')

mix.webpackConfig({
	plugins: [
		new CopyPlugin({
			patterns: [],
		}),
	]
});
