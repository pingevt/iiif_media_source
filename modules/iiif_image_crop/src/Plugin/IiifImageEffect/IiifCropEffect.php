<?php

namespace Drupal\iiif_image_crop\Plugin\IiifImageEffect;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\iiif_image_handling\IiifImageEffectWithCropBase;
use Drupal\iiif_image_style\Attribute\IiifImageEffect;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;

/**
 *
 */
#[IiifImageEffect(
  id: "iiif_image_crop",
  label: new TranslatableMarkup("Crop Source"),
  description: new TranslatableMarkup("Simple Crop")
)]
class IiifCropEffect extends IiifImageEffectWithCropBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(IiifImage $image, IiifImageUrlParams $params, array $context = NULL) {

    $crop_type = \Drupal::config('iiif_image_crop.settings')->get('crop_type');
    $crop = $this->getCrop($image, $crop_type, $context);

    if (!$crop) {
      return;
    }

    $position = $crop->position();
    $size = $crop->size();

    $params->region = "x,y,w,h";
    $params->region_x = $position['x'];
    $params->region_y = $position['y'];
    $params->region_w = $size['width'];
    $params->region_h = $size['height'];

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $summary = [
      '#markup' => 'Apply Crop as source.',
    ];
    $summary += parent::getSummary();

    return $summary;
  }

}
