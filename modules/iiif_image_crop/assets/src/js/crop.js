// import Cropper from 'cropperjs';

console.log('hello World');


let imgs = document.querySelectorAll('.field--widget-iiif-image-crop-widget .cropper-image img');
console.log(imgs);

imgs.forEach(element => {
  // console.log(element, element.parentElement.parentElement);
  let cropWrapperInput = element.parentElement.parentElement.querySelector('.crop-wrapper input');
  console.log(cropWrapperInput);
  console.log(cropWrapperInput.value);

  let initialized = false;



  const cropper = new Cropper(element, {
    viewMode: 1,
    scalable: false,
    rotatable: false,
    // zoomable: false,
    // aspectRatio: 16 / 9,
    ready() {
      console.log("ready");
      let initData = cropWrapperInput.value.split(",");
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
      console.log("crop");

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


  // let initData = cropWrapperInput.value.split(",");
  // let initImgData = cropper.getImageData();
  // console.log(initData, initImgData);

  // let intCrop = {
  //   x: initData[0] * initImgData.naturalWidth,
  //   y: initData[1] * initImgData.naturalHeight,
  //   width: initData[2] * initImgData.naturalWidth,
  //   height: initData[3] * initImgData.naturalHeight,
  // };

  // cropper.setData(intCrop);
});
