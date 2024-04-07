<?php

namespace Drupal\iiif_image_crop\Plugin\IiifImageEffect;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\iiif_image_style\Attribute\IiifImageEffect;
use Drupal\iiif_image_style\IiifImageEffectBase;

/**
 *
 */
#[IiifImageEffect(
  id: "iiif_image_crop",
  label: new TranslatableMarkup("IIIF Image Crop Effect"),
  description: new TranslatableMarkup("")
)]
class IiifCropEffect extends IiifImageEffectBase {

}
