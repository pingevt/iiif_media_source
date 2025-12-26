<?php

namespace Drupal\iiif_media_source\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Plugin\Field\FieldType\IiifId;

/**
 * Event to allow altering of a IiifImage object created from a field.
 *
 * This event is dispatched when a IIIF image object is created from a field
 * item, allowing subscribers to alter the image object or related data.
 */
class IiifGetImageFromFieldEvent extends Event {

  /**
   * The event name for subscribers.
   */
  const EVENT_NAME = 'iiif_image_from_field';

  /**
   * The field item the image belongs to.
   *
   * @var \Drupal\iiif_media_source\Plugin\Field\FieldType\IiifId
   */
  protected $field;

  /**
   * The IIIF Image object.
   *
   * @var \Drupal\iiif_media_source\Iiif\IiifImage
   */
  protected $iiifImage;

  /**
   * The values of the field item.
   *
   * @var array
   */
  protected $values;

  /**
   * Constructs a new IiifGetImageFromFieldEvent object.
   *
   * @param \Drupal\iiif_media_source\Plugin\Field\FieldType\IiifId $field
   *   The field item instance.
   * @param \Drupal\iiif_media_source\Iiif\IiifImage $image
   *   The IIIF image object.
   * @param array $values
   *   The field item values.
   */
  public function __construct(IiifId $field, IiifImage $image, array $values) {
    $this->field = $field;
    $this->iiifImage = $image;
    $this->values = $values;
  }

  /**
   * Gets the field item instance.
   *
   * @return \Drupal\iiif_media_source\Plugin\Field\FieldType\IiifId
   *   The field item.
   */
  public function getFieldItem() {
    return $this->field;
  }

  /**
   * Gets the IIIF image object.
   *
   * @return \Drupal\iiif_media_source\Iiif\IiifImage
   *   The IIIF image object.
   */
  public function getIiifImage() {
    return $this->iiifImage;
  }

  /**
   * Gets the field item values.
   *
   * @return array
   *   The field item values.
   */
  public function getValues() {
    return $this->values;
  }

}
