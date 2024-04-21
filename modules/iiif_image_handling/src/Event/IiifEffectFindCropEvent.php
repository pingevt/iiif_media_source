<?php

namespace Drupal\iiif_image_handling\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Event to allow altering of a IiifImage Url.
 */
class IiifEffectFindCropEvent extends Event {

  // This makes it easier for subscribers to reliably use our event name.
  const EVENT_NAME = 'iiif_effect_find_crop';

  /**
   *
   */
  public $crop;

  /**
   *
   */
  public $image;

  /**
   *
   */
  public $cropType;

  /**
   *
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
