<?php

namespace Drupal\iiif_image_handling\Event;

// phpcs:disable Drupal.Commenting.VariableComment.MissingVar

use Drupal\Component\EventDispatcher\Event;

/**
 * Event to allow altering of a IiifImage Url.
 */
class IiifEffectFindCropEvent extends Event {

  // This makes it easier for subscribers to reliably use our event name.
  const EVENT_NAME = 'iiif_effect_find_crop';

  /**
   * The crop.
   */
  public $crop;

  /**
   * The image.
   */
  public $image;

  /**
   * The crop type.
   */
  public $cropType;

  /**
   * The context.
   */
  public $context;

  /**
   * Constructs the object.
   */
  public function __construct($crop, $image, $crop_type, $context) {
    $this->crop = $crop;
    $this->image = $image;
    $this->cropType = $crop_type;
    $this->context = $context;
  }

}
