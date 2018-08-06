'use strict';
module.exports = function(grunt) {

  // Tasks are configured in partials located in grunt/
  require('load-grunt-config')(grunt);
  
  // By default, connect to a server and watch for changed files
  grunt.registerTask('default', ['connect:prod','watch']);
  
  // Let's process our development files for Production
  grunt.registerTask('process', ['concat', 'uglify', 'sass', 'autoprefixer', 'cssmin', 'imagemin']);
  
  // Or process SASS by itself
  // Now, let's move things to the Production directory
  grunt.registerTask('css', ['sass', 'autoprefixer', 'cssmin']);
  
  // Or process JS by itself
  grunt.registerTask('js', ['concat', 'uglify']);
  
  // Turn those SVGs into a sprite
  grunt.registerTask('svg', ['svgstore']);

};