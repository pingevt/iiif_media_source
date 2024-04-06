<?php

namespace Drupal\iiif_image_crop;

use Drupal\crop\Entity\Crop;
use Drupal\focal_point\FocalPointManager;
use Drupal\crop\CropInterface;
use Drupal\crop\CropStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\iiif_media_source\Iiif\IiifImage;

/**
 * Provides business logic related to focal point.
 */
class IiifCropManager {

  /**
   * Crop entity storage.
   *
   * @var \Drupal\crop\CropStorageInterface
   */
  protected $cropStorage;

  /**
   * Constructs FocalPointManager.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->cropStorage = $entity_type_manager->getStorage('crop');
  }

  /**
   *
   */
  public function getCropIiifEntity($item, $crop_type, $mid) {
// ksm($item, $crop_type, $mid);
    // ksm($item, $item->getEntity());
    // todo: fix this so it doesn't error out when we don't have a crop.
    // return NULL;

    $img = $item->getImg($item->getValue());
    $url = $img->getFullUrl();
// ksm($img, $url);
    if (Crop::cropExists($url, $crop_type)) {
      /** @var \Drupal\crop\CropInterface $crop */
      $crop = Crop::findCrop($url, $crop_type);
    }
    else {

      $values = [
        'type' => $crop_type,
        'entity_id' => $mid,
        'entity_type' => $item->getEntity()->bundle(),
        'uri' => $url,
      ];

      // ksm($values);

      $crop = $this->cropStorage->create($values);
    }

    // ksm($crop);

    return $crop;
  }

  /**
   * {@inheritdoc}
   */
  public function relativeToAbsolute($x, $y, $w, $h, $width, $height) {
    // Focal point JS provides relative location while crop entity
    // expects exact coordinate on the original image. Let's convert.
    return [
      'x' => (int) round(($x / 100.0) * $width),
      'y' => (int) round(($y / 100.0) * $height),
      'w' => (int) round(($w / 100.0) * $width),
      'h' => (int) round(($h / 100.0) * $height),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function absoluteToRelative($x, $y, $w, $h, $width, $height) {
    return [
      'x' => $width ? (float) round($x / $width * 10000) / 100 : 0,
      'y' => $height ? (float) round($y / $height * 10000) / 100 : 0,
      'w' => $width ? (float) round($w / $width * 10000) / 100 : 0,
      'h' => $height ? (float) round($h / $height * 10000) / 100 : 0,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function saveCropEntity(float $x, float $y, float $w, float $h, int $width, int $height, CropInterface $crop): CropInterface {
    $absolute = $this->relativeToAbsolute($x, $y, $w, $h, $width, $height);
ksm($x, $y, $w, $h, $width, $height);
    $crop->setPosition($absolute['x'], $absolute['y']);
    $crop->setSize($absolute['w'], $absolute['h']);
    $crop->save();

    return $crop;
  }

  /**
   *
   */
  public function applyCrop($image, &$style_settings, $crop) {

    // We will need to change the region setting in order to apply the crop.
    // 'full' => transform to "x,y,w,h", which is the cropped image settings.
    // 'square' => transform to "x,y,w,h", but based within the crop.
    // 'x,y,w,h' => transform so that values are inside the crop (or should we really do nothing?)
    // 'pct:x,y,w,h' => transform to "x,y,w,h", but percentages are based on the cropped image.

    // Apply this 'expandSettings' first, so we have all defaults.
    IiifImage::expandSettings($style_settings);

    [$x, $y] = array_values($crop->position());
    [$w, $h] = array_values($crop->size());

    switch($style_settings['region']) {
      case 'full':
        $region = [$x, $y, $w, $h];
        $style_settings['region'] = implode(",", $region);
        $style_settings['region_actual'] = implode(",", $region);

        break;

      case 'square':

        if ($w > $h) {
          $diff = $w - $h;
          $w = $h;
          $x += $diff / 2;
        }
        else {

          $diff = $h - $w;
          $h = $w;
          $y += $diff / 2;
        }

        $region = [$x, $y, $w, $h];
        $style_settings['region'] = implode(",", $region);
        $style_settings['region_actual'] = implode(",", $region);

        break;

      case 'x,y,w,h':
        // todo: figure this out.

        break;

      case 'pct:x,y,w,h':

        $nx = $x + (int) ($w * ($style_settings['region_x'] / 100));
        $ny = $y + (int) ($h * ($style_settings['region_y'] / 100));
        $nw = (int) ($w * ($style_settings['region_w'] / 100));
        $nh = (int) ($h * ($style_settings['region_h'] / 100));

        $region = [$nx, $ny, $nw, $nh];

        $style_settings['region'] = implode(",", $region);
        $style_settings['region_actual'] = implode(",", $region);

        break;
    }
  }

}
