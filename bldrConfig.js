
let drupal_modules = [
  "./",
  "./modules/iiif_image_contextual_media_adapter/",
  "./modules/iiif_image_crop/",
  "./modules/iiif_image_focalpoint/",
  "./modules/iiif_image_handling/",
  "./modules/iiif_image_style/",
];

let css_config = [];
let sass_config = [];
let js_config = [];
let images_config = [];

drupal_modules.forEach((val, i) => {
  css_config.push({
    src: val + 'assets/src/css/**/*.css',
    dest: val + 'assets/dist/css',
    watch: [val + 'assets/src/css/**/*.css'],
  });

  js_config.push({
    src: val + 'assets/src/js/**/*.js',
    dest: val + 'assets/dist/js',
    watch: [val + 'assets/src/js/**/*.js'],
  });

  // images_config.push({
  //   src: val + 'assets/src/images/*.{jpg,JPG,jpeg,JPEG,gif,png,svg}',
  //   dest: val + 'assets/dist/images',
  //   watch: [val + 'assets/src/images/**/*'],
  // });
});

// console.log(sass_config);

module.exports = {
  css: css_config,
  sass: sass_config,
  js: js_config,
  images: [], //images_config
  // --------------------- END BASIC CONFIG --------------------- //
  processSettings: {
    eslint: {
      forceBuildIfError: true
    },
    rollup: {
      outputOptions: {
        format: 'iife',
        sourcemap: true
      }
    }
  }
}
