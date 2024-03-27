<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining an iiif responsive image style entity type.
 */
interface IiifResponsiveImageStyleInterface extends ConfigEntityInterface {

  /**
   * The machine name for the empty image breakpoint image style option.
   */
  const EMPTY_IMAGE = '_empty image_';

  /**
   * The machine name for the original image breakpoint image style option.
   */
  const ORIGINAL_IMAGE = '_original image_';

}
