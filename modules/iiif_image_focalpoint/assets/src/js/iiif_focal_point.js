/**
 * @file
 * Javascript functionality for the focal point widget.
 */

(function ($, Drupal) {
  'use strict';

  let fps = [];

  /**
   * Focal Point indicator.
   */
  Drupal.behaviors.iiifFocalPointIndicator = {
    attach: function (context) {

      once('iiif-focal-point-hide-field', '.focal-point', context).forEach(function(el) {
        var $wrapper = $(el).closest('.iiif-focal-point-wrapper');
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

      once('iiif-focal-point-hide-field', '.iiif-focal-point-indicator', context).forEach(function(el) {

        // Set some variables for the different pieces at play.
        var $indicator = $(el);
        var $img = $(el).siblings('img');
        var $previewLink = $(el).siblings('.iiif-focal-point-preview-link');
        var $field = $("." + $(el).attr('data-selector'));
        var fp = new Drupal.IiifFocalPoint($indicator, $img, $field, $previewLink);

        fps.push(fp);

        // Set the position of the indicator on image load and any time the
        // field value changes. We use a bit of hackery to make certain that the
        // image is loaded before moving the crosshair. See http://goo.gl/B02vFO
        // The setTimeout was added to ensure the focal point is set properly on
        // modal windows. See http://goo.gl/s73ge.
        setTimeout(function () {
          $img.one('load', function () {
            fp.setIndicator();
          }).each(function () {
            if (this.complete) {
              $(this).trigger('load');
            }
          });
        }, 0);

      });
    }

  };

  /**
   * Object representing the focal point for a given image.
   *
   * @param $indicator object
   *   The indicator jQuery object whose position should be set.
   * @param $img object
   *   The image jQuery object to which the indicator is attached.
   * @param $field array
   *   The field jQuery object where the position can be found.
   * @param $previewLink object
   *   The previewLink jQuery object.
   */
  Drupal.IiifFocalPoint = function ($indicator, $img, $field, $previewLink) {
    var self = this;

    this.$indicator = $indicator;
    this.$img = $img;
    this.$field = $field;
    this.$previewLink = $previewLink;

    // Make the focal point indicator draggable and tell it to update the
    // appropriate field when it is moved by the user.
    this.$indicator.draggable({
      containment: self.$img,
      stop: function () {
        var imgOffset = self.$img.offset();
        var iiifFocalPointOffset = self.$indicator.offset();

        var leftDelta = iiifFocalPointOffset.left - imgOffset.left;
        var topDelta = iiifFocalPointOffset.top - imgOffset.top;

        self.set(leftDelta, topDelta);
      }
    });

    // Allow users to double-click the indicator to reveal the focal point form
    // element.
    this.$indicator.on('dblclick', function () {
      self.$field.closest('.iiif-focal-point-wrapper').toggleClass('visually-hidden');
    });

    // Allow users to click on the image preview in order to set the focalpoint
    // and set a cursor.
    this.$img.on('click', function (event) {
      self.set(event.offsetX, event.offsetY);
    });
    this.$img.css('cursor', 'crosshair');

    // Add a change event to the focal point field so it will properly update
    // the indicator position and preview link.
    this.$field.on('change', function () {
     $(document).trigger('drupalIiifFocalPointSet', { $iiifFocalPoint: self });
    });

    // Wrap the focal point indicator and thumbnail image in a div so that
    // everything still works with RTL languages.
    this.$indicator.add(this.$img).add(this.$previewLink).wrapAll("<div class='iiif-focal-point-wrapper' />");
  };

  /**
   * Set the focal point.
   *
   * @param offsetX int
   *   Left offset in pixels.
   * @param offsetY int
   *   Top offset in pixels.
   */
  Drupal.IiifFocalPoint.prototype.set = function (offsetX, offsetY) {
    var iiifFocalPoint = this.calculate(offsetX, offsetY);
    this.$field.val(iiifFocalPoint.x + ',' + iiifFocalPoint.y).trigger('change');

    $(document).trigger('drupalIiifFocalPointSet', { $iiifFocalPoint: this });
  };

  /**
   * Change the position of the focal point indicator. This may not work in IE7.
   */
  Drupal.IiifFocalPoint.prototype.setIndicator = function () {
    var coordinates = this.$field.val() !== '' && this.$field.val() !== undefined ? this.$field.val().split(',') : [50,50];

    var left = Math.min(this.$img.width(), (parseInt(coordinates[0], 10) / 100) * this.$img.width());
    var top = Math.min(this.$img.height(), (parseInt(coordinates[1], 10) / 100) * this.$img.height());

    this.$indicator.css('left', Math.max(0, left));
    this.$indicator.css('top', Math.max(0,top));
    this.$field.val(coordinates[0] + ',' + coordinates[1]);
  };

  /**
   * Calculate the focal point for the given image.
   *
   * @param offsetX int
   *   Left offset in pixels.
   * @param offsetY int
   *   Top offset in pixels.
   *
   * @returns object
   */
  Drupal.IiifFocalPoint.prototype.calculate = function (offsetX, offsetY) {
    var iiifFocalPoint = {};
    iiifFocalPoint.x = this.round(100 * offsetX / this.$img.width(), 0, 100);
    iiifFocalPoint.y = this.round(100 * offsetY / this.$img.height(), 0, 100);

    return iiifFocalPoint;
  };

  /**
   * Rounds the given value to the nearest integer within the given bounds.
   *
   * @param value float
   *   The value to round.
   * @param min int
   *   The lower bound.
   * @param max int
   *   The upper bound.
   *
   * @returns int
   */
  Drupal.IiifFocalPoint.prototype.round = function (value, min, max) {
    var roundedVal = Math.max(Math.round(value), min);
    roundedVal = Math.min(roundedVal, max);

    return roundedVal;
  };

  /**
   * Update the Focal Point indicator and preview link when focal point changes.
   *
   * @param {jQuery.Event} event
   *   The `drupalIiifFocalPointSet` event.
   * @param {object} data
   *   An object containing the data relevant to the event.
   *
   * @listens event:drupalIiifFocalPointSet
   */
  $(document).on('drupalIiifFocalPointSet', function (event, data) {
    data.$iiifFocalPoint.setIndicator();
  });

  /**
   * When resizing, update indicators.
   */
  $(window).on('resize', function (event, data) {
    fps.forEach((fp) => {
      $(document).trigger('drupalIiifFocalPointSet', { $iiifFocalPoint: fp });
    });
  });

  /**
   * If FP is in a tab we need to reset when tab is opened.
   */
  // Todo: narrow this down
  $(window).on('click', function (event, data) {
    fps.forEach((fp) => {
      $(document).trigger('drupalIiifFocalPointSet', { $iiifFocalPoint: fp });
    });
  });
})(jQuery, Drupal);
