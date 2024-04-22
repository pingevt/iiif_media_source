# IIIF Media source

todo: write description
implementing the Image API 3.0

Crop (Not Crop API entity): Primarily used for redefining the source image.
Focal Point: Used primarily for "Art Direction" when automatically sizing images so we don't loose focus

## Table of contents

- Requirements
- ~~Recommended modules~~
- Installation
- Configuration
- Troubleshooting
- FAQ
- Maintainers
- Changelog

## Requirements

todo: add reqs.

## Installation

Install as you would normally install a contributed Drupal module.

## Configuration

todo: write configuration.

## Troubleshooting

todo:

## FAQ

**Q:** What kind of questions are being asked?

**A:** i dunno...

## Maintainers

- Pete Inge - [pingevt](https://www.drupal.org/u/pingevt)

## Changelog

## TODOs

Field:
- [x] Field - should just be a copy of a text field
  - [x] Field has settings for server/prefix
  - [ ] Could add in some validation? but not sure what that would be... https://iiif.io/api/image/3.0/#2-uri-syntax
- [x] Default Widget should just be plain text, i think.
- [x] Default Formatter should just display ID.
  - [ ] need to verify and have fallbacks for each section.
  - [ ] Add in loading attribute (lazy, eager) to basic formatter.
- [x] Secondary (basic image) Widget includes image thumbnail.
- [x] Secondary (basic image) Formatter, should implement and validate all the uri options.
- [ ] Do we need the base Iiif class?
  - [ ] If so, needs to be a service?
  - [ ] Inject it into the field class?

Image Styles / Responsive Images
- [x] Image Style entity
- [x] Responsive Image style entity
- [x] IIIF Image style formatter
- [x] IIIF Responsive Image style formatter
- [x] We're going to need plugins... Imagestyle with plugins for the transformers.
- [ ] Add in some default IIIF Image Styles
- [ ] Admin librabry w/ CSS.
- [ ] Add in preview on Image Styles.
- [ ] Document plugin so others can create plugins.
- [ ] Documentation and helper text for forms.
- [ ] Fix errors when using "original image" for responsive images

Media Source
- [ ] Source just provides data for fields, if wanted on the media item.
- [ ] Provides:
  - [ ] version
  - [ ] width
  - [ ] height
  - [ ] sizes
  - [ ] tiles???
  - [ ] formats
  - [ ] qualities
  - [ ] maxArea
  - [ ] maxHeight
  - [ ] maxWidth
  - [ ] supports

Submodule: Image Handling
- [x] Class to handle form elements for adding to widgets.
  - [x] Need logic for 1 or other handlers.
- [ ] Class to handle form elements for own widgets.
- [ ] Need a common (CSS) library.

Submodule: Focalpoint
- [x] Add in widget to define a focal point of the image.
- [x] Thirdparty settings or something so we can combine everything into 1 form element.
- [x] Add thirdparty settings for thumbnail size
- [ ] Allow for Contextual Media field

Submodule: Crop
- [x] Add in widget to define a crop for the image.
- [x] Thirdparty settings or something so we can combine everything into 1 form element.
- [x] Add thirdparty settings for thumbnail size
- [x] "Drupalize" js file.
- [x] Need JS solution to hide field (copy from FP)
- [ ] Settings Page to variabalize settings for the cropper.js plugin.
- [ ] Allow for Contextual Media field

General:
- [ ] Check and confirm Config inspector
- [ ] Process js/css files

Tests:
- Unit Tests
  - [ ] Need to test and finish Dimension in `IiifImageUrlParams`
- Functional Tests
- Browser Tests
