<?php

namespace Drupal\iiif_media_source\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Event to allow altering of a IiifImage Url.
 */
class IiiifGetImageFromFieldEvent extends Event {

  // This makes it easier for subscribers to reliably use our event name.
  const EVENT_NAME = 'iiif_image_from_field';

  /**
   *
   */
  public $field;

  /**
   *
   */
  public $image;

  /**
   *
   */
  public $values;

  /**
   * Constructs the object.
   */
  public function __construct($field, $image, $values) {
    $this->field = $field;
    $this->image = $image;
    $this->values = $values;
  }

}
