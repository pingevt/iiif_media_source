<?php

namespace Drupal\iiif_image_style\EventSubscriber;

use Drupal\iiif_image_style\Event\IiifImageStyleSettingsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EntityTypeSubscriber.
 *
 * @package Drupal\iiif_image_style\EventSubscriber
 */
class IiifImageStyleSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   *
   * @return array
   *   The event names to listen for, and the methods that should be executed.
   */
  public static function getSubscribedEvents() {
    return [
      IiifImageStyleSettingsEvent::EVENT_NAME => 'imgStyleSettingsAlter',
    ];
  }

  /**
   * Alter the current IIIF Image Style.
   */
  public function imgStyleSettingsAlter(IiifImageStyleSettingsEvent $event) {

  }

}
