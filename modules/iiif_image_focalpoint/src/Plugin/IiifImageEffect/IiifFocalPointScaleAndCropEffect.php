<?php

namespace Drupal\iiif_image_focalpoint\Plugin\IiifImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\iiif_image_handling\IiifConfigurableImageEffectWithCropBase;
use Drupal\iiif_image_style\Attribute\IiifImageEffect;
use Drupal\iiif_media_source\Iiif\IiifImage;
use Drupal\iiif_media_source\Iiif\IiifImageUrlParams;

/**
 *
 */
#[IiifImageEffect(
  id: "iiif_focal_point_scale_crop",
  label: new TranslatableMarkup("Focal Point Scale & Crop"),
  description: new TranslatableMarkup("Simply scale and crop a photo to a specific size, with a focal point.")
)]
class IiifFocalPointScaleAndCropEffect extends IiifConfigurableImageEffectWithCropBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(IiifImage $image, IiifImageUrlParams $params, array $context = NULL) {

    $crop_type = \Drupal::config('iiif_image_focalpoint.settings')->get('crop_type');
    $crop = $this->getCrop($image, $crop_type, $context);

    $offset = ['x' => 0, 'y' => 0];
    $center = ['x' => 0, 'y' => 0];

    if ($params->getSetting('region') == "full") {
      $orig_width = $image->getWidth() ?? 1;
      $orig_height = $image->getHeight() ?? 1;
    }
    else {

      // Create temp Params with region only and get dimensions.
      $tempParams = IiifImageUrlParams::fullImageParams();
      $tempParams->applyRegionSettings($params->getRegionSettings());

      $dim = $tempParams->transformDimensions($image);
      $offset = $tempParams->transformPosition($image);

      $orig_width = $dim['width'];
      $orig_height = $dim['height'];

    }

    // Grab Crop center point, or default settings.
    if ($crop) {
      $center = $crop->position();
    }
    else {
      $default_center = \Drupal::config('iiif_image_focalpoint.settings')->get('default_value');
      [$x_ra, $y_ra] = explode(",", $default_center);

      $center['x'] = $orig_width * $x_ra / 100;
      $center['y'] = $orig_height * $y_ra / 100;
    }

    // Adjust if there is any offset.
    $center['x'] -= $offset['x'];
    $center['y'] -= $offset['y'];

    $fp_x = $center['x'] ? (int) $center['x'] : 0;
    $fp_y = $center['y'] ? (int) $center['y'] : 0;

    $dest_width = $this->configuration['width'] ? (int) $this->configuration['width'] : NULL;
    $dest_height = $this->configuration['height'] ? (int) $this->configuration['height'] : NULL;

    $dest_ratio = ($dest_height != 0) ? $dest_width / $dest_height : 1;
    $orig_ratio = $orig_width / $orig_height;

    if ($orig_ratio > $dest_ratio) {
      $region_width = $orig_height * $dest_width / $dest_height;
      $rx1 = (int) ($fp_x - ($region_width / 2));
      $rx2 = (int) ($fp_x + ($region_width / 2));
      $ry1 = 0;
      $ry2 = $orig_height;

      if ($rx1 < 0) {
        $rx2 += abs($rx1);
        $rx1 = 0;
      }
      if ($rx2 > $orig_height) {
        $rx1 -= $rx2 - $orig_height;
        $rx2 = $orig_height;
      }
    }
    else {
      $region_height = $orig_width * $dest_height / $dest_width;
      $rx1 = 0;
      $rx2 = $orig_width;
      $ry1 = (int) ($fp_y - ($region_height / 2));
      $ry2 = (int) ($region_height + $ry1);

      if ($ry1 < 0) {
        $ry2 += abs($ry1);
        $ry1 = 0;
      }
      if ($ry2 > $orig_height) {
        $ry1 -= $ry2 - $orig_height;
        $ry2 = $orig_height;
      }
    }

    // $region = "$rx1,$ry1," . ($rx2 - $rx1) . "," . ($ry2 - $ry1);
    // $size = "!$dest_width,$dest_height";
    $params->region = "x,y,w,h";
    $params->region_x = $rx1 + $offset['x'];
    $params->region_y = $ry1 + $offset['y'];
    $params->region_w = ($rx2 - $rx1);
    $params->region_h = ($ry2 - $ry1);
    $params->size = "!w,h";
    $params->size_w = $dest_width;
    $params->size_h = $dest_height;

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $summary = [
      '#theme' => 'iiif_focal_point_scale_crop_summary',
      '#data' => $this->configuration,
    ];
    $summary += parent::getSummary();

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'width' => NULL,
      'height' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    $form['width'] = [
      '#title' => $this->t('w'),
      '#type' => 'number',
      '#default_value' => $this->configuration['width'] ?? NULL,
      '#description' => $this->t(''),
    ];
    $form['height'] = [
      '#title' => $this->t('h'),
      '#type' => 'number',
      '#default_value' => $this->configuration['height'] ?? NULL,
      '#description' => $this->t(''),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['width'] = $form_state->getValue('width');
    $this->configuration['height'] = $form_state->getValue('height');
  }

}
