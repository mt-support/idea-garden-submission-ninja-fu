'use strict';
module.exports = function(grunt) {

  grunt.initConfig({

    // watch for changes and trigger sass, jshint, uglify and livereload
    watch: {
      css: {
        files: ['styles/scss/**/*.scss'],
        tasks: ['sass', 'autoprefixer', 'cssmin'],
      },
      js: {
        files: 'js/**/*.js',
        tasks: ['jshint', 'uglify'],
      },
      images: {
        files: ['images/src/*.{png,jpg,gif,svg}'],
        tasks: ['imagemin'],
      },
      options: {
        reload: true
      },
    },

    // sass
    sass: {
      dist: {
        options: {
          style: 'expanded',
          trace: true,
        },
        files: {
          'styles/css/style.css': 'styles/scss/style.scss'
        }
      }
    },

    // autoprefixer
    autoprefixer: {
      options: {
        browsers: 'last 2 versions',
      },
      files: {
        expand: true,
        flatten: true,
        src: 'styles/css/*.css',
        dest: 'styles/css/',
      },
    },

    // css minify
    cssmin: {
      options: {
        sourceMap: false
      },
      minify: {
        expand: true,
        src: ['styles/css/style.css', '.min.css'],
        dest: 'styles/css/',
        ext: '.css'
      }
    },

    // javascript linting with jshint
    jshint: {
        options: {
          jshintrc: '.jshintrc',
          "force": true
        },
        all: [
          'Gruntfile.js',
          'js/**/*.js'
        ]
    },

    // uglify to concat, minify, and make source maps
    uglify: {
      src: 'js/scripts.js',
      dest: 'js/scripts-min.js'
    },

    // image optimization
    imagemin: {
      dist: {
        options: {
          optimizationLevel: 7,
          progressive: true,
          interlaced: true
        },
        files: [{
          expand: true,
          cwd: 'images/src/',
          src: ['**/*.{png,jpg,gif,svg}'],
          dest: 'images/'
        }]
      }
    },

  });

  grunt.loadNpmTasks('grunt-autoprefixer');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-imagemin');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // register task
  grunt.registerTask('default', ['watch']);
  grunt.registerTask('build', ['sass', 'autoprefixer', 'cssmin', 'uglify', 'imagemin']); 

};