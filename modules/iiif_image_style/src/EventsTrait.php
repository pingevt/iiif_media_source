<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Trait to handle event dispatchers.
 */
trait EventsTrait {

  /**
   * The event Dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected ?EventDispatcherInterface $eventDispatcher = NULL;

  /**
   * Gets the event Dispatcher.
   *
   * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
   *   The event Dispatcher.
   */
  protected function eventDispatcher(): EventDispatcherInterface {
    if (!$this->eventDispatcher) {
      $this->eventDispatcher = \Drupal::service('event_dispatcher');

    }
    return $this->eventDispatcher;
  }

}
