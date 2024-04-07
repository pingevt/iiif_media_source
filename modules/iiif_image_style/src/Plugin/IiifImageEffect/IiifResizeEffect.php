<?php

namespace Drupal\iiif_image_style\Plugin\IiifImageEffect;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\iiif_image_style\Attribute\IiifImageEffect;
use Drupal\iiif_image_style\IiifImageEffectBase;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;

/**
 *
 *
 * @IiifImageEffect(
 *   id = "iiif_image_resize",
 *   label = "IIIF Image API resize",
 *   description = "Simple resizer"
 * )]
 */
class IiifResizeEffect extends IiifImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(IiifImage $image, IiifImageUrlParams $params) {

  }

}
