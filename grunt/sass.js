module.exports = {
  dist: {
    options: {
      // cssmin will minify later
      style: 'expanded',
    },
    files: {
      'styles/css/build/*.css': 'styles/scss/style.scss'
    }
  }
}