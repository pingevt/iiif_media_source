<?php

namespace Drupal\iiif_image_style\Plugin\IiifImageEffect;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\iiif_image_style\Attribute\IiifImageEffect;
use Drupal\iiif_image_style\IiifImageEffectBase;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;

/**
 *
 */
#[IiifImageEffect(
  id: "iiif_image_settings",
  label: new TranslatableMarkup("IIIF Image API Settings"),
  description: new TranslatableMarkup("Just a settings form for the basic IIIF Image API settings")
)]
class IiifSettingsEffect extends IiifImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(IiifImage $image, IiifImageUrlParams $params) {

  }

}
