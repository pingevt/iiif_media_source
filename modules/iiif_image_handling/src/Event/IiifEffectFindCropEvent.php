<?php

declare(strict_types=1);

namespace Drupal\iiif_image_handling\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Event to allow altering of a IiifImage Url.
 */
class IiifEffectFindCropEvent extends Event {

  // This makes it easier for subscribers to reliably use our event name.
  const EVENT_NAME = 'iiif_effect_find_crop';

  /**
   * The crop.
   *
   * @var mixed
   */
  private $crop;

  /**
   * The image.
   *
   * @var mixed
   */
  private $image;

  /**
   * The crop type.
   *
   * @var mixed
   */
  private $cropType;

  /**
   * The context.
   *
   * @var mixed
   */
  private $context;

  /**
   * Constructs the object.
   *
   * @param mixed $crop
   * @param mixed $image
   * @param mixed $crop_type
   * @param mixed $context
   */
  public function __construct($crop, $image, $crop_type, $context) {
    $this->crop = $crop;
    $this->image = $image;
    $this->cropType = $crop_type;
    $this->context = $context;
  }

  /**
   * Gets the crop.
   *
   * @return mixed
   */
  public function getCrop() {
    return $this->crop;
  }

  /**
   * Gets the image.
   *
   * @return mixed
   */
  public function getImage() {
    return $this->image;
  }

  /**
   * Gets the crop type.
   *
   * @return mixed
   */
  public function getCropType() {
    return $this->cropType;
  }

  /**
   * Gets the context.
   *
   * @return mixed
   */
  public function getContext() {
    return $this->context;
  }

}
