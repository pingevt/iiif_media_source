<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * The IiifImageEffect plugin.
 *
 * @Annotation
 */
final class IiifImageEffect extends Plugin {

  public $id;
  public $label;
  public $description;

}
