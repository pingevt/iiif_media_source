<?php

namespace Drupal\iiif_image_style\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\iiif_image_style\Entity\IiifImageStyle;

/**
 * Event to allow altering of a IiifImage Style.
 */
class IiifImageStyleSettingsEvent extends Event {

  // This makes it easier for subscribers to reliably use our event name.
  const EVENT_NAME = 'iiif_image_style_settings';

  /**
   * The Image Style Entity.
   *
   * @var \Drupal\iiif_image_style\Entity\IiifImageStyle
   */
  public $imageStyle;

  /**
   * Array of settings to alter.
   *
   * @var array
   */
  public $settings;

  /**
   * Constructs the object.
   */
  public function __construct(IiifImageStyle $image_style, &$settings = []) {
    $this->imageStyle = $image_style;
    $this->settings = $settings;
  }

}
