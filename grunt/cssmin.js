module.exports = {
  target: {
    files: [{
      expand: true,
      cwd: 'styles/build/css',
      src: ['*.css', '!*.min.css'],
      dest: 'styles/css',
      ext: '.min.css'
    }]
  }
}