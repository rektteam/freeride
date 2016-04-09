var gulp = require('gulp'),
	webpack = require('gulp-webpack'),
	sass = require('gulp-sass'),
	size = require('gulp-size'),
	prefix = require('gulp-autoprefixer'),
	rename = require('gulp-rename'),
	cssmin      = require('gulp-minify-css'),
	livereload = require('gulp-livereload'),
	concat = require('gulp-concat'),
	clean = require('gulp-clean'),
	uglify = require('gulp-uglify');

var jsPaths = [
		'./source/js/*.js',
		'./source/js/collection/*.js',
		'./source/js/view/*.js',
		'./source/js/model/*.js',
		'./source/js/vendor/*.js'
];

gulp.task('webpack', function() {
	return gulp.src(jsPaths)
		.pipe(webpack({
			entry: './source/js/entry.js',
			output: {
				filename: 'simple.js'
			},
			module: {
				loaders: [
					{ test: /\.css$/, loader: "style!css" }
				]
			}
		}))
		.pipe(uglify())
		.pipe(gulp.dest('./public/'));
});

gulp.task('scss', function() {
	return gulp.src('./source/css/*.scss')
		.pipe(concat('simple.scss'))
		.pipe(gulp.dest('./tmp/'))
		.pipe(sass())
		.pipe(size({ gzip: true, showFiles: true }))
		.pipe(prefix())
		.pipe(cssmin())
		.pipe(size({ gzip: true, showFiles: true }))
		.pipe(rename({ suffix: '.min' }))
		.pipe(gulp.dest('public'))
		.pipe(livereload())
});

gulp.task('watch', function() {
	livereload.listen();
	gulp.watch('./source/css/*.scss', ['scss']);
	gulp.watch('./source/js/**/*.js', ['webpack']);
});

gulp.task('default', ['webpack', 'scss', 'watch']);
gulp.task('js', ['webpack', 'watch']);
gulp.task('css', ['scss', 'watch']);