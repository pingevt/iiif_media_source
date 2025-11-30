<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Attribute for IIIF Image Effect plugins.
 *
 * Use this attribute to mark a class as a IIIF Image Effect plugin.
 *
 * Example:
 * #[IiifImageEffect(
 *   id: "crop",
 *   label: new TranslatableMarkup("Crop"),
 *   description: new TranslatableMarkup("Crops the IIIF image.")
 * )]
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class IiifImageEffect extends Plugin {

  /**
   * {@inheritdoc}
   */
  public function __construct(
    public readonly string $id,
    public readonly ?TranslatableMarkup $label = NULL,
    public readonly ?TranslatableMarkup $description = NULL,
  ) {}

}
