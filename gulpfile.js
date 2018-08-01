var gulp             = require('gulp'),
    plumber          = require('gulp-plumber'),
    sass             = require('gulp-ruby-sass'),
    autoprefixer     = require('gulp-autoprefixer'),
    cleanCSS         = require('gulp-clean-css'),
    concat           = require('gulp-concat'),
    uglify           = require('gulp-uglify'),
    svgstore         = require('gulp-svgstore'),
    svgmin           = require('gulp-svgmin'),
    path             = require('path'),
    inject           = require('gulp-inject'),
    livereload       = require('gulp-livereload');

var pluginName          = 'idea-garden-submission-ninja-fu',
    pluginPath          = 'wp-content/plugins/' + pluginName,
	 	jsPath             = pluginPath + '/js';

/**
 * Compile main plugin styles.
 */
gulp.task('styles', function() {
	return sass( [ pluginPath + '/styles/scss/**/*', pluginPath + '/scss/*' ] )
		.pipe(autoprefixer())
		.pipe(cleanCSS())
		.pipe(gulp.dest(pluginPath))
		.pipe(livereload());
});

/**
 * Concatenate scripts
 */
gulp.task('scripts', function(){
	return gulp.src([

	])
	.pipe(concat('scripts.js'))
	.pipe(uglify())
	.pipe(gulp.dest(pluginPath + '/js'))
});

/**
 * Watch task
 */
gulp.task('watch', function(){
	livereload.listen();

	gulp.watch(pluginPath + '/styles/scss/**/*', ['styles']);
  	gulp.watch(pluginPath + '/*.php' ).on('change',function(file) {
    	livereload.changed(file.path);
	});

});

/**
 * svgmin and store
 */
gulp.task('svgstore', function(){
	return gulp.src(pluginPath + '/assets/svg/*.svg')
	.pipe(svgmin(function(file){
		var prefix= path.basename(file.relative, path.extname(file.relative));
		return {
			plugins: [
        { removeXMLProcInst: true }
			]
		}
	}))
	.pipe(svgstore())
  .pipe(gulp.dest(pluginPath + '/images'));
});

/**
 * Build scripts and styles for deploy
 */
gulp.task( 'build', ['scripts', 'styles' ]);