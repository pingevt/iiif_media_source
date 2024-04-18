
console.log('hello World 42');


let imgs = document.querySelectorAll('.field--widget-iiif-image-crop-widget .cropper-image img, .field--widget-iiif-image-widget .cropper-image img');
console.log(imgs);

imgs.forEach(element => {
  // console.log(element, element.parentElement.parentElement);
  let cropWrapperInput = element.closest(".field--type-iiif-id").querySelector('.crop-wrapper input');
  console.log(cropWrapperInput);
  console.log(cropWrapperInput.value);

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
      console.log("ready");
      let initData = cropWrapperInput.value.split(",");
      // todo add this to data attrs or something to pull this and init cropper, rather than instead of waiting for cropper to be ready.
      let initImgData = this.cropper.getImageData();

      console.log(initData, initImgData);

      let intCrop = {
        x: initData[0] / 100 * initImgData.naturalWidth,
        y: initData[1] / 100 * initImgData.naturalHeight,
        width: initData[2] / 100 * initImgData.naturalWidth,
        height: initData[3] / 100 * initImgData.naturalHeight,
      };

      console.log(intCrop);

      this.cropper.setData(intCrop);

      initialized = true;
    },
    crop(event) {
      // console.log("crop");

      // console.log(event);
      // console.log(event.detail.x);
      // console.log(event.detail.y);
      // console.log(event.detail.width);
      // console.log(event.detail.height);
      // console.log(event.detail.rotate);
      // console.log(event.detail.scaleX);
      // console.log(event.detail.scaleY);
      // cropWrapperInput.value = "HI";

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
    // cropend(event) {
    //   console.log("END", event);
    //   console.log(cropper.getData());
    //   console.log(cropper.getImageData());
    // }
  });

});
