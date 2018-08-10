module.exports = {

  prod: {
    options: {
      port: 8001,
      base: '/',
      livereload: true,
      open: {
        target: 'http://tribe.events:8888',
        callback: function() {}
      },
    }
  }
  
}