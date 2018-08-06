module.exports = {
  defaults: {
    options: {
      prefix : 'shape-',
      formatting : {
        indent_size : 2
      }
    },
    files: {
      'lib/images/svg/dist/svg-defs.svg': ['lib/images/svg/source/*.svg']
    },
  },
}