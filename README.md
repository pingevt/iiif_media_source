# IIIF Media source

todo: write descritpion
implementing the Image API 3.0

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
  -  [ ] need to verify and have fallbacks for each section.
  - [ ] Add in loading attribute (lazy eager) to basic formatter.
- [x] Secondary (basic image) Widget includes image thumbnail.
- [x] Secondary (basic image) Formatter, should implement and validate all the uri options.
- [ ] Do we need the base Iiif class?
  - [ ] If so, needs to be a service.
  - [ ] Inject it intot he field class.

Image Styles / Responsive Images
- [x] Image Style entity
- [x] Responsive Image style entity
- [ ] IIIF Image style formatter
- [ ] IIIF Responsive Image style formatter
- [ ] Add in thirdparty setting for focal point
- [ ] Add in thirdparty setting for custom crop


Media Source
- [ ] Source just provides data for fields, if wanted on the media item.
- [ ] Provides:
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

Submodule: Focalpoint
- [ ] Add in widget to define a focal point of the image.

Submodule: Crop
- [ ] Add in widget to define a crop for the image.

Tests:
-
