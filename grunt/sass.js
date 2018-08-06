module.exports = {
  dist: {
    options: {
      // cssmin will minify later
      style: 'expanded',
    },
    files: {
      'styles/css/style.css': 'styles/scss/style.scss'
    }
  }
}