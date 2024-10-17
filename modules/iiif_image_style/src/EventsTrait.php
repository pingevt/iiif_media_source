<?php

namespace Drupal\iiif_image_style;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Trait to handle event dispatchers.
 */
trait EventsTrait {

  /**
   * The event Dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcher
   */
  protected $eventDispatcher;

  /**
   * Gets the event Dispatcher.
   *
   * @return \Symfony\Component\EventDispatcher\EventDispatcher
   */
  protected function eventDispatcher(): EventDispatcher {
    if (!$this->eventDispatcher) {
      $this->eventDispatcher = \Drupal::service('event_dispatcher');

    }
    return $this->eventDispatcher;
  }

}
