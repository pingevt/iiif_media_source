<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style;

use Drupal\breakpoint\BreakpointManagerInterface;

trait BreakpointManagerTrait {

  /**
   * Returns the breakpoint manager service.
   *
   * @return \Drupal\breakpoint\BreakpointManagerInterface
   */
  protected function getBreakpointManager(): BreakpointManagerInterface {
    return \Drupal::service('breakpoint.manager');
  }
}
