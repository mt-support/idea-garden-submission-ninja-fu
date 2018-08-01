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

/**
 * Compile main plugin styles.
 */
gulp.task('styles', function() {
	return sass( [ '/styles/scss/**/*', 'styles/scss/*' ] )
		.pipe(autoprefixer())
		.pipe(cleanCSS())
		.pipe(gulp.dest())
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
	.pipe(gulp.dest('/js'))
});

/**
 * Watch task
 */
gulp.task('watch', function(){
	livereload.listen();

	gulp.watch('/styles/scss/**/*', ['styles']);
  	gulp.watch('/php/*.php' ).on('change',function(file) {
    	livereload.changed(file.path);
	});

});

/**
 * svgmin and store
 */
gulp.task('svgstore', function(){
	return gulp.src('/images/svg/*.svg')
	.pipe(svgmin(function(file){
		var prefix= path.basename(file.relative, path.extname(file.relative));
		return {
			plugins: [
        { removeXMLProcInst: true }
			]
		}
	}))
	.pipe(svgstore())
  .pipe(gulp.dest('/images'));
});

/**
 * Build scripts and styles for deploy
 */
gulp.task( 'build', ['scripts', 'styles' ]);