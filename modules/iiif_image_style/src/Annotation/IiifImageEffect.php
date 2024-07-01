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

  /**
   * Iiif image Effect ID.
   *
   * @var string
   */
  public $id;

  /**
   * Iiif image Effect label.
   *
   * @var string
   */
  public $label;

  /**
   * Iiif image Effect description.
   *
   * @var string
   */
  public $description;

}
