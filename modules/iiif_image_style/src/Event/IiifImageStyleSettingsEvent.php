<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\iiif_image_style\Entity\IiifImageStyle;

/**
 * Event to allow altering of a IiifImage Style.
 */
class IiifImageStyleSettingsEvent extends Event {

  /**
   * Event name for subscribers.
   */
  public const EVENT_NAME = 'iiif_image_style_settings';

  /**
   * The Image Style Entity.
   *
   * @var \Drupal\iiif_image_style\Entity\IiifImageStyle
   */
  private IiifImageStyle $imageStyle;

  /**
   * Array of settings to alter.
   *
   * @var array
   */
  private array $settings;

  /**
   * Constructs the event object.
   *
   * @param \Drupal\iiif_image_style\Entity\IiifImageStyle $image_style
   *   The IIIF image style entity.
   * @param array $settings
   *   The settings array to alter.
   */
  public function __construct(IiifImageStyle $image_style, array $settings = []) {
    $this->imageStyle = $image_style;
    $this->settings = $settings;
  }

  /**
   * Gets the IIIF image style entity.
   *
   * @return \Drupal\iiif_image_style\Entity\IiifImageStyle
   *   The IIIF image style entity.
   */
  public function getImageStyle(): IiifImageStyle {
    return $this->imageStyle;
  }

  /**
   * Gets the settings array.
   *
   * @return array
   *   The settings array.
   */
  public function getSettings(): array {
    return $this->settings;
  }

  /**
   * Sets the settings array.
   *
   * @param array $settings
   *   The new settings array.
   *
   * @return $this
   */
  public function setSettings(array $settings): static {
    $this->settings = $settings;

    // todo: Add in some validation in here.
    return $this;
  }

}
