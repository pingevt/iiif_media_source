<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an annotation object for IIIF Image Effect plugins.
 *
 * Plugin classes using this annotation must be in the
 * Drupal\iiif_image_style\Plugin\IiifImageEffect namespace.
 *
 * @Annotation
 *
 * Example:
 * @IiifImageEffect(
 *   id = "crop",
 *   label = @Translation("Crop"),
 *   description = @Translation("Crops the IIIF image.")
 * )
 */
final class IiifImageEffect extends Plugin {

  /**
   * The IIIF Image Effect plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * IIIF Image Effect label.
   *
   * @var string
   */
  public $label;

  /**
   * IIIF Image Effect description.
   *
   * @var string
   */
  public $description;

}
