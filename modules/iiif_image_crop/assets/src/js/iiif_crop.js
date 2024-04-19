/**
 * @file
 * Javascript functionality for the IIIF Crop widget.
 */

(function ($, Drupal) {
  'use strict';

  /**
   * Focal Point indicator.
   */
  Drupal.behaviors.iiifCrop = {
    attach: function (context) {

      once('iiif-crop-hide-field', '.crop', context).forEach(function (el) {
        console.log(el);
        var $wrapper = $(el).closest('.crop-wrapper');
        // Add the "visually-hidden" class unless the focal point offset field
        // has an error. This will show the field for everyone when there is an
        // error and for non-sighted users no matter what. We add it the
        // form item to make sure the field is focusable while
        // the entire form item is hidden for sighted users.
        if (!$(el).hasClass('error')) {
          $wrapper.addClass('visually-hidden');
          $(el).on('focus', function () {
            $wrapper.removeClass('visually-hidden');
          }).on('blur', function () {
            $wrapper.addClass('visually-hidden');
          });
        }
      });


      let imgs = document.querySelectorAll('.field--widget-iiif-image-crop-widget .cropper-image img, .field--widget-iiif-image-widget .cropper-image img');

      imgs.forEach(element => {
        let cropWrapperInput = element.closest(".field--type-iiif-id").querySelector('.crop-wrapper input');

        let initialized = false;

        const cropper = new Cropper(element, {
          viewMode: 2,
          scalable: false,
          rotatable: true,
          dragMode: 'move',
          zoomable: false,
          // aspectRatio: 16 / 9,
          // data: {},
          ready() {

            let initData = cropWrapperInput.value.split(",");
            let initImgData = this.cropper.getImageData();

            let intCrop = {
              x: initData[0] / 100 * initImgData.naturalWidth,
              y: initData[1] / 100 * initImgData.naturalHeight,
              width: initData[2] / 100 * initImgData.naturalWidth,
              height: initData[3] / 100 * initImgData.naturalHeight,
            };

            this.cropper.setData(intCrop);

            initialized = true;
          },
          crop(event) {
            if (initialized) {

              let initImgData = this.cropper.getImageData();
              let x = (event.detail.x / initImgData.naturalWidth) * 100;
              let y = (event.detail.y / initImgData.naturalHeight) * 100;
              let w = (event.detail.width / initImgData.naturalWidth) * 100;
              let h = (event.detail.height / initImgData.naturalHeight) * 100;

              let d = [
                x.toFixed(2),
                y.toFixed(2),
                w.toFixed(2),
                h.toFixed(2),
              ];

              cropWrapperInput.value = d.join(",");
            }
          },
        });
      });
    }
  };
})(jQuery, Drupal);
