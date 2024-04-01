<?php

namespace Drupal\iiif_image_style;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;

/**
 * Trait to handle event dispatchers.
 */
trait EventsTrait {

  /**
   * The event Dispatcher.
   *
   * @var \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher
   */
  protected $eventDispatcher;

  /**
   * Gets the event Dispatcher.
   *
   * @return \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher
   */
  protected function eventDispatcher(): ContainerAwareEventDispatcher {
    if (!$this->eventDispatcher) {
      $this->eventDispatcher = \Drupal::service('event_dispatcher');

    }
    return $this->eventDispatcher;
  }

}
