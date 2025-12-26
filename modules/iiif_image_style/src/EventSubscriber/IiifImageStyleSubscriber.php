<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\EventSubscriber;

use Drupal\iiif_image_style\Event\IiifImageStyleSettingsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscribes to IIIF Image Style settings events.
 */
class IiifImageStyleSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      IiifImageStyleSettingsEvent::EVENT_NAME => 'imgStyleSettingsAlter',
    ];
  }

  /**
   * Alters the IIIF Image Style settings.
   *
   * @param \Drupal\iiif_image_style\Event\IiifImageStyleSettingsEvent $event
   *   The event object containing the image style and settings.
   */
  public function imgStyleSettingsAlter(IiifImageStyleSettingsEvent $event) {
    // @todo Implement settings alteration logic.
  }

}
