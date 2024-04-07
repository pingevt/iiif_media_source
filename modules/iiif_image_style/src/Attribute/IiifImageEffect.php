<?php

declare(strict_types=1);

namespace Drupal\iiif_image_style\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * The Block attribute.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class IiifImageEffect extends Plugin {

  /**
   *
   */
  public function __construct(
    public readonly string $id,
    public readonly ?TranslatableMarkup $label = NULL,
    public readonly ?TranslatableMarkup $description = NULL,
  ) {}

}
