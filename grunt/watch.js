module.exports = {
  options: {
    reload: true,
  },
  scripts: {
    files: ['js/*.js'],
    tasks: ['concat', 'uglify'],
    options: {
      spawn: false,
    },
  },
  css: {
    files: ['styles/scss/*.scss','styles/scss/**/*.scss' ],
    tasks: ['sass', 'autoprefixer', 'cssmin'],
    options: {
      spawn: false,
    },
  },
  images: {
    files: ['images/*.{png,jpg,gif}'],
    tasks: ['imagemin'],
    options: {
      spawn: false,
    },
  },
  svg: {
    files: ['images/svg/source/*.svg'],
    tasks: ['svgstore'],
    options: {
      spawn: false,
    },
  },
  html:{
    files: ['./**/*.html'],
    tasks: [],
    options: {
      spawn: false,
    },
  },
  php:{
    files: ['./**/*.php'],
    tasks: [],
    options: {
      spawn: false,
    },
  },
}