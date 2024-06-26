<?php

namespace Drupal\iiif_media_source\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Plugin\Field\FieldType\IiifId;

/**
 * Event to allow altering of a IiifImage Url.
 */
class IiifGetImageFromFieldEvent extends Event {

  // This makes it easier for subscribers to reliably use our event name.
  const EVENT_NAME = 'iiif_image_from_field';

  /**
   * The field the image belongs to.
   *
   * @var \Drupal\iiif_media_source\Plugin\Field\FieldType\IiifId
   */
  public $field;

  /**
   * The IIIF Image class.
   *
   * @var \Drupal\iiif_media_source\Iiif\IiifImage
   */
  public $image;

  /**
   * The given values of the field.
   *
   * @var array
   */
  public $values;

  /**
   * Constructs the object.
   */
  public function __construct(IiifId $field, IiifImage $image, array $values) {
    $this->field = $field;
    $this->image = $image;
    $this->values = $values;
  }

}
