<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style;

use Drupal\breakpoint\BreakpointManagerInterface;

/**
 * Trait to provide access to the breakpoint manager service.
 */
trait BreakpointManagerTrait {

  /**
   * Returns the breakpoint manager service.
   *
   * @return \Drupal\breakpoint\BreakpointManagerInterface
   *   The breakpoint manager service.
   */
  protected function getBreakpointManager(): BreakpointManagerInterface {
    return \Drupal::service('breakpoint.manager');
  }

}
